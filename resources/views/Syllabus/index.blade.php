@extends('layouts.app')

@section('content')

    <x-header-with-button title="My Syllabi" description="Manage and continue working on your course syllabi" />

    <div class="max-w-7xl mx-auto p-6 space-y-6">

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            {{-- Create Syllabus Card --}}
            <a href="{{ route('syllabus.create') }}"
                class="group flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300
                    h-56 bg-white hover:bg-slate-50 hover:border-emerald-400 transition shadow-sm">
                <div class="flex flex-col items-center text-center">
                    <span class="text-5xl text-gray-400 group-hover:text-emerald-500 transition">+</span>
                    <span class="mt-3 text-sm font-semibold text-slate-600 group-hover:text-emerald-600">
                        Create Syllabus
                    </span>
                </div>
            </a>

            {{-- Existing Syllabi --}}
            @forelse ($syllabi as $syllabus)
                <div class="flex flex-col rounded-xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition overflow-hidden">

                    {{-- Header --}}
                    <div class="px-4 py-3 bg-linear-to-r from-green-50 to-emerald-50 border-b border-slate-200">
                        <h3 class="font-semibold text-gray-900">
                            {{ $syllabus->course->course_code }}
                        </h3>
                        <p class="text-xs text-slate-600 mt-1">
                            {{ Str::limit($syllabus->course->course_title, 55) }}
                        </p>
                    </div>

                    {{-- Body --}}
                    <div class="flex-1 p-4 space-y-3 text-sm text-slate-600">

                        <div class="flex justify-between items-center">
                            <span class="text-xs text-slate-500">
                                Step
                                {{ array_search($syllabus->current_step, array_keys($syllabus->getWizardSteps())) + 1 }}
                                / {{ count($syllabus->getWizardSteps()) }}
                            </span>

                            <span
                                class="text-xs px-2 py-0.5 rounded-full
                            {{ $syllabus->status === 'draft' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($syllabus->status) }}
                            </span>
                        </div>

                        @if ($syllabus->academic_calendar)
                            <p>
                                <span class="font-medium text-gray-700">Calendar:</span>
                                {{ $syllabus->academic_calendar->academic_year }}
                            </p>
                        @endif

                        <p>
                            <span class="font-medium text-slate-700">Program:</span>
                            {{ $syllabus->course->program->name }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="p-4 border-t border-slate-200 flex gap-2">
                        <a href="{{ route('syllabus.wizard', ['syllabusId' => $syllabus->id]) }}"
                            class="flex-1 text-center bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium py-2 rounded transition">
                            {{ $syllabus->status === 'draft' ? 'Continue' : 'View' }}
                        </a>

                        <a href="{{ route('syllabus.preview', ['syllabus' => $syllabus->id]) }}"
                            class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-medium py-2 rounded transition">
                            Preview
                        </a>
                    </div>

                </div>
            @empty
                {{-- Empty State --}}
                <div class="col-span-full">
                    <x-feedback-status.alert :type="'info'" 
                                        :title="'No Syllabi'" 
                                        :message="'You have not created any syllabi yet. Start by creating a new syllabus.'" />
                </div>
            @endforelse

        </div>
    </div>
@endsection
