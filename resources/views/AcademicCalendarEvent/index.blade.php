@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 text-slate-800">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Academic Records</p>
            <h1 class="text-2xl font-semibold">Manage Events - {{ $academicYear }}</h1>
        </div>

        <x-button href="{{ route('academic.calendars.index') }}" variant="cancel">
            <i class="bx bx-arrow-back"></i> Back
        </x-button>
    </div>

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

                {{-- Form --}}
                <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl space-y-4 shadow-sm">
                    <h2 class="font-semibold text-slate-800">Add Event</h2>

                    <form action="{{ route('academic.calendar.events.store', $semester) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Type</label>
                                <select name="type" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                                    <option value="holiday">Holiday</option>
                                    <option value="exam">Exam</option>
                                    <option value="break">Break</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Date</label>
                                <input type="date" name="date" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200"
                                        min="{{ $semester->start_date }}" max="{{ $semester->end_date }}">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Name</label>
                            <input type="text" name="name" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                        </div>

                        <button type="submit" class="mt-2 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                            <i class="bx bx-plus"></i> Add Event
                        </button>
                    </form>
                </div>

                {{-- Events lists --}}
                <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl shadow-sm">
                    <h2 class="font-semibold text-slate-800 mb-2">Events for {{ $semester->semester }} Semester</h2>
                    <p class="text-slate-500 text-sm mb-3">
                        Semester Range:
                        {{ \Carbon\Carbon::parse($semester->start_date)->format('F j, Y') }}
                        -
                        {{ \Carbon\Carbon::parse($semester->end_date)->format('F j, Y') }}
                    </p>

                    <table class="w-full border-collapse border border-slate-200 text-sm">
                        <thead>
                            <tr class="bg-emerald-50 text-emerald-800">
                                <th class="border border-slate-200 px-3 py-2 text-left text-xs uppercase tracking-[0.2em]">Date</th>
                                <th class="border border-slate-200 px-3 py-2 text-left text-xs uppercase tracking-[0.2em]">Type</th>
                                <th class="border border-slate-200 px-3 py-2 text-left text-xs uppercase tracking-[0.2em]">Name</th>
                                <th class="border border-slate-200 px-3 py-2 text-left text-xs uppercase tracking-[0.2em]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($semester->events->sortBy('date') as $event)
                                <tr class="odd:bg-white even:bg-slate-50 hover:bg-emerald-50/60 transition">
                                    <td class="border border-slate-200 px-3 py-2">{{ \Carbon\Carbon::parse($event->date)->format('F j, Y') }}</td>
                                    <td class="border border-slate-200 px-3 py-2">{{ ucfirst($event->type) }}</td>
                                    <td class="border border-slate-200 px-3 py-2">{{ $event->name }}</td>

                                    <td class="border border-slate-200 px-3 py-2">
                                        <div class="flex gap-2">
                                        <button
                                            type="button"
                                            onclick="document.getElementById('updateEventModal_{{ $event->id }}').showModal()"
                                            class="text-emerald-700 hover:text-emerald-900 cursor-pointer">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <button
                                            type="button"
                                            onclick="document.getElementById('deleteEventModal_{{ $event->id }}').showModal()"
                                            class="text-rose-600 hover:text-rose-800 cursor-pointer">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        </div>
                                    </td>
                                </tr>
                                @include('AcademicCalendarEvent.modals.updateEventModal', ['event' => $event])
                                @include('AcademicCalendarEvent.modals.deleteEventModal', ['event' => $event])
                            @empty
                                <tr>
                                    <td class="border border-slate-200 px-3 py-2 text-center text-slate-500" colspan="4">No events yet.</td>
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
