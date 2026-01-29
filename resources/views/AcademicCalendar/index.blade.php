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
                        <x-button href="{{ route('academic.calendar.events.index', $sem->academic_year) }}"
                            variant="table-manage">Manage Events</x-button>

                        <x-button href="{{ route('academic.calendars.edit', $year) }}"
                            variant="table-edit">
                            <i class="bx bx-edit"></i> Edit A.Y. {{ $year }}
                        </x-button>

                        <form action="{{ route('academic.calendars.destroy', $year) }}"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this academic year and all its semesters?');">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="table-danger">
                                <i class="bx bx-trash"></i> Delete A.Y. {{ $year }}
                            </x-button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
