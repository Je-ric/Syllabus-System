@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">

        <div class="mb-6">
            <a href="{{ route('syllabus.index') }}"
                class="text-blue-600 hover:underline text-sm flex items-center gap-1 mb-4">
                <i class="bx bx-chevron-left"></i> Back to Syllabi
            </a>
            <h1 class="text-2xl font-bold">{{ $pageTitle }}</h1>
        </div>

        {{-- Course Information --}}
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Course Information</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-xs font-medium text-gray-600">Course Code</label>
                    <p class="font-semibold">{{ $course->course_code }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Program</label>
                    <p class="font-semibold">{{ $course->program->name }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Units</label>
                    <p class="font-semibold">{{ $course->credit_units }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Type</label>
                    <p class="font-semibold">
                        @if ($hasLab)
                            <span class="px-2 py-1 text-xs bg-purple-100 text-purple-700 rounded">LEC+LAB</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded">LEC</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="mt-4">
                <label class="text-xs font-medium text-gray-600">Course Title</label>
                <p class="font-semibold">{{ $course->course_title }}</p>
            </div>
        </div>

        {{-- Syllabus Form --}}
        <form action="{{ $formAction }}" method="POST" class="space-y-6">
            @csrf
            @if ($formMethod === 'PUT')
                @method('PUT')
            @endif

            <input type="hidden" name="course_id" value="{{ $course->id }}">

            <div class="border rounded-lg p-6 bg-white">
                <div class="mb-4 pb-4 border-b">
                    <h3 class="text-xl font-semibold text-blue-600 flex items-center gap-2">
                        <i class="bx bx-calendar"></i>
                        Academic Year & Semester
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Academic Year & Semester <span class="text-red-600">*</span>
                        </label>
                        <select name="academic_calendar_id"
                            class="w-full border rounded-lg px-4 py-2 @error('academic_calendar_id') border-red-500 @enderror"
                            required>
                            <option value="">-- Choose Academic Calendar --</option>
                            @foreach ($academicCalendars as $calendar)
                                <option value="{{ $calendar->id }}"
                                    {{ old('academic_calendar_id', isset($syllabus) ? $syllabus->academic_calendar_id : '') == $calendar->id ? 'selected' : '' }}>
                                    {{ $calendar->academic_year }} - {{ $calendar->semester }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_calendar_id')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- LECTURE --}}
            @if (!$hasLab || $hasLab)
                <div class="border rounded-lg p-6 bg-white">
                    <div class="mb-4 pb-4 border-b">
                        <h3 class="text-xl font-semibold text-blue-600 flex items-center gap-2">
                            <i class="bx bx-book-open"></i>
                            Lecture (LEC)
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Instructor Name</label>
                                <input type="text" name="lec_instructor_name"
                                    value="{{ old('lec_instructor_name', $lecComponent?->instructor_name ?? '') }}"
                                    class="w-full border rounded-lg px-4 py-2">
                                @error('lec_instructor_name')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Instructor Email</label>
                                <input type="email" name="lec_instructor_email"
                                    value="{{ old('lec_instructor_email', $lecComponent?->instructor_email ?? '') }}"
                                    class="w-full border rounded-lg px-4 py-2">
                                @error('lec_instructor_email')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" name="lec_phone"
                                    value="{{ old('lec_phone', $lecComponent?->phone ?? '') }}"
                                    class="w-full border rounded-lg px-4 py-2">
                                @error('lec_phone')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Office</label>
                                <input type="text" name="lec_office"
                                    value="{{ old('lec_office', $lecComponent?->office ?? '') }}"
                                    class="w-full border rounded-lg px-4 py-2">
                                @error('lec_office')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Class Hours</label>
                                <input type="text" name="lec_class_hours"
                                    value="{{ old('lec_class_hours', $lecComponent?->class_hours ?? '') }}"
                                    placeholder="e.g., 3 hours/week" class="w-full border rounded-lg px-4 py-2">
                                @error('lec_class_hours')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Schedule (Days/Times)</label>
                                <input type="text" name="lec_schedule"
                                    value="{{ old('lec_schedule', $lecComponent?->schedule ?? '') }}"
                                    placeholder="e.g., MWF 9:00-10:00, TTh 10:00-11:30"
                                    class="w-full border rounded-lg px-4 py-2">
                                @error('lec_schedule')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Consultation Hours</label>
                                <input type="text" name="lec_consultation_hours"
                                    value="{{ old('lec_consultation_hours', $lecComponent?->consultation_hours ?? '') }}"
                                    placeholder="e.g., MW 2:00-4:00 PM" class="w-full border rounded-lg px-4 py-2">
                                @error('lec_consultation_hours')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Performance Standard</label>
                                <select name="lec_performance_standard" class="w-full border rounded-lg px-4 py-2">
                                    <option value="50%"
                                        {{ old('lec_performance_standard', $lecComponent?->performance_standard ?? '50%') === '50%' ? 'selected' : '' }}>
                                        50%</option>
                                    <option value="60%"
                                        {{ old('lec_performance_standard', $lecComponent?->performance_standard ?? '50%') === '60%' ? 'selected' : '' }}>
                                        60%</option>
                                    <option value="75%"
                                        {{ old('lec_performance_standard', $lecComponent?->performance_standard ?? '50%') === '75%' ? 'selected' : '' }}>
                                        75%</option>
                                </select>
                                @error('lec_performance_standard')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- LABORATORY Component (if has_lec_lab = true) --}}
            @if ($hasLab)
                <div class="border rounded-lg p-6 bg-white">
                    <div class="mb-4 pb-4 border-b">
                        <h3 class="text-xl font-semibold text-purple-600 flex items-center gap-2">
                            <i class="bx bx-flask"></i>
                            Laboratory (LAB)
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Instructor Name</label>
                                <input type="text" name="lab_instructor_name"
                                    value="{{ old('lab_instructor_name', $labComponent?->instructor_name ?? '') }}"
                                    class="w-full border rounded-lg px-4 py-2">
                                @error('lab_instructor_name')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Instructor Email</label>
                                <input type="email" name="lab_instructor_email"
                                    value="{{ old('lab_instructor_email', $labComponent?->instructor_email ?? '') }}"
                                    class="w-full border rounded-lg px-4 py-2">
                                @error('lab_instructor_email')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" name="lab_phone"
                                    value="{{ old('lab_phone', $labComponent?->phone ?? '') }}"
                                    class="w-full border rounded-lg px-4 py-2">
                                @error('lab_phone')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Office/Lab Location</label>
                                <input type="text" name="lab_office"
                                    value="{{ old('lab_office', $labComponent?->office ?? '') }}"
                                    class="w-full border rounded-lg px-4 py-2">
                                @error('lab_office')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Schedule & Requirements --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Class Hours</label>
                                <input type="text" name="lab_class_hours"
                                    value="{{ old('lab_class_hours', $labComponent?->class_hours ?? '') }}"
                                    placeholder="e.g., 3 hours/week" class="w-full border rounded-lg px-4 py-2">
                                @error('lab_class_hours')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Schedule (Days/Times)</label>
                                <input type="text" name="lab_schedule"
                                    value="{{ old('lab_schedule', $labComponent?->schedule ?? '') }}"
                                    placeholder="e.g., W 2:00-5:00, F 2:00-5:00"
                                    class="w-full border rounded-lg px-4 py-2">
                                @error('lab_schedule')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Consultation Hours</label>
                                <input type="text" name="lab_consultation_hours"
                                    value="{{ old('lab_consultation_hours', $labComponent?->consultation_hours ?? '') }}"
                                    placeholder="e.g., Th 2:00-4:00 PM" class="w-full border rounded-lg px-4 py-2">
                                @error('lab_consultation_hours')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Performance Standard</label>
                                <select name="lab_performance_standard" class="w-full border rounded-lg px-4 py-2">
                                    <option value="50%"
                                        {{ old('lab_performance_standard', $labComponent?->performance_standard ?? '50%') === '50%' ? 'selected' : '' }}>
                                        50%</option>
                                    <option value="60%"
                                        {{ old('lab_performance_standard', $labComponent?->performance_standard ?? '50%') === '60%' ? 'selected' : '' }}>
                                        60%</option>
                                    <option value="75%"
                                        {{ old('lab_performance_standard', $labComponent?->performance_standard ?? '50%') === '75%' ? 'selected' : '' }}>
                                        75%</option>
                                </select>
                                @error('lab_performance_standard')
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex gap-4 justify-end">
                <a href="{{ route('syllabus.index') }}"
                    class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    {{ $formMethod === 'PUT' ? 'Update' : 'Create' }} Syllabus
                </button>
            </div>
        </form>

    </div>
@endsection
