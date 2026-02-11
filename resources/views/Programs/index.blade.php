@extends('layouts.app')

@section('content')

<x-header-with-button
        title="Program Management"
        description="Program Educational Objectives (PEO) and Program Outcomes (PO)"
    />

<div class="p-6 space-y-10 text-slate-800">

    <div class="mt-4">
        <livewire:programs.program-selector
            :program-id="optional($program)->id"
            redirect-route="programs.show"
            :autoRedirect="true"
        />
    </div>

    <div class="mt-6">
        <h2 class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">Mission</h2>
        <p class="text-slate-600 leading-relaxed">
            CLSU shall develop globally competitive, work-ready, socially-responsible
            and empowered human resources who value life-long learning; and to generate,
            disseminate, and apply knowledge and technologies for poverty alleviation,
            environmental protection, and sustainable development.
        </p>
    </div>

    {{-- When a program is selected --}}
    @if ($program)
        <div class="mt-6 border-t border-slate-200 pt-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">
                Program: <span class="text-emerald-700">{{ $program->name }}</span>
            </h2>

            <x-navigation.tabs-modern :tabs="[
                ['id' => 'peo', 'label' => 'PEOs', 'icon' => 'bx-graduation'],
                ['id' => 'po', 'label' => 'POs', 'icon' => 'bx-target']
            ]" defaultTab="peo">

                <x-slot name="slot_peo">
                    <div class="bg-white/90 border border-slate-200 rounded-2xl p-5 shadow-sm">
                        <h2 class="text-sm font-semibold text-slate-800 mb-3">Program Educational Objectives (PEOs)</h2>

                        <p class="text-sm text-slate-600 mb-4">Three to five years after graduation, the BSIT graduates are:</p>
                        <livewire:programs.manage-peos :program="$program" />
                    </div>
                </x-slot>

                <x-slot name="slot_po">
                    <div class="bg-white/90 border border-slate-200 rounded-2xl p-5 shadow-sm">
                        <h2 class="text-sm font-semibold text-slate-800 mb-3">Program Outcomes (POs) and its Relationship to the Program Educational Objectives</h2>

                        {{-- Show PEOs of the selected program for reference --}}
                        <livewire:programs.peo-display :program="$program" />

                        <p class="text-sm text-slate-600 mb-4">By the time of graduation, students of the program have the ability to:</p>
                        {{-- Manage POs and map to PEOs --}}
                        <livewire:programs.manage-pos :program="$program" />
                    </div>

                </x-slot>

            </x-navigation.tabs-modern>
        </div>
    @else
        <div class="text-center text-slate-500 mt-8">
            <p>Select a program to manage its Program Educational Objectives (PEO) and Program Outcomes (PO).</p>
        </div>
    @endif
</div>
@endsection
