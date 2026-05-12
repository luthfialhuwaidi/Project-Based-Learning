@extends('layouts.app')

@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard Monitoring MBG')

@section('sidebar-nav')
<a href="{{ route('guru.dashboard') }}" class="sidebar-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('guru.history') }}" class="sidebar-link {{ request()->routeIs('guru.history') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Riwayat MBG
</a>
@endsection

@push('styles')
<style>#minimap { height: 280px; border-radius: 12px; }</style>
@endpush

@section('content')
{{-- School Info --}}
<div class="bg-gradient-to-br from-purple-600 to-purple-700 text-white rounded-2xl p-6 mb-6 shadow-lg">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-purple-200 text-sm font-medium mb-1">🏫 Sekolah Anda</p>
            <h2 class="text-2xl font-bold">{{ $school->name }}</h2>
            <p class="text-purple-200 mt-1">{{ $school->address }}</p>
        </div>
        <div class="text-right">
            <p class="text-4xl font-bold">{{ $totalStudents }}</p>
            <p class="text-purple-200 text-sm">Total Siswa Aktif</p>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-5 mb-8">
    @foreach([
        ['label' => 'Pengiriman Hari Ini', 'value' => $todayDeliveries->count(), 'icon' => '📦', 'color' => 'blue'],
        ['label' => 'Sudah Dikonfirmasi', 'value' => $confirmedToday, 'icon' => '✅', 'color' => 'green'],
        ['label' => 'Pengiriman Aktif', 'value' => $activeDeliveries->count(), 'icon' => '🚚', 'color' => 'orange'],
    ] as $stat)
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <span class="text-3xl">{{ $stat['icon'] }}</span>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stat['value'] }}</p>
        <p class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Active Deliveries --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <span class="w-2 h-2 bg-green-500 rounded-full pulse-dot"></span>
                Pengiriman Aktif Sekarang
            </h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($activeDeliveries as $delivery)
            <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="font-bold text-gray-800">{{ $delivery->kode_pengiriman }}</p>
                        <p class="text-sm text-gray-500 flex items-center gap-1 mt-0.5">
                            🚚 {{ $delivery->courier->name }}
                        </p>
                    </div>
                    <span class="status-badge
                        @if($delivery->status === 'dalam_perjalanan') bg-blue-100 text-blue-700
                        @elseif($delivery->status === 'sudah_sampai') bg-green-100 text-green-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ $delivery->status_label }}
                    </span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('guru.track', $delivery) }}"
                        class="flex-1 text-center py-2 border-2 border-purple-200 text-purple-700 rounded-xl text-sm font-semibold hover:bg-purple-50 transition-colors">
                        🗺️ Live Tracking
                    </a>
                    @if($delivery->status === 'sudah_sampai')
                    <button onclick="showConfirmModal({{ $delivery->id }})"
                        class="flex-1 py-2 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 transition-colors">
                        ✅ Konfirmasi Terima
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center">
                <div class="text-4xl mb-3">⏳</div>
                <p class="text-gray-500">Belum ada pengiriman aktif</p>
                <p class="text-gray-400 text-sm mt-1">Kurir akan segera mengirimkan makanan</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Mini Map --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span>📍</span> Peta Realtime
        </h3>
        <div id="minimap"></div>
        @if($activeDeliveries->count() === 0)
        <p class="text-center text-gray-400 text-sm mt-3">Tidak ada kurir aktif saat ini</p>
        @endif
    </div>
</div>

{{-- All Today Deliveries --}}
<div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">📋 Semua Pengiriman Hari Ini</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kurir</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tiba</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Konfirmasi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($todayDeliveries as $delivery)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-mono text-sm font-semibold text-gray-800">{{ $delivery->kode_pengiriman }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $delivery->courier->name }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge
                            @if(in_array($delivery->status, ['selesai', 'diterima_guru', 'diterima_murid'])) bg-green-100 text-green-700
                            @elseif($delivery->status === 'dalam_perjalanan') bg-blue-100 text-blue-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ $delivery->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $delivery->arrived_at ? $delivery->arrived_at->format('H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($delivery->confirmations->where('teacher_confirmed', true)->count() > 0)
                        <span class="text-green-600 font-semibold text-sm">✅ Sudah</span>
                        @else
                        <span class="text-gray-400 text-sm">Belum</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('guru.track', $delivery) }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium hover:underline">
                            Tracking →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">Belum ada pengiriman hari ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Confirm Modal --}}
<div id="confirm-modal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
        <h3 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Penerimaan Makanan</h3>
        <p class="text-gray-500 mb-6">Pastikan makanan MBG sudah diterima dalam kondisi baik. Notifikasi akan dikirim ke semua orang tua.</p>
        <form id="confirm-form" method="POST">
            @csrf
            <textarea name="notes" placeholder="Catatan (opsional)" rows="3"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none mb-4"></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="closeConfirmModal()" class="flex-1 py-3 border-2 border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">✅ Konfirmasi Terima</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Mini Map
const map = L.map('minimap').setView([{{ $school->latitude ?? 0.5 }}, {{ $school->longitude ?? 101.4 }}], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '© OSM'}).addTo(map);

// Marker sekolah
L.marker([{{ $school->latitude ?? 0.5 }}, {{ $school->longitude ?? 101.4 }}], {
    icon: L.divIcon({className:'',html:'<div style="background:#9333ea;color:white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:16px">🏫</div>',iconSize:[32,32],iconAnchor:[16,16]})
}).addTo(map).bindPopup('{{ $school->name }}');

let courierMarkers = {};

// Load active deliveries
@foreach($activeDeliveries as $delivery)
@if($delivery->latestTracking)
const marker{{ $delivery->id }} = L.marker(
    [{{ $delivery->latestTracking->latitude }}, {{ $delivery->latestTracking->longitude }}],
    { icon: L.divIcon({className:'',html:'<div style="background:#2563eb;color:white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:16px">🚚</div>',iconSize:[32,32],iconAnchor:[16,16]}) }
).addTo(map).bindPopup('Kurir: {{ $delivery->courier->name }}');
courierMarkers[{{ $delivery->id }}] = marker{{ $delivery->id }};
@endif
@endforeach

// Realtime tracking
const channel = pusher.subscribe('school.{{ $school->id }}');
channel.bind('location.updated', function(data) {
    if (courierMarkers[data.delivery_id]) {
        courierMarkers[data.delivery_id].setLatLng([data.latitude, data.longitude]);
    } else {
        courierMarkers[data.delivery_id] = L.marker([data.latitude, data.longitude], {
            icon: L.divIcon({className:'',html:'<div style="background:#2563eb;color:white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:16px">🚚</div>',iconSize:[32,32],iconAnchor:[16,16]})
        }).addTo(map).bindPopup('Kurir: ' + data.courier_name);
    }
});

channel.bind('status.updated', function(data) {
    // Update status badge tanpa reload
    console.log('Status updated:', data.status);
    // Reload setelah delay singkat untuk refresh data
    setTimeout(() => location.reload(), 2000);
});

// Confirm Modal
function showConfirmModal(deliveryId) {
    document.getElementById('confirm-form').action = '/guru/konfirmasi/' + deliveryId;
    document.getElementById('confirm-modal').classList.remove('hidden');
}
function closeConfirmModal() {
    document.getElementById('confirm-modal').classList.add('hidden');
}
</script>
@endpush
