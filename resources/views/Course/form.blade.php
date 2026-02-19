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
    <x-feedback-status.alert
        type="warning"
        message="Please select a program from the courses page before creating a course."
        class="mb-6"
    />
@endif

<form action="{{ $formAction }}" method="POST" id="courseForm">
    @csrf
    @if($formMethod !== 'POST')
        @method($formMethod)
    @endif

    <input type="hidden" name="program_id" value="{{ $program?->id ?? '' }}">
    <input type="hidden" name="confirmed_submission" id="confirmedSubmission" value="0">

    @if($program)
        <x-feedback-status.alert
            type="success"
            title="Program"
            :message="$program->name"
            class="mb-6"
        />
    @endif

    <div class="grid grid-cols-2 gap-4 mb-4 text-slate-800">
        <div>
            <x-form.label class="block">Course Code</x-form.label>
            <x-form.input type="text" name="code" class="mt-2" value="{{ old('code', $course->course_code ?? '') }}" required />
            @error('code')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <x-form.label class="block">Course Title</x-form.label>
            <x-form.input type="text" name="name" class="mt-2" value="{{ old('name', $course->course_title ?? '') }}" required />
            @error('name')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="mb-4">
        <x-form.label class="block">Course Description</x-form.label>
        <x-form.textarea name="description" class="mt-2" rows="3">{{ old('description', $course->course_description ?? '') }}</x-form.textarea>
        @error('description')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="grid grid-cols-4 gap-4 mb-4 text-slate-800">
        <div>
            <x-form.label class="block">Credit Units</x-form.label>
            <x-form.input type="number" name="credits" class="mt-2" value="{{ old('credits', $course->credit_units ?? '') }}" min="0" required />
            @error('credits')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <x-form.label class="block">Has Laboratory</x-form.label>
            @php $labValue = old('has_lec_lab', isset($course) ? ($course->has_lec_lab ? '1' : '0') : ''); @endphp
            <x-form.radio
                name="has_lec_lab"
                :options="['1' => 'Yes', '0' => 'No']"
                :value="$labValue"
            />
        </div>
        <div>
            <x-form.label class="block">Year Level</x-form.label>
            <x-form.select name="year_level" class="mt-2">
                <option value="">Select Year</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ old('year_level', $course->year_level ?? '') == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                @endfor
            </x-form.select>
        </div>
        <div>
            <x-form.label class="block">Semester</x-form.label>
            <x-form.select name="semester" class="mt-2">
                <option value="">Select Semester</option>
                <option value="1" {{ old('semester', $course->semester ?? '') == '1' ? 'selected' : '' }}>1st Semester</option>
                <option value="2" {{ old('semester', $course->semester ?? '') == '2' ? 'selected' : '' }}>2nd Semester</option>
            </x-form.select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4 text-slate-800">
        <div>
            <x-form.label class="block">Prerequisite</x-form.label>
            <x-form.input type="text" name="prerequisite" class="mt-2" value="{{ old('prerequisite', $course->prerequisite ?? '') }}" />
        </div>
        <div>
            <x-form.label class="block">Corequisite</x-form.label>
            <x-form.input type="text" name="corequisite" class="mt-2" value="{{ old('corequisite', $course->corequisite ?? '') }}" />
        </div>
    </div>

    @if($program)
        <hr class="my-6 border-slate-200">
        <div class="flex flex-wrap justify-between gap-3 mb-4">
            <h2 class="text-lg font-semibold text-slate-800">Program Outcomes Mapping</h2>
            <p class="text-sm text-slate-500"><b>Level:</b> I - Introductory, E - Enabling, D - Demonstrative</p>
        </div>

        @if($programOutcomes->isEmpty())
            <x-empty-state
                icon="bx-notepad"
                title="No program outcomes yet"
                :message="'No program outcomes defined for ' . $program->name . '. Please define outcomes first.'"
                class="mb-4"
            />
        @else
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <table class="w-full" id="courseFormMapping">
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
                                    <x-form.radio
                                        :name="'po_mapping[' . $outcome->id . ']'"
                                        :options="['I' => 'I', 'E' => 'E', 'D' => 'D']"
                                        :value="$selected"
                                        class="mt-0 gap-3"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="text-sm text-slate-500 mt-2">Select IED level for each outcome that applies to this course (leave blank if not applicable)</p>
        @endif
    @endif

    <div class="flex justify-between gap-2 mt-6">
        <x-button href="{{ route('courses.index') }}"
                variant="cancel">
                Cancel
        </x-button>
        <div>
            <x-button variant="cancel" type="button" onclick="resetRadioButtons()">Reset IED Levels</x-button>
            <x-button type="button" variant="save" onclick="openCourseConfirmModal()">
                <i class="bx bx-save"></i> {{ $submitLabel }}
            </x-button>
        </div>
    </div>
</form>

@if($formMethod === 'POST')
    @include('Course.modals.confirmCourseModal')
@else
    @include('Course.modals.confirmEditCourseModal')
@endif

<script>
    const isCreateCourseForm = @json($formMethod === 'POST');
    const confirmModalId = isCreateCourseForm ? 'confirmCourseModal' : 'confirmEditCourseModal';

    function resetRadioButtons(){
        const radios = document.querySelectorAll('#courseForm #courseFormMapping input[type="radio"]');
        radios.forEach(radio => radio.checked = false);
    }

    function openCourseConfirmModal() {
        const modal = document.getElementById(confirmModalId);
        if (modal) {
            modal.showModal();
        }
    }

    function confirmCourseSubmit() {
        const flag = document.getElementById('confirmedSubmission');
        if (flag) {
            flag.value = '1';
        }
        document.getElementById('courseForm').submit();
    }

    document.getElementById('courseForm').addEventListener('submit', function (event) {
        const flag = document.getElementById('confirmedSubmission');
        if (!flag || flag.value !== '1') {
            event.preventDefault();
            openCourseConfirmModal();
        }
    });
</script>
@endsection
