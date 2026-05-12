<?php

namespace App\Http\Controllers\Api;

use App\Events\LocationUpdated;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryTracking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TrackingController extends Controller
{
    /**
     * POST /api/tracking/update
     * Digunakan oleh petugas untuk update lokasi GPS realtime
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $request->validate([
            'delivery_id' => 'required|exists:deliveries,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
        ]);

        $delivery = Delivery::findOrFail($request->delivery_id);

        // Validasi: hanya courier pemilik delivery
        if ($delivery->courier_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!in_array($delivery->status, ['dalam_perjalanan', 'sudah_sampai'])) {
            return response()->json(['error' => 'Pengiriman belum dalam perjalanan'], 422);
        }

        $tracking = DeliveryTracking::create([
            'delivery_id' => $delivery->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'speed' => $request->speed,
            'recorded_at' => now(),
        ]);

        // Broadcast ke channel realtime
        broadcast(new LocationUpdated($delivery->load('courier', 'school'), $tracking))->toOthers();

        return response()->json([
            'success' => true,
            'tracking' => [
                'id' => $tracking->id,
                'latitude' => $tracking->latitude,
                'longitude' => $tracking->longitude,
                'recorded_at' => $tracking->recorded_at->toISOString(),
            ]
        ]);
    }

    /**
     * GET /api/tracking/{delivery}
     * Ambil posisi terbaru pengiriman
     */
    public function getLatestLocation(Delivery $delivery): JsonResponse
    {
        $tracking = $delivery->latestTracking;

        if (!$tracking) {
            return response()->json(['error' => 'Belum ada data lokasi'], 404);
        }

        return response()->json([
            'delivery_id' => $delivery->id,
            'kode_pengiriman' => $delivery->kode_pengiriman,
            'status' => $delivery->status,
            'status_label' => $delivery->status_label,
            'courier' => [
                'id' => $delivery->courier_id,
                'name' => $delivery->courier->name,
            ],
            'school' => [
                'id' => $delivery->school_id,
                'name' => $delivery->school->name,
                'latitude' => $delivery->school->latitude,
                'longitude' => $delivery->school->longitude,
            ],
            'location' => [
                'latitude' => $tracking->latitude,
                'longitude' => $tracking->longitude,
                'accuracy' => $tracking->accuracy,
                'speed' => $tracking->speed,
                'recorded_at' => $tracking->recorded_at->toISOString(),
            ]
        ]);
    }

    /**
     * GET /api/tracking/{delivery}/history
     * Ambil riwayat tracking
     */
    public function getTrackingHistory(Delivery $delivery): JsonResponse
    {
        $trackings = $delivery->trackings()
            ->orderBy('recorded_at', 'asc')
            ->get(['latitude', 'longitude', 'speed', 'recorded_at']);

        return response()->json([
            'delivery_id' => $delivery->id,
            'kode_pengiriman' => $delivery->kode_pengiriman,
            'trackings' => $trackings,
        ]);
    }

    /**
     * GET /api/deliveries/active
     * Ambil semua pengiriman aktif hari ini
     */
    public function activeDeliveries(Request $request): JsonResponse
    {
        $query = Delivery::whereNotIn('status', ['selesai'])
            ->whereDate('delivery_date', today())
            ->with(['courier', 'school', 'latestTracking']);

        if ($request->school_id) {
            $query->where('school_id', $request->school_id);
        }

        $deliveries = $query->get()->map(function($d) {
            return [
                'id' => $d->id,
                'kode_pengiriman' => $d->kode_pengiriman,
                'status' => $d->status,
                'status_label' => $d->status_label,
                'courier_name' => $d->courier->name,
                'school_name' => $d->school->name,
                'school_lat' => $d->school->latitude,
                'school_lng' => $d->school->longitude,
                'latest_location' => $d->latestTracking ? [
                    'latitude' => $d->latestTracking->latitude,
                    'longitude' => $d->latestTracking->longitude,
                    'recorded_at' => $d->latestTracking->recorded_at?->toISOString(),
                ] : null,
            ];
        });

        return response()->json(['deliveries' => $deliveries]);
    }
}
