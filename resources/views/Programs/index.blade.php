@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-sitemap"
        title="Program Management"
        desc="Manage Program Educational Objectives (PEOs) and Program Outcomes (POs)."
    />

    <x-panel>
        {{-- Program selector — same card style as courses.index and syllabus.create --}}
        <x-card-section
            title="Select Program"
            icon="bx-network-chart"
            description="Choose a program to manage its PEOs and POs."
            class="mb-6">

            <livewire:programs.program-selector
                :program-id="optional($program)?->id"
                redirect-route="programs.show"
                :autoRedirect="true" />

        </x-card-section>

        {{-- University mission --}}
        <x-card-section
            title="University Missions"
            icon="bx-flag">

            <p class="text-[13px] text-[#475569] leading-relaxed">
                CLSU shall develop globally competitive, work-ready, socially-responsible
                and empowered human resources who value life-long learning; and to generate,
                disseminate, and apply knowledge and technologies for poverty alleviation,
                environmental protection, and sustainable development.
            </p>

        </x-card-section>

        {{-- ── Program selected ─────────────────────────────────────────────── --}}
        @if ($program)

            <div class="mt-6 border-t border-slate-200 pt-6">

                {{-- Program name breadcrumb --}}
                <div class="flex items-center gap-2 mb-4">
                    <i class="bx bx-network-chart text-[#16a34a] text-lg"></i>
                    <h2 class="text-[15px] font-bold text-[#0f172a]">
                        {{ $program->name }}
                    </h2>
                </div>

                <x-navigation.tabs-modern
                    :tabs="[
                        ['id' => 'peo', 'label' => 'Program Educational Objectives (PEOs)', 'icon' => 'bx-graduation'],
                        ['id' => 'po',  'label' => 'Program Outcomes (POs)',                'icon' => 'bx-target'],
                    ]"
                    defaultTab="peo"
                    stateKey="programs-{{ $program->id }}-tabs">

                    {{-- ── PEO Tab ────────────────────────────────────────────────── --}}
                    <x-slot name="slot_peo">
                        <x-wizard.section
                            title="Program Educational Objectives (PEOs)"
                            icon="list-check"
                            color="emerald">
                            <div class="mb-4">
                                <p class="mx-1 text-[13px] text-[#475569]">
                                    Three to five years after graduation, graduates of
                                    <strong>{{ $program->name }}</strong> are expected to be:
                                </p>
                            </div>
                            <livewire:programs.manage-peos :program="$program" />
                        </x-wizard.section>
                    </x-slot>

                    {{-- ── PO Tab ─────────────────────────────────────────────────── --}}
                    <x-slot name="slot_po">
                        <x-wizard.section
                            title="Program Outcomes (POs)"
                            icon="target-lock"
                            color="blue">
                            <div class="mb-4">
                                <p class="mt-0.5 text-[13px] text-[#475569]">
                                    By the time of graduation, students of
                                    <strong>{{ $program->name }}</strong> have the ability to:
                                </p>
                            </div>
                            <livewire:programs.manage-pos :program="$program" />
                        </x-wizard.section>
                    </x-slot>

                </x-navigation.tabs-modern>
            </div>

        @else
            <x-empty-state
                icon="bx-network-chart"
                title="No program selected"
                description="Choose a college, department, and program above to manage its PEOs and POs."
                class="mt-8"
            />
        @endif
    </x-panel>

@endsection
