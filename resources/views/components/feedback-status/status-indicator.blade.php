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
// This decouples visual tokens from semantic meaning.
$statusMap = [
    // State
    'success'  => ['variant' => 'emerald', 'icon' => 'bx bx-check-circle'],
    'active'   => ['variant' => 'emerald', 'icon' => 'bx bx-check-shield'],
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
    'faculty'  => ['variant' => 'emerald', 'icon' => 'bx bx-user'],
    // Course types
    'lec'      => ['variant' => 'emerald', 'icon' => 'bx bx-book'],
    'lec_lab'  => ['variant' => 'emerald', 'icon' => 'bx bx-flask'],
];

// ── Variant token map ────────────────────────────────────────────────────────
// pill  = the badge background + text + ring
// dot   = the colored dot fill
// All rings use ring-inset so they render cleanly inside bordered containers.
$variantTokens = [
    'emerald' => [
        'pill' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200',
        'dot'  => 'bg-emerald-500',
    ],
    'brand' => [  // alias → emerald
        'pill' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200',
        'dot'  => 'bg-emerald-500',
    ],
    'blue' => [
        'pill' => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-200',
        'dot'  => 'bg-blue-500',
    ],
    'lab' => [   // alias → blue
        'pill' => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-200',
        'dot'  => 'bg-blue-500',
    ],
    'sky' => [
        'pill' => 'bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-200',
        'dot'  => 'bg-sky-500',
    ],
    'amber' => [
        'pill' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200',
        'dot'  => 'bg-amber-400',
    ],
    'rose' => [
        'pill' => 'bg-rose-50 text-rose-600 ring-1 ring-inset ring-rose-200',
        'dot'  => 'bg-rose-400',
    ],
    'violet' => [
        'pill' => 'bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-200',
        'dot'  => 'bg-violet-500',
    ],
    'indigo' => [
        'pill' => 'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-200',
        'dot'  => 'bg-indigo-400',
    ],
    'slate' => [
        'pill' => 'bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200',
        'dot'  => 'bg-slate-400',
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
$tokens   = $variantTokens[$resolvedVariant] ?? $fallbackTokens;
$pillStyle = $tokens['pill'];
$dotStyle  = $tokens['dot'];

// ── Icon: explicit prop wins, then status auto-icon, then nothing ───────────
$iconClass = $icon ?? $resolvedAutoIcon;

// ── Size scale ───────────────────────────────────────────────────────────────
$sizeClasses = match ($size) {
    'sm' => 'text-[10px] px-2 py-0.5 gap-1',
    'lg' => 'text-[13px] px-3 py-1 gap-1.5',
    default => 'text-[11px] px-2.5 py-0.5 gap-1',  // md
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
$hasSlot         = $slot->isNotEmpty();
$autoLabel       = filled($status) ? ucfirst(str_replace('_', ' ', (string) $status)) : null;
$showExplicit    = filled($label);
$showSlot        = !$showExplicit && $hasSlot;
$showAutoLabel   = !$showExplicit && !$hasSlot && filled($autoLabel);

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