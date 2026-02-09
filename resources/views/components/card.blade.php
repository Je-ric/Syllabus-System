@props([
    'title' => null,
    'icon' => null,
    'variant' => 'default',
    'headerColor' => null,
    'bodyColor' => null,
    'borderColor' => null,
    'headerClass' => '',
    'bodyClass' => '',
    'id' => null,
])

@php
    $variants = [
        'default' => [
            'container' => 'bg-white border border-gray-200',
            'shadow' => 'shadow-lg',
            'header' => 'bg-gradient-to-r from-[#1a2235] to-[#2a3441] text-white',
            'body' => 'bg-white',
            'iconBg' => 'bg-white/20 backdrop-blur-sm',
            'iconColor' => 'text-white',
        ],
        'midnight-header' => [
            'container' => 'bg-white border border-gray-200',
            'shadow' => 'shadow-lg',
            'header' => 'bg-gradient-to-r from-gray-900 via-slate-800 to-indigo-900 text-white',
            'body' => 'bg-white',
            'iconBg' => 'bg-white/20 backdrop-blur-sm',
            'iconColor' => 'text-[#ffb51b]',
        ],
    ];

    $config = $variants[$variant] ?? $variants['default'];

    $containerClass = $config['container'];
    $shadowClass = $config['shadow'];
    $headerClass = $headerColor ?? $config['header'];
    $bodyClass = $bodyColor ?? $config['body'];
@endphp

<div class="w-full {{ $containerClass }} {{ $shadowClass }} rounded-xl overflow-hidden transition-all duration-300 group">
    @if($title || $icon)
        <div class="flex items-center gap-3 px-6 py-4 {{ $headerClass }} {{ $headerClass }} relative">

            @if($icon)
                <span class="relative inline-flex items-center justify-center w-8 h-8 {{ $config['iconBg'] }} rounded-xl transition-transform duration-300 group-hover:scale-110">
                    <i class="bx {{ $icon }} text-lg {{ $config['iconColor'] }}"></i>
                </span>
            @endif

            @if($title)
                <h3 @if($id) id="{{ $id }}" @endif
                    class="relative text-lg font-semibold tracking-tight flex-1">
                    {{ $title }}
                </h3>
            @endif

        </div>
    @endif

    <div class="p-6 {{ $bodyClass }} {{ $bodyClass }} relative">
        {{ $slot }}
    </div>
</div>

{{--
Usage Examples:

<!-- Default Professional Card -->
<x-overview.card title="Basic Information" icon="bx-info-circle">
    <p>Your content here...</p>
</x-overview.card>

<!-- Gradient Accent Card -->
<x-overview.card title="Schedule" icon="bx-calendar" variant="gradient">
    <p>Your content here...</p>
</x-overview.card>

<!-- Minimal Clean Card -->
<x-overview.card title="Statistics" icon="bx-bar-chart" variant="minimal">
    <p>Your content here...</p>
</x-overview.card>

<!-- Elevated Premium Card -->
<x-overview.card title="Important Notice" icon="bx-bell" variant="elevated">
    <p>Your content here...</p>
</x-overview.card>

<!-- Bordered Formal Card -->
<x-overview.card title="Documents" icon="bx-file" variant="bordered">
    <p>Your content here...</p>
</x-overview.card>

<!-- Custom Colors -->
<x-overview.card
    title="Custom Card"
    icon="bx-star"
    variant="default"
    headerColor="bg-gradient-to-r from-emerald-600 to-teal-600 text-white"
    bodyColor="bg-emerald-50"
>
    <p>Your content here...</p>
</x-overview.card>
--}}
