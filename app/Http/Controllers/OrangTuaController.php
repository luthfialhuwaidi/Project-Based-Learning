<?php

namespace App\Http\Controllers;

use App\Models\Confirmation;
use App\Models\Delivery;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrangTuaController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $students = $user->students()->with(['school', 'school.deliveries' => function($q) {
            $q->whereDate('delivery_date', today())->with(['courier', 'latestTracking', 'confirmations']);
        }])->get();

        // Notifikasi yang belum dibaca
        $unreadNotifications = $user->unreadNotifications()->latest()->take(5)->get();

        return view('orangtua.dashboard', compact('students', 'unreadNotifications'));
    }

    public function trackDelivery(Delivery $delivery)
    {
        // Pastikan orang tua punya anak di sekolah ini
        $user = Auth::user();
        $hasChild = $user->students()->where('school_id', $delivery->school_id)->exists();

        if (!$hasChild) {
            abort(403);
        }

        $delivery->load(['courier', 'school', 'latestTracking',
            'trackings' => fn($q) => $q->orderBy('recorded_at', 'desc')->limit(50)
        ]);

        return view('orangtua.track-delivery', compact('delivery'));
    }

    public function confirmEaten(Request $request, Delivery $delivery)
    {
        $user = Auth::user();
        $student = $user->students()->where('school_id', $delivery->school_id)->first();

        if (!$student) {
            abort(403);
        }

        $request->validate([
            'notes' => 'nullable|string|max:300',
        ]);

        $confirmation = Confirmation::where('delivery_id', $delivery->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$confirmation) {
            return back()->with('error', 'Konfirmasi guru belum dilakukan.');
        }

        $confirmation->update([
            'parent_confirmed' => true,
            'parent_confirmed_at' => now(),
            'eaten_status' => true,
            'eaten_at' => now(),
            'notes' => $request->notes ?? $confirmation->notes,
        ]);

        // Cek apakah semua anak sudah makan
        $allEaten = Confirmation::where('delivery_id', $delivery->id)
            ->where('eaten_status', false)
            ->doesntExist();

        if ($allEaten) {
            $delivery->update(['status' => 'selesai']);
        }

        ActivityLog::record('konfirmasi_makan', $confirmation, ['student' => $student->name]);

        return back()->with('success', $student->name . ' telah dikonfirmasi sudah makan!');
    }

    public function history()
    {
        $user = Auth::user();
        $students = $user->students()->with('school')->get();
        $schoolIds = $students->pluck('school_id');

        $deliveries = Delivery::whereIn('school_id', $schoolIds)
            ->with(['school', 'confirmations' => function($q) use ($students) {
                $q->whereIn('student_id', $students->pluck('id'));
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('orangtua.history', compact('deliveries', 'students'));
    }

    public function markNotificationRead(Request $request)
    {
        $user = Auth::user();
        if ($request->id) {
            $user->notifications()->where('id', $request->id)->update(['read_at' => now()]);
        } else {
            $user->unreadNotifications->markAsRead();
        }
        return response()->json(['success' => true]);
    }
}
