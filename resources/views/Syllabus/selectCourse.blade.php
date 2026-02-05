@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">

        <div class="mb-6">
            <h1 class="text-2xl font-bold">Create Syllabus</h1>
            <p class="text-gray-600 mt-2">Step 1: Select program → Step 2: Choose course → Step 3: Fill details</p>
        </div>

        <!-- Program Selector -->
        <div class="mb-6 bg-gray-50 border rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Select Program</h2>
            <livewire:programs.program-selector :program-id="optional($program)?->id" redirect-route="syllabus.create" :autoRedirect="true" />
        </div>

        @if ($program)
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-xl font-semibold">Courses in {{ $program->name }}</h2>
            </div>

            {{-- Group courses by year --}}
            @forelse ($groupedCourses as $year => $semesters)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 border-b border-gray-300 pb-2">Year {{ $year ?? 'N/A' }}</h3>

                    {{-- Group courses by semester --}}
                    @forelse ($semesters as $semester => $courses)
                        <div class="mb-6 bg-gray-50 border rounded-lg p-4">
                            <h4 class="font-medium text-gray-700 mb-3 border-b pb-1">
                                Semester {{ $semester ?? 'N/A' }}
                            </h4>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse">
                                    <thead class="bg-gray-100 border-b">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-semibold">CODE</th>
                                            <th class="px-4 py-2 text-left font-semibold">COURSE TITLE</th>
                                            <th class="px-4 py-2 text-center font-semibold">UNITS</th>
                                            <th class="px-4 py-2 text-center font-semibold">TYPE</th>
                                            <th class="px-4 py-2 text-center font-semibold">ACTION</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($courses as $course)
                                            <tr class="hover:bg-gray-100 border-b">
                                                <td class="px-4 py-2 font-medium text-gray-900">{{ $course->course_code }}</td>
                                                <td class="px-4 py-2 text-gray-700">{{ $course->course_title }}</td>
                                                <td class="px-4 py-2 text-center">{{ $course->credit_units }}</td>
                                                <td class="px-4 py-2 text-center">
                                                    @if ($course->has_lec_lab)
                                                        <x-feedback-status.status-indicator status="lec_lab" label="LEC+LAB"></x-feedback-status.status-indicator>
                                                    @else
                                                        <x-feedback-status.status-indicator status="lec" label="LEC"></x-feedback-status.status-indicator>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-center">
                                                    <a href="{{ route('syllabus.form', $course->id) }}"
                                                       class="text-blue-600 hover:underline font-medium">
                                                        Create Syllabus
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm mt-2">No courses for this semester.</p>
                    @endforelse
                </div>
            @empty
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <p class="text-gray-500 mb-3">No courses found for this program</p>
                </div>
            @endforelse
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <p class="text-gray-500">Select a program above to view and manage courses</p>
            </div>
        @endif
    </div>
@endsection
