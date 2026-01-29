@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">

        <div class="flex justify-between">
            <h1 class="text-2xl font-bold mb-4">{{ isset($isEdit) ? 'Edit Academic Calendar' : 'Create Academic Calendar' }}</h1>

            <x-button href="{{ route('academic.calendars.index') }}" variant="cancel">
                <i class="bx bx-arrow-back"></i> Back
            </x-button>
        </div>

        @include('includes.error-lists')

        @if(isset($hasEvents) && $hasEvents)
            <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded text-sm text-amber-800">
                <i class="bx bx-alert-circle"></i>
                <strong>Note:</strong> This academic calendar has events associated with it. You can only edit the dates. To delete this calendar, please remove all events from the Manage Events page first.
            </div>
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

            <div class="col-span-2">
                <x-form.label for="academic_year" isRequired="true" variant="title">
                    Academic Year (e.g., 2025-2026)
                </x-form.label>
                <input type="text"
                        name="academic_year"
                        value="{{ old('academic_year', $academicYear ?? '') }}"
                        placeholder="e.g., 2025-2026"
                        class="border rounded px-2 py-1 w-full">
            </div>

            <div class="border p-4 rounded">
                <h2 class="font-semibold mb-2">1st Semester</h2>

                <x-form.label for="start_date_1" isRequired="true" variant="date">
                    Start Date
                </x-form.label>
                <input type="date"
                        name="start_date_1"
                        value="{{ old('start_date_1', isset($semesters) ? $semesters->where('semester', '1st')->first()->start_date : '') }}"
                        class="border rounded px-2 py-1 w-full">

                <x-form.label for="end_date_1" isRequired="true" variant="date">
                    End Date
                </x-form.label>
                <input type="date"
                        name="end_date_1"
                        value="{{ old('end_date_1', isset($semesters) ? $semesters->where('semester', '1st')->first()->end_date : '') }}"
                        class="border rounded px-2 py-1 w-full">
            </div>

            <div class="border p-4 rounded">
                <h2 class="font-semibold mb-2">2nd Semester</h2>

                <x-form.label for="start_date_2" isRequired="true" variant="date">
                    Start Date
                </x-form.label>
                <input type="date"
                        name="start_date_2"
                        value="{{ old('start_date_2', isset($semesters) ? $semesters->where('semester', '2nd')->first()->start_date : '') }}"
                        class="border rounded px-2 py-1 w-full">

                <x-form.label for="end_date_2" isRequired="true" variant="date">
                    End Date
                </x-form.label>
                <input type="date"
                        name="end_date_2"
                        value="{{ old('end_date_2', isset($semesters) ? $semesters->where('semester', '2nd')->first()->end_date : '') }}"
                        class="border rounded px-2 py-1 w-full">
            </div>

            <div class="col-span-2 flex gap-2">
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
                    <x-button
                        type="button"
                        variant="save"
                        onclick="showConfirmModal()">
                        <i class="bx bx-save"></i> Create Calendar
                    </x-button>
                    <x-button type="reset" variant="cancel">
                        <i class="bx bx-reset"></i> Reset
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

    </div>
@endsection
