<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Confirmation;
use App\Models\Delivery;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MakananDiterimaNofification;

class GuruController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return view('guru.no-school');
        }

        $activeDeliveries = Delivery::where('school_id', $school->id)
            ->whereNotIn('status', ['selesai'])
            ->whereDate('delivery_date', today())
            ->with(['courier', 'latestTracking'])
            ->get();

        $todayDeliveries = Delivery::where('school_id', $school->id)
            ->whereDate('delivery_date', today())
            ->with(['courier', 'latestTracking', 'confirmations'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalStudents = $school->students()->where('is_active', true)->count();
        $confirmedToday = Confirmation::whereHas('delivery', function($q) use ($school) {
                $q->where('school_id', $school->id)->whereDate('delivery_date', today());
            })
            ->where('teacher_confirmed', true)
            ->count();

        return view('guru.dashboard', compact(
            'school', 'activeDeliveries', 'todayDeliveries', 'totalStudents', 'confirmedToday'
        ));
    }

    public function trackDelivery(Delivery $delivery)
    {
        $school = Auth::user()->school;

        if (!$school || $delivery->school_id !== $school->id) {
            abort(403);
        }

        $delivery->load(['courier', 'school', 'latestTracking',
            'trackings' => fn($q) => $q->orderBy('recorded_at', 'desc')->limit(100)
        ]);

        return view('guru.track-delivery', compact('delivery'));
    }

    public function confirmReceipt(Request $request, Delivery $delivery)
    {
        $school = Auth::user()->school;

        if (!$school || $delivery->school_id !== $school->id) {
            abort(403);
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        // Konfirmasi penerimaan untuk semua siswa sekolah
        $students = $school->students()->where('is_active', true)->get();

        foreach ($students as $student) {
            Confirmation::updateOrCreate(
                ['delivery_id' => $delivery->id, 'student_id' => $student->id],
                [
                    'teacher_confirmed' => true,
                    'teacher_confirmed_at' => now(),
                    'teacher_id' => Auth::id(),
                    'notes' => $request->notes,
                ]
            );

            // Notifikasi ke orang tua
            if ($student->parent) {
                $student->parent->notify(new MakananDiterimaNofification($delivery, $student));
            }
        }

        $delivery->update(['status' => 'diterima_guru']);

        ActivityLog::record('konfirmasi_guru', $delivery, ['school' => $school->name]);

        return back()->with('success', 'Penerimaan makanan telah dikonfirmasi dan orang tua telah diberitahu.');
    }

    public function history()
    {
        $school = Auth::user()->school;

        $deliveries = Delivery::where('school_id', $school?->id)
            ->with(['courier', 'confirmations'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('guru.history', compact('deliveries'));
    }
}
