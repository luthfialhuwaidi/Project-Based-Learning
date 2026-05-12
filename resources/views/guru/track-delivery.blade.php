@extends('layouts.app')

@section('title', 'Live Tracking - ' . $delivery->kode_pengiriman)
@section('page-title', 'Live Tracking Kurir')

@section('sidebar-nav')
<a href="{{ route('guru.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('guru.history') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Riwayat MBG
</a>
@endsection

@push('styles')
<style>
    #map { height: 500px; border-radius: 16px; }
    .info-pill { @apply bg-white rounded-2xl shadow-sm border border-gray-100 p-5; }
</style>
@endpush

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Info Panel --}}
    <div class="space-y-5">
        {{-- Status Card --}}
        <div class="bg-gradient-to-br from-purple-600 to-purple-700 text-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2.5 h-2.5 bg-purple-300 rounded-full pulse-dot"></span>
                <span class="text-purple-200 text-sm font-medium">Live Tracking</span>
            </div>
            <h3 class="text-xl font-bold">{{ $delivery->kode_pengiriman }}</h3>
            <p class="text-purple-200 text-sm mt-1">{{ $delivery->school->name }}</p>
            <div class="mt-4 bg-white/20 rounded-xl px-4 py-3">
                <p class="text-purple-100 text-xs font-medium uppercase tracking-wide">Status Saat Ini</p>
                <p class="text-white font-bold text-lg mt-0.5">{{ $delivery->status_label }}</p>
            </div>
        </div>

        {{-- Courier Info --}}
        <div class="info-pill">
            <h4 class="font-bold text-gray-700 mb-3 text-sm uppercase tracking-wide">👤 Info Kurir</h4>
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-xl">🚚</div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $delivery->courier->name }}</p>
                        <p class="text-xs text-gray-400">{{ $delivery->courier->phone ?? 'Tidak ada nomor' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delivery Info --}}
        <div class="info-pill">
            <h4 class="font-bold text-gray-700 mb-3 text-sm uppercase tracking-wide">📦 Info Pengiriman</h4>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Porsi</span>
                    <span class="text-sm font-bold text-gray-800">{{ $delivery->total_portions }} porsi</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Mulai Kirim</span>
                    <span class="text-sm font-semibold text-gray-700">{{ $delivery->started_at ? $delivery->started_at->format('H:i') : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Tiba</span>
                    <span class="text-sm font-semibold text-gray-700">{{ $delivery->arrived_at ? $delivery->arrived_at->format('H:i') : '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Live Location Info --}}
        <div class="info-pill" id="location-card">
            <h4 class="font-bold text-gray-700 mb-3 text-sm uppercase tracking-wide">📍 Posisi Terkini</h4>
            @if($delivery->latestTracking)
            <div class="space-y-1">
                <p class="font-mono text-sm text-gray-800" id="live-coords">
                    {{ number_format($delivery->latestTracking->latitude, 6) }},
                    {{ number_format($delivery->latestTracking->longitude, 6) }}
                </p>
                <p class="text-xs text-gray-400" id="live-time">
                    Update: {{ $delivery->latestTracking->recorded_at?->format('H:i:s') }}
                </p>
            </div>
            @else
            <p class="text-sm text-gray-400" id="live-coords">Menunggu data GPS...</p>
            <p class="text-xs text-gray-300" id="live-time"></p>
            @endif
        </div>

        {{-- Konfirmasi Button --}}
        @if($delivery->status === 'sudah_sampai')
        <div class="info-pill border-2 border-green-200">
            <h4 class="font-bold text-green-700 mb-2">✅ Makanan Sudah Sampai!</h4>
            <p class="text-sm text-gray-500 mb-4">Konfirmasi bahwa makanan telah diterima dengan baik di sekolah.</p>
            <form action="{{ route('guru.confirm', $delivery) }}" method="POST">
                @csrf
                <textarea name="notes" placeholder="Catatan kondisi makanan (opsional)" rows="2"
                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 mb-3 focus:ring-2 focus:ring-green-500 outline-none resize-none"></textarea>
                <button type="submit" class="w-full py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition-colors">
                    ✅ Konfirmasi Penerimaan Makanan
                </button>
            </form>
        </div>
        @elseif(in_array($delivery->status, ['diterima_guru', 'selesai']))
        <div class="bg-green-50 border border-green-200 rounded-2xl p-5 text-center">
            <span class="text-4xl">🎉</span>
            <p class="text-green-800 font-bold mt-2">Sudah Dikonfirmasi!</p>
            <p class="text-green-600 text-sm">Orang tua telah dinotifikasi</p>
        </div>
        @endif
    </div>

    {{-- Map --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    🗺️ Peta Tracking Realtime
                </h3>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span class="w-3 h-3 rounded-full bg-blue-500 inline-block pulse-dot"></span>
                    Live
                </div>
            </div>
            <div id="map"></div>

            {{-- Legend --}}
            <div class="flex items-center gap-6 mt-4">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <span class="text-lg">🚚</span> Kurir
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <span class="text-lg">🏫</span> Sekolah Tujuan
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <div class="w-8 h-1 bg-blue-400 rounded"></div> Rute
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const schoolLat = {{ $delivery->school->latitude ?? 0 }};
const schoolLng = {{ $delivery->school->longitude ?? 0 }};
const deliveryId = {{ $delivery->id }};

// Init Map
const map = L.map('map').setView([schoolLat, schoolLng], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '© OpenStreetMap'}).addTo(map);

// School Marker
L.marker([schoolLat, schoolLng], {
    icon: L.divIcon({className:'', html:'<div style="background:#9333ea;color:white;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 3px 10px rgba(0,0,0,0.3)">🏫</div>', iconSize:[40,40], iconAnchor:[20,20]})
}).addTo(map).bindPopup('<strong>{{ $delivery->school->name }}</strong><br>Tujuan Pengiriman');

// Courier Marker
let courierMarker = null;
let pathPoints = [];
let pathLine = null;

const courierIcon = L.divIcon({className:'', html:'<div style="background:#2563eb;color:white;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 3px 10px rgba(0,0,0,0.3)">🚚</div>', iconSize:[40,40], iconAnchor:[20,20]});

// Load existing tracking points
@foreach($delivery->trackings as $t)
pathPoints.push([{{ $t->latitude }}, {{ $t->longitude }}]);
@endforeach

@if($delivery->latestTracking)
courierMarker = L.marker([{{ $delivery->latestTracking->latitude }}, {{ $delivery->latestTracking->longitude }}], {icon: courierIcon})
    .addTo(map).bindPopup('Kurir: {{ $delivery->courier->name }}');
@endif

if (pathPoints.length > 1) {
    pathLine = L.polyline(pathPoints, {color:'#3b82f6', weight:4, opacity:0.7, dashArray:'8,4'}).addTo(map);
}

// Realtime Subscribe
const channel = pusher.subscribe('delivery.' + deliveryId);
channel.bind('location.updated', function(data) {
    const lat = data.latitude;
    const lng = data.longitude;

    if (!courierMarker) {
        courierMarker = L.marker([lat, lng], {icon: courierIcon}).addTo(map);
    } else {
        courierMarker.setLatLng([lat, lng]);
    }
    courierMarker.bindPopup('Kurir: {{ $delivery->courier->name }}<br>' + new Date(data.recorded_at).toLocaleTimeString('id-ID'));

    pathPoints.push([lat, lng]);
    if (pathLine) {
        pathLine.setLatLngs(pathPoints);
    } else {
        pathLine = L.polyline(pathPoints, {color:'#3b82f6', weight:4, opacity:0.7}).addTo(map);
    }

    // Update info card
    document.getElementById('live-coords').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
    document.getElementById('live-time').textContent = 'Update: ' + new Date(data.recorded_at).toLocaleTimeString('id-ID');

    map.panTo([lat, lng]);
});

channel.bind('status.updated', function(data) {
    if (data.status === 'sudah_sampai') {
        setTimeout(() => location.reload(), 1500);
    }
});
</script>
@endpush
