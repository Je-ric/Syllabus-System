@props([
    'status'  => null,   // semantic mode: 'success' | 'warning' | 'danger' | 'info' | 'neutral' | role keys
    'variant' => null,   // color mode: 'brand' | 'emerald' | 'lab' | 'blue' | 'amber' | 'rose' | 'slate' | 'violet' | 'indigo' | 'sky'
    'label'   => null,   // explicit label (overrides slot and auto-label)
    'dot'     => false,  // show a colored dot instead of an icon
    'icon'    => null,   // explicit icon class (overrides auto-icon)
    'size'    => 'md',   // 'sm' | 'md' | 'lg'
])

@php

// ── Status token map ─────────────────────────────────────────────────────────
// Each status resolves to a variant name + optional default icon.
// 'brand' is used for anything that represents the "good / default / active"
// state so the design-system emerald shows up consistently wherever the system
// is telling the user "this is fine."
$statusMap = [
    // State
    'success'  => ['variant' => 'brand',   'icon' => 'bx bx-check-circle'],
    'active'   => ['variant' => 'brand',   'icon' => 'bx bx-check-shield'],
    'neutral'  => ['variant' => 'slate',   'icon' => 'bx bx-minus-circle'],
    'disabled' => ['variant' => 'slate',   'icon' => 'bx bx-pause-circle'],
    'info'     => ['variant' => 'blue',    'icon' => 'bx bx-info-circle'],
    'warning'  => ['variant' => 'amber',   'icon' => 'bx bx-error-circle'],
    'pending'  => ['variant' => 'amber',   'icon' => 'bx bx-time-five'],
    'danger'   => ['variant' => 'rose',    'icon' => 'bx bx-x-circle'],
    'rejected' => ['variant' => 'rose',    'icon' => 'bx bx-block'],
    // Roles
    'admin'    => ['variant' => 'violet',  'icon' => 'bx bx-crown'],
    'dean'     => ['variant' => 'indigo',  'icon' => 'bx bx-medal'],
    'chair'    => ['variant' => 'blue',    'icon' => 'bx bx-user-pin'],
    'faculty'  => ['variant' => 'brand',   'icon' => 'bx bx-user'],
    // Course types
    'lec'      => ['variant' => 'brand',   'icon' => 'bx bx-book'],
    'lec_lab'  => ['variant' => 'lab',     'icon' => 'bx bx-flask'],
];

// ── Variant token map ────────────────────────────────────────────────────────
// Pill style: light bg + text color that matches the border color.
// Border = same hue as text, so they read as a cohesive colored pill.
// Design system state progression: Default 600 → Hover 700 → Active 800 → Disabled 100
$variantTokens = [
    // Brand / Emerald — Emerald 100 bg, Emerald 800 text+border
    'brand' => [
        'pill' => 'bg-[#D5FFF0] text-[#06754E] ring-1 ring-inset ring-[#06754E]',
        'dot'  => 'bg-[#00965F]',
    ],
    'emerald' => [
        'pill' => 'bg-[#D5FFF0] text-[#076042] ring-1 ring-inset ring-[#076042]',
        'dot'  => 'bg-[#00C075]',
    ],
    // Blue — Blue 200 bg, Blue 900 text+border
    'blue' => [
        'pill' => 'bg-[#DAF1FF] text-[#143D57] ring-1 ring-inset ring-[#143D57]',
        'dot'  => 'bg-[#3197D6]',
    ],
    'lab' => [   // alias → blue, for lab-inclusive course types
        'pill' => 'bg-[#DAF1FF] text-[#143D57] ring-1 ring-inset ring-[#143D57]',
        'dot'  => 'bg-[#3197D6]',
    ],
    // Sky — lighter blue tint
    'sky' => [
        'pill' => 'bg-[#EBF8FF] text-[#194C6E] ring-1 ring-inset ring-[#194C6E]',
        'dot'  => 'bg-[#3197D6]',
    ],
    // Amber / Yellow — Yellow 200 bg, Yellow 900 text+border
    'amber' => [
        'pill' => 'bg-[#FFF6E2] text-[#875200] ring-1 ring-inset ring-[#875200]',
        'dot'  => 'bg-[#F5B126]',
    ],
    // Rose / Red — Red 200 bg, Red 900 text+border
    'rose' => [
        'pill' => 'bg-[#FFE3E2] text-[#731814] ring-1 ring-inset ring-[#731814]',
        'dot'  => 'bg-[#E52F28]',
    ],
    // Violet — light violet bg, darker violet text+border
    'violet' => [
        'pill' => 'bg-[#F3EEFF] text-[#4C1D95] ring-1 ring-inset ring-[#4C1D95]',
        'dot'  => 'bg-[#7C3AED]',
    ],
    // Indigo — light indigo bg, darker indigo text+border
    'indigo' => [
        'pill' => 'bg-[#EEF2FF] text-[#312E81] ring-1 ring-inset ring-[#312E81]',
        'dot'  => 'bg-[#4338CA]',
    ],
    // Slate / Neutral — Grey 300 bg, Charcoal 500 text+border
    'slate' => [
        'pill' => 'bg-[#F1F3F5] text-[#394056] ring-1 ring-inset ring-[#394056]',
        'dot'  => 'bg-[#72809E]',
    ],
];

$fallbackTokens = $variantTokens['slate'];

// ── Resolve variant + icon from status (if in status mode) ──────────────────
$resolvedVariant  = $variant;  // may be null if only status is set
$resolvedAutoIcon = null;

if (filled($status) && isset($statusMap[$status])) {
    $resolvedVariant  = $resolvedVariant ?? $statusMap[$status]['variant'];
    $resolvedAutoIcon = $statusMap[$status]['icon'] ?? null;
}

// ── Pick the token set ───────────────────────────────────────────────────────
$tokens    = $variantTokens[$resolvedVariant] ?? $fallbackTokens;
$pillStyle = $tokens['pill'];
$dotStyle  = $tokens['dot'];

// ── Icon: explicit prop wins, then status auto-icon, then nothing ───────────
$iconClass = $icon ?? $resolvedAutoIcon;

// ── Size scale ───────────────────────────────────────────────────────────────
$sizeClasses = match ($size) {
    'sm' => 'text-[10px] px-2 py-0.5 gap-1',
    'lg' => 'text-[13px] px-3 py-1 gap-1.5',
    default => 'text-[11px] px-2.5 py-[3px] gap-1',  // md
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

// ── Label resolution ─────────────────────────────────────────────────────────
// Priority: explicit $label prop → slot content → auto-derive from $status
$hasSlot       = $slot->isNotEmpty();
$autoLabel     = filled($status) ? ucfirst(str_replace('_', ' ', (string) $status)) : null;
$showExplicit  = filled($label);
$showSlot      = !$showExplicit && $hasSlot;
$showAutoLabel = !$showExplicit && !$hasSlot && filled($autoLabel);

@endphp

<span {{ $attributes->class([
    'inline-flex items-center font-semibold rounded-full whitespace-nowrap',
    $sizeClasses,
    $pillStyle,
]) }}>

    {{-- Leading indicator: dot or icon, never both --}}
    @if ($dot)
        <span class="rounded-full shrink-0 {{ $dotSizeClass }} {{ $dotStyle }}" aria-hidden="true"></span>
    @elseif ($iconClass)
        <i class="{{ $iconClass }} {{ $iconSizeClass }} shrink-0 leading-none" aria-hidden="true"></i>
    @endif

    {{-- Label --}}
    @if ($showExplicit)
        {{ $label }}
    @elseif ($showSlot)
        {{ $slot }}
    @elseif ($showAutoLabel)
        {{ $autoLabel }}
    @endif

</span>
