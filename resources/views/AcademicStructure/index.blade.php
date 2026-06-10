@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-buildings"
        title="Academic Structure Management"
        desc="Manage colleges, departments, and academic programs across the institution">
        <x-button variant="outline" onclick="collapseAll()">
            <i class="bx bx-collapse-vertical"></i> Collapse All
        </x-button>
        <x-button variant="outline" onclick="expandAll()">
            <i class="bx bx-expand-vertical"></i> Expand All
        </x-button>
        <x-button variant="add-button"
                onclick="document.getElementById('addCollegeModal').showModal()">
            <i class="bx bx-plus"></i> Add College
        </x-button>
    </x-page-header>

    <x-panel>
        <div id="collegeAccordions" class="space-y-3">
            @forelse ($colleges as $college)
                <details class="bg-white border border-[#e2e8f0] rounded-xl overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                    <summary class="group flex items-center justify-between p-4 cursor-pointer select-none list-none hover:bg-[#f8fafc] transition-colors">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <i class="bx bx-chevron-right text-xl text-[#94a3b8] chevron-icon transition-transform duration-200"></i>
                            <i class="bx bxs-school text-xl text-[#16a34a] shrink-0"></i>
                            <span class="text-[13px] font-bold text-[#0f172a] truncate">{{ $college->name }}</span>
                        </div>
                        <div class="flex gap-1 shrink-0 ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                            <x-button type="button" variant="table-confirm"
                                onclick="event.stopPropagation(); document.getElementById('updateCollegeModal_{{ $college->id }}').showModal()"
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

                        <div class="flex items-center justify-end gap-2 py-1">
                            <button type="button"
                                onclick="openAddDepartmentModal({{ $college->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold
                                       text-[#16a34a] border border-dashed border-[#86efac]
                                       bg-white hover:bg-[#f0fdf4] hover:border-[#16a34a] transition">
                                <i class="bx bx-plus text-sm leading-none"></i> Add Department
                            </button>
                        </div>

                        {{-- Departments --}}
                        <div class="ml-6 space-y-3">
                            @forelse ($departments->where('college_id', $college->id) as $dept)
                                <details class="bg-[#f8fafc] border border-[#e2e8f0] rounded-xl overflow-hidden">
                                    <summary class="group flex items-center justify-between p-3 cursor-pointer select-none list-none hover:bg-[#f0fdf4] transition-colors">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <i class="bx bx-chevron-right text-lg text-[#94a3b8] chevron-icon transition-transform duration-200"></i>
                                            <i class="bx bx-building text-lg text-[#475569] shrink-0"></i>
                                            <span class="text-[13px] font-semibold text-[#0f172a] truncate">{{ $dept->name }}</span>
                                        </div>
                                        <div class="flex gap-1 shrink-0 ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                            <x-button type="button" variant="table-confirm"
                                                onclick="event.stopPropagation(); document.getElementById('updateDepartmentModal_{{ $dept->id }}').showModal()"
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

                                        <div class="flex items-center justify-end gap-2 py-1">
                                            <button type="button"
                                                onclick="openAddProgramModal({{ $dept->id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold
                                                       text-[#16a34a] border border-dashed border-[#86efac]
                                                       bg-white hover:bg-[#f0fdf4] hover:border-[#16a34a] transition">
                                                <i class="bx bx-plus text-sm leading-none"></i> Add Program
                                            </button>
                                        </div>

                                        {{-- Programs --}}
                                        <div class="ml-6 space-y-2">
                                            @forelse ($dept->programs as $program)
                                                <div class="bg-white border border-[#e2e8f0] rounded-xl overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

                                                <div class="flex items-start justify-between p-3 gap-3 group">
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
                                                        <div class="flex gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                                            <x-button type="button" variant="table-confirm"
                                                                onclick="document.getElementById('updateProgramModal_{{ $program->id }}').showModal()"
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
        @include('AcademicStructure.modals.updateCollegeModal', ['college' => $college])
        @include('AcademicStructure.modals.deleteCollegeModal', ['college' => $college])
        @foreach ($departments->where('college_id', $college->id) as $dept)
            @include('AcademicStructure.modals.updateDepartmentModal', ['dept' => $dept])
            @include('AcademicStructure.modals.deleteDepartmentModal', ['dept' => $dept])
            @foreach ($dept->programs as $program)
                @include('AcademicStructure.modals.updateProgramModal', ['program' => $program])
                @include('AcademicStructure.modals.deleteProgramModal', ['program' => $program])
            @endforeach
        @endforeach
    @endforeach

@endsection

@push('scripts')
<script>
function expandAll() {
    document.querySelectorAll('#collegeAccordions details').forEach(d => { d.open = true; });
}

function collapseAll() {
    document.querySelectorAll('#collegeAccordions details').forEach(d => {
        d.open = false;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const collegeAccordions = document.querySelectorAll('#collegeAccordions > details');

    collegeAccordions.forEach(details => {
        // Rotate chevron on open/close
        const chevron = details.querySelector(':scope > summary .chevron-icon');

        details.addEventListener('toggle', function () {
            if (chevron) chevron.style.transform = this.open ? 'rotate(90deg)' : '';

            if (this.open) {
                // Collapse sibling college accordions
                collegeAccordions.forEach(other => {
                    if (other !== this && other.open) {
                        other.open = false;
                    }
                });

                // Attach department accordion toggle listeners (lazy — runs once per open)
                this.querySelectorAll(':scope > div > div > details').forEach(deptDetails => {
                    if (deptDetails._listenerAttached) return;
                    deptDetails._listenerAttached = true;

                    const deptChevron = deptDetails.querySelector(':scope > summary .chevron-icon');

                    deptDetails.addEventListener('toggle', function () {
                        if (deptChevron) deptChevron.style.transform = this.open ? 'rotate(90deg)' : '';
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
