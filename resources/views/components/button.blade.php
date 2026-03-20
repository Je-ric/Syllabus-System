@props([
    'href'       => null,
    'type'       => 'button',
    'variant'    => 'primary',
    'loading'    => null,
    'wireTarget' => null,
])

@php
    // [&_i] targets ALL descendant <i> tags (Boxicons) anywhere inside the button,
    // including those wrapped in a <span> slot — fixes alignment in all usage patterns.
    // - leading-none   → removes line-height that pushes icons upward
    // - text-[1.1em]   → makes icon scale with the button's font size
    // - translate-y-px → 1 px nudge to optically center against cap-height text
    $iconFix = '[&_i]:leading-none [&_i]:text-[1.1em] [&_i]:translate-y-px ';

    // Table buttons: compact, no scale animation (jarring on small targets), disabled support
    $tableBtn = 'inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg
                transition-colors duration-150
                disabled:opacity-50 disabled:pointer-events-none ' . $iconFix;

    // Form / CRUD buttons: larger, scale animation is fine on full-size buttons
    $formBtn  = 'inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg
                shadow-sm transition-all duration-150 active:scale-95
                focus:ring-2 focus:outline-none
                disabled:opacity-50 disabled:pointer-events-none ' . $iconFix;

    // Livewire / wizard buttons: small inline buttons used in Livewire steps
    $wizardBtn = 'inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg
                transition-colors duration-150 disabled:opacity-50 disabled:pointer-events-none ' . $iconFix;

    $styles = [
        // ── Table buttons ──────────────────────────────────────────────────────
        'table-restore' => $tableBtn . 'text-white bg-indigo-600 hover:bg-indigo-700',
        'table-confirm' => $tableBtn . 'text-white bg-emerald-600 hover:bg-emerald-700',
        'table-disable' => $tableBtn . 'text-white bg-slate-500 hover:bg-slate-600',
        'table-danger'  => $tableBtn . 'text-white bg-rose-600 hover:bg-rose-700',
        'table-manage'  => $tableBtn . 'text-white bg-slate-600 hover:bg-slate-700',
        'table-edit'    => $tableBtn . 'text-white bg-blue-600 hover:bg-blue-700',
        'table-view'    => $tableBtn . 'text-white bg-cyan-600 hover:bg-cyan-700',
        'table-cancel'  => $tableBtn . 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 hover:border-slate-400',

        // ── Form / CRUD buttons ────────────────────────────────────────────────
        'cancel' => $formBtn . 'bg-white text-slate-700 border border-slate-300
                                hover:bg-slate-50 hover:border-slate-400
                                focus:ring-slate-400/30',

        'danger' => $formBtn . 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-600/30',

        // ── CLSU-branded buttons ───────────────────────────────────────────────
        'add-button' => $formBtn . 'focus:ring-[#009639]/30 bg-emerald-600 text-white hover:bg-emerald-700',

        'save' => $formBtn . '
            bg-[linear-gradient(90deg,#009639_0%,#92d12c_100%)]
            text-white
            hover:brightness-110
            focus:ring-[#009639]/30',

        'primary' => $formBtn . '
            bg-[linear-gradient(90deg,#003a10_0%,#009639_100%)]
            text-white
            hover:brightness-110
            focus:ring-[#009639]/30',

        'secondary' => $formBtn . '
            bg-[linear-gradient(90deg,#ffd700_0%,#e0a70d_100%)]
            text-[#1a5f30]
            hover:brightness-105
            focus:ring-[#e0a70d]/30',

        'outline' => $formBtn . '
            bg-white text-[#1a5f30]
            border-2 border-[#1a5f30]
            hover:bg-[#1a5f30] hover:text-white
            focus:ring-[#1a5f30]/30',

        'add-dashed' => $formBtn . 'justify-center bg-white text-emerald-700 border-2 border-dashed border-emerald-300
            hover:border-emerald-500 hover:bg-emerald-50 focus:ring-emerald-500/20',

        // ── Small / wizard buttons ─────────────────────────────────────────────
        'sm-primary' => $wizardBtn . 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-50 hover:border-emerald-400',
        'sm-cancel'  => $wizardBtn . 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 hover:border-slate-400',
        'sm-danger'  => $wizardBtn . 'bg-rose-600 text-white hover:bg-rose-700',
        'sm-warning' => $wizardBtn . 'bg-amber-50 text-amber-700 border border-amber-300 hover:bg-amber-100',
        'sm-info'    => $wizardBtn . 'bg-blue-50 text-blue-700 border border-blue-300 hover:bg-blue-100',
        'sm-success' => $wizardBtn . 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100',
        'sm-soft'    => $wizardBtn . 'bg-lime-50 text-lime-700 border border-lime-300 hover:bg-lime-100',
        // sm-add: wizard-sized solid emerald — use when add-button is beside sm-* buttons
        'sm-add'     => $wizardBtn . 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-300/50',
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

    $autoTarget = $wireTarget ?: $parsedTarget;
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
                {{-- Named loading text: hide slot, show spinner + label --}}
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
                {{-- Append-spinner: slot stays, spinner appears beside it --}}
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
  table-confirm  table-edit    table-view    table-manage
  table-danger   table-disable table-restore table-cancel

Form (larger, scales on click):
  primary    save    add-button    cancel    danger
  secondary  outline add-dashed

Small / wizard (use when beside other sm-* buttons for size consistency):
  sm-primary  sm-cancel  sm-danger
  sm-warning  sm-info    sm-success  sm-soft
  sm-add      ← solid emerald, same size as sm-* — use instead of add-button
              when the button is beside sm-warning / sm-cancel etc.

USAGE
────────────────────────────────────────────────────────────────────
<x-button variant="primary" type="submit">
    <i class="bx bx-save"></i> Save
</x-button>

<x-button href="{{ route('users.create') }}" variant="add-button">
    <i class="bx bx-plus"></i> Add User
</x-button>

{{-- Beside sm-warning / sm-cancel: use sm-add for matching height --}}
{{-- <x-button variant="sm-warning" wire:click="regenerate" loading="Regenerating…">
    <i class="bx bx-refresh"></i> Regenerate
</x-button>
<x-button variant="sm-add" wire:click="saveAll" loading="Saving…">
    <i class="bx bx-save"></i> Save All
</x-button>

<x-button variant="save"
    wire:click="saveForm"
    wire:target="saveForm"
    loading="Saving…">
    <i class="bx bx-save"></i> Save
</x-button>

<x-button variant="table-edit"
    onclick="document.getElementById('editModal').showModal()">
    <i class="bx bx-edit"></i> Edit
</x-button> --}}
