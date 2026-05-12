@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('page-title', 'Dashboard Kurir MBG')

@section('sidebar-nav')
<a href="{{ route('petugas.dashboard') }}" class="sidebar-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('petugas.delivery.create') }}" class="sidebar-link {{ request()->routeIs('petugas.delivery.create') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Buat Pengiriman
</a>
<a href="{{ route('petugas.history') }}" class="sidebar-link {{ request()->routeIs('petugas.history') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Riwayat Pengiriman
</a>
@endsection

@section('header-actions')
<a href="{{ route('petugas.delivery.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 transition-colors shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Buat Pengiriman
</a>
@endsection

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
    @foreach([
        ['label' => 'Pengiriman Hari Ini', 'value' => $todayDeliveries->count(), 'icon' => '📦', 'color' => 'blue'],
        ['label' => 'Selesai Hari Ini', 'value' => $completedToday, 'icon' => '✅', 'color' => 'green'],
        ['label' => 'Total Pengiriman', 'value' => $totalDeliveries, 'icon' => '🚚', 'color' => 'purple'],
        ['label' => 'Aktif Sekarang', 'value' => $activeDelivery ? 1 : 0, 'icon' => '📍', 'color' => 'orange'],
    ] as $stat)
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <span class="text-2xl">{{ $stat['icon'] }}</span>
            <div class="w-10 h-10 rounded-xl bg-{{ $stat['color'] }}-50 flex items-center justify-center">
                <div class="w-3 h-3 rounded-full bg-{{ $stat['color'] }}-500"></div>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $stat['value'] }}</p>
        <p class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Active Delivery --}}
@if($activeDelivery)
<div class="bg-gradient-to-br from-green-600 to-green-700 rounded-2xl p-6 mb-8 text-white shadow-lg">
    <div class="flex items-start justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 bg-green-300 rounded-full pulse-dot"></span>
                <span class="text-green-200 text-sm font-medium">Pengiriman Aktif</span>
            </div>
            <h3 class="text-xl font-bold">{{ $activeDelivery->kode_pengiriman }}</h3>
            <p class="text-green-200">{{ $activeDelivery->school->name }}</p>
        </div>
        <span class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl text-sm font-semibold border border-white/30">
            {{ $activeDelivery->status_label }}
        </span>
    </div>

    {{-- Status Progress --}}
    <div class="mb-6">
        <div class="flex items-center gap-0">
            @php
            $steps = ['dimasak', 'dikemas', 'dalam_perjalanan', 'sudah_sampai', 'selesai'];
            $stepLabels = ['Dimasak', 'Dikemas', 'Perjalanan', 'Sampai', 'Selesai'];
            $currentIndex = array_search($activeDelivery->status, $steps);
            @endphp
            @foreach($steps as $i => $step)
            <div class="flex items-center {{ $i < count($steps) - 1 ? 'flex-1' : '' }}">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2
                        {{ $i <= $currentIndex ? 'bg-white text-green-700 border-white' : 'bg-green-500/30 text-green-200 border-green-400/50' }}">
                        {{ $i < $currentIndex ? '✓' : ($i + 1) }}
                    </div>
                    <span class="text-xs text-green-200 mt-1 whitespace-nowrap">{{ $stepLabels[$i] }}</span>
                </div>
                @if($i < count($steps) - 1)
                <div class="flex-1 h-0.5 mx-1 {{ $i < $currentIndex ? 'bg-white' : 'bg-green-500/30' }}"></div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('petugas.delivery.show', $activeDelivery) }}"
            class="flex-1 bg-white text-green-700 font-semibold py-3 rounded-xl text-center hover:bg-green-50 transition-colors">
            Lihat Detail & Tracking
        </a>
        @php
        $nextLabels = ['dimasak' => 'Selesai Dikemas', 'dikemas' => 'Mulai Kirim', 'dalam_perjalanan' => 'Sudah Sampai', 'sudah_sampai' => 'Selesai Antar'];
        $nextLabel = $nextLabels[$activeDelivery->status] ?? null;
        @endphp
        @if($nextLabel)
        <form action="{{ route('petugas.delivery.update-status', $activeDelivery) }}" method="POST">
            @csrf
            <button type="submit" class="bg-white/20 hover:bg-white/30 border border-white/40 text-white font-semibold px-6 py-3 rounded-xl transition-colors">
                {{ $nextLabel }} →
            </button>
        </form>
        @endif
    </div>
</div>
@else
<div class="bg-white rounded-2xl p-8 mb-8 border-2 border-dashed border-gray-200 text-center">
    <div class="text-5xl mb-4">🚚</div>
    <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Pengiriman Aktif</h3>
    <p class="text-gray-500 mb-6">Mulai pengiriman MBG baru untuk hari ini</p>
    <a href="{{ route('petugas.delivery.create') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Pengiriman Baru
    </a>
</div>
@endif

{{-- Today Deliveries List --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-800">Pengiriman Hari Ini</h3>
        <span class="text-sm text-gray-500">{{ $todayDeliveries->count() }} pengiriman</span>
    </div>
    <div class="divide-y divide-gray-50">
        @forelse($todayDeliveries as $delivery)
        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-xl">
                    @if($delivery->status === 'selesai') ✅
                    @elseif($delivery->status === 'dalam_perjalanan') 🚚
                    @elseif($delivery->status === 'sudah_sampai') 📍
                    @else 📦 @endif
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $delivery->kode_pengiriman }}</p>
                    <p class="text-sm text-gray-500">{{ $delivery->school->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="status-badge
                    @if($delivery->status === 'selesai') bg-gray-100 text-gray-700
                    @elseif($delivery->status === 'dalam_perjalanan') bg-blue-100 text-blue-700
                    @elseif($delivery->status === 'sudah_sampai' || $delivery->status === 'diterima_guru') bg-green-100 text-green-700
                    @else bg-yellow-100 text-yellow-700 @endif">
                    {{ $delivery->status_label }}
                </span>
                <a href="{{ route('petugas.delivery.show', $delivery) }}" class="text-green-600 hover:text-green-700 p-2 hover:bg-green-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
        @empty
        <div class="px-6 py-12 text-center">
            <p class="text-gray-400">Belum ada pengiriman hari ini</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
