@props(['level'])

@php
$colors = [
    'I' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
    'E' => 'bg-yellow-50 text-yellow-700 ring-1 ring-yellow-200',
    'D' => 'bg-green-50 text-green-700 ring-1 ring-green-200',
];

$classes = $colors[$level] ?? 'bg-gray-50 text-gray-500 ring-1 ring-gray-200';
@endphp

<span class="inline-flex items-center justify-center px-2.5 py-0.5 text-[0.65rem] font-semibold rounded-full tracking-wide {{ $classes }}">
    {{ $level ?? '-' }}
</span>
