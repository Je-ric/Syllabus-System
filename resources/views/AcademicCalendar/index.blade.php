@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Academic Calendars</h1>

        <x-button href="{{ route('academic.calendars.create') }}" variant="add-button">
            <i class="bx bx-plus"></i>Create New Calendar
        </x-button>

        @if ($calendars->isEmpty())
            <p>No academic calendars yet.</p>
        @else
            <div class="space-y-4">
                @foreach ($calendars->groupBy('academic_year') as $year => $semesters)
                    @php
                        $totalEvents = $semesters->flatMap->events->count();
                        $hasEvents = $totalEvents > 0;
                    @endphp
                    <div class="border p-4 rounded">
                        <h2 class="font-semibold">Academic Year: {{ $year }}</h2>
                        <ul class="mt-2">
                            @foreach ($semesters as $sem)
                                <li>
                                    {{ $sem->semester }} Semester:
                                    {{ \Carbon\Carbon::parse($sem->start_date)->format('F j, Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($sem->end_date)->format('F j, Y') }}
                                </li>
                            @endforeach
                        </ul>

                        @if($hasEvents)
                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded text-sm text-blue-700">
                                <i class="bx bx-info-circle"></i>
                                This academic year has <strong>{{ $totalEvents }} event(s)</strong>. Edit and delete are disabled while events exist. Delete the events from the Manage Events page if you need to modify this calendar.
                            </div>
                        @endif

                        <div class="mt-3 space-x-2">
                            <x-button href="{{ route('academic.calendar.events.index', $sem->academic_year) }}"
                                variant="table-manage">Manage Events</x-button>

                            @if($hasEvents)
                                <x-button variant="table-edit" disabled class="opacity-50 cursor-not-allowed">
                                    <i class="bx bx-edit"></i> Edit
                                </x-button>
                            @else
                                <x-button href="{{ route('academic.calendars.edit', $year) }}"
                                    variant="table-edit">
                                    <i class="bx bx-edit"></i> Edit
                                </x-button>
                            @endif

                            @if($hasEvents)
                                <x-button type="button" variant="table-danger" disabled class="opacity-50 cursor-not-allowed">
                                    <i class="bx bx-trash"></i> Delete
                                </x-button>
                            @else
                                <x-button
                                    type="button"
                                    variant="table-danger"
                                    onclick="document.getElementById('deleteAYModal_{{ str_replace('-', '_', $year) }}').showModal()">
                                    <i class="bx bx-trash"></i> Delete
                                </x-button>
                            @endif
                        </div>

                        @if(!$hasEvents)
                            @include('AcademicCalendar.modals.deleteAYModal', ['year' => $year, 'semesters' => $semesters])
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
