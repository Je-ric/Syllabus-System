@extends('layouts.app')

@section('content')

<x-page-header
    icon="bx-book"
    title="{{ $pageTitle }}"
    desc="Fill in the course details and map program outcomes below.">
    <x-button href="{{ route('courses.index') }}" variant="cancel">
        <i class="bx bx-arrow-back"></i> Back to Courses
    </x-button>
</x-page-header>

<x-panel>
    @if (!$program)
        <x-feedback-status.alert type="warning" class="mb-6">
            Please select a program from the courses page before creating a course.
        </x-feedback-status.alert>
    @endif

    <form action="{{ $formAction }}" method="POST" id="courseForm" class="space-y-5">
        @csrf
        @if ($formMethod !== 'POST')
            @method($formMethod)
        @endif

        <input type="hidden" name="program_id" value="{{ $program?->id ?? '' }}">
        <input type="hidden" name="confirmed_submission" id="confirmedSubmission" value="0">

        {{-- Program info chip --}}
        @if ($program)
            <div class="flex items-center gap-3 rounded-[14px] border border-[#e4e4e7] bg-[#fafafa] px-4 py-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-[10px] bg-[#dcfce7] shrink-0">
                    <i class="bx bx-book-open text-[#16a34a] text-base leading-none"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#a1a1aa]">Program</p>
                    <p class="text-[13px] font-semibold text-[#09090b] truncate">{{ $program->name }}</p>
                </div>
            </div>
        @endif

        {{-- ── Section: Course Details ──────────────────────────────────────── --}}
        <div class="rounded-[16px] border border-[#e4e4e7] bg-white overflow-hidden"
             style="box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-3 border-b border-[#e4e4e7] bg-[#f4f4f5]">
                <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#52525b]">Course Details</p>
            </div>

            <div class="px-5 pt-4 pb-5 space-y-4">
                {{-- Code + Title --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.field label="Course Code" for="code" :isRequired="true" error="code">
                        <x-form.input id="code" type="text" name="code"
                            value="{{ old('code', $course->course_code ?? '') }}" required />
                    </x-form.field>
                    <x-form.field label="Course Title" for="name" :isRequired="true" error="name">
                        <x-form.input id="name" type="text" name="name"
                            value="{{ old('name', $course->course_title ?? '') }}" required />
                    </x-form.field>
                </div>

                {{-- Description --}}
                <x-form.field label="Course Description" for="description" error="description">
                    <x-form.textarea id="description" name="description" rows="3">{{ old('description', $course->course_description ?? '') }}</x-form.textarea>
                </x-form.field>

                {{-- Credits + Lab + Year + Semester --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-form.field label="Credit Units" for="credits" :isRequired="true" error="credits">
                        <x-form.input id="credits" type="number" name="credits" min="0"
                            value="{{ old('credits', $course->credit_units ?? '') }}" required />
                    </x-form.field>

                    <div class="space-y-1">
                        <x-form.label :isRequired="true">Has Laboratory</x-form.label>
                        @php
                            $labValue = old('has_lec_lab', isset($course) ? ($course->has_lec_lab ? '1' : '0') : '');
                            $hasSyllabi = isset($course) && $course->syllabi()->exists();
                        @endphp
                        @if ($hasSyllabi)
                            <div class="flex items-start gap-2 rounded-[10px] border border-[#fde68a] bg-[#fffbeb] px-3 py-2 mb-1">
                                <i class="bx bx-lock text-[#d97706] text-sm mt-0.5 shrink-0"></i>
                                <p class="text-[11px] text-[#92400e] leading-snug">Locked — delete all syllabi first to change this.</p>
                            </div>
                        @endif
                        <x-form.radio
                            name="has_lec_lab"
                            :options="['1' => 'Yes', '0' => 'No']"
                            :value="$labValue"
                            :disabled="$hasSyllabi ?? false" />
                        @if ($hasSyllabi ?? false)
                            <input type="hidden" name="has_lec_lab" value="{{ $labValue }}">
                        @endif
                        @error('has_lec_lab')
                            <p class="text-xs text-[#e11d48] flex items-center gap-1 mt-1">
                                <i class="bx bx-error-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <x-form.field label="Year Level" for="year_level" :isRequired="true" error="year_level">
                        <x-form.select id="year_level" name="year_level">
                            <option value="">Select Year</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ old('year_level', $course->year_level ?? '') == $i ? 'selected' : '' }}>
                                    Year {{ $i }}
                                </option>
                            @endfor
                        </x-form.select>
                    </x-form.field>

                    <x-form.field label="Semester" for="semester" :isRequired="true" error="semester">
                        <x-form.select id="semester" name="semester">
                            <option value="">Select Semester</option>
                            <option value="1" {{ old('semester', $course->semester ?? '') == '1' ? 'selected' : '' }}>1st Semester</option>
                            <option value="2" {{ old('semester', $course->semester ?? '') == '2' ? 'selected' : '' }}>2nd Semester</option>
                        </x-form.select>
                    </x-form.field>
                </div>

                {{-- Passing Mark + Class Hours --}}
                <div x-data="{ hasLab: '{{ old('has_lec_lab', isset($course) ? ($course->has_lec_lab ? '1' : '0') : '0') }}' === '1' }"
                     x-on:change.capture="$nextTick(() => { const el = document.querySelector('[name=has_lec_lab]:checked'); hasLab = el?.value === '1'; })"
                     class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <x-form.field label="Passing Mark" for="passing_mark">
                        <x-form.select id="passing_mark" name="passing_mark">
                            @foreach(['50.00'=>'50%','55.00'=>'55%','60.00'=>'60%','65.00'=>'65%','70.00'=>'70%','75.00'=>'75%','80.00'=>'80%'] as $val => $label)
                                <option value="{{ $val }}" {{ old('passing_mark', number_format($course->passing_mark ?? 60, 2, '.', '')) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </x-form.select>
                    </x-form.field>
                    <x-form.field label="LEC Class Hours" for="lec_class_hours">
                        <x-form.select id="lec_class_hours" name="lec_class_hours">
                            @foreach(['1 hr','1 hr and 30 min','2 hr','2 hr and 30 min','3 hr'] as $h)
                                <option value="{{ $h }}" {{ old('lec_class_hours', $course->lec_class_hours ?? '3 hr') == $h ? 'selected' : '' }}>{{ $h }}</option>
                            @endforeach
                        </x-form.select>
                    </x-form.field>
                    <div x-show="hasLab" x-cloak>
                        <x-form.field label="LAB Class Hours" for="lab_class_hours">
                            <x-form.select id="lab_class_hours" name="lab_class_hours">
                                @foreach(['1 hr','1 hr and 30 min','2 hr','2 hr and 30 min','3 hr'] as $h)
                                    <option value="{{ $h }}" {{ old('lab_class_hours', $course->lab_class_hours ?? '3 hr') == $h ? 'selected' : '' }}>{{ $h }}</option>
                                @endforeach
                            </x-form.select>
                        </x-form.field>
                    </div>
                </div>

                {{-- Prerequisite + Corequisite --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.field label="Prerequisite (None if not applicable)" for="prerequisite">
                        <x-form.input id="prerequisite" type="text" name="prerequisite"
                            value="{{ old('prerequisite', $course->prerequisite ?? '') }}" />
                    </x-form.field>
                    <x-form.field label="Corequisite (None if not applicable)" for="corequisite">
                        <x-form.input id="corequisite" type="text" name="corequisite"
                            value="{{ old('corequisite', $course->corequisite ?? '') }}" />
                    </x-form.field>
                </div>
            </div>
        </div>

        {{-- ── Section: PO Mapping ──────────────────────────────────────────── --}}
        @if ($program)
            <div class="rounded-[16px] border border-[#e4e4e7] bg-white overflow-hidden"
                 style="box-shadow: 0 1px 6px rgba(0,0,0,0.04);">

                <div class="px-5 py-3 border-b border-[#e4e4e7] bg-[#f4f4f5] flex items-center justify-between gap-3">
                    <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#52525b]">
                        Program Outcomes Mapping
                    </p>
                    <span class="text-[11px] text-[#a1a1aa] whitespace-nowrap shrink-0">
                        <strong class="text-[#52525b]">IED:</strong> I – Introductory · E – Enabling · D – Demonstrating
                    </span>
                </div>

                @if ($programOutcomes->isEmpty())
                    <div class="p-5">
                        <x-empty-state
                            icon="bx-notepad"
                            title="No program outcomes yet"
                            :message="'No outcomes defined for ' . $program->name . '. Define them in the program settings first.'" />
                    </div>
                @else
                    <x-table.container class="rounded-none border-0 shadow-none">
                        <x-table.table id="courseFormMapping" class="w-full table-fixed">
                            <x-table.head>
                                <x-table.row>
                                    <x-table.th class="w-20">PO</x-table.th>
                                    <x-table.th>Program Outcome</x-table.th>
                                    <x-table.th class="w-40 text-center">IED Level</x-table.th>
                                </x-table.row>
                            </x-table.head>
                            <x-table.body>
                                @foreach ($programOutcomes as $outcome)
                                    <x-table.row hover>
                                        <x-table.td class="font-mono font-bold text-[#18181b]">
                                            {{ $outcome->po_code }}
                                        </x-table.td>
                                        <x-table.td class="leading-relaxed">
                                            {{ $outcome->po_text }}
                                        </x-table.td>
                                        <x-table.td>
                                            @php $selected = $poSelections[$outcome->id] ?? null; @endphp
                                            <x-form.radio
                                                :name="'po_mapping[' . $outcome->id . ']'"
                                                :options="['I' => 'I', 'E' => 'E', 'D' => 'D']"
                                                :value="$selected"
                                                class="m-0 shrink-0" />
                                        </x-table.td>
                                    </x-table.row>
                                @endforeach
                            </x-table.body>
                        </x-table.table>
                    </x-table.container>
                    <p class="px-5 py-3 text-[11px] text-[#a1a1aa] border-t border-[#f4f4f5]">
                        Leave blank if this outcome does not apply to this course.
                    </p>
                @endif
            </div>
        @endif

        {{-- Form actions --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <x-button href="{{ route('courses.index') }}" variant="cancel">
                <i class="bx bx-arrow-back"></i> Cancel
            </x-button>

            <div class="flex gap-2">
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
