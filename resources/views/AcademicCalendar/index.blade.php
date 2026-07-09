@extends('layouts.app')

@section('content')

    <x-layout.page-header
        icon="bx-calendar"
        title="Academic Calendars"
        desc="Manage academic year and semester dates">
        <x-ui.button variant="add-button" href="{{ route('academic.calendars.create') }}">
            <i class="bx bx-plus"></i> Add Academic Year
        </x-ui.button>
    </x-layout.page-header>

    <x-layout.panel>
        @if ($calendars->isEmpty())
            <x-feedback-status.empty-state
                icon="bx-calendar"
                title="No Academic Calendars"
                message="Create an academic calendar to manage semester dates and events." />
        @else
            <div class="mb-4">
                <x-feedback-status.alert type="info" :showTitle="false">
                    Academic years with existing events are <strong>locked</strong> — remove all events first to edit or delete.
                    Use <strong>Manage Events</strong> to add holidays, exams, and breaks.
                </x-feedback-status.alert>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                @foreach ($calendars->groupBy('academic_year') as $year => $semesters)
                    @php
                        $totalEvents = $semesters->flatMap->events->count();
                        $hasEvents   = $totalEvents > 0;
                        $firstSem    = $semesters->firstWhere('semester', '1st');
                        $secondSem   = $semesters->firstWhere('semester', '2nd');
                    @endphp

                    <div class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

                        {{-- Header --}}
                        <div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#f8fafc] border-b border-[#e2e8f0]">
                            <div class="flex items-center gap-2.5">
                                <span class="shrink-0 w-9 h-9 rounded-lg bg-[#16a34a] flex items-center justify-center">
                                    <i class="bx bx-calendar text-white text-lg leading-none"></i>
                                </span>
                                <div>
                                    <h2 class="text-[15px] font-bold text-[#0f172a]">A.Y. {{ $year }}</h2>
                                    <p class="text-[13px] text-[#475569]">{{ $semesters->count() }} semester{{ $semesters->count() !== 1 ? 's' : '' }}</p>
                                </div>
                            </div>

                            @if ($hasEvents)
                                <x-feedback-status.status-indicator variant="amber" :dot="true">
                                    <i class="bx bx-lock-alt text-[11px]"></i>
                                    {{ $totalEvents }} event{{ $totalEvents !== 1 ? 's' : '' }}
                                </x-feedback-status.status-indicator>
                            @endif
                        </div>

                        {{-- Semester dates --}}
                        <div class="grid grid-cols-2 divide-x divide-[#e2e8f0] border-b border-[#e2e8f0]">
                            <div class="px-5 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8] mb-1">1st Semester</p>
                                <p class="text-[13px] font-semibold text-[#0f172a]">
                                    {{ $firstSem ? \Carbon\Carbon::parse($firstSem->start_date)->format('M j') : '—' }}
                                    –
                                    {{ $firstSem ? \Carbon\Carbon::parse($firstSem->end_date)->format('M j, Y') : '—' }}
                                </p>
                            </div>
                            <div class="px-5 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8] mb-1">2nd Semester</p>
                                <p class="text-[13px] font-semibold text-[#0f172a]">
                                    {{ $secondSem ? \Carbon\Carbon::parse($secondSem->start_date)->format('M j') : '—' }}
                                    –
                                    {{ $secondSem ? \Carbon\Carbon::parse($secondSem->end_date)->format('M j, Y') : '—' }}
                                </p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 px-5 py-3">
                            <x-ui.button href="{{ route('academic.calendar.events.index', $year) }}"
                                variant="table-manage">
                                <i class="bx bx-calendar-event"></i> Manage Events
                            </x-ui.button>

                            @if ($hasEvents)
                                <x-ui.button variant="table-edit" disabled
                                    class="opacity-40 cursor-not-allowed"
                                    title="Remove all events to edit">
                                    <i class="bx bx-edit"></i>
                                </x-ui.button>
                                <x-ui.button variant="table-danger" disabled
                                    class="opacity-40 cursor-not-allowed"
                                    title="Remove all events to delete">
                                    <i class="bx bx-trash"></i>
                                </x-ui.button>
                            @else
                                <x-ui.button href="{{ route('academic.calendars.edit', $year) }}"
                                    variant="table-edit" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </x-ui.button>
                                <x-ui.button type="button" variant="table-danger" title="Delete"
                                    onclick="document.getElementById('deleteAYModal_{{ str_replace('-', '_', $year) }}').showModal()">
                                    <i class="bx bx-trash"></i>
                                </x-ui.button>
                                @include('AcademicCalendar.modals.deleteAYModal', ['year' => $year, 'semesters' => $semesters])
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-layout.panel>

@endsection
