@props([
    'status'  => null,
    'variant' => null,
    'label'   => null,
    'dot'     => false,
    'icon'    => null,
    'size'    => 'md',
])

@php

$statusMap = [
    'success'  => ['variant' => 'brand',   'icon' => 'bx bx-check-circle'],
    'active'   => ['variant' => 'brand',   'icon' => 'bx bx-check-shield'],
    'neutral'  => ['variant' => 'slate',   'icon' => 'bx bx-minus-circle'],
    'disabled' => ['variant' => 'slate',   'icon' => 'bx bx-pause-circle'],
    'info'     => ['variant' => 'blue',    'icon' => 'bx bx-info-circle'],
    'warning'  => ['variant' => 'amber',   'icon' => 'bx bx-error-circle'],
    'pending'  => ['variant' => 'amber',   'icon' => 'bx bx-time-five'],
    'danger'   => ['variant' => 'rose',    'icon' => 'bx bx-x-circle'],
    'rejected' => ['variant' => 'rose',    'icon' => 'bx bx-block'],
    'admin'    => ['variant' => 'violet',  'icon' => 'bx bx-crown'],
    'dean'     => ['variant' => 'indigo',  'icon' => 'bx bx-medal'],
    'chair'    => ['variant' => 'blue',    'icon' => 'bx bx-user-pin'],
    'faculty'  => ['variant' => 'brand',   'icon' => 'bx bx-user'],
    'lec'      => ['variant' => 'brand',   'icon' => 'bx bx-book'],
    'lec_lab'  => ['variant' => 'lab',     'icon' => 'bx bx-flask'],
];

// Pill = tinted bg + saturated text, ring one step lighter than text (not equal to it).
// This keeps the outline present but quiet, so text stays the loudest element in the pill.
$variantTokens = [
    'brand' => [
        'pill' => 'bg-[#D5FFF0] text-[#06754E] ring-1 ring-inset ring-[#AEFFE2]',
        'dot'  => 'bg-[#00965F]',
    ],
    'emerald' => [
        'pill' => 'bg-[#D5FFF0] text-[#076042] ring-1 ring-inset ring-[#AEFFE2]',
        'dot'  => 'bg-[#00C075]',
    ],
    'blue' => [
        'pill' => 'bg-[#DAF1FF] text-[#143D57] ring-1 ring-inset ring-[#AEDFFF]',
        'dot'  => 'bg-[#3197D6]',
    ],
    'lab' => [
        'pill' => 'bg-[#DAF1FF] text-[#143D57] ring-1 ring-inset ring-[#AEDFFF]',
        'dot'  => 'bg-[#3197D6]',
    ],
    'sky' => [
        'pill' => 'bg-[#EBF8FF] text-[#194C6E] ring-1 ring-inset ring-[#CDEBFF]',
        'dot'  => 'bg-[#3197D6]',
    ],
    'amber' => [
        'pill' => 'bg-[#FFF6E2] text-[#875200] ring-1 ring-inset ring-[#FFE9B5]',
        'dot'  => 'bg-[#F5B126]',
    ],
    'rose' => [
        'pill' => 'bg-[#FFE3E2] text-[#9F1239] ring-1 ring-inset ring-[#FFA2A2]',
        'dot'  => 'bg-[#E52F28]',
    ],
    'violet' => [
        'pill' => 'bg-[#F3EEFF] text-[#5B21B6] ring-1 ring-inset ring-[#DDD1FA]',
        'dot'  => 'bg-[#7C3AED]',
    ],
    'indigo' => [
        'pill' => 'bg-[#EEF2FF] text-[#3730A3] ring-1 ring-inset ring-[#D3DAFE]',
        'dot'  => 'bg-[#4338CA]',
    ],
    'slate' => [
        'pill' => 'bg-[#F1F3F5] text-[#394056] ring-1 ring-inset ring-[#E3E8EB]',
        'dot'  => 'bg-[#72809E]',
    ],
];

$fallbackTokens = $variantTokens['slate'];

$resolvedVariant  = $variant;
$resolvedAutoIcon = null;

if (filled($status) && isset($statusMap[$status])) {
    $resolvedVariant  = $resolvedVariant ?? $statusMap[$status]['variant'];
    $resolvedAutoIcon = $statusMap[$status]['icon'] ?? null;
}

$tokens    = $variantTokens[$resolvedVariant] ?? $fallbackTokens;
$pillStyle = $tokens['pill'];
$dotStyle  = $tokens['dot'];

$iconClass = $icon ?? $resolvedAutoIcon;

$sizeClasses = match ($size) {
    'sm'    => 'text-[10px] px-2 py-[3px] gap-1 rounded-[6px]',
    'lg'    => 'text-[13px] px-3 py-[5px] gap-1.5 rounded-[8px]',
    default => 'text-[11px] px-2.5 py-[4px] gap-1 rounded-[7px]',
};

$dotSizeClass = match ($size) {
    'sm'    => 'w-1 h-1',
    'lg'    => 'w-2 h-2',
    default => 'w-1.5 h-1.5',
};

$iconSizeClass = match ($size) {
    'sm'    => 'text-[10px]',
    'lg'    => 'text-[13px]',
    default => 'text-[11px]',
};

$hasSlot       = $slot->isNotEmpty();
$autoLabel     = filled($status) ? ucfirst(str_replace('_', ' ', (string) $status)) : null;
$showExplicit  = filled($label);
$showSlot      = !$showExplicit && $hasSlot;
$showAutoLabel = !$showExplicit && !$hasSlot && filled($autoLabel);

@endphp

<span {{ $attributes->class([
    'inline-flex items-center font-semibold whitespace-nowrap',
    $sizeClasses,
    $pillStyle,
]) }}>

    @if ($dot)
        <span class="rounded-full shrink-0 {{ $dotSizeClass }} {{ $dotStyle }}" aria-hidden="true"></span>
    @elseif ($iconClass)
        <i class="{{ $iconClass }} {{ $iconSizeClass }} shrink-0 leading-none" aria-hidden="true"></i>
    @endif

    @if ($showExplicit)
        {{ $label }}
    @elseif ($showSlot)
        {{ $slot }}
    @elseif ($showAutoLabel)
        {{ $autoLabel }}
    @endif

</span>
