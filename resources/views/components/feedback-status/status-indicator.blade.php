@props([
    'status'  => null,
    'label'   => null,
    'variant' => null,
    'dot'     => false,
    'icon'    => null,
    'size'    => 'md',
])

@php
// Design-token aligned status styles
$statusStyles = [
    'success'  => 'bg-[#f0fdf4] text-[#166534] ring-1 ring-[#bbf7d0]',
    'neutral'  => 'bg-[#f8fafc] text-[#475569] ring-1 ring-[#e2e8f0]',
    'info'     => 'bg-[#eff6ff] text-[#1e40af] ring-1 ring-[#bfdbfe]',
    'warning'  => 'bg-[#fffbeb] text-[#92400e] ring-1 ring-[#fcd34d]',
    'danger'   => 'bg-[#fff1f2] text-[#9f1239] ring-1 ring-[#fda4af]',
    'active'   => 'bg-[#f0fdf4] text-[#166534] ring-1 ring-[#bbf7d0]',
    'pending'  => 'bg-[#fffbeb] text-[#92400e] ring-1 ring-[#fcd34d]',
    'rejected' => 'bg-[#fff1f2] text-[#9f1239] ring-1 ring-[#fda4af]',
    'disabled' => 'bg-[#f8fafc] text-[#475569] ring-1 ring-[#e2e8f0]',
    'admin'    => 'bg-[#faf5ff] text-[#581c87] ring-1 ring-[#d8b4fe]',
    'dean'     => 'bg-[#eef2ff] text-[#3730a3] ring-1 ring-[#a5b4fc]',
    'chair'    => 'bg-[#eff6ff] text-[#1e40af] ring-1 ring-[#93c5fd]',
    'faculty'  => 'bg-[#f0fdf4] text-[#166534] ring-1 ring-[#bbf7d0]',
    'lec'      => 'bg-[#f0fdf4] text-[#166534] ring-1 ring-[#bbf7d0]',
    'lec_lab'  => 'bg-[#f0fdf4] text-[#166534] ring-1 ring-[#bbf7d0]',
];

$statusIcons = [
    'success'  => 'bx bx-check-circle',
    'neutral'  => 'bx bx-minus-circle',
    'info'     => 'bx bx-info-circle',
    'warning'  => 'bx bx-error-circle',
    'danger'   => 'bx bx-x-circle',
    'active'   => 'bx bx-check-shield',
    'pending'  => 'bx bx-time-five',
    'rejected' => 'bx bx-block',
    'disabled' => 'bx bx-pause-circle',
    'admin'    => 'bx bx-crown',
    'dean'     => 'bx bx-medal',
    'chair'    => 'bx bx-user-pin',
    'faculty'  => 'bx bx-user',
    'lec'      => 'bx bx-book',
    'lec_lab'  => 'bx bx-flask',
];

// Variant styles — brand=green, lab=sky-blue, rose=error, slate=neutral
$variantStyles = [
    'brand'   => ['pill' => 'bg-[#f0fdf4] text-[#166534] ring-1 ring-[#bbf7d0]',  'dot' => 'bg-[#16a34a]'],
    'emerald' => ['pill' => 'bg-[#f0fdf4] text-[#166534] ring-1 ring-[#bbf7d0]',  'dot' => 'bg-[#16a34a]'],
    'lab'     => ['pill' => 'bg-[#eff6ff] text-[#1e40af] ring-1 ring-[#bfdbfe]',  'dot' => 'bg-[#3b82f6]'],
    'blue'    => ['pill' => 'bg-[#eff6ff] text-[#1e40af] ring-1 ring-[#bfdbfe]',  'dot' => 'bg-[#3b82f6]'],
    'amber'   => ['pill' => 'bg-[#fffbeb] text-[#92400e] ring-1 ring-[#fcd34d]',  'dot' => 'bg-[#f59e0b]'],
    'rose'    => ['pill' => 'bg-[#fff1f2] text-[#9f1239] ring-1 ring-[#fda4af]',  'dot' => 'bg-[#f43f5e]'],
    'slate'   => ['pill' => 'bg-[#f8fafc] text-[#475569] ring-1 ring-[#e2e8f0]',  'dot' => 'bg-[#94a3b8]'],
    'violet'  => ['pill' => 'bg-[#faf5ff] text-[#581c87] ring-1 ring-[#d8b4fe]',  'dot' => 'bg-[#8b5cf6]'],
    'indigo'  => ['pill' => 'bg-[#eef2ff] text-[#3730a3] ring-1 ring-[#a5b4fc]',  'dot' => 'bg-[#6366f1]'],
    'sky'     => ['pill' => 'bg-[#f0f9ff] text-[#075985] ring-1 ring-[#bae6fd]',  'dot' => 'bg-[#0ea5e9]'],
];

$isStatusMode = filled($status);

$style = $isStatusMode
    ? ($statusStyles[$status] ?? 'bg-[#f8fafc] text-[#475569] ring-1 ring-[#e2e8f0]')
    : ($variantStyles[$variant]['pill'] ?? $variantStyles['slate']['pill']);

$defaultStatusIcon = $isStatusMode ? ($statusIcons[$status] ?? null) : null;
$iconClass  = $icon ?? $defaultStatusIcon;
$dotClass   = $variantStyles[$variant]['dot'] ?? 'bg-[#94a3b8]';

$resolvedLabel = $label
    ?? ($slot->isNotEmpty() ? null : ($isStatusMode ? ucfirst(str_replace('_', ' ', (string) $status)) : null));
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1 font-semibold rounded-full',
    'text-[11px] px-2.5 py-0.5',
    $style,
]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $dotClass }}" aria-hidden="true"></span>
    @elseif ($iconClass)
        <i class="{{ $iconClass }} text-[11px] shrink-0" aria-hidden="true"></i>
    @endif

    @if ($resolvedLabel)
        {{ $resolvedLabel }}
    @else
        {{ $slot }}
    @endif
</span>
