@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-buildings"
        title="Academic Structure Management"
        desc="Manage colleges, departments, and academic programs across the institution">
        <x-button variant="add-button"
                onclick="document.getElementById('addCollegeModal').showModal()">
            <i class="bx bx-plus"></i> Add College
        </x-button>
    </x-page-header>

    <x-panel>
        <div id="collegeAccordions" class="space-y-3">
            @forelse ($colleges as $college)
                <details class="bg-white border border-[#e2e8f0] rounded-xl overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                    <summary class="flex items-center justify-between p-4 cursor-pointer select-none list-none hover:bg-[#f8fafc] transition-colors">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <i class="bx bx-chevron-right text-xl text-[#94a3b8] chevron-icon transition-transform duration-200"></i>
                            <i class="bx bxs-school text-xl text-[#16a34a] shrink-0"></i>
                            <span class="text-[13px] font-bold text-[#0f172a] truncate">{{ $college->name }}</span>
                        </div>
                        {{-- table-edit: compact blue button — correct for an inline edit toggle --}}
                        <div class="flex gap-1 shrink-0 ml-3">
                            <x-button type="button" variant="table-confirm"
                                onclick="event.stopPropagation(); toggle('editCollege{{ $college->id }}')"
                                title="Edit college">
                                <i class="bx bx-edit"></i>
                            </x-button>
                            <x-button type="button" variant="table-danger"
                                onclick="event.stopPropagation(); document.getElementById('deleteCollegeModal_{{ $college->id }}').showModal()"
                                title="Delete college">
                                <i class="bx bx-trash"></i>
                            </x-button>
                        </div>
                    </summary>

                    <div class="px-4 pb-4 space-y-4">

                        {{-- Edit college form — amber "editing" cue (same across all levels) --}}
                        <div id="editCollege{{ $college->id }}"
                            class="hidden rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-3">
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
                                <details class="bg-[#f8fafc] border border-[#e2e8f0] rounded-xl overflow-hidden">
                                    <summary class="flex items-center justify-between p-3 cursor-pointer select-none list-none hover:bg-[#f0fdf4] transition-colors">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <i class="bx bx-chevron-right text-lg text-[#94a3b8] chevron-icon transition-transform duration-200"></i>
                                            <i class="bx bx-building text-lg text-[#475569] shrink-0"></i>
                                            <span class="text-[13px] font-semibold text-[#0f172a] truncate">{{ $dept->name }}</span>
                                        </div>
                                        <div class="flex gap-1 shrink-0 ml-3">
                                            <x-button type="button" variant="table-confirm"
                                                onclick="event.stopPropagation(); toggle('editDept{{ $dept->id }}')"
                                                title="Edit department">
                                                <i class="bx bx-edit"></i>
                                            </x-button>
                                            <x-button type="button" variant="table-danger"
                                                onclick="event.stopPropagation(); document.getElementById('deleteDepartmentModal_{{ $dept->id }}').showModal()"
                                                title="Delete department">
                                                <i class="bx bx-trash"></i>
                                            </x-button>
                                        </div>
                                    </summary>

                                    <div class="px-3 pb-3 space-y-4">

                                        {{-- Edit department form — same amber cue as college --}}
                                        <div id="editDept{{ $dept->id }}"
                                            class="hidden rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4">
                                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-3">
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
                                                <div class="bg-white border border-[#e2e8f0] rounded-xl overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

                                                    <div class="flex items-start justify-between p-3 gap-3">
                                                        <div class="flex gap-3 flex-1 min-w-0">
                                                            <i class="bx bx-book-open text-lg text-[#16a34a] mt-0.5 shrink-0"></i>
                                                            <div class="min-w-0">
                                                                <p class="text-[13px] font-semibold text-[#0f172a] truncate">
                                                                    {{ $program->name }}
                                                                </p>
                                                                <div class="mt-1.5 flex flex-wrap gap-x-4 gap-y-0.5 text-[13px] text-[#475569]">
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
                                                        <div class="flex gap-1 shrink-0">
                                                            <x-button type="button" variant="table-confirm"
                                                                onclick="toggle('editProgram{{ $program->id }}')"
                                                                title="Edit program">
                                                                <i class="bx bx-edit"></i>
                                                            </x-button>
                                                            <x-button type="button" variant="table-danger"
                                                                onclick="document.getElementById('deleteProgramModal_{{ $program->id }}').showModal()"
                                                                title="Delete program">
                                                                <i class="bx bx-trash"></i>
                                                            </x-button>
                                                        </div>
                                                    </div>

                                                    {{-- Edit program form — same amber cue --}}
                                                    <div id="editProgram{{ $program->id }}"
                                                        class="hidden border-t border-[#e2e8f0] bg-[#f8fafc] p-4">
                                                        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-3">
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
                                                <p class="text-[13px] text-[#94a3b8] italic py-1 pl-1">
                                                    No programs yet — add one above.
                                                </p>
                                            @endforelse
                                        </div>

                                    </div>
                                </details>
                            @empty
                                <p class="text-[13px] text-[#94a3b8] italic py-1 pl-1">
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
    </x-panel>

    @include('AcademicStructure.modals.addCollegeModal')
    @include('AcademicStructure.modals.addDepartmentModal')
    @include('AcademicStructure.modals.addProgramModal')

    @foreach ($colleges as $college)
        @include('AcademicStructure.modals.deleteCollegeModal', ['college' => $college])
        @foreach ($departments->where('college_id', $college->id) as $dept)
            @include('AcademicStructure.modals.deleteDepartmentModal', ['dept' => $dept])
            @foreach ($dept->programs as $program)
                @include('AcademicStructure.modals.deleteProgramModal', ['program' => $program])
            @endforeach
        @endforeach
    @endforeach

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
