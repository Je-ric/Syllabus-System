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

                {{-- Program header --}}
                <div class="flex items-center gap-3 mb-5 px-1">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-100 ring-1 ring-emerald-200 shrink-0">
                        <i class="bx bx-network-chart text-emerald-700 text-lg"></i>
                    </span>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-emerald-600">Selected Program</p>
                        <h2 class="text-[15px] font-bold text-slate-800 leading-tight">{{ $program->name }}</h2>
                    </div>
                </div>

                <x-navigation.tabs-modern
                    :tabs="[
                        ['id' => 'peo',    'label' => 'Program Educational Objectives (PEOs)', 'icon' => 'bx-medal'],
                        ['id' => 'po',     'label' => 'Program Outcomes (POs)', 'icon' => 'bx-target-lock'],
                        ['id' => 'matrix', 'label' => 'Matrix View',   'icon' => 'bx-grid-alt'],
                    ]"
                    defaultTab="peo"
                    stateKey="programs-{{ $program->id }}-tabs">

                    {{-- ── PEO Tab ─────────────────────────────────────────────── --}}
                    <x-slot name="slot_peo">
                        <div class="flex items-start gap-3 mb-5 p-4 rounded-xl bg-emerald-50/60 border border-emerald-100">
                            <i class="bx bx-info-circle text-emerald-500 text-base mt-0.5 shrink-0"></i>
                            <p class="text-[13px] text-emerald-800 leading-relaxed">
                                Three to five years after graduation, graduates of
                                <strong>{{ $program->name }}</strong> are expected to be:
                            </p>
                        </div>
                        <livewire:programs.manage-peos :program="$program" />
                    </x-slot>

                    {{-- ── PO + Mapping Tab ───────────────────────────────────── --}}
                    <x-slot name="slot_po">
                        <div class="flex items-start gap-3 mb-5 p-4 rounded-xl bg-blue-50/60 border border-blue-100">
                            <i class="bx bx-info-circle text-blue-500 text-base mt-0.5 shrink-0"></i>
                            <p class="text-[13px] text-blue-800 leading-relaxed">
                                By the time of graduation, students of
                                <strong>{{ $program->name }}</strong> have the ability to:
                            </p>
                        </div>
                        <livewire:programs.manage-pos :program="$program" />
                    </x-slot>

                    {{-- ── Matrix View Tab ────────────────────────────────────── --}}
                    <x-slot name="slot_matrix">
                        <div class="flex items-start gap-3 mb-5 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <i class="bx bx-info-circle text-slate-400 text-base mt-0.5 shrink-0"></i>
                            <p class="text-[13px] text-slate-600 leading-relaxed">
                                Read-only mapping matrix of Program Outcomes (POs) against Program Educational Objectives (PEOs)
                                for <strong>{{ $program->name }}</strong>. Manage mappings in the POs & Mapping tab.
                            </p>
                        </div>
                        <livewire:programs.matrix-view :program="$program" />
                    </x-slot>

                </x-navigation.tabs-modern>
            </div>

        @endif

    </x-panel>

@endsection
