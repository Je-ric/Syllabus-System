@extends('layouts.app')

@section('content')
<div class="p-6 space-y-4">

    <h1 class="text-2xl font-bold mb-6">Academic Structure Management</h1>

    {{-- ADD COLLEGE --}}
    @include('AcademicStructure.partials.college-form')

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
@endsection

<script>
function toggle(id) {
    const element = document.getElementById(id);
    element.classList.toggle('hidden');

    // Auto-open parent accordion if form is being shown
    if (!element.classList.contains('hidden')) {
        const parentDetails = element.closest('details');
        if (parentDetails) {
            parentDetails.open = true;
        }
    }
}

// College accordion - only one open at a time
document.addEventListener('DOMContentLoaded', function() {
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
});

// Department accordion - only one open at a time within each college
document.addEventListener('DOMContentLoaded', function() {
    const collegeDetails = document.querySelectorAll('#collegeAccordions > details');

    collegeDetails.forEach(college => {
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


