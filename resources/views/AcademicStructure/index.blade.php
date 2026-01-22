{{-- @extends('layouts.app')

@section('content')
    <div class="p-6 space-y-10 bg-white text-black">

        <h1 class="text-xl font-bold">Academic Structure Management</h1>

        ADD COLLEGE (grandparent)
        <div class="border p-4 rounded">
            <button class="bg-black text-white px-4 py-2 rounded"
                    onclick="toggleAddCollegeForm()"
                    id="collegeAdd">
                    Add College
            </button>
            <div id="addCollegeForm" class="mt-4 hidden">
                <form method="POST"
                    action="{{ route('college.store') }}">
                    @csrf
                    <input type="text"
                            name="name"
                            placeholder="College Name"
                            class="border p-2 rounded w-1/3"
                            required>
                    <button class="bg-black text-white px-4 py-2 rounded ml-2">
                        Add College
                    </button>
                    <button type="button"
                            onclick="toggleAddCollegeForm()"
                            class="ml-2 bg-gray-500 text-white px-4 py-2 rounded">
                            Cancel
                    </button>
                </form>
            </div>
        </div>

        <div class="border p-6 rounded space-y-6">
            COLLEGE ACCORDIONS
            @foreach ($colleges as $college)
                <div class="border p-4 rounded">
                    <details>
                        <summary class="font-semibold cursor-pointer text-lg flex justify-between items-center">
                            <span>
                                {{ $loop->iteration }}.   {{ $college->name }}
                            </span>
                            <button type="button"
                                    class="text-sm bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
                                    onclick="event.stopPropagation(); toggleEditCollegeForm({{ $college->id }})">
                                Edit
                            </button>
                        </summary>

                        Edit College Form
                        <div id="editCollegeForm{{ $college->id }}" class="hidden bg-gray-100 p-4 rounded mt-2 mb-4">
                            <form method="POST" action="{{ route('college.update', $college) }}">
                                @csrf
                                @method('PUT')
                                <input type="text"
                                        name="name"
                                        value="{{ $college->name }}"
                                        placeholder="College Name"
                                        class="border p-2 rounded w-1/3"
                                        required>
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded ml-2">
                                    Save
                                </button>
                                <button type="button"
                                        onclick="toggleEditCollegeForm({{ $college->id }})"
                                        class="ml-2 bg-gray-500 text-white px-4 py-2 rounded">
                                        Cancel
                                </button>
                            </form>
                        </div>

                        <div class="ml-6 mt-4">

                            Add department inside college (parent)
                            <button class="bg-black text-white px-4 py-2 rounded mb-4"
                                    onclick="toggleAddDeptForm({{ $college->id }})"
                                    id="departmentAdd{{ $college->id }}">
                                    Add Department
                            </button>

                            <div id="addDeptForm{{ $college->id }}" class="hidden mb-4">
                                <form method="POST"
                                    action="{{ route('department.store') }}">
                                    @csrf
                                    <input type="hidden"
                                            name="college_id"
                                            value="{{ $college->id }}">
                                    <input type="text"
                                            name="name"
                                            placeholder="Department Name"
                                            class="border p-2 rounded w-1/3" required>
                                    <button class="bg-black text-white px-4 py-2 rounded ml-2">
                                        Add Department
                                    </button>
                                    <button type="button"
                                            onclick="toggleAddDeptForm({{ $college->id }})"
                                            class="ml-2 bg-gray-500 text-white px-4 py-2 rounded">
                                            Cancel
                                    </button>
                                </form>
                            </div>

                            DEPARTMENTS
                            @php
                                $collegeDepartments = $departments->where('college_id', $college->id);
                            @endphp

                            @if ($collegeDepartments->count() > 0)
                                @foreach ($collegeDepartments as $dept)
                                    <details class="mb-4 border-t pt-2">
                                        <summary class="cursor-pointer font-medium flex justify-between items-center">
                                            <span>
                                                <i class="bx bx-building"></i>
                                                {{ $dept->name }}
                                            </span>
                                            <button type="button"
                                                    class="text-sm bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
                                                    onclick="event.stopPropagation(); toggleEditDeptForm({{ $dept->id }})">
                                                Edit
                                            </button>
                                        </summary>

                                        Edit Department Form
                                        <div id="editDeptForm{{ $dept->id }}" class="hidden bg-gray-100 p-4 rounded mt-2 mb-4">
                                            <form method="POST" action="{{ route('department.update', $dept) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="text"
                                                        name="name"
                                                        value="{{ $dept->name }}"
                                                        placeholder="Department Name"
                                                        class="border p-2 rounded w-1/3"
                                                        required>
                                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded ml-2">
                                                    Save
                                                </button>
                                                <button type="button"
                                                        onclick="toggleEditDeptForm({{ $dept->id }})"
                                                        class="ml-2 bg-gray-500 text-white px-4 py-2 rounded">
                                                        Cancel
                                                </button>
                                            </form>
                                        </div>

                                        <div class="ml-6 mt-2">

                                            Add program inside department (grandchild)
                                            <button class="bg-black text-white px-4 py-2 rounded mb-4"
                                                    onclick="toggleAddProgramForm({{ $dept->id }})"
                                                    id="programAdd{{ $dept->id }}">
                                                    Add Program
                                            </button>

                                            <div id="addProgramForm{{ $dept->id }}" class="hidden mb-4">
                                                <form method="POST" action="{{ route('program.store') }}">
                                                    @csrf
                                                    <input type="hidden"
                                                            name="department_id"
                                                            value="{{ $dept->id }}">
                                                    <input type="text"
                                                            name="name"
                                                            placeholder="Program Name"
                                                            class="border p-2 rounded w-1/3"
                                                            required>
                                                    <input type="text"
                                                            name="bor_approval_no"
                                                            placeholder="BOR Approval No"
                                                            class="border p-2 rounded ml-2"
                                                            required>
                                                    <input type="date"
                                                            name="bor_approval_date"
                                                            placeholder="BOR Approval Date"
                                                            class="border p-2 rounded ml-2"
                                                            required>
                                                    <button class="bg-black text-white px-4 py-2 rounded ml-2">
                                                        Add Program
                                                    </button>
                                                    <button type="button"
                                                            onclick="toggleAddProgramForm({{ $dept->id }})"
                                                            class="ml-2 bg-gray-500 text-white px-4 py-2 rounded">
                                                        Cancel
                                                    </button>
                                                </form>
                                            </div>

                                            PROGRAMS
                                            <ul class="list-disc pl-5 space-y-2">
                                                @foreach ($dept->programs as $program)
                                                    <li class="flex items-start gap-2">
                                                        <i class="bx bx-book-open"></i>
                                                        <div class="flex-1">
                                                            <div class="flex justify-between items-start mb-1">
                                                                <strong>{{ $program->name }}</strong>
                                                                <button type="button"
                                                                        class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600"
                                                                        onclick="toggleEditProgramForm({{ $program->id }})">
                                                                    Edit
                                                                </button>
                                                            </div>

                                                            Edit Program Form
                                                            <div id="editProgramForm{{ $program->id }}" class="hidden bg-gray-100 p-3 rounded mb-2">
                                                                <form method="POST" action="{{ route('program.update', $program) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="space-y-2">
                                                                        <input type="text"
                                                                                name="name"
                                                                                value="{{ $program->name }}"
                                                                                placeholder="Program Name"
                                                                                class="border p-2 rounded w-full text-sm"
                                                                                required>
                                                                        <input type="text"
                                                                                name="bor_approval_no"
                                                                                value="{{ $program->bor_approval_no }}"
                                                                                placeholder="BOR Approval No"
                                                                                class="border p-2 rounded w-full text-sm"
                                                                                required>
                                                                        <input type="date"
                                                                                name="bor_approval_date"
                                                                                value="{{ $program->bor_approval_date }}"
                                                                                class="border p-2 rounded w-full text-sm"
                                                                                required>
                                                                        <div class="flex gap-2">
                                                                            <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-sm">
                                                                                Save
                                                                            </button>
                                                                            <button type="button"
                                                                                    onclick="toggleEditProgramForm({{ $program->id }})"
                                                                                    class="bg-gray-500 text-white px-3 py-1 rounded text-sm">
                                                                                    Cancel
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>

                                                            Display Program Details
                                                            <div id="programDetails{{ $program->id }}">
                                                                <b>BOR Approval No:</b> {{ $program->bor_approval_no }}<br>
                                                                <b>BOR Approval Date:</b> {{ \Carbon\Carbon::parse($program->bor_approval_date)->format('F d, Y') }}
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </details>
                                @endforeach
                            @else
                                <p class="text-gray-600">No departments</p>
                            @endif
                        </div>
                    </details>
                </div>
            @endforeach
       </div>

    </div>

    <script>
        function toggleAddCollegeForm() {
            const form = document.getElementById('addCollegeForm');
            form.classList.toggle('hidden');
            document.getElementById('collegeAdd').classList.toggle('hidden');
        }

        function toggleEditCollegeForm(collegeId) {
            const form = document.getElementById('editCollegeForm' + collegeId);
            form.classList.toggle('hidden');
        }

        function toggleAddDeptForm(collegeId) {
            const form = document.getElementById('addDeptForm' + collegeId);
            form.classList.toggle('hidden');
            document.getElementById('departmentAdd' + collegeId).classList.toggle('hidden');
        }

        function toggleEditDeptForm(deptId) {
            const form = document.getElementById('editDeptForm' + deptId);
            form.classList.toggle('hidden');
        }

        function toggleAddProgramForm(deptId) {
            const form = document.getElementById('addProgramForm' + deptId);
            form.classList.toggle('hidden');
            document.getElementById('programAdd' + deptId).classList.toggle('hidden');
        }

        function toggleEditProgramForm(programId) {
            const form = document.getElementById('editProgramForm' + programId);
            const details = document.getElementById('programDetails' + programId);
            form.classList.toggle('hidden');
            details.classList.toggle('hidden');
        }
    </script>
@endsection --}}

@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    <h1 class="text-xl font-bold">Academic Structure Management</h1>

    {{-- ADD COLLEGE --}}
    @include('AcademicStructure.partials.college-form')

    {{-- COLLEGES --}}
    @foreach ($colleges as $college)
        @include('AcademicStructure.partials.college-item', [
            'college' => $college,
            'departments' => $departments
        ])
    @endforeach

</div>
@endsection

<script>
function toggle(id) {
    document.getElementById(id).classList.toggle('hidden');
}
</script>


