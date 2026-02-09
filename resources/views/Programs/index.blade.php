@extends('layouts.app')

@section('content')

<x-header-with-button
        title="Program Management"
        description="Program Educational Objectives (PEO) and Program Outcomes (PO)"
    />

<div class="p-6 space-y-8">

    <div class="mt-4">
        <livewire:programs.program-selector
            :program-id="optional($program)->id"
            redirect-route="programs.show"
            :autoRedirect="true"
        />
    </div>

    <div class="mt-6">
        <h2 class="text-lg font-semibold text-black mb-2">Mission:</h2>
        <p class="text-gray-600 leading-relaxed">
            CLSU shall develop globally competitive, work-ready, socially-responsible
            and empowered human resources who value life-long learning; and to generate,
            disseminate, and apply knowledge and technologies for poverty alleviation,
            environmental protection, and sustainable development.
        </p>
    </div>

    {{-- When a program is selected --}}
    @if ($program)
        <div class="mt-6 border-t pt-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Program: {{ $program->name }}</h2>

            <x-navigation.tabs-modern :tabs="[
                ['id' => 'peo', 'label' => 'PEOs', 'icon' => 'bx-graduation'],
                ['id' => 'po', 'label' => 'POs', 'icon' => 'bx-target']
            ]" defaultTab="peo">

                <x-slot name="slot_peo">
                    @include('Programs.partials.peos', ['program' => $program])
                </x-slot>

                <x-slot name="slot_po">
                    @include('Programs.partials.outcomes', ['program' => $program])
                </x-slot>

            </x-navigation.tabs-modern>
        </div>
    @else
        <div class="text-center text-gray-500 mt-8">
            <p>Select a program to manage its Program Educational Objectives (PEO) and Program Outcomes (PO).</p>
        </div>
    @endif
</div>
@endsection
