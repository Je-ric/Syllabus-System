@props(['current', 'total', 'color' => 'bg-[#d97706]'])

@php $pct = $total > 0 ? round(($current / $total) * 100) : 0; @endphp

<div {{ $attributes }}>
    <div class="flex items-center justify-between mb-1">
        <span class="text-[11px] text-[#a1a1aa] font-medium">Step {{ $current }} of {{ $total }}</span>
        <span class="text-[11px] text-[#a1a1aa]">{{ $pct }}%</span>
    </div>
    <div class="h-1 rounded-full bg-[#f4f4f5] overflow-hidden">
        <div class="h-full rounded-full {{ $color }} transition-all" style="width: {{ $pct }}%"></div>
    </div>
</div>
