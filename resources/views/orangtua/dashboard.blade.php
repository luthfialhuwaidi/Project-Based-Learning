@extends('layouts.app')

@section('title', 'Dashboard Orang Tua')
@section('page-title', 'Status MBG Anak Saya')

@section('sidebar-nav')
<a href="{{ route('orangtua.dashboard') }}" class="sidebar-link {{ request()->routeIs('orangtua.dashboard') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('orangtua.history') }}" class="sidebar-link {{ request()->routeIs('orangtua.history') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Riwayat MBG
</a>
@endsection

@section('content')
{{-- Welcome Banner --}}
<div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 mb-6 text-white shadow-lg">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-orange-100 text-sm">Halo,</p>
            <h2 class="text-2xl font-bold">{{ auth()->user()->name }} 👋</h2>
            <p class="text-orange-200 mt-1">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <div class="text-6xl opacity-80">👨‍👩‍👧‍👦</div>
    </div>
</div>

{{-- Notifications --}}
@if($unreadNotifications->count() > 0)
<div class="mb-6 space-y-3">
    @foreach($unreadNotifications as $notif)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-4" id="notif-{{ $notif->id }}">
        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-xl flex-shrink-0">🔔</div>
        <div class="flex-1">
            <p class="font-semibold text-blue-800">{{ $notif->data['message'] ?? 'Notifikasi baru' }}</p>
            <p class="text-xs text-blue-500 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
        </div>
        <button onclick="markRead('{{ $notif->id }}')" class="text-blue-400 hover:text-blue-600 transition-colors flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endforeach
</div>
@endif

{{-- Anak-anak --}}
@forelse($students as $student)
@php
$todayDeliveries = $student->school->deliveries ?? collect();
$latestDelivery = $todayDeliveries->first();
$confirmation = $latestDelivery ? $latestDelivery->confirmations->where('student_id', $student->id)->first() : null;
@endphp
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
    {{-- Student Header --}}
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-500 flex items-center justify-center text-2xl text-white font-bold shadow-md">
                {{ $student->gender === 'L' ? '👦' : '👧' }}
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-800">{{ $student->name }}</h3>
                <p class="text-sm text-gray-500">{{ $student->school->name }} • Kelas {{ $student->class ?? '-' }}</p>
            </div>
            @if($latestDelivery)
            <span class="status-badge
                @if(in_array($latestDelivery->status, ['selesai', 'diterima_guru', 'diterima_murid'])) bg-green-100 text-green-700
                @elseif($latestDelivery->status === 'dalam_perjalanan') bg-blue-100 text-blue-700
                @elseif($latestDelivery->status === 'sudah_sampai') bg-teal-100 text-teal-700
                @else bg-yellow-100 text-yellow-700 @endif text-sm">
                {{ $latestDelivery->status_label }}
            </span>
            @endif
        </div>
    </div>

    {{-- Status MBG --}}
    @if($latestDelivery)
    <div class="p-6">
        {{-- Status Steps --}}
        <div class="mb-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Status MBG Hari Ini</p>
            @php
            $steps = [
                ['key' => 'dimasak', 'label' => 'Dimasak', 'icon' => '👨‍🍳'],
                ['key' => 'dikemas', 'label' => 'Dikemas', 'icon' => '📦'],
                ['key' => 'dalam_perjalanan', 'label' => 'Di Jalan', 'icon' => '🚚'],
                ['key' => 'sudah_sampai', 'label' => 'Sampai', 'icon' => '📍'],
                ['key' => 'diterima_guru', 'label' => 'Diterima Guru', 'icon' => '✅'],
            ];
            $statusOrder = ['dimasak', 'dikemas', 'dalam_perjalanan', 'sudah_sampai', 'diterima_guru', 'diterima_murid', 'selesai'];
            $currentIdx = array_search($latestDelivery->status, $statusOrder);
            @endphp
            <div class="flex items-center">
                @foreach($steps as $i => $step)
                @php $stepIdx = array_search($step['key'], $statusOrder); @endphp
                <div class="flex flex-col items-center {{ $i < count($steps) - 1 ? 'flex-1' : '' }}">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl mb-2
                        {{ $stepIdx <= $currentIdx ? 'bg-green-100 shadow-sm' : 'bg-gray-100' }} transition-all">
                        {{ $step['icon'] }}
                    </div>
                    <span class="text-xs font-medium {{ $stepIdx <= $currentIdx ? 'text-green-700' : 'text-gray-400' }} text-center">
                        {{ $step['label'] }}
                    </span>
                </div>
                @if($i < count($steps) - 1)
                <div class="flex-1 h-1 mx-2 rounded-full {{ $stepIdx < $currentIdx ? 'bg-green-400' : 'bg-gray-200' }} -mt-6"></div>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Info Pengiriman --}}
        <div class="grid grid-cols-2 gap-4 mb-5">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 font-medium mb-1">Kurir</p>
                <p class="font-semibold text-gray-800">🚚 {{ $latestDelivery->courier->name }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 font-medium mb-1">Kode Pengiriman</p>
                <p class="font-semibold text-gray-800 font-mono">{{ $latestDelivery->kode_pengiriman }}</p>
            </div>
        </div>

        {{-- Konfirmasi Section --}}
        <div class="space-y-3">
            {{-- Lihat Tracking --}}
            @if(in_array($latestDelivery->status, ['dalam_perjalanan', 'sudah_sampai']))
            <a href="{{ route('orangtua.track', $latestDelivery) }}"
                class="w-full flex items-center justify-center gap-2 py-3 border-2 border-blue-200 text-blue-700 rounded-xl font-semibold hover:bg-blue-50 transition-colors">
                <span>🗺️</span> Lihat Lokasi Kurir
            </a>
            @endif

            {{-- Konfirmasi Sudah Makan --}}
            @if($confirmation && $confirmation->teacher_confirmed && !$confirmation->eaten_status)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-green-800 font-semibold mb-3">✅ Makanan sudah diterima guru! Apakah {{ $student->name }} sudah makan?</p>
                <form action="{{ route('orangtua.confirm-eaten', $latestDelivery) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition-colors">
                        🍽️ Ya, {{ $student->name }} Sudah Makan!
                    </button>
                </form>
            </div>
            @elseif($confirmation && $confirmation->eaten_status)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                <span class="text-3xl">🎉</span>
                <p class="text-green-800 font-bold mt-2">{{ $student->name }} sudah makan!</p>
                <p class="text-green-600 text-sm mt-1">{{ $confirmation->eaten_at?->format('H:i') }}</p>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="p-8 text-center">
        <div class="text-5xl mb-3">⏳</div>
        <h4 class="text-gray-700 font-semibold mb-1">Menunggu Pengiriman</h4>
        <p class="text-gray-400 text-sm">Pengiriman MBG untuk hari ini belum dimulai</p>
    </div>
    @endif
</div>
@empty
<div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
    <div class="text-5xl mb-4">👶</div>
    <h3 class="text-xl font-bold text-gray-700 mb-2">Data Anak Belum Terdaftar</h3>
    <p class="text-gray-500">Hubungi sekolah atau administrator untuk mendaftarkan data anak Anda.</p>
</div>
@endforelse
@endsection

@push('scripts')
<script>
function markRead(notifId) {
    fetch('{{ route('orangtua.notification.read') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify({id: notifId})
    }).then(() => {
        document.getElementById('notif-' + notifId)?.remove();
    });
}

// Realtime notifikasi untuk orang tua
const userId = {{ auth()->id() }};
const notifChannel = pusher.subscribe('App.Models.User.' + userId);
notifChannel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
    // Tampilkan notifikasi realtime
    const notifDiv = document.createElement('div');
    notifDiv.className = 'fixed top-4 right-4 bg-green-600 text-white rounded-2xl p-4 shadow-2xl z-50 max-w-sm animate-bounce';
    notifDiv.innerHTML = `
        <div class="flex items-center gap-3">
            <span class="text-2xl">🔔</span>
            <div>
                <p class="font-bold text-sm">Notifikasi MBG</p>
                <p class="text-green-100 text-xs">${data.message || 'Ada pembaruan MBG'}</p>
            </div>
        </div>
    `;
    document.body.appendChild(notifDiv);
    setTimeout(() => notifDiv.remove(), 5000);

    // Reload dashboard setelah 2 detik
    setTimeout(() => location.reload(), 2000);
});
</script>
@endpush
