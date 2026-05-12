@props(['status', 'label' => null])

@php
$colors = [
    'dimasak'          => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'dikemas'          => 'bg-orange-100 text-orange-800 border-orange-200',
    'dalam_perjalanan' => 'bg-blue-100 text-blue-800 border-blue-200',
    'sudah_sampai'     => 'bg-teal-100 text-teal-800 border-teal-200',
    'diterima_guru'    => 'bg-green-100 text-green-800 border-green-200',
    'diterima_murid'   => 'bg-purple-100 text-purple-800 border-purple-200',
    'selesai'          => 'bg-gray-100 text-gray-700 border-gray-200',
];

$dots = [
    'dimasak'          => 'bg-yellow-500',
    'dikemas'          => 'bg-orange-500',
    'dalam_perjalanan' => 'bg-blue-500 pulse-dot',
    'sudah_sampai'     => 'bg-teal-500',
    'diterima_guru'    => 'bg-green-500',
    'diterima_murid'   => 'bg-purple-500',
    'selesai'          => 'bg-gray-400',
];

$labels = [
    'dimasak'          => 'Sedang Dimasak',
    'dikemas'          => 'Sedang Dikemas',
    'dalam_perjalanan' => 'Dalam Perjalanan',
    'sudah_sampai'     => 'Sudah Sampai',
    'diterima_guru'    => 'Diterima Guru',
    'diterima_murid'   => 'Diterima Murid',
    'selesai'          => 'Selesai',
];

$colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
$dotClass = $dots[$status] ?? 'bg-gray-400';
$displayLabel = $label ?? ($labels[$status] ?? $status);
@endphp

<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $colorClass }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
    {{ $displayLabel }}
</span>
