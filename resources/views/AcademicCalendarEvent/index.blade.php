@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-calendar-event"
        title="Manage Events — {{ $academicYear }}"
        desc="Add, edit, and delete events for each semester.">
        <x-button href="{{ route('academic.calendars.index') }}" variant="cancel">
            <i class="bx bx-arrow-back"></i> Back
        </x-button>
    </x-page-header>

    <x-panel>
        <x-feedback-status.alert type="info" :showTitle="false" class="mb-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-semibold">Note:</span>
                <span>If event type is</span>
                <x-feedback-status.status-indicator variant="amber" size="sm" :dot="true">exam</x-feedback-status.status-indicator>
                <span>or</span>
                <x-feedback-status.status-indicator variant="rose" size="sm" :dot="true">non_teaching</x-feedback-status.status-indicator>
                <span>then Weekly Coverage will lock that week automatically.</span>
            </div>
            It is advisable to add only the dates that are relevant for week referencing, such as breaks, non-teaching days, and holidays. 
            These entries help generate the academic weeks accurately and allow faculty members to adjust their weekly lesson coverage accordingly.
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
    </x-panel>

@endsection