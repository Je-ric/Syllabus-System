{{--
    x-wizard.info-card
    ─────────────────────────────────────────────────────────────────────────────
    A soft read-only info panel for displaying contextual data alongside a form.
    Used in Weekly Coverage (class schedule + calendar summary) and similar steps.

    Props:
      title   string  optional  — small section heading inside the card
      icon    string  optional  — boxicons name WITHOUT "bx-" prefix
      color   string  optional  — 'slate' (default) | 'emerald' | 'blue' | 'amber'

    Slot:
      Body content — pair with x-wizard.info-row for data rows.

    ─── USAGE ────────────────────────────────────────────────────────────────────
    <x-wizard.info-card title="Academic Calendar" icon="calendar" color="slate">
        <x-wizard.info-row label="Period"
            value="{{ $start }} – {{ $end }}" />
        <x-wizard.info-row label="Weeks"   :value="$weekCount" />
        <x-wizard.info-row label="Locked"  :value="$lockedCount" muted />
    </x-wizard.info-card>
--}}

@props([
    'title' => null,
    'icon'  => null,
    'color' => 'slate',
])

@php
    $palette = [
        'slate'   => ['wrap' => 'bg-slate-50 border-slate-200',   'title' => 'text-slate-700'],
        'emerald' => ['wrap' => 'bg-emerald-50/60 border-emerald-200', 'title' => 'text-emerald-800'],
        'blue'    => ['wrap' => 'bg-blue-50/60 border-blue-200',   'title' => 'text-blue-800'],
        'amber'   => ['wrap' => 'bg-amber-50/60 border-amber-200', 'title' => 'text-amber-800'],
    ];
    $p = $palette[$color] ?? $palette['slate'];
@endphp

<div {{ $attributes->class(["rounded-xl border p-4 {$p['wrap']}"]) }}>

    @if ($title)
        <div class="flex items-center gap-1.5 text-xs font-semibold {{ $p['title'] }} mb-3">
            @if ($icon)
                <i class="bx bx-{{ $icon }} text-sm opacity-80" aria-hidden="true"></i>
            @endif
            {{ $title }}
        </div>
    @endif

    {{ $slot }}

</div>
