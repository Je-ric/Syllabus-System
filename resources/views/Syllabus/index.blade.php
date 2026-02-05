@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">

        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold">My Syllabi</h1>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            <a href="{{ route('syllabus.create') }}"
                class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg h-48 bg-white shadow hover:shadow-lg hover:bg-gray-50 transition cursor-pointer">
                <span class="text-4xl text-gray-400 font-bold">+</span>
                <span class="mt-2 text-gray-600 font-medium">Create Syllabus</span>
            </a>
        </div>

        @if ($syllabi->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($syllabi as $syllabus)
                    <div class="border rounded-lg bg-white shadow hover:shadow-lg transition overflow-hidden">
                        <div class="bg-linear-to-r from-blue-50 to-indigo-50 px-4 py-3 border-b">
                            <h3 class="font-semibold text-gray-900">{{ $syllabus->course->course_code }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($syllabus->course->course_title, 50) }}</p>
                        </div>
                        <div class="p-4">
                            <div class="mb-4 flex items-center justify-between">

                                <span class="text-xs text-gray-500">Step {{ array_search($syllabus->current_step, array_keys($syllabus->getWizardSteps())) + 1 }} / 5</span>
                            </div>
                            <div class="text-sm text-gray-600 mb-4 space-y-1">
                                @if($syllabus->academic_calendar)
                                    <p><span class="font-medium">Calendar:</span> {{ $syllabus->academic_calendar->academic_year }}</p>
                                @endif
                                <p><span class="font-medium">Program:</span> {{ $syllabus->course->program->name }}</p>
                                <p><span class="font-medium">Credits:</span> {{ $syllabus->course->credit_units }} units</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('syllabus.wizard', ['syllabusId' => $syllabus->id]) }}"
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 rounded text-sm text-center transition">
                                    @if($syllabus->status === 'draft')
                                        Continue Editing
                                    @else
                                        View
                                    @endif
                                </a>
                                <a href="{{ route('syllabus.show', $syllabus->id) }}"
                                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium py-2 px-3 rounded text-sm text-center transition">
                                    Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed">
                <p class="text-gray-500 mb-4">No syllabi created yet</p>
                <a href="{{ route('syllabus.create') }}" class="text-blue-600 hover:underline font-medium">
                    Create your first syllabus
                </a>
            </div>
        @endif

    </div>
@endsection
