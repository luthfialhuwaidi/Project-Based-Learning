<?php

namespace App\Http\Controllers;

use App\Events\DeliveryStatusUpdated;
use App\Models\ActivityLog;
use App\Models\Delivery;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $activeDelivery = Delivery::where('courier_id', $user->id)
            ->whereNotIn('status', ['selesai'])
            ->whereDate('delivery_date', today())
            ->with(['school', 'latestTracking'])
            ->first();

        $todayDeliveries = Delivery::where('courier_id', $user->id)
            ->whereDate('delivery_date', today())
            ->with('school')
            ->get();

        $totalDeliveries = Delivery::where('courier_id', $user->id)->count();
        $completedToday = Delivery::where('courier_id', $user->id)
            ->whereDate('delivery_date', today())
            ->where('status', 'selesai')
            ->count();

        return view('petugas.dashboard', compact(
            'activeDelivery', 'todayDeliveries', 'totalDeliveries', 'completedToday'
        ));
    }

    public function createDelivery()
    {
        $schools = School::orderBy('name')->get();
        return view('petugas.create-delivery', compact('schools'));
    }

    public function storeDelivery(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'total_portions' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        // Cek apakah sudah ada pengiriman aktif
        $existing = Delivery::where('courier_id', Auth::id())
            ->whereNotIn('status', ['selesai'])
            ->whereDate('delivery_date', today())
            ->where('school_id', $request->school_id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Sudah ada pengiriman aktif ke sekolah ini hari ini.');
        }

        $delivery = Delivery::create([
            'kode_pengiriman' => Delivery::generateKode(),
            'courier_id' => Auth::id(),
            'school_id' => $request->school_id,
            'status' => 'dimasak',
            'total_portions' => $request->total_portions,
            'notes' => $request->notes,
            'delivery_date' => today(),
        ]);

        ActivityLog::record('buat_pengiriman', $delivery, ['kode' => $delivery->kode_pengiriman]);
        broadcast(new DeliveryStatusUpdated($delivery->load('school', 'courier')))->toOthers();

        return redirect()->route('petugas.delivery.show', $delivery)
            ->with('success', 'Pengiriman berhasil dibuat dengan kode: ' . $delivery->kode_pengiriman);
    }

    public function showDelivery(Delivery $delivery)
    {
        $this->authorizeDelivery($delivery);
        $delivery->load(['school', 'trackings' => function($q) {
            $q->orderBy('recorded_at', 'desc')->limit(50);
        }]);
        return view('petugas.delivery-detail', compact('delivery'));
    }

    public function updateStatus(Request $request, Delivery $delivery)
    {
        $this->authorizeDelivery($delivery);

        $validTransitions = [
            'dimasak' => 'dikemas',
            'dikemas' => 'dalam_perjalanan',
            'dalam_perjalanan' => 'sudah_sampai',
            'sudah_sampai' => 'selesai',
        ];

        $nextStatus = $validTransitions[$delivery->status] ?? null;

        if (!$nextStatus) {
            return back()->with('error', 'Status tidak dapat diubah lagi.');
        }

        $data = ['status' => $nextStatus];

        if ($nextStatus === 'dalam_perjalanan') {
            $data['started_at'] = now();
        } elseif ($nextStatus === 'sudah_sampai') {
            $data['arrived_at'] = now();
        } elseif ($nextStatus === 'selesai') {
            $data['completed_at'] = now();
        }

        $delivery->update($data);
        $delivery->load('school', 'courier');

        ActivityLog::record('update_status', $delivery, ['status' => $nextStatus]);
        broadcast(new DeliveryStatusUpdated($delivery))->toOthers();

        return back()->with('success', 'Status berhasil diperbarui ke: ' . $delivery->fresh()->status_label);
    }

    public function history()
    {
        $deliveries = Delivery::where('courier_id', Auth::id())
            ->with('school')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('petugas.history', compact('deliveries'));
    }

    private function authorizeDelivery(Delivery $delivery): void
    {
        if ($delivery->courier_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pengiriman ini.');
        }
    }
}
