@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-sitemap"
        title="Program Management"
        desc="Manage Program Educational Objectives (PEOs) and Program Outcomes (POs)."
    />

    <x-panel>

        <x-card-section title="Select Program" icon="bx-network-chart"
            description="Choose a program to manage its PEOs and POs." class="mb-6">
            <livewire:programs.program-selector
                :program-id="optional($program)?->id"
                redirect-route="programs.show"
                :autoRedirect="true" />
        </x-card-section>

        <x-card-section title="University Mission" icon="bx-flag">
            <p class="text-[13px] text-slate-500 leading-relaxed">
                CLSU shall develop globally competitive, work-ready, socially-responsible and empowered human resources
                who value life-long learning; and to generate, disseminate, and apply knowledge and technologies for
                poverty alleviation, environmental protection, and sustainable development.
            </p>
        </x-card-section>

        @if (!$program)
            <x-empty-state
                icon="bx-network-chart"
                title="No program selected"
                description="Choose a college, department, and program above to manage its PEOs and POs."
                class="mt-8"
            />
        @else

            <div class="mt-6 border-t border-slate-200 pt-6">

                <div class="flex items-center gap-2 mb-4">
                    <i class="bx bx-network-chart text-emerald-600 text-lg"></i>
                    <h2 class="text-[15px] font-bold text-slate-800">{{ $program->name }}</h2>
                </div>

                <x-navigation.tabs-modern
                    :tabs="[
                        ['id' => 'peo', 'label' => 'PEOs', 'icon' => 'bx-graduation'],
                        ['id' => 'po',  'label' => 'POs & Mapping', 'icon' => 'bx-target-lock'],
                    ]"
                    defaultTab="peo"
                    stateKey="programs-{{ $program->id }}-tabs">

                    {{-- ── PEO Tab ─────────────────────────────────────────────── --}}
                    <x-slot name="slot_peo">
                        <p class="text-[13px] text-slate-500 mb-4">
                            Three to five years after graduation, graduates of
                            <strong class="text-slate-700">{{ $program->name }}</strong> are expected to be:
                        </p>
                        <livewire:programs.manage-peos :program="$program" />
                    </x-slot>

                    {{-- ── PO + Mapping Tab ───────────────────────────────────── --}}
                    <x-slot name="slot_po">
                        <p class="text-[13px] text-slate-500 mb-4">
                            By the time of graduation, students of
                            <strong class="text-slate-700">{{ $program->name }}</strong> have the ability to:
                        </p>
                        <livewire:programs.manage-pos :program="$program" />
                    </x-slot>

                </x-navigation.tabs-modern>
            </div>

        @endif

    </x-panel>

@endsection
