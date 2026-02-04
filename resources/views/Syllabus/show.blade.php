@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">

        <div class="mb-6 flex justify-between items-center">
            <div>
                <a href="{{ route('syllabus.index') }}"
                    class="text-blue-600 hover:underline text-sm flex items-center gap-1 mb-4">
                    <i class="bx bx-chevron-left"></i> Back to Syllabi
                </a>
                <h1 class="text-2xl font-bold">{{ $syllabus->course->course_code }} - Syllabus</h1>
            </div>
            @if ($syllabus->isEditable())
                <a href="{{ route('syllabus.edit', $syllabus->id) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="bx bx-edit"></i> Edit
                </a>
            @endif
        </div>

        <!-- Status Card -->
        <div class="mb-6 bg-white border rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-xs font-medium text-gray-600">Status</label>
                    <div class="mt-2">
                        @php
                            $statusColor = match ($syllabus->status) {
                                'draft' => 'bg-gray-100 text-gray-700',
                                'under_review' => 'bg-blue-100 text-blue-700',
                                'for_revision' => 'bg-yellow-100 text-yellow-700',
                                'approved' => 'bg-green-100 text-green-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $syllabus->status)) }}
                        </span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Prepared By</label>
                    <p class="font-semibold mt-2">{{ $syllabus->preparer->name }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Academic Year</label>
                    <p class="font-semibold mt-2">{{ $syllabus->academicCalendar->academic_year }}</p>
                </div>
            </div>
        </div>

        <!-- Course Information -->
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Course Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-medium text-gray-600">Course Code</label>
                    <p class="font-semibold text-lg">{{ $syllabus->course->course_code }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Course Title</label>
                    <p class="font-semibold text-lg">{{ $syllabus->course->course_title }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Program</label>
                    <p class="font-semibold">{{ $syllabus->course->program->name }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Credit Units</label>
                    <p class="font-semibold">{{ $syllabus->course->credit_units }} units</p>
                </div>
            </div>
            @if ($syllabus->course->course_description)
                <div class="mt-4">
                    <label class="text-xs font-medium text-gray-600">Description</label>
                    <p class="mt-2 text-gray-700">{{ $syllabus->course->course_description }}</p>
                </div>
            @endif
        </div>

        <!-- Lecture Component -->
        @if ($syllabus->course->components->where('type', 'LEC')->first())
            @php $lec = $syllabus->course->components->where('type', 'LEC')->first(); @endphp
            <div class="mb-6 border rounded-lg p-6 bg-white">
                <h3 class="text-xl font-semibold text-blue-600 mb-4 flex items-center gap-2">
                    <i class="bx bx-book-open"></i>
                    Lecture (LEC)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs font-medium text-gray-600">Instructor</label>
                                <p class="font-semibold">{{ $lec->instructor_name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Email</label>
                                <p class="text-blue-600">{{ $lec->instructor_email }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Phone</label>
                                <p>{{ $lec->phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Office</label>
                                <p>{{ $lec->office ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs font-medium text-gray-600">Class Hours</label>
                                <p class="font-semibold">{{ $lec->class_hours }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Schedule</label>
                                <p>{{ $lec->getFormattedSchedule() }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Consultation Hours</label>
                                <p>{{ $lec->consultation_hours ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Performance Standard</label>
                                <p class="font-semibold text-blue-600">{{ $lec->performance_standard }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Laboratory Component -->
        @if ($syllabus->course->components->where('type', 'LAB')->first())
            @php $lab = $syllabus->course->components->where('type', 'LAB')->first(); @endphp
            <div class="mb-6 border rounded-lg p-6 bg-white">
                <h3 class="text-xl font-semibold text-purple-600 mb-4 flex items-center gap-2">
                    <i class="bx bx-flask"></i>
                    Laboratory (LAB)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs font-medium text-gray-600">Instructor</label>
                                <p class="font-semibold">{{ $lab->instructor_name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Email</label>
                                <p class="text-blue-600">{{ $lab->instructor_email }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Phone</label>
                                <p>{{ $lab->phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Lab Location</label>
                                <p>{{ $lab->office ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs font-medium text-gray-600">Class Hours</label>
                                <p class="font-semibold">{{ $lab->class_hours }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Schedule</label>
                                <p>{{ $lab->getFormattedSchedule() }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Consultation Hours</label>
                                <p>{{ $lab->consultation_hours ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Performance Standard</label>
                                <p class="font-semibold text-purple-600">{{ $lab->performance_standard }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Revisions -->
        @if ($syllabus->revisions->count() > 0)
            <div class="mb-6 border rounded-lg p-6 bg-white">
                <h3 class="text-lg font-semibold mb-4">Revision History</h3>
                <div class="space-y-3">
                    @foreach ($syllabus->revisions as $revision)
                        <div class="border-l-4 border-blue-500 pl-4 py-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold">Revision {{ $revision->revision_no }}</p>
                                    <p class="text-sm text-gray-600">{{ $revision->implementation_semester }}</p>
                                </div>
                                <p class="text-xs text-gray-500">{{ $revision->revision_date->format('M d, Y') }}</p>
                            </div>
                            @if ($revision->highlights)
                                <p class="text-sm text-gray-700 mt-2">{{ $revision->highlights }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endsection
