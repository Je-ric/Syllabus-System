@props([
    'href'    => null,
    'type'    => 'button',
    'variant' => 'primary',
    'loading' => null,
])

@php
    // Table buttons: compact, no scale animation (jarring on small targets), disabled support
    $tableBtn = 'inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg
                transition-colors duration-150
                disabled:opacity-50 disabled:pointer-events-none ';

    // Form / CRUD buttons: larger, scale animation is fine on full-size buttons
    $formBtn  = 'inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg
                shadow-sm transition-all duration-150 active:scale-95
                focus:ring-2 focus:outline-none
                disabled:opacity-50 disabled:pointer-events-none ';

    // Livewire buttons: small inline buttons used in Livewire steps
    $wizardBtn = 'inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg
                transition-colors duration-150 disabled:opacity-50 disabled:pointer-events-none ';

    $styles = [
        // ── Table buttons ──────────────────────────────────────────────────────
        'table-restore' => $tableBtn . 'text-white bg-indigo-600 hover:bg-indigo-700',
        'table-confirm' => $tableBtn . 'text-white bg-emerald-600 hover:bg-emerald-700',
        'table-disable' => $tableBtn . 'text-white bg-slate-500 hover:bg-slate-600',
        'table-danger'  => $tableBtn . 'text-white bg-rose-600 hover:bg-rose-700',
        'table-manage'  => $tableBtn . 'text-white bg-slate-600 hover:bg-slate-700',
        'table-edit'    => $tableBtn . 'text-white bg-blue-600 hover:bg-blue-700',
        'table-view'    => $tableBtn . 'text-white bg-cyan-600 hover:bg-cyan-700',
        // slate, not gray
        'table-cancel'  => $tableBtn . 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 hover:border-slate-400',

        // ── Form / CRUD buttons ────────────────────────────────────────────────
        // slate, not gray
        'cancel' => $formBtn . 'bg-white text-slate-700 border border-slate-300
                                hover:bg-slate-50 hover:border-slate-400
                                focus:ring-slate-400/30',

        'danger' => $formBtn . 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-600/30',

        // ── CLSU-branded buttons ───────────────────────────────────────────────
        'add-button' => $formBtn . '
            bg-[linear-gradient(90deg,#003a10_0%,#009639_100%)]
            text-white
            hover:brightness-110
            focus:ring-[#009639]/30',

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

        'soft' => $formBtn . '
            bg-[linear-gradient(90deg,#92d12c_0%,#cdfb13_100%)]
            text-[#003a10]
            hover:brightness-105
            focus:ring-[#92d12c]/30',

        'outline' => $formBtn . '
            bg-white text-[#1a5f30]
            border-2 border-[#1a5f30]
            hover:bg-[#1a5f30] hover:text-white
            focus:ring-[#1a5f30]/30',

        // Dashed "add" button used in wizard-like editors
        'add-dashed' => $formBtn . 'justify-center bg-white text-emerald-700 border-2 border-dashed border-emerald-300
            hover:border-emerald-500 hover:bg-emerald-50 focus:ring-emerald-500/20',

        // (small inline) buttons
        'sm-primary' => $wizardBtn . 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-50 hover:border-emerald-400',
        'sm-cancel'  => $wizardBtn . 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 hover:border-slate-400',
        'sm-danger'  => $wizardBtn . 'bg-rose-600 text-white hover:bg-rose-700',
        'sm-warning' => $wizardBtn . 'bg-amber-50 text-amber-700 border border-amber-300 hover:bg-amber-100',
        'sm-info'    => $wizardBtn . 'bg-blue-50 text-blue-700 border border-blue-300 hover:bg-blue-100',
        'sm-success' => $wizardBtn . 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100',
        'sm-soft'    => $wizardBtn . 'bg-lime-50 text-lime-700 border border-lime-300 hover:bg-lime-100',
    ];

    $class = $styles[strval($variant)] ?? $styles['primary'];
    $wireTarget = $loading ? ($attributes->get('wire:target') ?? '') : '';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@elseif ($loading)
    <button type="{{ $type }}"
        wire:loading.attr="disabled"
        {{ $attributes->merge(['class' => $class]) }}>

        @if ($wireTarget)
            <span wire:loading.remove wire:target="{{ $wireTarget }}">{{ $slot }}</span>
            <span wire:loading wire:target="{{ $wireTarget }}" class="inline-flex items-center gap-1.5">
                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ $loading }}
            </span>
        @else
            <span wire:loading.remove>{{ $slot }}</span>
            <span wire:loading class="inline-flex items-center gap-1.5">
                <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ $loading }}
            </span>
        @endif
    </button>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@endif

{{--
VARIANTS
────────────────────────────────────────────────────────────────────
Table (compact, no scale):
  table-confirm  table-edit    table-view    table-manage
  table-danger   table-disable table-restore table-cancel

Form (larger, scales on click):
  primary    save    add-button    cancel    danger
  secondary  soft    outline

USAGE
────────────────────────────────────────────────────────────────────
<x-button variant="primary" type="submit">
    <i class="bx bx-save"></i> Save
</x-button>

<x-button href="{{ route('users.create') }}" variant="add-button">
    <i class="bx bx-plus"></i> Add User
</x-button>

<x-button variant="cancel" onclick="modal.close()">Cancel</x-button>

<x-button variant="table-edit"
    onclick="document.getElementById('editModal').showModal()">
    <i class="bx bx-edit"></i> Edit
</x-button>

<x-button variant="table-danger" disabled title="Cannot delete">
    <i class="bx bx-trash"></i>
</x-button>
--}}
