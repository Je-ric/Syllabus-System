@extends('layouts.app')

@section('content')
    <div class="p-6 space-y-10 bg-white text-black">

        <h1 class="text-xl font-bold">Academic Structure Management</h1>

        {{-- ADD COLLEGE (grandparent) --}}
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

        {{-- COLLEGE ACCORDIONS --}}
        @foreach ($colleges as $college)
            <div class="border p-4 rounded">
                <details>
                    <summary class="font-semibold cursor-pointer text-lg">{{ $college->name }}</summary>
                    <div class="ml-6 mt-4">

                        {{-- Add department inside college (parent) --}}
                        <button class="bg-black text-white px-4 py-2 rounded mb-4"
                                onclick="toggleAddDeptForm({{ $college->id }})"
                                id="departmentAdd">
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

                        {{-- DEPARTMENTS --}}
                        @php
                            $collegeDepartments = $departments->where('college_id', $college->id);
                        @endphp

                        @if ($collegeDepartments->count() > 0)
                            @foreach ($collegeDepartments as $dept)
                                <details class="mb-4 border-t pt-2">
                                    <summary class="cursor-pointer font-medium">{{ $dept->name }}</summary>
                                    <div class="ml-6 mt-2">

                                        {{-- Add program inside department (grandchild) --}}
                                        <button class="bg-black text-white px-4 py-2 rounded mb-4"
                                                onclick="toggleAddProgramForm({{ $dept->id }})"
                                                id="programAdd">
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

                                        {{-- PROGRAMS --}}
                                        <ul class="list-disc pl-5">
                                            @foreach ($dept->programs as $program)
                                                <li>
                                                    <strong>{{ $program->name }}</strong><br>
                                                    <b>BOR Approval No:</b> {{ $program->bor_approval_no }}<br>
                                                    <b>BOR Approval Date:</b> {{ \Carbon\Carbon::parse($program->bor_approval_date)->format('F d, Y') }}
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

    <script>
        function toggleAddCollegeForm() {
            const form = document.getElementById('addCollegeForm');
            form.classList.toggle('hidden');
            document.getElementById('collegeAdd').classList.toggle('hidden');
        }

        function toggleAddDeptForm(collegeId) {
            const form = document.getElementById('addDeptForm' + collegeId);
            form.classList.toggle('hidden');
            document.getElementById('departmentAdd').classList.toggle('hidden');
        }

        function toggleAddProgramForm(deptId) {
            const form = document.getElementById('addProgramForm' + deptId);
            form.classList.toggle('hidden');
            document.getElementById('programAdd').classList.toggle('hidden');
        }
    </script>
@endsection
