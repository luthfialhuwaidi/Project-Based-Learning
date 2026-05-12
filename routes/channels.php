<?php

use App\Models\Delivery;
use Illuminate\Support\Facades\Broadcast;

// Channel untuk pengiriman spesifik (semua yang auth boleh listen)
Broadcast::channel('delivery.{deliveryId}', function ($user, $deliveryId) {
    $delivery = Delivery::find($deliveryId);
    if (!$delivery) return false;

    // Kurir pemilik
    if ($user->isPetugas() && $delivery->courier_id === $user->id) return true;

    // Guru di sekolah tujuan
    if ($user->isGuru() && $user->school?->id === $delivery->school_id) return true;

    // Orang tua yang punya anak di sekolah tujuan
    if ($user->isOrangTua()) {
        return $user->students()->where('school_id', $delivery->school_id)->exists();
    }

    return false;
});

// Channel untuk sekolah (guru + orang tua siswa sekolah tsb)
Broadcast::channel('school.{schoolId}', function ($user, $schoolId) {
    if ($user->isGuru() && $user->school?->id == $schoolId) return true;
    if ($user->isOrangTua()) {
        return $user->students()->where('school_id', $schoolId)->exists();
    }
    if ($user->isPetugas()) return true;
    return false;
});

// Channel publik untuk semua pengiriman (guru & petugas)
Broadcast::channel('public-deliveries', function ($user) {
    return $user->isPetugas() || $user->isGuru();
});
