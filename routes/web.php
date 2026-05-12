<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\PetugasController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', fn() => redirect()->route('login'));

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// === PETUGAS (KURIR) ROUTES ===
Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengiriman/buat', [PetugasController::class, 'createDelivery'])->name('delivery.create');
    Route::post('/pengiriman/buat', [PetugasController::class, 'storeDelivery'])->name('delivery.store');
    Route::get('/pengiriman/{delivery}', [PetugasController::class, 'showDelivery'])->name('delivery.show');
    Route::post('/pengiriman/{delivery}/status', [PetugasController::class, 'updateStatus'])->name('delivery.update-status');
    Route::get('/riwayat', [PetugasController::class, 'history'])->name('history');
});

// === GURU ROUTES ===
Route::prefix('guru')->name('guru.')->middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
    Route::get('/tracking/{delivery}', [GuruController::class, 'trackDelivery'])->name('track');
    Route::post('/konfirmasi/{delivery}', [GuruController::class, 'confirmReceipt'])->name('confirm');
    Route::get('/riwayat', [GuruController::class, 'history'])->name('history');
});

// === ORANG TUA ROUTES ===
Route::prefix('orangtua')->name('orangtua.')->middleware(['auth', 'role:orangtua'])->group(function () {
    Route::get('/dashboard', [OrangTuaController::class, 'dashboard'])->name('dashboard');
    Route::get('/tracking/{delivery}', [OrangTuaController::class, 'trackDelivery'])->name('track');
    Route::post('/konfirmasi-makan/{delivery}', [OrangTuaController::class, 'confirmEaten'])->name('confirm-eaten');
    Route::get('/riwayat', [OrangTuaController::class, 'history'])->name('history');
    Route::post('/notifikasi/baca', [OrangTuaController::class, 'markNotificationRead'])->name('notification.read');
});
