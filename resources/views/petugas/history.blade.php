@extends('layouts.app')

@section('title', 'Riwayat Pengiriman')
@section('page-title', 'Riwayat Pengiriman')

@section('sidebar-nav')
<a href="{{ route('petugas.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('petugas.delivery.create') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Buat Pengiriman
</a>
<a href="{{ route('petugas.history') }}" class="sidebar-link active">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Riwayat Pengiriman
</a>
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-bold text-gray-800 text-lg">Semua Riwayat Pengiriman</h3>
        <span class="text-sm text-gray-500">Total: {{ $deliveries->total() }} pengiriman</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sekolah</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Porsi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Durasi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($deliveries as $delivery)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-mono text-sm font-bold text-gray-800">{{ $delivery->kode_pengiriman }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800 text-sm">{{ $delivery->school->name }}</p>
                        <p class="text-xs text-gray-400">{{ $delivery->school->kecamatan }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $delivery->delivery_date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                        {{ $delivery->total_portions }} porsi
                    </td>
                    <td class="px-6 py-4">
                        <span class="status-badge
                            @if($delivery->status === 'selesai') bg-green-100 text-green-700
                            @elseif($delivery->status === 'dalam_perjalanan') bg-blue-100 text-blue-700
                            @elseif(in_array($delivery->status, ['diterima_guru', 'sudah_sampai'])) bg-teal-100 text-teal-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ $delivery->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        @if($delivery->started_at && $delivery->completed_at)
                            {{ $delivery->started_at->diffForHumans($delivery->completed_at, true) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('petugas.delivery.show', $delivery) }}"
                            class="text-green-600 hover:text-green-700 text-sm font-medium hover:underline flex items-center gap-1">
                            Detail
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="text-5xl mb-3">📭</div>
                        <p class="text-gray-500 font-medium">Belum ada riwayat pengiriman</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($deliveries->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $deliveries->links() }}
    </div>
    @endif
</div>
@endsection
