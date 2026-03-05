@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-sitemap"
        title="Program Management"
        desc="Manage Program Educational Objectives (PEOs) and Program Outcomes (POs)."
    />

    <x-panel>
        {{-- Program selector — same card style as courses.index and syllabus.create --}}
        <div class="border border-slate-200/80 rounded-2xl p-5 mb-6 bg-white/90 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 mb-3">
                Select Program
            </p>
            <livewire:programs.program-selector
                :program-id="optional($program)?->id"
                redirect-route="programs.show"
                :autoRedirect="true" />
        </div>
    
        {{-- University mission --}}
        <div class="mt-5 rounded-2xl border border-slate-300 bg-slate-50 px-5 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 mb-1">
                University Mission
            </p>
            <p class="text-sm text-slate-600 leading-relaxed">
                CLSU shall develop globally competitive, work-ready, socially-responsible
                and empowered human resources who value life-long learning; and to generate,
                disseminate, and apply knowledge and technologies for poverty alleviation,
                environmental protection, and sustainable development.
            </p>
        </div>
    
        {{-- ── Program selected ─────────────────────────────────────────────── --}}
        @if ($program)
    
            <div class="mt-6 border-t border-slate-200 pt-6">
    
                {{-- Program name breadcrumb --}}
                <div class="flex items-center gap-2 mb-4">
                    <i class="bx bx-network-chart text-emerald-500 text-lg"></i>
                    <h2 class="text-base font-semibold text-slate-800">
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
                                <p class="mx-1 text-xs text-slate-500">
                                    Three to five years after graduation, graduates of
                                    <strong>{{ $program->name }}</strong> are expected to be:
                                </p>
                            </div>
                            <livewire:programs.manage-peos :program="$program" />
                        </x-wizard.section>
                    </x-slot>
    
                    {{-- ── PO Tab ─────────────────────────────────────────────────── --}}
                    <x-slot name="slot_po">
    
                        {{-- Reference panel: read-only PEOs shown at the top of the PO tab --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                            <livewire:programs.peo-display :program="$program" />
                        </div>
    
                        {{-- Editable PO section below the reference --}}
                            <x-wizard.section
                                title="Program Outcomes (POs)"
                                icon="target-lock"
                                color="blue">
                                <div class="mb-4">
                                    <p class="mt-0.5 text-xs text-slate-500">
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
