@extends('layouts.app')

@section('content')

    {{-- HEADER --}}
    <div class="mb-8 border-b pb-4">
        <a href="{{ route('organizational.colleges.index') }}" class="text-sm text-black hover:underline">
            ← Back to Colleges
        </a>

        <h1 class="text-3xl font-bold text-black mt-3">
            {{ $college->name }}
        </h1>
        <p class="text-gray-600 text-sm mt-1">
            Manage department chairs and faculty members
        </p>
    </div>

    @if ($college->departments->isEmpty())
        <div class="border border-black rounded-lg p-10 text-center">
            <p class="text-gray-600">No departments found in this college.</p>
        </div>
    @else
        <div class="space-y-8">

            @foreach ($college->departments as $department)
                <div class="border border-black rounded-xl bg-white">

                    {{-- DEPARTMENT HEADER --}}
                    <div class="border-b border-black px-6 py-4 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-black">
                            {{ $department->name }}
                        </h2>

                        <button onclick="document.getElementById('assignFacultyModal-{{ $department->id }}').showModal()"
                            class="border border-black px-4 py-1.5 text-sm rounded-md hover:bg-black hover:text-white transition">
                            + Add Faculty
                        </button>
                    </div>


                    <div class="p-6 space-y-6">

                        {{-- CHAIR SECTION --}}
                        <div>
                            <h3 class="text-xs uppercase tracking-wider text-gray-500 mb-2">
                                Department Chair
                            </h3>

                            @if ($chairAssignments->get($department->id)?->first())
                                <div class="border border-black rounded-lg p-4 flex justify-between items-center">

                                    <div>
                                        <p class="font-semibold text-black">
                                            {{ $chairAssignments->get($department->id)->first()->user->name }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ $chairAssignments->get($department->id)->first()->user->email }}
                                        </p>
                                    </div>

                                    <form action="{{ route('organizational.remove-chair') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="department_id" value="{{ $department->id }}">
                                        <input type="hidden" name="user_id"
                                            value="{{ $chairAssignments->get($department->id)->first()->user->id }}">

                                        <button type="submit"
                                            class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-black transition">
                                            Delete Chair
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="border border-dashed border-black rounded-lg p-4 text-center mb-2">
                                    <p class="text-gray-500 text-sm">No chair assigned</p>
                                </div>

                                @if ($potentialChairs->count() > 0)
                                    <button
                                        onclick="document.getElementById('assignChairModal-{{ $department->id }}').showModal()"
                                        class="border border-black px-4 py-2 rounded-md bg-black text-white hover:bg-red-600 transition text-sm">
                                        Assign Chair
                                    </button>
                                @endif
                            @endif
                        </div>


                        {{-- FACULTY SECTION --}}
                        <div>
                            <h3 class="text-xs uppercase tracking-wider text-gray-500 mb-3">
                                Faculty Members
                            </h3>

                            @if ($facultyAssignments->get($department->id)?->count() > 0)
                                <div class="border border-black rounded-lg overflow-hidden">

                                    {{-- table header --}}
                                    <div class="grid grid-cols-3 bg-black text-white text-sm font-medium px-4 py-2">
                                        <div>Name</div>
                                        <div>Office</div>
                                        <div class="text-right">Action</div>
                                    </div>

                                    {{-- scrollable faculty list --}}
                                    <div class="max-h-64 overflow-y-auto divide-y divide-black">
                                        @foreach ($facultyAssignments->get($department->id) as $facultyAssignment)
                                            <div class="grid grid-cols-3 items-center px-4 py-3 text-sm">

                                                <div class="font-medium text-black">
                                                    {{ $facultyAssignment->user->name }}
                                                    {{ $facultyAssignment->user->email }}
                                                </div>
                                                <div class="text-gray-600">
                                                    {{ $facultyAssignment->user->office }}
                                                </div>

                                                <div class="text-right">
                                                    <form action="{{ route('organizational.remove-faculty') }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="department_id"
                                                            value="{{ $department->id }}">
                                                        <input type="hidden" name="user_id"
                                                            value="{{ $facultyAssignment->user->id }}">

                                                        <button type="submit"
                                                            class="px-3 py-1 bg-red-600 text-white text-xs rounded-md hover:bg-black transition">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            @else
                                <div class="border border-dashed border-black rounded-lg p-6 text-center">
                                    <p class="text-gray-500 text-sm">No faculty assigned yet</p>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>


                {{-- MODALS --}}
                @include('OrganizationalHierarchy.modals.assignChairModal', [
                    'departmentId' => $department->id,
                    'departmentName' => $department->name,
                    'potentialChairs' => $potentialChairs,
                ])

                @include('OrganizationalHierarchy.modals.assignFacultyModal', [
                    'departmentId' => $department->id,
                    'departmentName' => $department->name,
                    'potentialFaculty' => $potentialFaculty,
                    'assignedFacultyIds' =>
                        $facultyAssignments->get($department->id)?->pluck('user_id')->toArray() ?? [],
                ])
            @endforeach

        </div>
    @endif

@endsection
