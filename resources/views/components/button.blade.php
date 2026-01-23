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
        'manage' =>
            'btn bg-emerald-500 text-white hover:bg-emerald-600 active:scale-95 transition-transform duration-200',

        'restore' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
                    bg-indigo-600 text-white hover:bg-indigo-700 active:scale-95
                    transition-transform duration-200',

        'active' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
                    bg-emerald-600 text-white hover:bg-emerald-700 active:scale-95
                    transition-transform duration-200',

        'disable' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
                    bg-slate-500 text-white hover:bg-slate-600 active:scale-95
                    transition-transform duration-200',

        'reject' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
                    bg-rose-600 text-white hover:bg-rose-700 active:scale-95
                    transition-transform duration-200',

        'assign-role' => 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md
                        bg-slate-600 text-white hover:bg-slate-700 active:scale-95
                        transition-transform duration-200',

        'add-button' => '
                    bg-black text-white
                    px-4 py-2.5
                    rounded-lg
                    font-medium text-sm leading-5
                    shadow-xs
                    hover:bg-black-700
                    focus:ring-2 focus:ring-black-200
                    focus:outline-none
                    transition-colors
                ',
        'save-input' => '
                    bg-green-600 text-white
                    px-4 py-2.5
                    rounded-lg
                    font-medium text-sm leading-5
                    shadow-xs
                    hover:bg-green-700
                    focus:ring-2 focus:ring-green-200
                    focus:outline-none
                    transition-colors
                ',

        'cancel' => '
                    text-body
                    bg-neutral-secondary-medium
                    border border-default-medium
                    px-4 py-2.5
                    rounded-base
                    font-medium text-sm leading-5
                    shadow-xs
                    rounded-lg
                    hover:bg-neutral-tertiary-medium
                    hover:text-heading
                    focus:ring-2 focus:ring-neutral-tertiary
                    focus:outline-none
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
