@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-calendar"
        title="{{ isset($isEdit) ? 'Edit Academic Calendar' : 'Create Academic Calendar' }}"
        desc="Academic Year and Semester Dates">
        <x-ui.button variant="cancel" href="{{ route('academic.calendars.index') }}">
            <i class="bx bx-arrow-back"></i> Back
        </x-ui.button>
    </x-page-header>

    <x-panel>
        @if(isset($hasEvents) && $hasEvents)
            <x-feedback-status.alert
                type="warning"
                title="Note"
                message="This academic calendar has events. You can only edit the dates. To delete, remove all events first."
                class="mb-4" />
        @endif

        {{--
            Livewire handles all validation in real time.
            No precognition, no $form(), no <form> POST here.
            Pre-cognition result incompatibility with the syllabus creation.
            Mahaba ipaliwanag.
        --}}
        <livewire:academic-calendar.academic-calendar-form
            :isEdit="isset($isEdit) && $isEdit"
            :academicYear="$academicYear ?? ''"
            :originalValues="$originalValues ?? []" />
    </x-panel>

@endsection
