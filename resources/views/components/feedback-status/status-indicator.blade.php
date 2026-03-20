@props([
    // Semantic status mode (existing API)
    'status' => null,
    'label'  => null,

    // UI variant mode (wizard-badge-like)
    'variant' => null,
    'dot'     => false,

    // Icon class (pass full class string, e.g. "bx bx-check-circle")
    'icon' => null,

    // Appearance
    // All indicators are pills (rounded-full). Use size to control padding.
    // Kept for backward compatibility; padding is now consistent everywhere.
    'size'  => 'md',
])

@php
$statusStyles = [
    // General statuses
    'success' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
    'neutral' => 'bg-slate-50 text-slate-600 ring-1 ring-slate-200',
    'info'    => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
    'warning' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
    'danger'  => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',

    // User Account Statuses
    'active'   => 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-300',
    'pending'  => 'bg-amber-50 text-amber-800 ring-1 ring-amber-300',
    'rejected' => 'bg-rose-50 text-rose-800 ring-1 ring-rose-300',
    'disabled' => 'bg-slate-100 text-slate-700 ring-1 ring-slate-300',

    // User Roles
    'admin'    => 'bg-purple-50 text-purple-800 ring-1 ring-purple-300',
    'dean'     => 'bg-indigo-50 text-indigo-800 ring-1 ring-indigo-300',
    'chair'    => 'bg-blue-50 text-blue-800 ring-1 ring-blue-300',
    'faculty'  => 'bg-green-50 text-green-800 ring-1 ring-green-300',

    // Course Components
    'lec' => 'bg-sky-50 text-sky-700 ring-1 ring-sky-200',
    'lec_lab' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
];

$statusIcons = [
    'success' => 'bx bx-check-circle',
    'neutral' => 'bx bx-minus-circle',
    'info' => 'bx bx-info-circle',
    'warning' => 'bx bx-error-circle',
    'danger' => 'bx bx-x-circle',
    'active' => 'bx bx-check-shield',
    'pending' => 'bx bx-time-five',
    'rejected' => 'bx bx-block',
    'disabled' => 'bx bx-pause-circle',
    'admin' => 'bx bx-crown',
    'dean' => 'bx bx-medal',
    'chair' => 'bx bx-user-pin',
    'faculty' => 'bx bx-user',
    'lec' => 'bx bx-book',
    'lec_lab' => 'bx bx-flask',
];

$variantStyles = [
    // Matches (and extends) the old `x-wizard.badge` variants
    'emerald' => ['pill' => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200', 'dot' => 'bg-emerald-500'],
    'blue'    => ['pill' => 'bg-blue-100 text-blue-700 ring-1 ring-blue-200',          'dot' => 'bg-blue-500'],
    'amber'   => ['pill' => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',       'dot' => 'bg-amber-500'],
    'rose'    => ['pill' => 'bg-rose-100 text-rose-700 ring-1 ring-rose-200',          'dot' => 'bg-rose-500'],
    'slate'   => ['pill' => 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',       'dot' => 'bg-slate-400'],
    'violet'  => ['pill' => 'bg-violet-100 text-violet-700 ring-1 ring-violet-200',    'dot' => 'bg-violet-500'],
    'indigo'  => ['pill' => 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200',    'dot' => 'bg-indigo-500'],
    'sky'     => ['pill' => 'bg-sky-100 text-sky-700 ring-1 ring-sky-200',             'dot' => 'bg-sky-500'],
];

$isStatusMode = filled($status);

$style = $isStatusMode
    ? ($statusStyles[$status] ?? 'bg-slate-100 text-slate-700 ring-1 ring-slate-300')
    : ($variantStyles[$variant]['pill'] ?? $variantStyles['slate']['pill']);

$defaultStatusIcon = $isStatusMode ? ($statusIcons[$status] ?? null) : null;
$iconClass = $icon ?? $defaultStatusIcon;
$padClass = '';

$resolvedLabel = $label
    ?? ($slot->isNotEmpty() ? null : ($isStatusMode ? ucfirst(str_replace('_', ' ', (string) $status)) : null));
$dotClass = $variantStyles[$variant]['dot'] ?? 'bg-slate-400';
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1 text-xs font-semibold px-3 py-1',
    'rounded-full',
    $style,
]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $dotClass }}" aria-hidden="true"></span>
    @elseif ($iconClass)
        <i class="{{ $iconClass }} text-xs shrink-0" aria-hidden="true"></i>
    @endif

    @if ($resolvedLabel)
        {{ $resolvedLabel }}
    @else
        {{ $slot }}
    @endif
</span>
