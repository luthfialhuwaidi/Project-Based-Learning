@extends('layouts.app')

@section('title', 'Detail Pengiriman - ' . $delivery->kode_pengiriman)
@section('page-title', 'Detail Pengiriman')

@section('sidebar-nav')
<a href="{{ route('petugas.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('petugas.delivery.create') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Buat Pengiriman
</a>
<a href="{{ route('petugas.history') }}" class="sidebar-link {{ request()->routeIs('petugas.history') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Riwayat Pengiriman
</a>
@endsection

@push('styles')
<style>
    #map { height: 400px; border-radius: 16px; }
    .leaflet-container { border-radius: 16px; }
</style>
@endpush

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column --}}
    <div class="lg:col-span-1 space-y-5">
        {{-- Info Pengiriman --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span>📦</span> Info Pengiriman
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Kode Pengiriman</p>
                    <p class="font-bold text-gray-800 text-lg">{{ $delivery->kode_pengiriman }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Tujuan Sekolah</p>
                    <p class="font-semibold text-gray-700">{{ $delivery->school->name }}</p>
                    <p class="text-sm text-gray-500">{{ $delivery->school->address }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Porsi</p>
                        <p class="font-bold text-gray-800">{{ $delivery->total_portions }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Tanggal</p>
                        <p class="font-semibold text-gray-700">{{ $delivery->delivery_date->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Status</p>
                    <span class="status-badge mt-1
                        @if(in_array($delivery->status, ['selesai', 'diterima_guru', 'diterima_murid'])) bg-green-100 text-green-700
                        @elseif($delivery->status === 'dalam_perjalanan') bg-blue-100 text-blue-700
                        @elseif($delivery->status === 'sudah_sampai') bg-teal-100 text-teal-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                        <span class="w-2 h-2 rounded-full
                            @if(in_array($delivery->status, ['selesai', 'diterima_guru'])) bg-green-500
                            @elseif($delivery->status === 'dalam_perjalanan') bg-blue-500 pulse-dot
                            @else bg-yellow-500 @endif"></span>
                        {{ $delivery->status_label }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Update Status --}}
        @php
        $validTransitions = ['dimasak' => 'dikemas', 'dikemas' => 'dalam_perjalanan', 'dalam_perjalanan' => 'sudah_sampai', 'sudah_sampai' => 'selesai'];
        $nextStatus = $validTransitions[$delivery->status] ?? null;
        $nextLabels = ['dikemas' => '📦 Tandai Sedang Dikemas', 'dalam_perjalanan' => '🚚 Mulai Perjalanan', 'sudah_sampai' => '📍 Tandai Sudah Sampai', 'selesai' => '✅ Selesai Mengantar'];
        @endphp
        @if($nextStatus && !in_array($delivery->status, ['selesai']))
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4">🔄 Update Status</h3>
            <form action="{{ route('petugas.delivery.update-status', $delivery) }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full py-4 rounded-xl font-bold text-white transition-all
                        @if($nextStatus === 'dalam_perjalanan') bg-blue-600 hover:bg-blue-700 shadow-blue-200
                        @elseif($nextStatus === 'selesai') bg-green-600 hover:bg-green-700 shadow-green-200
                        @else bg-yellow-500 hover:bg-yellow-600 shadow-yellow-200 @endif
                        shadow-lg">
                    {{ $nextLabels[$nextStatus] ?? $nextStatus }}
                </button>
            </form>
        </div>
        @endif

        {{-- Live Tracking Control (ketika dalam perjalanan) --}}
        @if($delivery->status === 'dalam_perjalanan')
        <div class="bg-blue-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-3 h-3 bg-blue-300 rounded-full pulse-dot"></span>
                <h3 class="font-bold">Live GPS Tracking</h3>
            </div>
            <p class="text-blue-200 text-sm mb-4">Lokasi Anda sedang dibagikan secara realtime ke sekolah dan orang tua.</p>
            <div id="tracking-status" class="bg-white/20 rounded-xl p-3 text-sm">
                <p id="tracking-info">Menunggu GPS...</p>
                <p id="tracking-coords" class="text-blue-200 text-xs mt-1"></p>
            </div>
        </div>
        @endif
    </div>

    {{-- Right Column - Map --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span>📍</span> Peta Tracking
                </h3>
                @if($delivery->latestTracking)
                <span class="text-xs text-gray-400">
                    Update: {{ $delivery->latestTracking->recorded_at?->diffForHumans() }}
                </span>
                @endif
            </div>
            <div id="map"></div>
        </div>

        {{-- Tracking History --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">📋 Riwayat Pergerakan</h3>
            </div>
            <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto">
                @forelse($delivery->trackings->take(20) as $track)
                <div class="px-6 py-3 flex items-center justify-between text-sm">
                    <div>
                        <span class="font-mono text-gray-700">{{ number_format($track->latitude, 6) }}, {{ number_format($track->longitude, 6) }}</span>
                    </div>
                    <span class="text-gray-400 text-xs">{{ $track->recorded_at->format('H:i:s') }}</span>
                </div>
                @empty
                <div class="px-6 py-8 text-center">
                    <p class="text-gray-400 text-sm">Belum ada data tracking</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Koordinat sekolah tujuan
const schoolLat = {{ $delivery->school->latitude ?? -6.2 }};
const schoolLng = {{ $delivery->school->longitude ?? 106.8 }};
const deliveryId = {{ $delivery->id }};
const deliveryStatus = '{{ $delivery->status }}';

// Init Peta
const map = L.map('map').setView([schoolLat, schoolLng], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

// Marker Sekolah
const schoolIcon = L.divIcon({
    className: 'custom-icon',
    html: '<div style="background:#16a34a;color:white;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 2px 8px rgba(0,0,0,0.3)">🏫</div>',
    iconSize: [36, 36],
    iconAnchor: [18, 18]
});
const schoolMarker = L.marker([schoolLat, schoolLng], {icon: schoolIcon})
    .addTo(map)
    .bindPopup('<strong>{{ $delivery->school->name }}</strong><br>Tujuan pengiriman');

// Marker Kurir
const courierIcon = L.divIcon({
    className: 'custom-icon',
    html: '<div style="background:#2563eb;color:white;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 2px 8px rgba(0,0,0,0.3)">🚚</div>',
    iconSize: [36, 36],
    iconAnchor: [18, 18]
});

let courierMarker = null;
let trackingPath = [];
let pathLine = null;

@if($delivery->latestTracking)
const initialLat = {{ $delivery->latestTracking->latitude }};
const initialLng = {{ $delivery->latestTracking->longitude }};
courierMarker = L.marker([initialLat, initialLng], {icon: courierIcon})
    .addTo(map)
    .bindPopup('Posisi Kurir Terakhir');
trackingPath.push([initialLat, initialLng]);
@endif

// Gambar rute tracking yang ada
@foreach($delivery->trackings->take(100) as $track)
trackingPath.push([{{ $track->latitude }}, {{ $track->longitude }}]);
@endforeach

if (trackingPath.length > 1) {
    pathLine = L.polyline(trackingPath, {color: '#3b82f6', weight: 3, opacity: 0.7}).addTo(map);
    map.fitBounds(pathLine.getBounds().pad(0.1));
}

// === REALTIME: Pusher Subscribe ===
const channel = pusher.subscribe('delivery.' + deliveryId);

channel.bind('location.updated', function(data) {
    const lat = data.latitude;
    const lng = data.longitude;

    if (!courierMarker) {
        courierMarker = L.marker([lat, lng], {icon: courierIcon}).addTo(map).bindPopup('Posisi Kurir');
    } else {
        courierMarker.setLatLng([lat, lng]);
    }
    courierMarker.bindPopup(`<strong>Kurir: {{ $delivery->courier->name }}</strong><br>Update: ${new Date(data.recorded_at).toLocaleTimeString('id-ID')}`);

    trackingPath.push([lat, lng]);
    if (pathLine) {
        pathLine.setLatLngs(trackingPath);
    } else {
        pathLine = L.polyline(trackingPath, {color: '#3b82f6', weight: 3}).addTo(map);
    }

    map.panTo([lat, lng]);
});

channel.bind('status.updated', function(data) {
    if (data.status === 'sudah_sampai' || data.status === 'selesai') {
        location.reload();
    }
});

// === LIVE GPS SHARING (Hanya kalau status dalam_perjalanan) ===
@if($delivery->status === 'dalam_perjalanan')
const apiToken = '{{ auth()->user()->createToken('tracking')->plainTextToken }}';
let watchId = null;
let trackingInterval = null;

function startTracking() {
    if (!navigator.geolocation) {
        document.getElementById('tracking-info').textContent = 'GPS tidak tersedia di browser ini';
        return;
    }

    watchId = navigator.geolocation.watchPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            document.getElementById('tracking-info').textContent = '✅ GPS aktif - Sedang berbagi lokasi';
            document.getElementById('tracking-coords').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)} (akurasi: ${Math.round(accuracy)}m)`;

            // Kirim ke server setiap 5 detik
            if (!trackingInterval) {
                trackingInterval = setInterval(function() {
                    sendLocation(lat, lng, accuracy);
                }, 5000);
            }
        },
        function(err) {
            document.getElementById('tracking-info').textContent = '❌ GPS error: ' + err.message;
        },
        { enableHighAccuracy: true, maximumAge: 3000, timeout: 10000 }
    );
}

function sendLocation(lat, lng, accuracy) {
    fetch('/api/tracking/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + apiToken,
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify({
            delivery_id: deliveryId,
            latitude: lat,
            longitude: lng,
            accuracy: accuracy
        })
    }).catch(err => console.error('Tracking error:', err));
}

// Mulai tracking otomatis
startTracking();
@endif
</script>
@endpush
