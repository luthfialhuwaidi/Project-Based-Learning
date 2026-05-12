@extends('layouts.app')

@section('title', 'Tracking Kurir')
@section('page-title', 'Tracking Lokasi Kurir')

@section('sidebar-nav')
<a href="{{ route('orangtua.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('orangtua.history') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Riwayat MBG
</a>
@endsection

@push('styles')
<style>#map { height: 450px; border-radius: 16px; }</style>
@endpush

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="space-y-5">
        {{-- Status Card --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-2xl p-6 shadow-lg">
            <p class="text-orange-100 text-sm mb-2 flex items-center gap-2">
                <span class="w-2 h-2 bg-orange-300 rounded-full pulse-dot"></span>
                Status Pengiriman
            </p>
            <h3 class="text-2xl font-bold">{{ $delivery->status_label }}</h3>
            <p class="text-orange-200 mt-1 text-sm">{{ $delivery->kode_pengiriman }}</p>
            <div class="mt-4 bg-white/20 rounded-xl px-4 py-3 text-sm">
                <p class="text-orange-100">🏫 {{ $delivery->school->name }}</p>
                <p class="text-orange-200 mt-0.5">🚚 Kurir: {{ $delivery->courier->name }}</p>
            </div>
        </div>

        {{-- ETA Info --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-700 mb-3 text-sm">⏱️ Waktu Pengiriman</h4>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Mulai Kirim</span>
                    <span class="font-semibold text-gray-800">{{ $delivery->started_at ? $delivery->started_at->format('H:i') : 'Belum mulai' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Tiba di Sekolah</span>
                    <span class="font-semibold text-gray-800">{{ $delivery->arrived_at ? $delivery->arrived_at->format('H:i') : 'Menunggu...' }}</span>
                </div>
            </div>
        </div>

        {{-- Live Coords --}}
        @if($delivery->latestTracking)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-700 mb-3 text-sm">📍 Posisi Terkini Kurir</h4>
            <p class="font-mono text-sm text-gray-700" id="live-coords">
                {{ number_format($delivery->latestTracking->latitude, 6) }},
                {{ number_format($delivery->latestTracking->longitude, 6) }}
            </p>
            <p class="text-xs text-gray-400 mt-1" id="live-time">
                Update: {{ $delivery->latestTracking->recorded_at?->format('H:i:s') }}
            </p>
        </div>
        @endif

        <a href="{{ route('orangtua.dashboard') }}"
            class="flex items-center justify-center gap-2 w-full py-3 border-2 border-gray-200 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition-colors text-sm">
            ← Kembali ke Dashboard
        </a>
    </div>

    {{-- Map --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-800 mb-4">🗺️ Peta Lokasi Kurir (View Only)</h3>
        <div id="map"></div>
        <p class="text-xs text-gray-400 text-center mt-3">Peta akan otomatis update saat kurir bergerak</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
const schoolLat = {{ $delivery->school->latitude ?? 0 }};
const schoolLng = {{ $delivery->school->longitude ?? 0 }};
const deliveryId = {{ $delivery->id }};

const map = L.map('map').setView([schoolLat, schoolLng], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'© OSM'}).addTo(map);

// Marker sekolah
L.marker([schoolLat, schoolLng], {
    icon: L.divIcon({className:'', html:'<div style="background:#f97316;color:white;border-radius:50%;width:38px;height:38px;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 2px 8px rgba(0,0,0,0.3)">🏫</div>', iconSize:[38,38], iconAnchor:[19,19]})
}).addTo(map).bindPopup('{{ $delivery->school->name }}');

let courierMarker = null;
let pathPoints = [];
let pathLine = null;

@if($delivery->latestTracking)
courierMarker = L.marker([{{ $delivery->latestTracking->latitude }}, {{ $delivery->latestTracking->longitude }}], {
    icon: L.divIcon({className:'', html:'<div style="background:#ea580c;color:white;border-radius:50%;width:38px;height:38px;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 2px 8px rgba(0,0,0,0.3)">🚚</div>', iconSize:[38,38], iconAnchor:[19,19]})
}).addTo(map).bindPopup('Kurir MBG');
@endif

@foreach($delivery->trackings as $t)
pathPoints.push([{{ $t->latitude }}, {{ $t->longitude }}]);
@endforeach

if (pathPoints.length > 1) {
    pathLine = L.polyline(pathPoints, {color:'#f97316', weight:3, opacity:0.7}).addTo(map);
}

// Realtime
const channel = pusher.subscribe('delivery.' + deliveryId);
channel.bind('location.updated', function(data) {
    if (!courierMarker) {
        courierMarker = L.marker([data.latitude, data.longitude], {
            icon: L.divIcon({className:'', html:'<div style="background:#ea580c;color:white;border-radius:50%;width:38px;height:38px;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 2px 8px rgba(0,0,0,0.3)">🚚</div>', iconSize:[38,38], iconAnchor:[19,19]})
        }).addTo(map);
    } else {
        courierMarker.setLatLng([data.latitude, data.longitude]);
    }

    pathPoints.push([data.latitude, data.longitude]);
    if (pathLine) pathLine.setLatLngs(pathPoints);
    else pathLine = L.polyline(pathPoints, {color:'#f97316', weight:3}).addTo(map);

    const coordEl = document.getElementById('live-coords');
    const timeEl = document.getElementById('live-time');
    if (coordEl) coordEl.textContent = data.latitude.toFixed(6) + ', ' + data.longitude.toFixed(6);
    if (timeEl) timeEl.textContent = 'Update: ' + new Date(data.recorded_at).toLocaleTimeString('id-ID');

    map.panTo([data.latitude, data.longitude]);
});

channel.bind('status.updated', function(data) {
    if (data.status === 'sudah_sampai' || data.status === 'diterima_guru') {
        setTimeout(() => location.reload(), 1500);
    }
});
</script>
@endpush
