@extends('layouts.app')

@section('content')

    <x-layout.page-header
        icon="bx-calendar-event"
        title="Manage Events — {{ $academicYear }}"
        desc="Add, edit, and delete events for each semester.">
        <x-ui.help-trigger />
        <x-ui.button href="{{ route('academic.calendars.index') }}" variant="cancel">
            <i class="bx bx-arrow-back"></i> Back
        </x-ui.button>
    </x-layout.page-header>

    <x-layout.help-panel module="academic-calendar" />

    <x-layout.panel>
        <x-feedback-status.alert type="info" :showTitle="false" class="mb-4">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-semibold">Event Types:</span>
                </div>
                <div class="space-y-1.5 text-[13px]">
                    <div class="flex items-center gap-2">
                        <i class="bx bx-info-circle text-blue-600"></i>
                        <span><strong>Holiday/Other:</strong> Reference only - week created, editable by faculty</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="bx bx-skip-next text-amber-600"></i>
                        <span><strong>Break:</strong> Week SKIPPED entirely - no syllabus week created</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="bx bx-lock text-red-600"></i>
                        <span><strong>Exam/Non-Teaching:</strong> Week LOCKED - faculty cannot edit</span>
                    </div>
                </div>
                <p class="text-[12px] text-[#71717a] mt-2">
                    Use "Break" for Christmas/semester breaks, "Holiday" for suspensions, and "Exam" for exam periods.
                </p>
            </div>
        </x-feedback-status.alert>
        

        @php
            $tabs = $semesters->values()->map(fn($s) => [
                'id'    => 'sem_' . $s->id,
                'label' => $s->semester . ' Semester',
            ])->toArray();
        @endphp

        <x-navigation.tabs-modern
            :tabs="$tabs"
            :defaultTab="$tabs[0]['id'] ?? null"
            :stateKey="'academic-calendar-events:' . $academicYear">

            @foreach($semesters as $semester)
                <x-slot :name="'slot_sem_' . $semester->id">
                    {{--
                        One Livewire component per semester.
                        It owns the add-form, the events list, and the edit modal.
                        Real-time validation runs on wire:model.blur for text inputs
                        and wire:model.live for selects.
                    --}}
                    <livewire:academic-calendar.academic-calendar-event-form
                        :semesterId="$semester->id"
                        :academicYear="$academicYear"
                        :key="'event-form-' . $semester->id" />
                </x-slot>
            @endforeach
        </x-navigation.tabs-modern>
    </x-layout.panel>

@endsection
