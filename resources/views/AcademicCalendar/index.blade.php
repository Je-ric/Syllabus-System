@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-calendar"
        title="Academic Calendars"
        desc="Manage academic year and semester dates">
        <x-button variant="add-button" href="{{ route('academic.calendars.create') }}">
            <i class="bx bx-plus"></i> Add Academic Year
        </x-button>
    </x-page-header>

    <x-panel>
        @if ($calendars->isEmpty())
            <x-empty-state
                icon="bx-calendar"
                title="No Academic Calendars"
                message="Create an academic calendar to manage semester dates and events." />
        @else
            <div class="mb-4 flex items-start gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                <i class="bx bx-info-circle text-blue-400 text-base shrink-0 mt-0.5"></i>
                <span>
                    Academic years with existing events are <strong>locked</strong> — remove all events first to edit or delete.
                    Use <strong>Manage Events</strong> to add holidays, exams, and breaks.
                </span>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                @foreach ($calendars->groupBy('academic_year') as $year => $semesters)
                    @php
                        $totalEvents = $semesters->flatMap->events->count();
                        $hasEvents   = $totalEvents > 0;
                        $firstSem    = $semesters->firstWhere('semester', '1st');
                        $secondSem   = $semesters->firstWhere('semester', '2nd');
                    @endphp

                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                        {{-- Header --}}
                        <div class="flex items-center justify-between gap-3 px-5 py-4 bg-slate-50 border-b border-slate-200">
                            <div class="flex items-center gap-2.5">
                                <span class="shrink-0 w-9 h-9 rounded-lg bg-emerald-800 flex items-center justify-center">
                                    <i class="bx bx-calendar text-white text-lg leading-none"></i>
                                </span>
                                <div>
                                    <h2 class="text-sm font-bold text-slate-800">A.Y. {{ $year }}</h2>
                                    <p class="text-xs text-slate-500">{{ $semesters->count() }} semester{{ $semesters->count() !== 1 ? 's' : '' }}</p>
                                </div>
                            </div>

                            @if ($hasEvents)
                                <span class="inline-flex items-center gap-1 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-2.5 py-1">
                                    <i class="bx bx-lock-alt text-[11px]"></i>
                                    {{ $totalEvents }} event{{ $totalEvents !== 1 ? 's' : '' }}
                                </span>
                            @endif
                        </div>

                        {{-- Semester dates --}}
                        <div class="grid grid-cols-2 divide-x divide-slate-100 border-b border-slate-100">
                            <div class="px-5 py-3">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400 mb-1">1st Semester</p>
                                <p class="text-sm font-semibold text-slate-700">
                                    {{ $firstSem ? \Carbon\Carbon::parse($firstSem->start_date)->format('M j') : '—' }}
                                    –
                                    {{ $firstSem ? \Carbon\Carbon::parse($firstSem->end_date)->format('M j, Y') : '—' }}
                                </p>
                            </div>
                            <div class="px-5 py-3">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400 mb-1">2nd Semester</p>
                                <p class="text-sm font-semibold text-slate-700">
                                    {{ $secondSem ? \Carbon\Carbon::parse($secondSem->start_date)->format('M j') : '—' }}
                                    –
                                    {{ $secondSem ? \Carbon\Carbon::parse($secondSem->end_date)->format('M j, Y') : '—' }}
                                </p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 px-5 py-3">
                            <x-button href="{{ route('academic.calendar.events.index', $year) }}"
                                variant="table-manage">
                                <i class="bx bx-calendar-event"></i> Manage Events
                            </x-button>

                            @if ($hasEvents)
                                <x-button variant="table-edit" disabled
                                    class="opacity-40 cursor-not-allowed"
                                    title="Remove all events to edit">
                                    <i class="bx bx-edit"></i>
                                </x-button>
                                <x-button variant="table-danger" disabled
                                    class="opacity-40 cursor-not-allowed"
                                    title="Remove all events to delete">
                                    <i class="bx bx-trash"></i>
                                </x-button>
                            @else
                                <x-button href="{{ route('academic.calendars.edit', $year) }}"
                                    variant="table-edit" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </x-button>
                                <x-button type="button" variant="table-danger" title="Delete"
                                    onclick="document.getElementById('deleteAYModal_{{ str_replace('-', '_', $year) }}').showModal()">
                                    <i class="bx bx-trash"></i>
                                </x-button>
                                @include('AcademicCalendar.modals.deleteAYModal', ['year' => $year, 'semesters' => $semesters])
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-panel>

@endsection
