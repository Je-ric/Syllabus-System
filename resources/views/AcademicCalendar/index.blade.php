@extends('layouts.app')

@section('content')

        <x-header-with-button title="Academic Calendars"
                        description="Academic Year and Semester Dates">
            <x-button variant="add-button"
                    href="{{ route('academic.calendars.create') }}">
                    <i class="bx bx-plus"></i>Create Academic Calendar
            </x-button>
        </x-header-with-button>

        @if ($calendars->isEmpty())
            <p>No academic calendars yet.</p>
        @else
            <div class="space-y-4">
                @foreach ($calendars->groupBy('academic_year') as $year => $semesters)
                    @php
                        $totalEvents = $semesters->flatMap->events->count();
                        $hasEvents = $totalEvents > 0;
                    @endphp
                    <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-lg font-semibold text-slate-800">Academic Year: {{ $year }}</h2>
                            <span class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $semesters->count() }} semester(s)</span>
                        </div>
                        <ul class="mt-3 space-y-1 text-sm text-slate-700">
                            @foreach ($semesters as $sem)
                                <li>
                                    <span class="font-semibold text-emerald-700">{{ $sem->semester }} Semester:</span>
                                    {{ \Carbon\Carbon::parse($sem->start_date)->format('F j, Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($sem->end_date)->format('F j, Y') }}
                                </li>
                            @endforeach
                        </ul>

                        @if($hasEvents)
                            <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                                <i class="bx bx-info-circle"></i>
                                This academic year has <strong>{{ $totalEvents }} event(s)</strong>. Edit and delete are disabled while events exist. Delete the events from the Manage Events page if you need to modify this calendar.
                            </div>
                        @endif

                        <div class="mt-4 flex flex-wrap gap-2">
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
@endsection
