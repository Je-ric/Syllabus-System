{{-- Available for anchor links --}}

@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
])

@php
    $tableBtn = 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
                active:scale-95 transition-transform duration-200 '; //remember dapat may space sa dulo, concatenation kase, it's either dito or sa isa.
    $formBtn = 'inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg
                shadow-sm transition-all duration-200 hover:shadow-md active:scale-95 focus:ring-2 focus:outline-none ';

    $variant = strval($variant);
    $styles = [

        // ================================
        // TABLE BUTTONS
        // ================================
        // restore, undo, recover
        'table-restore' => $tableBtn . 'text-white bg-indigo-600 hover:bg-indigo-700', // or dito
        // active, save, approve, confirm
        'table-confirm' => $tableBtn . 'text-white bg-emerald-600 hover:bg-emerald-700',
        // neutral, disable, inactive
        'table-disable' => $tableBtn . 'text-white bg-slate-500 hover:bg-slate-600',
        // reject, delete, remove (destructive)
        'table-danger' => $tableBtn . 'text-white bg-rose-600 hover:bg-rose-700',
        // assign role, permissions, manage access
        'table-manage' => $tableBtn . 'text-white bg-slate-600 hover:bg-slate-700',
        // edit, update, modify
        'table-edit' => $tableBtn . 'text-white bg-blue-600 hover:bg-blue-700',
        // view, preview, inspect
        'table-view' => $tableBtn . 'text-white bg-cyan-600 hover:bg-cyan-700',
        // cancel, abort, back
        'table-cancel' => $tableBtn . 'bg-white text-gray-700  border border-gray-300',



        // ================================
        // FORM BUTTONS
        // ================================
        // 'add-button' => $formBtn . 'bg-black text-white hover:bg-slate-800 focus:ring-black/30',
        'cancel' => $formBtn . 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 focus:ring-gray-400/30',
        // 'save' => $formBtn . 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-600/30',
        'danger' => $formBtn . 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-600/30',

        'add-button' => $formBtn . '
            bg-[linear-gradient(90deg,#003a10_0%,#009639_100%)]
            text-white font-semibold
            hover:bg-[linear-gradient(90deg,#002b0c_0%,#007a2e_100%)]
            focus:ring-[#009639]/30
        ',

        'save' => $formBtn . '
            bg-[linear-gradient(90deg,#009639_0%,#92d12c_100%)]
            text-white font-semibold
            hover:bg-[linear-gradient(90deg,#007a2e_0%,#7fbf26_100%)]
            focus:ring-[#009639]/30
        ',

        // =================================
        // CLSU THEMED
        // =================================
        'primary' => $formBtn . '
            bg-[linear-gradient(90deg,#003a10_0%,#009639_100%)]
            text-white font-semibold
            hover:bg-[linear-gradient(90deg,#002b0c_0%,#007a2e_100%)]
            focus:ring-[#009639]/30
        ',

        'secondary' => $formBtn . '
            bg-[linear-gradient(90deg,#ffd700_0%,#e0a70d_100%)]
            text-[#1a5f30] font-semibold
            hover:bg-[linear-gradient(90deg,#e0a70d_0%,#ffd700_100%)]
            focus:ring-[#e0a70d]/30
        ',

        'soft' => $formBtn . '
            bg-[linear-gradient(90deg,#92d12c_0%,#cdfb13_100%)]
            text-[#003a10] font-medium
            hover:bg-[linear-gradient(90deg,#7fbf26_0%,#b8e911_100%)]
            focus:ring-[#92d12c]/30
        ',

        'outline' => $formBtn . '
            bg-white
            text-[#1a5f30] font-semibold
            border-2 border-[#1a5f30]
            hover:bg-[#1a5f30] hover:text-white
            focus:ring-[#1a5f30]/30
        ',


    ];

    $class = $styles[$variant] ?? $styles['primary'];
@endphp

{{-- So, it allows button to work as clickable link if provided a href, but mag-aact as regular button if none --}}
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </button>
@endif

{{--
Usage: <x-button variant="primary">Submit</x-button>
        <x-button href="/dashboard" variant="secondary">Go to Dashboard</x-button>
        <x-button variant="attendance-dark">Attendance on Dark</x-button>
        <x-button variant="disabled-dark" disabled>Disabled on Dark</x-button>
        <x-button variant="table-action-view" class="tooltip" data-tip="View Details"><i class="bx bx-show"></i></x-button>
        <x-button variant="save-entry" type="submit">Save Entry</x-button>
        <x-button variant="attendance">Clock In</x-button>


--}}
