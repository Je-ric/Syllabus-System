@extends('layouts.app')

@section('content')
    <div class="p-6 space-y-8">

        <h1 class="text-2xl font-bold text-gray-800">Program Management</h1>

        <div class="mt-4">
            <livewire:programs.program-selector :program-id="optional($program)->id" />
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
            <div class="mt-6 border-t pt-6 space-y-6">

                <h2 class="text-xl font-semibold text-gray-800 mb-4">Program: {{ $program->name }}</h2>

                <div class="bg-white border rounded-lg shadow-sm p-4">
                    @include('Programs.partials.peos', ['program' => $program])
                </div>

                <div class="bg-white border rounded-lg shadow-sm p-4">
                    @include('Programs.partials.outcomes', ['program' => $program])
                </div>

            </div>
        @else
            <div class="text-center text-gray-500 mt-8">
                <p>Select a program to manage its Program Educational Objectives (PEO) and Program Outcomes (PO).</p>
            </div>
        @endif

    </div>
@endsection
