@extends('layouts.app')

@section('content')

    <x-header-with-button title="Manage Events - {{ $academicYear }}"
                    description="Add, edit, and delete events for each semester.">
        <x-button href="{{ route('academic.calendars.index') }}" variant="cancel">
            <i class="bx bx-arrow-back"></i> Back
        </x-button>
    </x-header-with-button>
    <livewire:academic-calendar.manage-events :academicYear="$academicYear" />
@endsection
