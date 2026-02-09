@extends('layouts.app')

@section('content')

    <x-header-with-button title="{{ $college->name }}" description="Assign and manage department chairs and faculty members">
        <x-button variant="cancel" href="{{ route('organizational.colleges.index') }}">← Back to Colleges</x-button>
    </x-header-with-button>

    @if ($college->departments->isEmpty())
        <div class="border border-green-900 rounded-lg p-10 text-center bg-green-50">
            <p class="text-green-900 font-medium">No departments found in this college.</p>
        </div>
    @else

        {{-- DEPARTMENTS --}}
        <div class="space-y-6">

            @foreach ($college->departments as $department)
                <div class="border border-green-900 rounded-xl bg-white shadow-sm">

                    <div class="bg-green-grad px-5 py-3 flex justify-between items-center rounded-t-xl">
                        <h2 class="text-lg font-bold text-white tracking-wide flex items-center gap-2">
                            <i class="bx bxs-buildings text-yellow-400 text-xl"></i>
                            {{ $department->name }}
                        </h2>

                        <x-button
                            onclick="document.getElementById('assignFacultyModal-{{ $department->id }}').showModal()"
                            variant="secondary">
                            <i class="bx bx-user-plus mr-1"></i> Add Faculty
                        </x-button>
                    </div>

                    {{-- CARD BODY --}}
                    <div class="p-5 space-y-6">

                        {{-- CHAIR --}}
                        <div>
                            <h3
                                class="text-xs uppercase tracking-wider text-green-800 mb-2 font-semibold flex items-center gap-1">
                                <i class="bx bxs-user-badge text-green-900"></i>
                                Department Chair
                            </h3>

                            @if ($chairAssignments->get($department->id)?->first())
                                <div
                                    class="border border-green-900 rounded-lg p-4 flex justify-between items-center bg-green-50 max-w-xl">

                                    <div>
                                        <p class="font-semibold text-green-900 leading-tight">
                                            {{ $chairAssignments->get($department->id)->first()->user->name }}
                                        </p>
                                        <p class="text-xs text-green-800">
                                            {{ $chairAssignments->get($department->id)->first()->user->email }}
                                        </p>
                                    </div>

                                    <form action="{{ route('organizational.remove-chair') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="department_id" value="{{ $department->id }}">
                                        <input type="hidden" name="user_id"
                                            value="{{ $chairAssignments->get($department->id)->first()->user->id }}">

                                        <button type="submit"
                                            class="w-9 h-9 flex items-center justify-center bg-red-600 text-white rounded hover:bg-black transition"
                                            title="Remove Chair">
                                            <i class="bx bx-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div
                                    class="border border-dashed border-green-900 rounded-lg p-4 text-center bg-green-50 max-w-xl mb-2">
                                    <p class="text-green-900 text-sm font-medium">No chair assigned</p>
                                </div>

                                @if ($potentialChairs->count() > 0)
                                    <x-button
                                        onclick="document.getElementById('assignChairModal-{{ $department->id }}').showModal()"
                                        variant="secondary">
                                        Assign Chair
                                    </x-button>
                                @endif
                            @endif
                        </div>

                        {{-- FACULTY --}}
                        <div>
                            <h3
                                class="text-xs uppercase tracking-wider text-green-800 mb-3 font-semibold flex items-center gap-1">
                                <i class="bx bxs-group text-green-900"></i>
                                Faculty Members
                            </h3>

                            @if ($facultyAssignments->get($department->id)?->count() > 0)
                                <div class="border border-green-900 rounded-lg bg-white">

                                    {{-- FACULTY LIST --}}
                                    <div class="grid grid-cols-2">
                                        @foreach ($facultyAssignments->get($department->id) as $facultyAssignment)
                                            <div
                                                class="flex items-center justify-between px-4 py-3 text-sm bg-green-50">

                                                <div class="pr-4">
                                                    <p class="font-medium text-green-900 leading-tight">
                                                        {{ $facultyAssignment->user->name }}
                                                    </p>
                                                    <p class="text-xs text-green-800">
                                                        {{ $facultyAssignment->user->email }}
                                                        • {{ $facultyAssignment->user->office }}
                                                    </p>
                                                </div>

                                                <form action="{{ route('organizational.remove-faculty') }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="department_id"
                                                        value="{{ $department->id }}">
                                                    <input type="hidden" name="user_id"
                                                        value="{{ $facultyAssignment->user->id }}">

                                                    <button type="submit"
                                                        class="w-7 h-7 flex items-center justify-center bg-red-600 text-white rounded hover:bg-black transition"
                                                        title="Remove Faculty">
                                                        <i class="bx bx-trash text-sm"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            @else
                                <div
                                    class="border border-dashed border-green-900 rounded-lg p-4 text-center bg-green-50 max-w-xl">
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

                </div>
            @endforeach

        </div>
    @endif

@endsection
