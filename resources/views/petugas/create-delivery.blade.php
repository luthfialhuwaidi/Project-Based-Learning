@extends('layouts.app')

@section('title', 'Buat Pengiriman Baru')
@section('page-title', 'Buat Pengiriman MBG')

@section('sidebar-nav')
<a href="{{ route('petugas.dashboard') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('petugas.delivery.create') }}" class="sidebar-link active">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Buat Pengiriman
</a>
<a href="{{ route('petugas.history') }}" class="sidebar-link">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Riwayat Pengiriman
</a>
@endsection

@push('styles')
<style>#school-map { height: 300px; border-radius: 12px; }</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6 text-white">
            <h2 class="text-xl font-bold flex items-center gap-3">
                <span class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">🚚</span>
                Form Pengiriman MBG Baru
            </h2>
            <p class="text-green-200 mt-1 text-sm">Isi data pengiriman untuk hari ini: {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>

        <form action="{{ route('petugas.delivery.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            {{-- Pilih Sekolah --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    🏫 Pilih Sekolah Tujuan <span class="text-red-500">*</span>
                </label>
                <select name="school_id" id="school-select" required
                    class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-gray-800 bg-gray-50 focus:bg-white text-sm font-medium">
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach($schools as $school)
                    <option value="{{ $school->id }}"
                        data-lat="{{ $school->latitude }}"
                        data-lng="{{ $school->longitude }}"
                        data-address="{{ $school->address }}"
                        data-teacher="{{ $school->teacher->name ?? 'Belum ada guru' }}"
                        {{ old('school_id') == $school->id ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                    @endforeach
                </select>
                @error('school_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- School Info Card (dinamis) --}}
            <div id="school-info" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-blue-500 font-semibold uppercase">Alamat</p>
                        <p class="text-blue-800 font-medium text-sm" id="info-address">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-500 font-semibold uppercase">Guru PIC</p>
                        <p class="text-blue-800 font-medium text-sm" id="info-teacher">-</p>
                    </div>
                </div>
                <div id="school-map"></div>
            </div>

            {{-- Jumlah Porsi --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    🍱 Jumlah Porsi <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="changePortions(-10)"
                        class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-xl font-bold text-gray-700 text-xl transition-colors">-</button>
                    <input type="number" name="total_portions" id="portions-input"
                        value="{{ old('total_portions', 30) }}"
                        min="1" max="500" required
                        class="flex-1 text-center px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-2xl font-bold text-gray-800">
                    <button type="button" onclick="changePortions(10)"
                        class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-xl font-bold text-gray-700 text-xl transition-colors">+</button>
                </div>
                <p class="text-xs text-gray-400 mt-2">Masukkan total porsi makanan yang akan dikirim</p>
                @error('total_portions')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    📝 Catatan (Opsional)
                </label>
                <textarea name="notes" rows="3" placeholder="Contoh: Menu hari ini nasi + ayam goreng + sayur bayam..."
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-gray-800 text-sm resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- Info Status Awal --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3">
                <span class="text-2xl">ℹ️</span>
                <div>
                    <p class="text-yellow-800 font-semibold text-sm">Status akan dimulai dari "Sedang Dimasak"</p>
                    <p class="text-yellow-600 text-xs mt-1">Anda dapat mengubah status pengiriman secara bertahap setelah pengiriman dibuat.</p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex gap-4 pt-2">
                <a href="{{ route('petugas.dashboard') }}"
                    class="flex-1 text-center py-4 border-2 border-gray-200 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                    ← Kembali
                </a>
                <button type="submit"
                    class="flex-1 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-green-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Buat Pengiriman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let schoolMap = null;
let schoolMarker = null;

document.getElementById('school-select').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const lat = parseFloat(option.dataset.lat);
    const lng = parseFloat(option.dataset.lng);
    const address = option.dataset.address;
    const teacher = option.dataset.teacher;

    if (this.value && lat && lng) {
        document.getElementById('school-info').classList.remove('hidden');
        document.getElementById('info-address').textContent = address || '-';
        document.getElementById('info-teacher').textContent = teacher || '-';

        if (!schoolMap) {
            schoolMap = L.map('school-map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '© OSM'}).addTo(schoolMap);
        }

        if (schoolMarker) schoolMarker.remove();
        schoolMarker = L.marker([lat, lng], {
            icon: L.divIcon({
                className: '',
                html: '<div style="background:#16a34a;color:white;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 2px 8px rgba(0,0,0,0.3)">🏫</div>',
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            })
        }).addTo(schoolMap).bindPopup(option.text).openPopup();
        schoolMap.setView([lat, lng], 15);
    } else {
        document.getElementById('school-info').classList.add('hidden');
    }
});

function changePortions(delta) {
    const input = document.getElementById('portions-input');
    const current = parseInt(input.value) || 0;
    const newVal = Math.max(1, Math.min(500, current + delta));
    input.value = newVal;
}

// Auto-trigger jika ada nilai lama
if (document.getElementById('school-select').value) {
    document.getElementById('school-select').dispatchEvent(new Event('change'));
}
</script>
@endpush
