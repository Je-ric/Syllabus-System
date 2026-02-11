@extends('layouts.app')

@section('content')

<x-header-with-button
        title="{{ $pageTitle }}"
        description="blank muna, passed from controller">
        <x-button href="{{ route('courses.index') }}" variant="cancel">
            <i class="bx bx-chevron-left"></i> Back to Courses
        </x-button>
</x-header-with-button>

@if(!$program)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-amber-800">
        <p>Please select a program from the courses page before creating a course.</p>
    </div>
@endif

<form action="{{ $formAction }}" method="POST" id="courseForm">
    @csrf
    @if($formMethod !== 'POST')
        @method($formMethod)
    @endif

    <input type="hidden" name="program_id" value="{{ $program?->id ?? '' }}">

    @if($program)
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6">
            <p class="text-slate-700"><span class="font-semibold">Program:</span> {{ $program->name }}</p>
        </div>
    @endif

    <div class="grid grid-cols-2 gap-4 mb-4 text-slate-800">
        <div>
            <x-form.label class="block">Course Code</x-form.label>
            <input type="text" name="code" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" value="{{ old('code', $course->course_code ?? '') }}" required>
            @error('code')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <x-form.label class="block">Course Title</x-form.label>
            <input type="text" name="name" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" value="{{ old('name', $course->course_title ?? '') }}" required>
            @error('name')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="mb-4">
        <x-form.label class="block">Course Description</x-form.label>
        <textarea name="description" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" rows="3">{{ old('description', $course->course_description ?? '') }}</textarea>
        @error('description')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="grid grid-cols-4 gap-4 mb-4 text-slate-800">
        <div>
            <x-form.label class="block">Credit Units</x-form.label>
            <input type="number" name="credits" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" value="{{ old('credits', $course->credit_units ?? '') }}" min="0" required>
            @error('credits')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <x-form.label class="block">Has Laboratory</x-form.label>
            <div class="flex gap-4 mt-2 text-sm text-slate-700">
                @php $labValue = old('has_lec_lab', isset($course) ? ($course->has_lec_lab ? '1' : '0') : ''); @endphp
                <label><input type="radio" name="has_lec_lab" value="1" {{ $labValue === '1' ? 'checked' : '' }}> Yes</label>
                <label><input type="radio" name="has_lec_lab" value="0" {{ $labValue === '0' ? 'checked' : '' }}> No</label>
            </div>
        </div>
        <div>
            <x-form.label class="block">Year Level</x-form.label>
            <select name="year_level" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                <option value="">Select Year</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ old('year_level', $course->year_level ?? '') == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                @endfor
            </select>
        </div>
        <div>
            <x-form.label class="block">Semester</x-form.label>
            <select name="semester" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                <option value="">Select Semester</option>
                <option value="1" {{ old('semester', $course->semester ?? '') == '1' ? 'selected' : '' }}>1st Semester</option>
                <option value="2" {{ old('semester', $course->semester ?? '') == '2' ? 'selected' : '' }}>2nd Semester</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4 text-slate-800">
        <div>
            <x-form.label class="block">Prerequisite</x-form.label>
            <input type="text" name="prerequisite" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" value="{{ old('prerequisite', $course->prerequisite ?? '') }}">
        </div>
        <div>
            <x-form.label class="block">Corequisite</x-form.label>
            <input type="text" name="corequisite" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" value="{{ old('corequisite', $course->corequisite ?? '') }}">
        </div>
    </div>

    @if($program)
        <hr class="my-6 border-slate-200">
        <div class="flex flex-wrap justify-between gap-3 mb-4">
            <h2 class="text-lg font-semibold text-slate-800">Program Outcomes Mapping</h2>
            <p class="text-sm text-slate-500"><b>Level:</b> I - Introductory, E - Enabling, D - Demonstrative</p>
        </div>

        @if($programOutcomes->isEmpty())
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4 text-amber-800">
                <p>No program outcomes defined for {{ $program->name }}. Please define outcomes first.</p>
            </div>
        @else
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <table class="w-full">
                    <thead class="bg-emerald-50 border-b border-slate-200 text-emerald-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.2em] font-semibold">PO Code</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.2em] font-semibold">Learning Outcome</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.2em] font-semibold">IED Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programOutcomes as $outcome)
                            <tr class="border-b border-slate-200 hover:bg-emerald-50/60">
                                <td class="px-4 py-3 font-semibold text-slate-700">
                                    {{-- {{ $outcome->po_code }} --}}
                                    PO{{ $loop->iteration }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    {{ $outcome->po_text }}
                                </td>
                                <td class="px-4 py-3">
                                    @php $selected = $poSelections[$outcome->id] ?? null; @endphp
                                    <div class="flex gap-4">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="po_mapping[{{ $outcome->id }}]" value="I" class="mr-1" {{ $selected === 'I' ? 'checked' : '' }}>
                                            <span class="text-xs text-slate-600">I</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="po_mapping[{{ $outcome->id }}]" value="E" class="mr-1" {{ $selected === 'E' ? 'checked' : '' }}>
                                            <span class="text-xs text-slate-600">E</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="po_mapping[{{ $outcome->id }}]" value="D" class="mr-1" {{ $selected === 'D' ? 'checked' : '' }}>
                                            <span class="text-xs text-slate-600">D</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-sm text-slate-500 mt-2">Select IED level for each outcome that applies to this course (leave blank if not applicable)</p>
        @endif
    @endif

    <div class="flex justify-end gap-2 mt-6">
        <x-button href="{{ route('courses.index') }}"
                variant="cancel">
                Cancel
        </x-button>
        <x-button type="button" variant="save" onclick="showConfirmCourseModal()">
            <i class="bx bx-save"></i> {{ $submitLabel }}
        </x-button>
    </div>
</form>

@endsection
