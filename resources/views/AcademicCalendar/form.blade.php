@extends('layouts.app')

@section('content')

        <x-header-with-button title="{{ isset($isEdit) ? 'Edit Academic Calendar' : 'Create Academic Calendar' }}"
                        description="Academic Year and Semester Dates">
            <x-button variant="cancel"
                    href="{{ route('academic.calendars.index') }}">
                    <i class="bx bx-left-arrow-alt"></i>Back
            </x-button>
        </x-header-with-button>

        @include('includes.error-lists')

        @if(isset($hasEvents) && $hasEvents)
            <x-feedback-status.alert
                type="warning"
                title="Note"
                message="This academic calendar has events associated with it. You can only edit the dates. To delete this calendar, please remove all events from the Manage Events page first."
                class="mb-4"
            />
        @endif

        <form id="academicCalendarForm"
                action="{{ isset($isEdit)
                ? route('academic.calendars.update', $academicYear)
                : route('academic.calendars.store') }}"
                method="POST"
                class="grid grid-cols-2 gap-6"
                @if(!isset($isEdit)) onsubmit="return false;" @endif>
            @csrf
            @if (isset($isEdit))
                @method('PUT')
            @endif

            @if(isset($isEdit))
                <div class="col-span-2 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-sm text-amber-900">
                    <p class="text-xs uppercase tracking-[0.2em] font-semibold text-amber-700 mb-2">Current Values</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <p class="font-semibold">Academic Year</p>
                            <p>{{ $originalValues['academic_year'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="font-semibold">1st Semester</p>
                            <p>
                                {{ isset($originalValues['start_date_1']) ? \Carbon\Carbon::parse($originalValues['start_date_1'])->format('M d, Y') : '-' }}
                                -
                                {{ isset($originalValues['end_date_1']) ? \Carbon\Carbon::parse($originalValues['end_date_1'])->format('M d, Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="font-semibold">2nd Semester</p>
                            <p>
                                {{ isset($originalValues['start_date_2']) ? \Carbon\Carbon::parse($originalValues['start_date_2'])->format('M d, Y') : '-' }}
                                -
                                {{ isset($originalValues['end_date_2']) ? \Carbon\Carbon::parse($originalValues['end_date_2'])->format('M d, Y') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-span-2 bg-white/90 border border-slate-200/80 rounded-2xl p-5 shadow-sm">
                <x-form.label for="academic_year" isRequired="true" variant="title">
                    Academic Year (e.g., 2025-2026)
                </x-form.label>
                <x-form.input type="text"
                    name="academic_year"
                    value="{{ old('academic_year', $academicYear ?? '') }}"
                    placeholder="e.g., 2025-2026"
                    class="mt-2" />
            </div>

            <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-3">1st Semester</h2>

                <x-form.label for="start_date_1" isRequired="true" variant="date">
                    Start Date
                </x-form.label>
                <x-form.date-picker
                    name="start_date_1"
                    value="{{ old('start_date_1', isset($semesters) ? $semesters->where('semester', '1st')->first()->start_date : '') }}"
                    class="mt-2"
                />

                <x-form.label for="end_date_1" isRequired="true" variant="date">
                    End Date
                </x-form.label>
                <x-form.date-picker
                    name="end_date_1"
                    value="{{ old('end_date_1', isset($semesters) ? $semesters->where('semester', '1st')->first()->end_date : '') }}"
                    class="mt-2"
                />
            </div>

            <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-3">2nd Semester</h2>

                <x-form.label for="start_date_2" isRequired="true" variant="date">
                    Start Date
                </x-form.label>
                <x-form.date-picker
                    name="start_date_2"
                    value="{{ old('start_date_2', isset($semesters) ? $semesters->where('semester', '2nd')->first()->start_date : '') }}"
                    class="mt-2"
                />

                <x-form.label for="end_date_2" isRequired="true" variant="date">
                    End Date
                </x-form.label>
                <x-form.date-picker
                    name="end_date_2"
                    value="{{ old('end_date_2', isset($semesters) ? $semesters->where('semester', '2nd')->first()->end_date : '') }}"
                    class="mt-2"
                />
            </div>

            <div class="col-span-2 flex flex-wrap gap-2">
                @if(isset($isEdit))
                    @if($hasEvents ?? false)
                        <x-button type="button" variant="save" disabled title="Cannot update while events exist">
                            <i class="bx bx-save"></i> Update Calendar
                        </x-button>
                    @else
                        <x-button type="submit" variant="save">
                            <i class="bx bx-save"></i> Update Calendar
                        </x-button>
                    @endif
                    <x-button
                        type="button"
                        variant="cancel"
                        onclick="document.getElementById('cancelEditModal').showModal()">
                        <i class="bx bx-x"></i> Cancel
                    </x-button>
                @else
                    <x-button type="reset" variant="cancel">
                        <i class="bx bx-reset"></i> Reset
                    </x-button>
                    <x-button
                        type="button"
                        variant="save"
                        onclick="showConfirmModal()">
                        <i class="bx bx-save"></i> Create Calendar
                    </x-button>
                @endif
            </div>
        </form>

        {{-- Modals --}}
        @if(!isset($isEdit))
            @include('AcademicCalendar.modals.confirmAYModal')
        @else
            @include('AcademicCalendar.modals.cancelEditModal')
        @endif

@endsection
