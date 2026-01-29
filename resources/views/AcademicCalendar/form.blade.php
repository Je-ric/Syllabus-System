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

        <form action="{{ isset($isEdit)
                ? route('academic.calendars.update', $academicYear)
                : route('academic.calendars.store') }}"
                method="POST" class="grid grid-cols-2 gap-6">
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
                <x-button type="submit" variant="save">
                    {{ isset($isEdit) ? 'Update Calendar' : 'Create Calendar' }}
                </x-button>

                @if(isset($isEdit))
                    <x-button href="{{ route('academic.calendars.index') }}" variant="cancel">
                        Cancel
                    </x-button>
                @else
                    <x-button type="reset" variant="cancel">
                        Reset
                    </x-button>
                @endif
            </div>
        </form>

    </div>
@endsection
