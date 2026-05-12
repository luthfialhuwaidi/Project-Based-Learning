@extends('layouts.app')

@section('title', 'Riwayat MBG')
@section('page-title', 'Riwayat MBG Anak')

@section('sidebar-nav')
<a href="{{ route('orangtua.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('orangtua.history') }}" class="sidebar-link active">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Riwayat MBG
</a>
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">Riwayat MBG Anak Saya</h3>
        <p class="text-sm text-gray-400 mt-1">
            @foreach($students as $s)
            <span class="inline-flex items-center gap-1 bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full text-xs font-medium mr-2">
                {{ $s->gender === 'L' ? '👦' : '👧' }} {{ $s->name }}
            </span>
            @endforeach
        </p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sekolah</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Diterima Guru</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sudah Makan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($deliveries as $delivery)
                @php
                $myConfirmations = $delivery->confirmations;
                $allEaten = $myConfirmations->every(fn($c) => $c->eaten_status);
                $anyConfirmed = $myConfirmations->contains(fn($c) => $c->teacher_confirmed);
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $delivery->delivery_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-600">{{ $delivery->kode_pengiriman }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $delivery->school->name }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge {{ $delivery->status === 'selesai' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $delivery->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $anyConfirmed ? '✅ Ya' : '⏳ Belum' }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $allEaten && $myConfirmations->count() > 0 ? '🍽️ Ya' : '⏳ Belum' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">Belum ada riwayat MBG</td>
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
