{{-- resources/views/button-showcase.blade.php --}}
@extends('layouts.app') {{-- or your layout file --}}

@section('content')
<div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold mb-4">Button Showcase</h1>

    @php
        $buttonVariants = [
            // Table buttons
            'table-restore', 'table-confirm', 'table-disable', 'table-danger',
            'table-manage', 'table-edit', 'table-view', 'table-cancel',
            // Form / CRUD buttons
            'primary', 'save', 'add-button', 'cancel', 'danger',
            'secondary', 'soft', 'outline', 'add-dashed',
            // Wizard / small buttons
            'sm-primary', 'sm-cancel', 'sm-danger', 'sm-warning', 'sm-info', 'sm-success', 'sm-soft',
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($buttonVariants as $variant)
            <div class="flex flex-col items-start gap-2">
                <p class="text-xs font-mono text-slate-500">{{ $variant }}</p>
                <x-ui.button variant="{{ $variant }}">
                    {{ ucfirst(str_replace(['-', 'sm_'], [' ', ''], $variant)) }}
                </x-ui.button>
            </div>
        @endforeach
    </div>

    {{-- Example of buttons with loading --}}
    <div class="mt-8 space-y-2">
        <h2 class="text-lg font-semibold">Loading Button Example</h2>
        <x-ui.button variant="primary" type="button" loading="Processing...">
            Submit
        </x-ui.button>
    </div>

    {{-- Example of link button --}}
    <div class="mt-4 space-y-2">
        <h2 class="text-lg font-semibold">Link Button Example</h2>
        <x-ui.button variant="add-button" href="#">
            Go to Page
        </x-ui.button>
    </div>
</div>
@endsection
