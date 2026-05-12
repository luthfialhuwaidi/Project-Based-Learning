@props(['label', 'value', 'icon' => '📊', 'color' => 'green', 'sub' => null])

<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between mb-4">
        <div class="text-3xl">{{ $icon }}</div>
        <div class="w-3 h-3 rounded-full bg-{{ $color }}-400 mt-1"></div>
    </div>
    <p class="text-3xl font-extrabold text-gray-800">{{ $value }}</p>
    <p class="text-sm font-medium text-gray-500 mt-1">{{ $label }}</p>
    @if($sub)
    <p class="text-xs text-gray-400 mt-0.5">{{ $sub }}</p>
    @endif
</div>
