@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Manage Events - {{ $academicYear }}</h1>
    <a href="{{ route('academic.calendars.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">Back to Academic Calendars</a>

    @include('includes.session-success')

    @php
        $tabs = $semesters->map(fn($s) => [
            'id' => $s->semester,
            'label' => $s->semester . ' Semester'
        ])->toArray();
    @endphp

    <x-navigation.tabs-modern :tabs="$tabs" :defaultTab="$tabs[0]['id'] ?? null">
        @foreach($semesters as $semester)
            @slot('slot_' . $semester->semester)
            <div class="grid grid-cols-2 gap-6">

                {{-- Add Event Form --}}
                <div class="border p-4 rounded space-y-4">
                    <h2 class="font-semibold mb-2">Add Event</h2>
                    <p class="text-gray-500 text-sm mb-2">
                        Semester Range:
                        {{ \Carbon\Carbon::parse($semester->start_date)->format('F j, Y') }}
                        -
                        {{ \Carbon\Carbon::parse($semester->end_date)->format('F j, Y') }}
                    </p>

                    <form action="{{ route('academic.calendar.events.store', $semester) }}" method="POST">
                        @csrf
                        <div>
                            <label>Type</label>
                            <select name="type" class="border rounded px-2 py-1 w-full">
                                <option value="holiday">Holiday</option>
                                <option value="exam">Exam</option>
                                <option value="break">Break</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label>Name</label>
                            <input type="text" name="name" class="border rounded px-2 py-1 w-full">
                        </div>
                        <div>
                            <label>Date</label>
                            <input type="date" name="date" class="border rounded px-2 py-1 w-full"
                                   min="{{ $semester->start_date }}" max="{{ $semester->end_date }}">
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded mt-2">Add Event</button>
                    </form>
                </div>

                {{-- Events Table --}}
                <div class="border p-4 rounded">
                    <h2 class="font-semibold mb-2">Events for {{ $semester->semester }} Semester</h2>
                    <p class="text-gray-500 text-sm mb-2">
                        Semester Range:
                        {{ \Carbon\Carbon::parse($semester->start_date)->format('F j, Y') }}
                        -
                        {{ \Carbon\Carbon::parse($semester->end_date)->format('F j, Y') }}
                    </p>

                    <table class="w-full border-collapse border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-2 py-1">Date</th>
                                <th class="border px-2 py-1">Type</th>
                                <th class="border px-2 py-1">Name</th>
                                <th class="border px-2 py-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($semester->events->sortBy('date') as $event)
                                <tr>
                                    <td class="border px-2 py-1">{{ \Carbon\Carbon::parse($event->date)->format('F j, Y') }}</td>
                                    <td class="border px-2 py-1">{{ ucfirst($event->type) }}</td>
                                    <td class="border px-2 py-1">{{ $event->name }}</td>
                                    <td class="border px-2 py-1"><i class="bx bx-trash text-red-600 hover:text-red-800"></i></td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="border px-2 py-1 text-center" colspan="4">No events yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
            @endslot
        @endforeach
    </x-navigation.tabs-modern>
</div>
@endsection
