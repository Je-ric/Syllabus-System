@extends('layouts.app')

@section('content')

    <x-header-with-button
        title="Academic Structure Management"
        description="Manage colleges, departments, and academic programs across the institution">
        <x-button variant="add-button" onclick="document.getElementById('addCollegeModal').showModal()">
            <i class="bx bx-plus"></i> Add College
        </x-button>
    </x-header-with-button>

    <div id="collegeAccordions" class="space-y-3">
        @forelse ($colleges as $college)
            <details class="bg-white/90 border border-slate-200/80 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                <summary class="flex items-center justify-between p-4 cursor-pointer select-none list-none">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <i class="bx bx-chevron-right text-xl text-slate-400 chevron-icon transition-transform duration-200"></i>
                        <i class="bx bxs-school text-xl text-emerald-600 shrink-0"></i>
                        <span class="font-semibold text-slate-800 truncate">{{ $college->name }}</span>
                    </div>
                    {{-- table-edit: compact blue button — correct for an inline edit toggle --}}
                    <x-button type="button" variant="table-confirm" class="shrink-0 ml-3"
                        onclick="event.stopPropagation(); toggle('editCollege{{ $college->id }}')"
                        title="Edit college">
                        <i class="bx bx-edit"></i>
                    </x-button>
                </summary>

                <div class="px-4 pb-4 space-y-4">

                    {{-- Edit college form — amber "editing" cue (same across all levels) --}}
                    <div id="editCollege{{ $college->id }}"
                        class="hidden rounded-xl border border-amber-200 bg-amber-50/80 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-amber-700 mb-3">
                            Edit College
                        </p>
                        <form method="POST" action="{{ route('college.update', $college) }}" class="space-y-3">
                            @csrf
                            @method('PUT')
                            <div>
                                <x-form.label for="collegeName{{ $college->id }}" variant="title" isRequired>
                                    College Name
                                </x-form.label>
                                <x-form.input
                                    id="collegeName{{ $college->id }}"
                                    name="name"
                                    value="{{ $college->name }}"
                                    class="mt-1"
                                    required />
                            </div>
                            <div class="flex gap-2">
                                <x-button type="submit" variant="save">
                                    <i class="bx bx-save"></i> Save
                                </x-button>
                                <x-button type="button" variant="cancel"
                                    onclick="toggle('editCollege{{ $college->id }}')">
                                    Cancel
                                </x-button>
                            </div>
                        </form>
                    </div>

                    {{-- Add department — outline variant: lighter than add-button, appropriate inside an accordion --}}
                    <x-button variant="outline" onclick="openAddDepartmentModal({{ $college->id }})">
                        <i class="bx bx-plus"></i> Add Department
                    </x-button>

                    {{-- Departments --}}
                    <div class="ml-6 space-y-3">
                        @forelse ($departments->where('college_id', $college->id) as $dept)
                            <details class="bg-slate-50/80 border border-slate-200 rounded-xl shadow-sm">
                                <summary class="flex items-center justify-between p-3 cursor-pointer select-none list-none">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <i class="bx bx-chevron-right text-lg text-slate-400 chevron-icon transition-transform duration-200"></i>
                                        {{-- Consistent icon color: slate-600 (neutral, not rose) --}}
                                        <i class="bx bx-building text-lg text-slate-600 shrink-0"></i>
                                        <span class="font-medium text-slate-700 truncate">{{ $dept->name }}</span>
                                    </div>
                                    <x-button type="button" variant="table-confirm" class="shrink-0 ml-3"
                                        onclick="event.stopPropagation(); toggle('editDept{{ $dept->id }}')"
                                        title="Edit department">
                                        <i class="bx bx-edit"></i>
                                    </x-button>
                                </summary>

                                <div class="px-3 pb-3 space-y-4">

                                    {{-- Edit department form — same amber cue as college --}}
                                    <div id="editDept{{ $dept->id }}"
                                        class="hidden rounded-xl border border-amber-200 bg-amber-50/80 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-amber-700 mb-3">
                                            Edit Department
                                        </p>
                                        <form method="POST" action="{{ route('department.update', $dept) }}" class="space-y-3">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="college_id" value="{{ $college->id }}">
                                            <div>
                                                <x-form.label for="deptName{{ $dept->id }}" variant="title" isRequired>
                                                    Department Name
                                                </x-form.label>
                                                <x-form.input
                                                    id="deptName{{ $dept->id }}"
                                                    name="name"
                                                    value="{{ $dept->name }}"
                                                    class="mt-1"
                                                    required />
                                            </div>
                                            <div class="flex gap-2">
                                                <x-button type="submit" variant="save">
                                                    <i class="bx bx-save"></i> Save
                                                </x-button>
                                                <x-button type="button" variant="cancel"
                                                    onclick="toggle('editDept{{ $dept->id }}')">
                                                    Cancel
                                                </x-button>
                                            </div>
                                        </form>
                                    </div>

                                    {{-- Add program --}}
                                    <x-button variant="outline" onclick="openAddProgramModal({{ $dept->id }})">
                                        <i class="bx bx-plus"></i> Add Program
                                    </x-button>

                                    {{-- Programs --}}
                                    <div class="ml-6 space-y-2">
                                        @forelse ($dept->programs as $program)
                                            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

                                                <div class="flex items-start justify-between p-3 gap-3">
                                                    <div class="flex gap-3 flex-1 min-w-0">
                                                        <i class="bx bx-book-open text-lg text-sky-600 mt-0.5 shrink-0"></i>
                                                        <div class="min-w-0">
                                                            <p class="font-medium text-slate-800 truncate">
                                                                {{ $program->name }}
                                                            </p>
                                                            <div class="mt-1.5 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-slate-500">
                                                                <span>
                                                                    <span class="font-medium text-slate-600">BOR No:</span>
                                                                    {{ $program->bor_approval_no }}
                                                                </span>
                                                                <span>
                                                                    <span class="font-medium text-slate-600">Approved:</span>
                                                                    {{ \Carbon\Carbon::parse($program->bor_approval_date)->format('M d, Y') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <x-button type="button" variant="table-confirm" class="shrink-0"
                                                        onclick="toggle('editProgram{{ $program->id }}')"
                                                        title="Edit program">
                                                        <i class="bx bx-edit"></i>
                                                    </x-button>
                                                </div>

                                                {{-- Edit program form — same amber cue --}}
                                                <div id="editProgram{{ $program->id }}"
                                                    class="hidden border-t border-amber-200 bg-amber-50/80 p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-amber-700 mb-3">
                                                        Edit Program
                                                    </p>
                                                    <form method="POST" action="{{ route('program.update', $program) }}" class="space-y-3">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="department_id" value="{{ $dept->id }}">
                                                        <div>
                                                            <x-form.label for="programName{{ $program->id }}" variant="title" isRequired>
                                                                Program Name
                                                            </x-form.label>
                                                            <x-form.input
                                                                id="programName{{ $program->id }}"
                                                                name="name"
                                                                value="{{ $program->name }}"
                                                                class="mt-1"
                                                                required />
                                                        </div>
                                                        <div>
                                                            <x-form.label for="borNo{{ $program->id }}" variant="title" isRequired>
                                                                BOR Approval No.
                                                            </x-form.label>
                                                            <x-form.input
                                                                id="borNo{{ $program->id }}"
                                                                name="bor_approval_no"
                                                                value="{{ $program->bor_approval_no }}"
                                                                class="mt-1"
                                                                required />
                                                        </div>
                                                        <div>
                                                            <x-form.label for="borDate{{ $program->id }}" variant="date" isRequired>
                                                                BOR Date Approval
                                                            </x-form.label>
                                                            <x-form.input
                                                                id="borDate{{ $program->id }}"
                                                                type="date"
                                                                name="bor_approval_date"
                                                                value="{{ $program->bor_approval_date }}"
                                                                class="mt-1"
                                                                required />
                                                        </div>
                                                        <div class="flex gap-2">
                                                            <x-button type="submit" variant="save">
                                                                <i class="bx bx-save"></i> Save
                                                            </x-button>
                                                            <x-button type="button" variant="cancel"
                                                                onclick="toggle('editProgram{{ $program->id }}')">
                                                                Cancel
                                                            </x-button>
                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                        @empty
                                            <p class="text-xs text-slate-400 italic py-1 pl-1">
                                                No programs yet — add one above.
                                            </p>
                                        @endforelse
                                    </div>

                                </div>
                            </details>
                        @empty
                            <p class="text-xs text-slate-400 italic py-1 pl-1">
                                No departments yet — add one above.
                            </p>
                        @endforelse
                    </div>

                </div>
            </details>
        @empty
            <x-empty-state
                icon="bxs-school"
                title="No colleges yet"
                message="Start by adding the first college using the button above.">
                <x-button variant="add-button" onclick="document.getElementById('addCollegeModal').showModal()">
                    <i class="bx bx-plus"></i> Add College
                </x-button>
            </x-empty-state>
        @endforelse
    </div>

    @include('AcademicStructure.modals.addCollegeModal')
    @include('AcademicStructure.modals.addDepartmentModal')
    @include('AcademicStructure.modals.addProgramModal')

@endsection

@push('scripts')
<script>
function toggle(id) {
    const el = document.getElementById(id);
    if (el) el.classList.toggle('hidden');
}

function closeAllForms(parent) {
    parent.querySelectorAll('div[id^="edit"]').forEach(el => el.classList.add('hidden'));
}

document.addEventListener('DOMContentLoaded', () => {
    const collegeAccordions = document.querySelectorAll('#collegeAccordions > details');

    collegeAccordions.forEach(details => {
        // Rotate chevron on open/close
        const chevron = details.querySelector(':scope > summary .chevron-icon');

        details.addEventListener('toggle', function () {
            if (chevron) chevron.style.transform = this.open ? 'rotate(90deg)' : '';

            if (!this.open) {
                closeAllForms(this);
            } else {
                // Collapse sibling college accordions
                collegeAccordions.forEach(other => {
                    if (other !== this && other.open) {
                        other.open = false;
                        closeAllForms(other);
                    }
                });

                // Attach department accordion toggle listeners (lazy — runs once per open)
                this.querySelectorAll(':scope > div > div > details').forEach(deptDetails => {
                    if (deptDetails._listenerAttached) return;
                    deptDetails._listenerAttached = true;

                    const deptChevron = deptDetails.querySelector(':scope > summary .chevron-icon');

                    deptDetails.addEventListener('toggle', function () {
                        if (deptChevron) deptChevron.style.transform = this.open ? 'rotate(90deg)' : '';
                        if (!this.open) closeAllForms(this);
                    });
                });
            }
        });
    });
});

function openAddDepartmentModal(collegeId) {
    const input = document.getElementById('addDepartment_college_id');
    if (input) input.value = collegeId;
    document.getElementById('addDepartmentModal').showModal();
}

function openAddProgramModal(departmentId) {
    const input = document.getElementById('addProgram_department_id');
    if (input) input.value = departmentId;
    document.getElementById('addProgramModal').showModal();
}
</script>
@endpush
