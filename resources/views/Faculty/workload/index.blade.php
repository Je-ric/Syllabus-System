@extends('layouts.app')

@section('content')

    {{-- Page header --}}
    <x-layout.page-header
        title="My Workload"
        description="Your teaching assignments synced from CAIS.">
        <x-ui.button variant="primary" onclick="document.getElementById('syncModal').showModal()">
            <i class="bx bx-sync leading-none"></i> Sync from CAIS
        </x-ui.button>
    </x-layout.page-header>

    <x-layout.panel>
    {{-- Last synced notice --}}
    @if($loads->isNotEmpty())
        @php $lastSync = $loads->max('synced_at'); @endphp
        <p class="text-[12px] text-[#a1a1aa]">
            <i class="bx bx-check-circle text-emerald-500"></i>
            Last synced {{ $lastSync?->diffForHumans() }}
        </p>
    @endif

    {{-- Teaching loads table --}}
    <div class="rounded-sm border border-[#ececee] bg-white overflow-hidden"
        style="box-shadow: rgba(0,0,0,0.04) 0px 4px 12px 0px;">

        {{-- Column headers --}}
        <div class="grid grid-cols-[1fr_1fr_auto_auto] gap-x-3 items-center
                    px-5 py-3 bg-[#fafafa] border-b border-[#ececee]
                    text-[11px] font-bold uppercase tracking-[0.14em] text-[#a1a1aa] select-none">
            <div>Subject</div>
            <div>Schedule / Room</div>
            <div>Semester</div>
            <div>Type</div>
        </div>

        <div class="divide-y divide-[#f4f4f5]">
            @forelse($loads as $load)
                @php
                    $sched    = $load->classSchedule;
                    $semester = $load->caisSemester ?? $sched?->caisSemester;
                @endphp
                <div class="grid grid-cols-[1fr_1fr_auto_auto] gap-x-3 items-center px-5 py-3.5 hover:bg-[#fafafa] transition-colors">

                    {{-- Subject --}}
                    <div>
                        <p class="text-[13px] font-semibold text-[#09090b]">
                            {{ $sched?->subject_code ?? '—' }}
                        </p>
                        <p class="text-[12px] text-[#71717a] truncate">
                            {{ $sched?->subject_title ?? '—' }}
                        </p>
                    </div>

                    {{-- Schedule / Room --}}
                    <div>
                        <p class="text-[13px] text-[#09090b]">{{ $sched?->time ?? '—' }}</p>
                        <p class="text-[12px] text-[#71717a]">
                            {{ $sched?->room ?? '' }}
                            @if($sched?->section) · Sec {{ $sched->section }} @endif
                        </p>
                    </div>

                    {{-- Semester --}}
                    <div class="text-[12px] text-[#52525b] whitespace-nowrap">
                        {{ $semester?->name ?? '—' }}
                        @if($semester?->year)
                            <span class="text-[#a1a1aa]">({{ $semester->year }})</span>
                        @endif
                    </div>

                    {{-- Class type badge --}}
                    <div>
                        @if($sched?->class_type)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                                {{ strtoupper($sched->class_type) === 'LAB'
                                    ? 'bg-blue-50 text-blue-700'
                                    : 'bg-emerald-50 text-emerald-700' }}">
                                {{ strtoupper($sched->class_type) }}
                            </span>
                        @else
                            <span class="text-[12px] text-[#a1a1aa]">—</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-5 py-12">
                    <x-feedback-status.empty-state
                        icon="bx-book-open"
                        title="No workload synced yet"
                        message="Click 'Sync from CAIS' and enter your CAIS credentials to load your teaching assignments." />
                </div>
            @endforelse
        </div>
    </div>
</x-layout.panel>


{{-- ── CAIS Credentials Modal ──────────────────────────────────────────── --}}
<x-modal.dialog id="syncModal"
    class="rounded-2xl border border-[#e4e4e7] p-0 w-full max-w-md backdrop:bg-[#09090b]/50 backdrop:backdrop-blur-[3px]"
    style="box-shadow: 0 8px 40px rgba(0,0,0,0.14);">

    <form method="POST" action="{{ route('workload.sync') }}" id="syncForm">
        @csrf

        <x-modal.header modalId="syncModal">
            <x-slot name="icon"><i class="bx bx-sync text-emerald-600 text-xl"></i></x-slot>
            Sync Workload from CAIS
        </x-modal.header>

        <x-modal.body>
            <p class="text-[13px] text-[#52525b] mb-4">
                Enter your <strong>CAIS credentials</strong> to fetch your current teaching assignments.
                These are the same credentials you use to log in to the CLSU CAIS system.
            </p>

            <div class="space-y-4">
                <x-form.field label="Employee ID" for="cais_employee_id" required>
                    <x-form.input
                        id="cais_employee_id"
                        name="cais_employee_id"
                        type="text"
                        placeholder="e.g. 2024-00123"
                        value="{{ old('cais_employee_id', auth()->user()->cais_employee_id) }}"
                        autocomplete="username"
                        required />
                </x-form.field>

                <x-form.field label="CAIS Password" for="cais_password" required>
                    <div class="relative" x-data="{ show: false }">
                        <x-form.input
                            id="cais_password"
                            name="cais_password"
                            type="password"
                            x-bind:type="show ? 'text' : 'password'"
                            placeholder="Your CAIS password"
                            autocomplete="current-password"
                            required />
                        <button type="button"
                            @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#a1a1aa] hover:text-[#52525b] transition-colors">
                            <i class="bx text-base" :class="show ? 'bx-hide' : 'bx-show'"></i>
                        </button>
                    </div>
                </x-form.field>
            </div>

            <p class="mt-4 text-[11px] text-[#a1a1aa]">
                <i class="bx bx-lock-alt"></i>
                Your credentials are sent directly to CAIS and are never stored by this system.
            </p>
        </x-modal.body>

        <x-modal.footer>
            <x-ui.button type="submit" variant="primary" id="syncSubmitBtn">
                <i class="bx bx-sync leading-none"></i> Sync Now
            </x-ui.button>
        </x-modal.footer>

    </form>
</x-modal.dialog>

@push('scripts')
<script>
    // Auto-open the modal if the sync failed and we need to retry
    @if(session('open_sync_modal'))
        document.getElementById('syncModal')?.showModal();
    @endif

    // Disable submit button while form is submitting to prevent double-submit
    document.getElementById('syncForm')?.addEventListener('submit', function () {
        const btn = document.getElementById('syncSubmitBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bx bx-loader-alt bx-spin leading-none"></i> Syncing…';
        }
    });
</script>
@endpush

@endsection