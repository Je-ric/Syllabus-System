@extends('layouts.app')

@section('content')

<x-page-header
    icon="bx-book"
    title="{{ $pageTitle }}"
    desc="Fill in the course details and map program outcomes below.">
    <x-button href="{{ route('courses.index') }}" variant="cancel">
        <i class="bx bx-chevron-left"></i> Back to Courses
    </x-button>
</x-page-header>

<x-panel>
    {{-- No program selected warning --}}
    @if (!$program)
        <x-feedback-status.alert type="warning" class="mb-6">
            Please select a program from the courses page before creating a course.
        </x-feedback-status.alert>
    @endif
    
    <form action="{{ $formAction }}" method="POST" id="courseForm">
        @csrf
        @if ($formMethod !== 'POST')
            @method($formMethod)
        @endif
    
        <input type="hidden" name="program_id" value="{{ $program?->id ?? '' }}">
        <input type="hidden" name="confirmed_submission" id="confirmedSubmission" value="0">
    
        {{-- Selected program info card (not an alert — this is contextual info, not a status message) --}}
        @if ($program)
            <div class="mb-6 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50/80 px-5 py-3.5 shadow-sm">
                <i class="bx bx-book-open text-xl text-emerald-600 shrink-0"></i>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-400">Program</p>
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $program->name }}</p>
                </div>
            </div>
        @endif
    
        {{-- ── Section: Course Details ────────────────────────────────────────── --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm p-6 mb-5 space-y-5">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 -mb-1">
                Course Details
            </p>
    
            {{-- Code + Title --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <x-form.label for="code" variant="title" isRequired>Course Code</x-form.label>
                    <x-form.input id="code" type="text" name="code"
                        value="{{ old('code', $course->course_code ?? '') }}" required />
                    @error('code')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-1.5">
                    <x-form.label for="name" variant="title" isRequired>Course Title</x-form.label>
                    <x-form.input id="name" type="text" name="name"
                        value="{{ old('name', $course->course_title ?? '') }}" required />
                    @error('name')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
    
            {{-- Description --}}
            <div class="space-y-1.5">
                <x-form.label for="description" variant="title">Course Description</x-form.label>
                <x-form.textarea id="description" name="description" rows="3">{{ old('description', $course->course_description ?? '') }}</x-form.textarea>
                @error('description')
                    <p class="text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
    
            {{-- Credits + Lab + Year + Semester — responsive 2→4 col --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="space-y-1.5">
                    <x-form.label for="credits" variant="title" isRequired>Credit Units</x-form.label>
                    <x-form.input id="credits" type="number" name="credits" min="0"
                        value="{{ old('credits', $course->credit_units ?? '') }}" required />
                    @error('credits')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
    
                <div class="space-y-1.5">
                    <x-form.label variant="title" isRequired>Has Laboratory</x-form.label>
                    @php
                        $labValue = old('has_lec_lab', isset($course) ? ($course->has_lec_lab ? '1' : '0') : '');
                    @endphp
                    <x-form.radio
                        name="has_lec_lab"
                        :options="['1' => 'Yes', '0' => 'No']"
                        :value="$labValue" />
                </div>
    
                <div class="space-y-1.5">
                    <x-form.label for="year_level" variant="title" isRequired>Year Level</x-form.label>
                    <x-form.select id="year_level" name="year_level">
                        <option value="">Select Year</option>
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}"
                                {{ old('year_level', $course->year_level ?? '') == $i ? 'selected' : '' }}>
                                Year {{ $i }}
                            </option>
                        @endfor
                    </x-form.select>
                    @error('year_level')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
    
                <div class="space-y-1.5">
                    <x-form.label for="semester" variant="title" isRequired>Semester</x-form.label>
                    <x-form.select id="semester" name="semester">
                        <option value="">Select Semester</option>
                        <option value="1" {{ old('semester', $course->semester ?? '') == '1' ? 'selected' : '' }}>1st Semester</option>
                        <option value="2" {{ old('semester', $course->semester ?? '') == '2' ? 'selected' : '' }}>2nd Semester</option>
                    </x-form.select>
                    @error('semester')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
    
            {{-- Prerequisite + Corequisite --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <x-form.label for="prerequisite" variant="title">Prerequisite</x-form.label>
                    <x-form.input id="prerequisite" type="text" name="prerequisite"
                        value="{{ old('prerequisite', $course->prerequisite ?? '') }}" />
                </div>
                <div class="space-y-1.5">
                    <x-form.label for="corequisite" variant="title">Corequisite</x-form.label>
                    <x-form.input id="corequisite" type="text" name="corequisite"
                        value="{{ old('corequisite', $course->corequisite ?? '') }}" />
                </div>
            </div>
        </div>
    
        {{-- ── Section: PO Mapping ─────────────────────────────────────────────── --}}
        @if ($program)
            <div class="rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm overflow-hidden mb-5">
    
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/60 flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                        Program Outcomes Mapping
                    </p>
                    <span class="flex items-center gap-2 text-xs text-slate-400 whitespace-nowrap shrink-0">
                        <strong>IED:</strong> I&nbsp;– Introductory &nbsp;·&nbsp; E&nbsp;– Enabling &nbsp;·&nbsp; D&nbsp;– Demonstrating
                    </span>
                </div>
    
                @if ($programOutcomes->isEmpty())
                    <div class="p-6">
                        <x-empty-state
                            icon="bx-notepad"
                            title="No program outcomes yet"
                            :message="'No outcomes defined for ' . $program->name . '. Define them in the program settings first.'" />
                    </div>
                @else
                    <x-table.container class="rounded-none border-0 bg-transparent shadow-none">
                        <x-table.table id="courseFormMapping" class="w-full table-fixed">
                            <x-table.head class="border-b border-slate-200">
                                <x-table.row>
                                    <x-table.th class="border-0 w-20">PO</x-table.th>
                                    <x-table.th class="border-0">Program Outcome</x-table.th>
                                    <x-table.th class="border-0 w-40 text-center">IED Level</x-table.th>
                                </x-table.row>
                            </x-table.head>

                            <x-table.body class="divide-y divide-slate-100">
                                @foreach ($programOutcomes as $outcome)
                                    <x-table.row class="hover:bg-emerald-50/40 transition-colors">
                                        
                                        <x-table.td class="border-0 w-20 font-mono font-bold text-sm text-slate-700">
                                            {{ $outcome->po_code }}
                                        </x-table.td>

                                        <x-table.td class="border-0 text-sm text-slate-700 leading-relaxed wrap-break-words">
                                            {{ $outcome->po_text }}
                                        </x-table.td>

                                        <x-table.td class="border-0 w-40 text-sm text-slate-700">
                                            @php $selected = $poSelections[$outcome->id] ?? null; @endphp

                                            <div class="flex items-center justify-start gap-3 whitespace-nowrap">
                                                <x-form.radio
                                                    :name="'po_mapping[' . $outcome->id . ']'"
                                                    :options="['I' => 'I', 'E' => 'E', 'D' => 'D']"
                                                    :value="$selected"
                                                    class="m-0 shrink-0" />
                                            </div>
                                        </x-table.td>

                                    </x-table.row>
                                @endforeach
                            </x-table.body>
                        </x-table.table>
                    </x-table.container>
                    <p class="px-5 py-3 text-xs text-slate-400 border-t border-slate-100">
                        Leave blank if this outcome does not apply to this course.
                    </p>
                @endif
            </div>
        @endif
    
        {{-- ── Form actions ────────────────────────────────────────────────────── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <x-button href="{{ route('courses.index') }}" variant="cancel">
                <i class="bx bx-arrow-back"></i> Cancel
            </x-button>
    
            <div class="flex gap-2">
                {{-- Reset is a mild destructive action — outline variant signals reversibility --}}
                <x-button type="button" variant="outline" onclick="resetRadioButtons()">
                    <i class="bx bx-reset"></i> Reset IED Levels
                </x-button>
                <x-button type="button" variant="save" onclick="openCourseConfirmModal()">
                    <i class="bx bx-save"></i> {{ $submitLabel }}
                </x-button>
            </div>
        </div>
    </form>
</x-panel>

@if ($formMethod === 'POST')
    @include('Course.modals.confirmCourseModal')
@else
    @include('Course.modals.confirmEditCourseModal')
@endif

@endsection

@push('scripts')
<script>
    const confirmModalId = @json($formMethod === 'POST' ? 'confirmCourseModal' : 'confirmEditCourseModal');

    function resetRadioButtons() {
        document.querySelectorAll('#courseForm #courseFormMapping input[type="radio"]')
            .forEach(r => r.checked = false);
    }

    function openCourseConfirmModal() {
        document.getElementById(confirmModalId)?.showModal();
    }

    function confirmCourseSubmit() {
        const flag = document.getElementById('confirmedSubmission');
        if (flag) flag.value = '1';
        document.getElementById('courseForm').submit();
    }

    document.getElementById('courseForm').addEventListener('submit', function (e) {
        const flag = document.getElementById('confirmedSubmission');
        if (!flag || flag.value !== '1') {
            e.preventDefault();
            openCourseConfirmModal();
        }
    });
</script>
@endpush
