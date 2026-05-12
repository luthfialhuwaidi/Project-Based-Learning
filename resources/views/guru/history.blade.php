@extends('layouts.app')

@section('title', 'Riwayat MBG')
@section('page-title', 'Riwayat Penerimaan MBG')

@section('sidebar-nav')
<a href="{{ route('guru.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('guru.history') }}" class="sidebar-link active">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Riwayat MBG
</a>
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">Riwayat Pengiriman MBG ke Sekolah</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kurir</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Porsi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Konfirmasi Guru</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($deliveries as $delivery)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-sm font-bold text-gray-800">{{ $delivery->kode_pengiriman }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $delivery->courier->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $delivery->delivery_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm font-semibold">{{ $delivery->total_portions }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge {{ in_array($delivery->status, ['selesai','diterima_guru']) ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $delivery->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($delivery->confirmations->where('teacher_confirmed', true)->count() > 0)
                            <span class="text-green-600 font-semibold text-sm">✅ Dikonfirmasi</span>
                        @else
                            <span class="text-gray-400 text-sm">Belum</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">Belum ada riwayat</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deliveries->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $deliveries->links() }}</div>
    @endif
</div>
@endsection
