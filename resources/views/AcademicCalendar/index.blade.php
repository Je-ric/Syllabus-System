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
            <div class="rounded-2xl border border-slate-200 bg-white/80 p-8 text-center text-slate-500">
                No academic calendars yet.
            </div>
        @else
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                @foreach ($calendars->groupBy('academic_year') as $year => $semesters)
                    @php
                        $totalEvents = $semesters->flatMap->events->count();
                        $hasEvents = $totalEvents > 0;
                        $firstSem = $semesters->firstWhere('semester', '1st');
                        $secondSem = $semesters->firstWhere('semester', '2nd');
                    @endphp
                    <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl shadow-sm flex flex-col">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-lg font-semibold text-slate-800">Academic Year: {{ $year }}</h2>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">
                                {{ $semesters->count() }} semester(s)
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500 mb-1">1st Semester</p>
                                <p class="font-semibold text-slate-700">
                                    {{ $firstSem ? \Carbon\Carbon::parse($firstSem->start_date)->format('M d, Y') : '-' }}
                                    -
                                    {{ $firstSem ? \Carbon\Carbon::parse($firstSem->end_date)->format('M d, Y') : '-' }}
                                </p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500 mb-1">2nd Semester</p>
                                <p class="font-semibold text-slate-700">
                                    {{ $secondSem ? \Carbon\Carbon::parse($secondSem->start_date)->format('M d, Y') : '-' }}
                                    -
                                    {{ $secondSem ? \Carbon\Carbon::parse($secondSem->end_date)->format('M d, Y') : '-' }}
                                </p>
                            </div>
                        </div>

                        @if($hasEvents)
                            <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                                <i class="bx bx-info-circle"></i>
                                This academic year has <strong>{{ $totalEvents }} event(s)</strong>. Edit and delete are disabled while events exist. Delete the events from the Manage Events page if you need to modify this calendar.
                            </div>
                        @endif

                        <div class="mt-4 flex items-center gap-2">
                            <x-button href="{{ route('academic.calendar.events.index', $year) }}"
                                variant="table-manage">Manage Events</x-button>

                            @if($hasEvents)
                                <x-button variant="table-edit" disabled class="opacity-50 cursor-not-allowed min-w-10 px-3" title="Update">
                                    <i class="bx bx-edit text-lg"></i>
                                </x-button>
                            @else
                                <x-button href="{{ route('academic.calendars.edit', $year) }}"
                                    variant="table-edit"
                                    class="min-w-10 px-3"
                                    title="Update">
                                    <i class="bx bx-edit text-lg"></i>
                                </x-button>
                            @endif

                            @if($hasEvents)
                                <x-button type="button" variant="table-danger" disabled class="opacity-50 cursor-not-allowed min-w-10 px-3" title="Delete">
                                    <i class="bx bx-trash text-lg"></i>
                                </x-button>
                            @else
                                <x-button
                                    type="button"
                                    variant="table-danger"
                                    class="min-w-10 px-3"
                                    title="Delete"
                                    onclick="document.getElementById('deleteAYModal_{{ str_replace('-', '_', $year) }}').showModal()">
                                    <i class="bx bx-trash text-lg"></i>
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
