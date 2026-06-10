@props([
    'href'       => null,
    'type'       => 'button',
    'variant'    => 'primary',
    'loading'    => null,
    'wireTarget' => null,
])

@php
    $iconFix = '[&_i]:leading-none [&_i]:text-[1.1em] [&_i]:translate-y-px ';

    $tableBtn  = 'inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg
                  transition-colors duration-150
                  disabled:opacity-50 disabled:pointer-events-none ' . $iconFix;

    $formBtn   = 'inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg
                  shadow-sm transition-all duration-150 active:scale-95
                  focus:ring-2 focus:outline-none
                  disabled:opacity-50 disabled:pointer-events-none ' . $iconFix;

    $wizardBtn = 'inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg
                  transition-colors duration-150
                  disabled:opacity-50 disabled:pointer-events-none ' . $iconFix;

    $styles = [

        // ── Table buttons (compact, no scale) ──────────────────────────────────
        'table-confirm'  => $tableBtn . 'text-white bg-emerald-600 hover:bg-emerald-700',
        'table-edit'     => $tableBtn . 'text-white bg-blue-600 hover:bg-blue-700',
        'table-view'     => $tableBtn . 'text-white bg-cyan-600 hover:bg-cyan-700',
        'table-manage'   => $tableBtn . 'text-white bg-slate-600 hover:bg-slate-700',
        'table-danger'   => $tableBtn . 'text-white bg-rose-600 hover:bg-rose-700',
        'table-disable'  => $tableBtn . 'text-white bg-slate-500 hover:bg-slate-600',
        'table-restore'  => $tableBtn . 'text-white bg-indigo-600 hover:bg-indigo-700',
        'table-cancel'   => $tableBtn . 'bg-white text-slate-700 border border-slate-300
                                         hover:bg-slate-50 hover:border-slate-400',

        // ── Form / CRUD buttons (larger, scales on click) ──────────────────────
        'primary'    => $formBtn . '
            bg-[linear-gradient(90deg,#003a10_0%,#009639_100%)]
            text-white hover:brightness-110
            focus:ring-[#009639]/30',

        'save'       => $formBtn . '
            bg-[linear-gradient(90deg,#009639_0%,#92d12c_100%)]
            text-white hover:brightness-110
            focus:ring-[#009639]/30',

        'secondary'  => $formBtn . '
            bg-[linear-gradient(90deg,#ffd700_0%,#e0a70d_100%)]
            text-[#1a5f30] hover:brightness-105
            focus:ring-[#e0a70d]/30',

        // FIX: on hover, keep the border visible by only filling bg, not swallowing border color.
        // border-[#1a5f30] stays, bg fills to a slightly lighter green so the border still reads.
        'outline'    => $formBtn . '
            bg-white text-[#1a5f30]
            border border-[#1a5f30]
            hover:bg-[#f0fdf4]
            focus:ring-[#1a5f30]/30',

        'add-button' => $formBtn . '
            bg-emerald-600 text-white hover:bg-emerald-700
            focus:ring-[#009639]/30',

        'add-dashed' => $formBtn . '
            justify-center bg-white text-emerald-700
            border-2 border-dashed border-emerald-300
            hover:border-emerald-500 hover:bg-emerald-50
            focus:ring-emerald-500/20',

        'cancel'     => $formBtn . '
            bg-white text-[#475569]
            border border-[#e2e8f0]
            hover:bg-[#f8fafc] hover:border-[#94a3b8]
            focus:ring-[#94a3b8]/20',

        'back'       => $formBtn . '
            bg-white text-[#475569]
            border border-[#e2e8f0]
            hover:bg-[#f8fafc] hover:border-[#94a3b8]
            focus:ring-[#94a3b8]/20',

        'danger'     => $formBtn . '
            bg-rose-600 text-white hover:bg-rose-700
            focus:ring-rose-600/30',

        // ── Small / wizard buttons ─────────────────────────────────────────────
        // FIX: sm-primary hover was repeating base bg instead of darkening it.
        'sm-primary' => $wizardBtn . 'bg-emerald-50 text-emerald-700 border border-emerald-300
                                       hover:bg-emerald-100 hover:border-emerald-400',
        'sm-cancel'  => $wizardBtn . 'bg-white text-[#475569] border border-[#e2e8f0]
                                       hover:bg-[#f8fafc] hover:border-[#94a3b8]',
        'sm-danger'  => $wizardBtn . 'bg-rose-600 text-white hover:bg-rose-700',
        'sm-warning' => $wizardBtn . 'bg-amber-50 text-amber-700 border border-amber-300
                                       hover:bg-amber-100 hover:border-amber-400',
        'sm-info'    => $wizardBtn . 'bg-blue-50 text-blue-700 border border-blue-300
                                       hover:bg-blue-100 hover:border-blue-400',
        'sm-success' => $wizardBtn . 'bg-emerald-50 text-emerald-700 border border-emerald-300
                                       hover:bg-emerald-100 hover:border-emerald-400',
        'sm-soft'    => $wizardBtn . 'bg-lime-50 text-lime-700 border border-lime-300
                                       hover:bg-lime-100 hover:border-lime-400',
        'sm-add'     => $wizardBtn . 'bg-emerald-600 text-white hover:bg-emerald-700
                                       focus:ring-2 focus:ring-emerald-300/50',
    ];

    $class = $styles[strval($variant)] ?? $styles['primary'];

    $attributeKeys = array_keys($attributes->getAttributes());
    $wireClickKey = null;
    foreach ($attributeKeys as $key) {
        if (str_starts_with($key, 'wire:click')) {
            $wireClickKey = $key;
            break;
        }
    }
    $wireClickValue = $wireClickKey ? $attributes->get($wireClickKey) : null;

    $existingTargetAttr = $attributes->get('wire:target');
    $parsedTarget = null;
    if (is_string($wireClickValue) && preg_match('/^\s*([A-Za-z_][A-Za-z0-9_]*)\s*(?:\(|$)/', $wireClickValue, $m)) {
        $parsedTarget = $m[1];
    }

    $autoTarget   = $wireTarget ?: $parsedTarget;
    $spinnerTarget = $autoTarget ?: $existingTargetAttr;
    $shouldHandleLoading = filled($loading) || filled($spinnerTarget);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}"
        {{ $attributes->merge(['class' => $class]) }}
        @if ($shouldHandleLoading && !$attributes->has('wire:loading.attr')) wire:loading.attr="disabled" @endif
        @if ($shouldHandleLoading && $autoTarget && !$attributes->has('wire:target')) wire:target="{{ $autoTarget }}" @endif>

        @if ($shouldHandleLoading)
            @if (filled($loading))
                <span wire:loading.remove @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                      class="inline-flex items-center gap-1.5 leading-none">{{ $slot }}</span>
                <span wire:loading @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                      class="inline-flex items-center gap-1.5 leading-none">
                    <svg class="animate-spin h-3.5 w-3.5 shrink-0 leading-none" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="leading-none">{{ $loading }}</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 leading-none">
                    <span class="inline-flex items-center gap-1.5 leading-none">{{ $slot }}</span>
                    <svg wire:loading @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                         class="animate-spin h-3.5 w-3.5 shrink-0 leading-none" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
            @endif
        @else
            <span class="inline-flex items-center gap-1.5 leading-none">{{ $slot }}</span>
        @endif
    </button>
@endif

{{--
VARIANTS
────────────────────────────────────────────────────────────────────
Table (compact, no scale):
  table-confirm   → emerald  — Approve, Activate, generic confirm
  table-edit      → blue     — Edit (✏) — use this instead of table-confirm for edit actions
  table-view      → cyan     — View / Preview
  table-manage    → slate-600 — Roles, Settings
  table-danger    → rose     — Reject, Delete
  table-disable   → slate-500 — Disable, Pause
  table-restore   → indigo   — Restore, Undo
  table-cancel    → white/border — Cancel inline action

Form (larger, scales on click):
  primary     → dark-to-green gradient — main submit action
  save        → green-to-lime gradient — save / update
  secondary   → gold gradient          — secondary / accent action
  outline     → white + green border   — ghost / secondary confirm
  add-button  → solid emerald          — add new record
  add-dashed  → dashed border          — add optional / inline item
  cancel      → white + slate border   — cancel / dismiss
  danger      → solid rose             — destructive action

Small / wizard (match height when beside sm-* buttons):
  sm-primary  sm-cancel  sm-danger
  sm-warning  sm-info    sm-success  sm-soft
  sm-add      ← solid emerald, same height as sm-* variants
--}}
