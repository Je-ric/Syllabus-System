@extends('layouts.app')

@section('content')
    <div class="p-6 space-y-10 bg-white text-black">

        <h1 class="text-xl font-bold">Academic Structure Management</h1>

        {{-- ADD COLLEGE BUTTON --}}
        <div class="border p-4 rounded">
            <button class="bg-black text-white px-4 py-2 rounded" onclick="toggleAddCollegeForm()">Add College</button>
            <div id="addCollegeForm" class="mt-4 hidden">
                <form method="POST" action="{{ route('college.store') }}">
                    @csrf
                    <input type="text" name="name" placeholder="College Name" class="border p-2 rounded w-1/3"
                        required>
                    <button class="bg-black text-white px-4 py-2 rounded ml-2">
                        Add College
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
                        {{-- ADD DEPARTMENT BUTTON INSIDE COLLEGE --}}
                        <button class="bg-black text-white px-4 py-2 rounded mb-4"
                            onclick="toggleAddDeptForm({{ $college->id }})">Add Department</button>
                        <div id="addDeptForm{{ $college->id }}" class="hidden mb-4">
                            <form method="POST" action="{{ route('department.store') }}">
                                @csrf
                                <input type="hidden" name="college_id" value="{{ $college->id }}">
                                <input type="text" name="name" placeholder="Department Name"
                                    class="border p-2 rounded w-1/3" required>
                                <button class="bg-black text-white px-4 py-2 rounded ml-2">
                                    Add Department
                                </button>
                            </form>
                        </div>

                        {{-- DEPARTMENTS --}}
                        @php $collegeDepartments = $departments->where('college_id', $college->id) @endphp
                        @if ($collegeDepartments->count() > 0)
                            @foreach ($collegeDepartments as $dept)
                                <details class="mb-4 border-t pt-2">
                                    <summary class="cursor-pointer font-medium">{{ $dept->name }}</summary>
                                    <div class="ml-6 mt-2">
                                        {{-- ADD PROGRAM BUTTON INSIDE DEPARTMENT --}}
                                        <button class="bg-black text-white px-4 py-2 rounded mb-4"
                                            onclick="toggleAddProgramForm({{ $dept->id }})">Add Program</button>
                                        <div id="addProgramForm{{ $dept->id }}" class="hidden mb-4">
                                            <form method="POST" action="{{ route('program.store') }}">
                                                @csrf
                                                <input type="hidden" name="department_id" value="{{ $dept->id }}">
                                                <input type="text" name="name" placeholder="Program Name"
                                                    class="border p-2 rounded w-1/3" required>
                                                <input type="text" name="bor_approval_no" placeholder="BOR Approval No"
                                                    class="border p-2 rounded ml-2">
                                                <input type="date" name="bor_approval_date"
                                                    placeholder="BOR Approval Date" class="border p-2 rounded ml-2">
                                                <button class="bg-black text-white px-4 py-2 rounded ml-2">
                                                    Add Program
                                                </button>
                                            </form>
                                        </div>

                                        {{-- PROGRAMS --}}
                                        <ul class="list-disc pl-5">
                                            @foreach ($dept->programs as $program)
                                                <li>
                                                    <strong>{{ $program->name }}</strong><br>
                                                    BOR Approval No: {{ $program->bor_approval_no }}<br>
                                                    BOR Approval Date: {{ $program->bor_approval_date }}
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
        }

        function toggleAddDeptForm(collegeId) {
            const form = document.getElementById('addDeptForm' + collegeId);
            form.classList.toggle('hidden');
        }

        function toggleAddProgramForm(deptId) {
            const form = document.getElementById('addProgramForm' + deptId);
            form.classList.toggle('hidden');
        }
    </script>
@endsection
