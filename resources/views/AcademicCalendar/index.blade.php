@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Academic Calendars</h1>

    <x-button href="{{ route('academic.calendars.create') }}" variant="add-button">
        <i class="bx bx-plus"></i>Create New Calendar
    </x-button>

    @if($calendars->isEmpty())
        <p>No academic calendars yet.</p>
    @else
        <div class="space-y-4">
            @foreach($calendars->groupBy('academic_year') as $year => $semesters)
                <div class="border p-4 rounded">
                    <h2 class="font-semibold">Academic Year: {{ $year }}</h2>
                    <ul class="mt-2">
                        @foreach($semesters as $sem)
                            <li>
                                {{ $sem->semester }} Semester:
                                {{ $sem->start_date }} - {{ $sem->end_date }}
                                <a href="{{ route('academic.calendar.events.index', $sem->academic_year) }}"
                                    class="text-blue-500 underline ml-2">Manage Events</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
