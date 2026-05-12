<?php

use App\Http\Controllers\Api\TrackingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API Routes menggunakan Sanctum token authentication
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', fn(Request $request) => $request->user());

    // Tracking endpoints
    Route::prefix('tracking')->name('api.tracking.')->group(function () {
        // Update lokasi GPS (khusus petugas)
        Route::post('/update', [TrackingController::class, 'updateLocation'])->name('update');

        // Get lokasi terbaru suatu pengiriman
        Route::get('/{delivery}', [TrackingController::class, 'getLatestLocation'])->name('latest');

        // Get riwayat tracking suatu pengiriman
        Route::get('/{delivery}/history', [TrackingController::class, 'getTrackingHistory'])->name('history');
    });

    // Active deliveries
    Route::get('/deliveries/active', [TrackingController::class, 'activeDeliveries'])->name('api.deliveries.active');
});

// Public: untuk pengecekan status tanpa auth (opsional, tambah rate limit di produksi)
Route::get('/status/{kode}', function ($kode) {
    $delivery = \App\Models\Delivery::where('kode_pengiriman', $kode)
        ->with(['school:id,name', 'courier:id,name'])
        ->first();

    if (!$delivery) {
        return response()->json(['error' => 'Kode pengiriman tidak ditemukan'], 404);
    }

    return response()->json([
        'kode_pengiriman' => $delivery->kode_pengiriman,
        'status' => $delivery->status,
        'status_label' => $delivery->status_label,
        'school' => $delivery->school->name,
        'courier' => $delivery->courier->name,
        'delivery_date' => $delivery->delivery_date->format('d/m/Y'),
    ]);
})->name('api.status.check');
