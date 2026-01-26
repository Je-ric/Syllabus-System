{{-- Available for anchor links --}}

@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
])

@php
    $variant = strval($variant);
    $styles = [
        'primary' => 'btn btn-primary text-white hover:bg-blue-600 active:scale-95 transition-transform duration-200',
        'success' => 'btn btn-success text-white hover:bg-green-600 active:scale-95 transition-transform duration-200',
        'danger' => 'btn bg-red-600 text-white hover:bg-red-400 active:scale-95 transition-transform duration-200',
        'warning' => 'btn btn-warning text-white hover:bg-[#e6a011] active:scale-95 transition-transform duration-200',
        'info' => 'btn btn-info text-white hover:bg-blue-600 active:scale-95 transition-transform duration-200',
        'manage' => 'btn bg-emerald-500 text-white hover:bg-emerald-600 active:scale-95 transition-transform duration-200',

       // restore, undo, recover
        'table-restore' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
            bg-indigo-600 text-white hover:bg-indigo-700 active:scale-95
            transition-transform duration-200',

        // active, save, approve, confirm
        'table-confirm' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
            bg-emerald-600 text-white hover:bg-emerald-700 active:scale-95
            transition-transform duration-200',

        // neutral, disable, inactive
        'table-disable' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
            bg-slate-500 text-white hover:bg-slate-600 active:scale-95
            transition-transform duration-200',

        // reject, delete, remove (destructive)
        'table-danger' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
            bg-rose-600 text-white hover:bg-rose-700 active:scale-95
            transition-transform duration-200',

        // assign role, permissions, manage access
        'table-manage' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
            bg-slate-600 text-white hover:bg-slate-700 active:scale-95
            transition-transform duration-200',

        // edit, update, modify
        'table-edit' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
            bg-blue-600 text-white hover:bg-blue-700 active:scale-95
            transition-transform duration-200',

        // view, preview, inspect
        'table-view' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
            bg-cyan-600 text-white hover:bg-cyan-700 active:scale-95
            transition-transform duration-200',

        // cancel, abort, back
        'table-cancel' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
            bg-gray-500 text-white hover:bg-gray-600 active:scale-95
            transition-transform duration-200',

        'add-button' => '
                    inline-flex items-center gap-2
                    px-4 py-2.5
                    text-sm font-semibold
                    rounded-lg
                    bg-black text-white
                    shadow-sm
                    hover:bg-slate-800 hover:shadow-md
                    focus:ring-2 focus:ring-black/30 focus:outline-none
                    active:scale-95
                    transition-all duration-200
                ',

    ];

    // $class =
    //     'inline-flex items-center justify-center gap-1 px-2 py-2 rounded-md font-medium transition duration-200 ' .
    //     ($styles[$variant] ?? $styles['primary']);
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
