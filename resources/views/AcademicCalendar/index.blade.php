@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-calendar"
        title="Academic Calendars"
        desc="Academic Year and Semester Dates">
        <x-button variant="add-button" href="{{ route('academic.calendars.create') }}">
            <i class="bx bx-plus"></i> Create Academic Calendar
        </x-button>
    </x-page-header>

    <x-panel>
        @if ($calendars->isEmpty())
            <x-empty-state
                icon="bx-calendar"
                title="No Academic Calendars"
                message="Create academic calendars to manage semester dates and events." />
        @else
            <x-feedback-status.alert type="info" :showTitle="false" class="mb-4">
                <div class="text-sm leading-relaxed">
                    <span class="font-semibold">Remember:</span>
                    Use <x-feedback-status.status-indicator variant="slate" size="sm">Manage Events</x-feedback-status.status-indicator>
                    to add or remove holidays, exams, breaks, and other dates.
                    Academic years with existing events cannot be edited or deleted until those events are removed.
                </div>
            </x-feedback-status.alert>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                @foreach ($calendars->groupBy('academic_year') as $year => $semesters)
                    @php
                        $totalEvents = $semesters->flatMap->events->count();
                        $hasEvents   = $totalEvents > 0;
                        $firstSem    = $semesters->firstWhere('semester', '1st');
                        $secondSem   = $semesters->firstWhere('semester', '2nd');
                    @endphp

                    <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl shadow-sm flex flex-col">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-lg font-semibold text-slate-800">A.Y. {{ $year }}</h2>
                            <x-feedback-status.status-indicator variant="emerald" size="sm" :dot="true">
                                {{ $semesters->count() }} semester(s)
                            </x-feedback-status.status-indicator>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <x-form.label>1st Semester</x-form.label>
                                <p class="font-semibold text-slate-700">
                                    {{ $firstSem ? \Carbon\Carbon::parse($firstSem->start_date)->format('M d, Y') : '—' }}
                                    –
                                    {{ $firstSem ? \Carbon\Carbon::parse($firstSem->end_date)->format('M d, Y') : '—' }}
                                </p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <x-form.label>2nd Semester</x-form.label>
                                <p class="font-semibold text-slate-700">
                                    {{ $secondSem ? \Carbon\Carbon::parse($secondSem->start_date)->format('M d, Y') : '—' }}
                                    –
                                    {{ $secondSem ? \Carbon\Carbon::parse($secondSem->end_date)->format('M d, Y') : '—' }}
                                </p>
                            </div>
                        </div>

                        @if ($hasEvents)
                            <x-feedback-status.alert type="warning" :showTitle="false" class="mt-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold">Edit / Delete locked</span>
                                    <x-feedback-status.status-indicator variant="amber" size="sm" :dot="true">
                                        {{ $totalEvents }} event{{ $totalEvents !== 1 ? 's' : '' }}
                                    </x-feedback-status.status-indicator>
                                </div>
                            </x-feedback-status.alert>
                        @endif

                        <div class="mt-4 flex items-center gap-2">
                            <x-button href="{{ route('academic.calendar.events.index', $year) }}"
                                variant="table-manage">Manage Events</x-button>

                            @if ($hasEvents)
                                <x-button variant="table-edit" disabled
                                    class="opacity-50 cursor-not-allowed min-w-10 px-3" title="Cannot edit while events exist">
                                    <i class="bx bx-edit text-lg"></i>
                                </x-button>
                                <x-button type="button" variant="table-danger" disabled
                                    class="opacity-50 cursor-not-allowed min-w-10 px-3" title="Cannot delete while events exist">
                                    <i class="bx bx-trash text-lg"></i>
                                </x-button>
                            @else
                                <x-button href="{{ route('academic.calendars.edit', $year) }}"
                                    variant="table-edit" class="min-w-10 px-3" title="Edit">
                                    <i class="bx bx-edit text-lg"></i>
                                </x-button>
                                <x-button type="button" variant="table-danger" class="min-w-10 px-3" title="Delete"
                                    onclick="document.getElementById('deleteAYModal_{{ str_replace('-', '_', $year) }}').showModal()">
                                    <i class="bx bx-trash text-lg"></i>
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