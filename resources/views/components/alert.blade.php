@props(['type' => 'success', 'message'])

@php
$styles = [
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'error'   => 'bg-red-50 border-red-200 text-red-800',
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    'info'    => 'bg-blue-50 border-blue-200 text-blue-800',
];
$icons = [
    'success' => '✅',
    'error'   => '❌',
    'warning' => '⚠️',
    'info'    => 'ℹ️',
];
@endphp

<div class="border rounded-xl px-4 py-3 flex items-center gap-3 {{ $styles[$type] ?? $styles['info'] }}" data-auto-dismiss>
    <span>{{ $icons[$type] ?? $icons['info'] }}</span>
    <span class="text-sm font-medium">{{ $message }}</span>
</div>
