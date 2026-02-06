@extends('layouts.app')

@section('content')

    {{-- HEADER --}}
    <div class="mb-8 border-b-4 border-yellow-500 pb-4">
        <a href="{{ route('organizational.colleges.index') }}" class="text-green-800 hover:underline text-sm font-semibold">
            ← Back to Colleges
        </a>

        <h1 class="text-3xl font-bold text-green-900 mt-3">
            {{ $college->name }}
        </h1>
        <p class="text-green-800 text-sm mt-1">
            Manage department chairs and faculty members
        </p>
    </div>

    @if ($college->departments->isEmpty())
        <div class="border border-green-900 rounded-lg p-10 text-center bg-green-50">
            <p class="text-green-900 font-medium">No departments found in this college.</p>
        </div>
    @else
        <div class="space-y-8">

            @foreach ($college->departments as $department)
                <div class="border border-green-900 rounded-xl bg-white shadow-md">

                    {{-- DEPARTMENT HEADER --}}
                    <div class="bg-green-900 px-6 py-4 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-white tracking-wide">
                            {{ $department->name }}
                        </h2>

                        <button onclick="document.getElementById('assignFacultyModal-{{ $department->id }}').showModal()"
                            class="border border-green-900 px-4 py-1.5 text-sm rounded-md bg-yellow-500 text-green-900 hover:bg-yellow-400 hover:text-white transition">
                            + Add Faculty
                        </button>
                    </div>

                    <div class="p-6 space-y-6">

                        {{-- CHAIR SECTION --}}
                        <div>
                            <h3 class="text-xs uppercase tracking-wider text-green-800 mb-2 font-semibold">
                                Department Chair
                            </h3>

                            @if ($chairAssignments->get($department->id)?->first())
                                <div
                                    class="border border-green-900 rounded-lg p-4 flex justify-between items-center bg-green-50">

                                    <div>
                                        <p class="font-semibold text-green-900">
                                            {{ $chairAssignments->get($department->id)->first()->user->name }}
                                        </p>
                                        <p class="text-sm text-green-800">
                                            {{ $chairAssignments->get($department->id)->first()->user->email }}
                                        </p>
                                    </div>

                                    <form action="{{ route('organizational.remove-chair') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="department_id" value="{{ $department->id }}">
                                        <input type="hidden" name="user_id"
                                            value="{{ $chairAssignments->get($department->id)->first()->user->id }}">

                                        {{-- Icon only delete button --}}
                                        <button type="submit"
                                            class="w-10 h-10 flex items-center justify-center bg-red-600 text-white rounded-md hover:bg-black transition"
                                            title="Remove Chair">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                                       a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                                       M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m5 0H4" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div
                                    class="border border-dashed border-green-900 rounded-lg p-4 text-center mb-2 bg-green-50">
                                    <p class="text-green-900 text-sm font-medium">No chair assigned</p>
                                </div>

                                @if ($potentialChairs->count() > 0)
                                    <button
                                        onclick="document.getElementById('assignChairModal-{{ $department->id }}').showModal()"
                                        class="border border-green-900 px-4 py-2 rounded-md bg-yellow-500 text-green-900 hover:bg-yellow-400 hover:text-white transition text-sm">
                                        Assign Chair
                                    </button>
                                @endif
                            @endif
                        </div>

                        {{-- FACULTY SECTION --}}
                        <div>
                            <h3 class="text-xs uppercase tracking-wider text-green-800 mb-3 font-semibold">
                                Faculty Members
                            </h3>

                            @if ($facultyAssignments->get($department->id)?->count() > 0)
                                <div class="border border-green-900 rounded-lg overflow-hidden">

                                    {{-- table header --}}
                                    <div class="grid grid-cols-3 bg-green-900 text-white text-sm font-medium px-4 py-2">
                                        <div>Name</div>
                                        <div>Office</div>
                                        <div class="text-right">Action</div>
                                    </div>

                                    {{-- scrollable faculty list --}}
                                    <div class="max-h-64 overflow-y-auto divide-y divide-green-900">
                                        @foreach ($facultyAssignments->get($department->id) as $facultyAssignment)
                                            <div class="grid grid-cols-3 items-center px-4 py-3 text-sm bg-green-50">
                                                <div class="font-medium text-green-900">
                                                    {{ $facultyAssignment->user->name }}
                                                    <p class="text-xs text-green-800">{{ $facultyAssignment->user->email }}
                                                    </p>
                                                </div>
                                                <div class="text-green-800">
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

                                                        {{-- Icon only delete button --}}
                                                        <button type="submit"
                                                            class="w-8 h-8 flex items-center justify-center bg-red-600 text-white rounded-md hover:bg-black transition"
                                                            title="Remove Faculty">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                                                        a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                                                        M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m5 0H4" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            @else
                                <div class="border border-dashed border-green-900 rounded-lg p-6 text-center bg-green-50">
                                    <p class="text-green-900 text-sm font-medium">No faculty assigned yet</p>
                                </div>
                            @endif
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
