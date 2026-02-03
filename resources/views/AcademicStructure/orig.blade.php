@extends('layouts.app')

@section('content')
<div class="p-6 space-y-4">

    <h1 class="text-2xl font-bold mb-6">Academic Structure Management</h1>

    {{-- ADD COLLEGE --}}
    <x-button variant="add-button" onclick="document.getElementById('addCollegeModal').showModal()">
        <i class="bx bx-plus"></i> Add College
    </x-button>


    {{-- COLLEGES --}}
    <div id="collegeAccordions" class="space-y-3">
        @foreach ($colleges as $college)
            @include('AcademicStructure.partials.college-item', [
                'college' => $college,
                'departments' => $departments
            ])
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

    // Auto-open parent accordion if form is being shown
    if (!element.classList.contains('hidden')) {
        const parentDetails = element.closest('details');
        if (parentDetails) {
            parentDetails.open = true;
        }
    }
}

function openAddDepartmentModal(collegeId) {
    const input = document.getElementById('addDepartment_college_id');
    if (input) input.value = collegeId;
    const modal = document.getElementById('addDepartmentModal');
    if (modal) modal.showModal();
}

function openAddProgramModal(departmentId) {
    const input = document.getElementById('addProgram_department_id');
    if (input) input.value = departmentId;
    const modal = document.getElementById('addProgramModal');
    if (modal) modal.showModal();
}

document.addEventListener('DOMContentLoaded', function() {
    // College accordion - only one open at a time
    const collegeAccordions = document.querySelectorAll('#collegeAccordions > details');

    collegeAccordions.forEach(details => {
        details.addEventListener('toggle', function() {
            if (this.open) {
                collegeAccordions.forEach(other => {
                    if (other !== this && other.open) {
                        other.open = false;
                    }
                });
            }
        });
    });

    // Department accordion - only one open at a time within each college
    collegeAccordions.forEach(college => {
        const departmentAccordions = college.querySelectorAll('div > details');
        departmentAccordions.forEach(details => {
            details.addEventListener('toggle', function() {
                if (this.open) {
                    departmentAccordions.forEach(other => {
                        if (other !== this && other.open) {
                            other.open = false;
                        }
                    });
                }
            });
        });
    });
});
</script>


