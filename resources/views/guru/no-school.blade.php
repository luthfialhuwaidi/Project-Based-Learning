@extends('layouts.app')
@section('title', 'Sekolah Belum Terdaftar')
@section('page-title', 'Dashboard Guru')
@section('sidebar-nav')
<a href="{{ route('guru.dashboard') }}" class="sidebar-link active">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
@endsection
@section('content')
<div class="flex items-center justify-center min-h-96">
    <div class="text-center max-w-md">
        <div class="text-7xl mb-6">🏫</div>
        <h2 class="text-2xl font-bold text-gray-700 mb-3">Belum Terdaftar di Sekolah</h2>
        <p class="text-gray-500 mb-6">Akun guru Anda belum dikaitkan dengan sekolah manapun. Hubungi administrator sistem untuk menautkan akun Anda ke sekolah yang sesuai.</p>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-left">
            <p class="text-blue-800 font-semibold text-sm mb-1">Apa yang harus dilakukan?</p>
            <ul class="text-blue-600 text-sm space-y-1">
                <li>• Hubungi administrator sistem MBG</li>
                <li>• Minta admin untuk menautkan akun Anda ke sekolah</li>
                <li>• Atau hubungi Dinas Pendidikan setempat</li>
            </ul>
        </div>
    </div>
</div>
@endsection
