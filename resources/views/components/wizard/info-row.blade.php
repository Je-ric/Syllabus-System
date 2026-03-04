{{--
    x-wizard.info-row
    ─────────────────────────────────────────────────────────────────────────────
    A single label / value data row intended for use inside x-wizard.info-card.

    Props:
      label   string   required   — row label shown on the left
      value   string   optional   — display value; if null/empty renders "—"
      muted   bool     optional   — renders value in lighter colour (default false)
      bold    bool     optional   — renders value in semi-bold (default false)

    ─── USAGE ────────────────────────────────────────────────────────────────────
    <x-wizard.info-row label="Semester"  value="1st Semester 2024–2025" />
    <x-wizard.info-row label="Weeks"     :value="$weekCount" bold />
    <x-wizard.info-row label="Locked"    :value="$count . ' weeks'" muted />
    <x-wizard.info-row label="Calendar"  />
--}}

@props([
    'label',
    'value' => null,
    'muted' => false,
    'bold'  => false,
])

<div class="flex items-start justify-between gap-4 py-1.5
            border-b border-black/5 last:border-0">

    <span class="text-xs font-medium text-slate-500 shrink-0">
        {{ $label }}
    </span>

    <span @class([
        'text-xs text-right',
        'font-semibold text-slate-800' => $bold && $value,
        'font-medium text-slate-700'   => ! $bold && $value && ! $muted,
        'text-slate-400'               => $muted && $value,
        'text-slate-300 italic'        => ! $value,
    ])>
        {{ $value ?? '—' }}
    </span>

</div>
