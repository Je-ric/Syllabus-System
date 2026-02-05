@extends('layouts.app')

@section('content')
<div class="p-6 space-y-4">

    <h1 class="text-2xl font-bold mb-6">Academic Structure Management</h1>

    {{-- ADD COLLEGE --}}
    <x-button variant="add-button" onclick="document.getElementById('addCollegeModal').showModal()">
        <i class="bx bx-plus"></i> Add College
    </x-button>

    <div id="collegeAccordions" class="space-y-3">
        @foreach ($colleges as $college)
            <details class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <summary class="flex items-center justify-between p-4 cursor-pointer select-none">
                    <div class="flex items-center gap-3 flex-1">
                        <i class="bx bx-chevron-down text-xl text-gray-500 chevron-icon"></i>
                        <i class="bx bxs-school text-2xl text-green-500"></i>
                        <span class="font-semibold text-lg text-gray-800">{{ $college->name }}</span>
                    </div>
                    <x-button type="button" variant="secondary" onclick="event.stopPropagation(); toggle('editCollege{{ $college->id }}')">
                        <i class="bx bx-edit"></i>
                    </x-button>
                </summary>

                <div class="px-4 pb-4 space-y-4">
                    {{-- EDIT COLLEGE --}}
                    <div id="editCollege{{ $college->id }}" class="hidden bg-green-50 border border-green-200 p-4 rounded-lg">
                        <form method="POST" action="{{ route('college.update', $college) }}">
                            @csrf
                            @method('PUT')
                            <div class="space-y-3">
                                <x-form.label for="collegeName{{ $college->id }}" variant="title" isRequired>College Name</x-form.label>
                                <x-form.input id="collegeName{{ $college->id }}" name="name" value="{{ $college->name }}" required />

                                <div class="flex gap-2 mt-2">
                                    <x-button type="submit" variant="save">Save</x-button>
                                    <x-button type="button" variant="cancel" onclick="toggle('editCollege{{ $college->id }}')">Cancel</x-button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- ADD DEPARTMENT --}}
                    <x-button variant="add-button" onclick="openAddDepartmentModal({{ $college->id }})">
                        <i class="bx bx-plus"></i> Add Department
                    </x-button>

                    {{-- DEPARTMENTS --}}
                    <div class="ml-8 space-y-3">
                        @foreach ($departments->where('college_id', $college->id) as $dept)
                            <details class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm">
                                <summary class="flex items-center justify-between p-3 cursor-pointer select-none">
                                    <div class="flex items-center gap-3 flex-1">
                                        <i class="bx bx-chevron-down text-lg text-gray-500 chevron-icon"></i>
                                        <i class="bx bx-building text-xl text-red-500"></i>
                                        <span class="font-medium text-gray-800">{{ $dept->name }}</span>
                                    </div>
                                    <x-button type="button" variant="secondary" onclick="event.stopPropagation(); toggle('editDept{{ $dept->id }}')">
                                        <i class="bx bx-edit"></i>
                                    </x-button>
                                </summary>

                                <div class="px-3 pb-3 space-y-4">
                                    {{-- EDIT DEPARTMENT --}}
                                    <div id="editDept{{ $dept->id }}" class="hidden bg-red-50 border border-red-200 p-4 rounded-lg">
                                        <form method="POST" action="{{ route('department.update', $dept) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="college_id" value="{{ $college->id }}">
                                            <div class="space-y-3">
                                                <x-form.label for="deptName{{ $dept->id }}" variant="title" isRequired>Department Name</x-form.label>
                                                <x-form.input id="deptName{{ $dept->id }}" name="name" value="{{ $dept->name }}" required />

                                                <div class="flex gap-2 mt-2">
                                                    <x-button type="submit" variant="save">Save</x-button>
                                                    <x-button type="button" variant="cancel" onclick="toggle('editDept{{ $dept->id }}')">Cancel</x-button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    {{-- ADD PROGRAM --}}
                                    <x-button variant="add-button" onclick="openAddProgramModal({{ $dept->id }})">
                                        <i class="bx bx-plus"></i> Add Program
                                    </x-button>

                                    {{-- PROGRAMS --}}
                                    <div class="ml-8 space-y-2">
                                        @foreach ($dept->programs as $program)
                                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                                <div class="flex items-start justify-between p-3">
                                                    <div class="flex gap-3 flex-1">
                                                        <i class="bx bx-book-open text-lg text-blue-500 mt-1"></i>
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-800">{{ $program->name }}</div>
                                                            <div class="text-sm text-gray-600 mt-1">
                                                                <span class="inline-block mr-3"><b>BOR No: </b>{{ $program->bor_approval_no }}</span><br>
                                                                <span class="inline-block"><b>BOR Date Approval: </b>{{ \Carbon\Carbon::parse($program->bor_approval_date)->format('F d, Y') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <x-button type="button" variant="secondary" onclick="toggle('editProgram{{ $program->id }}')">
                                                        <i class="bx bx-edit"></i>
                                                    </x-button>
                                                </div>

                                                {{-- EDIT PROGRAM --}}
                                                <div id="editProgram{{ $program->id }}" class="hidden border-t border-gray-200 bg-blue-50 p-4">
                                                    <form method="POST" action="{{ route('program.update', $program) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="department_id" value="{{ $dept->id }}">
                                                        <div class="space-y-3">
                                                            <x-form.label for="programName{{ $program->id }}" variant="title" isRequired>Program Name</x-form.label>
                                                            <x-form.input id="programName{{ $program->id }}" name="name" value="{{ $program->name }}" required />

                                                            <x-form.label for="borNo{{ $program->id }}" variant="title" isRequired>BOR Approval No</x-form.label>
                                                            <x-form.input id="borNo{{ $program->id }}" name="bor_approval_no" value="{{ $program->bor_approval_no }}" required />

                                                            <x-form.label for="borDate{{ $program->id }}" variant="date" isRequired>BOR Date Approval</x-form.label>
                                                            <x-form.input id="borDate{{ $program->id }}" type="date" name="bor_approval_date" value="{{ $program->bor_approval_date }}" required />

                                                            <div class="flex gap-2 mt-2">
                                                                <x-button type="submit" variant="save">Save</x-button>
                                                                <x-button type="button" variant="cancel" onclick="toggle('editProgram{{ $program->id }}')">Cancel</x-button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            </details>
                        @endforeach
                    </div>

                </div>
            </details>
        @endforeach
    </div>

</div>

@include('AcademicStructure.modals.addCollegeModal')
@include('AcademicStructure.modals.addDepartmentModal')
@include('AcademicStructure.modals.addProgramModal')
@endsection

<script>
function toggle(id) {
    const element = document.getElementById(id);
    if (!element) return;
    element.classList.toggle('hidden');
}

function closeAllForms(parent = document) {
    parent.querySelectorAll('div[id^="edit"]').forEach(el => el.classList.add('hidden'));
}

document.addEventListener('DOMContentLoaded', function() {
    const collegeAccordions = document.querySelectorAll('#collegeAccordions > details');

    collegeAccordions.forEach(details => {
        details.addEventListener('toggle', function() {
            if (!this.open) {
                // Close all edit forms within this accordion
                closeAllForms(this);
            } else {
                // Close other college accordions
                collegeAccordions.forEach(other => {
                    if (other !== this && other.open) {
                        other.open = false;
                        closeAllForms(other);
                    }
                });
            }

            // Close all child department forms if this college closes
            const departmentAccordions = details.querySelectorAll('div > details');
            departmentAccordions.forEach(dept => {
                dept.addEventListener('toggle', function() {
                    if (!this.open) closeAllForms(this);
                    else {
                        departmentAccordions.forEach(other => {
                            if (other !== this && other.open) {
                                other.open = false;
                                closeAllForms(other);
                            }
                        });
                    }
                });
            });
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
