@extends('layouts.app')

@section('content')
    @php
        $canManageChair = $canManageChair ?? false;
        $canManageFaculty = $canManageFaculty ?? false;
    @endphp

    <x-header-with-button title="{{ $college->name }}" description="Manage department leadership, faculty assignments, and academic structure">
        <x-button variant="cancel" href="{{ route('organizational.colleges.index') }}">
            <i class="bx bx-left-arrow-alt"></i> Back to Colleges
        </x-button>
    </x-header-with-button>

    {{-- No Departments --}}
    @if ($college->departments->isEmpty())
        <div class="border border-emerald-200 rounded-xl p-12 text-center bg-linear-to-br from-emerald-50 to-green-50 shadow-sm">
            <div class="flex justify-center mb-4">
                <i class="bx bxs-building text-5xl text-emerald-300"></i>
            </div>
            <p class="text-emerald-800 font-semibold text-lg mb-2">
                No departments found
            </p>
            <p class="text-emerald-700 text-sm">
                Departments for this college will appear here once created.
            </p>
        </div>
    @else

        {{-- DEPARTMENTS --}}
        <div class="space-y-6">

            @foreach ($college->departments as $department)
                <div class="border border-emerald-200 rounded-xl bg-white shadow-sm hover:shadow-md transition-shadow overflow-hidden">

                    {{-- Department Header --}}
                    <div class="bg-emerald-800 px-6 py-5 flex justify-between items-center">
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <div class="shrink-0 w-11 h-11 rounded-lg bg-linear-to-br from-amber-400 to-yellow-500 flex items-center justify-center shadow-md">
                                <i class="bx bxs-buildings text-white text-lg font-bold"></i>
                            </div>
                            <h2 class="text-lg font-bold text-white truncate" title="{{ $department->name }}">
                                {{ $department->name }}
                            </h2>
                        </div>

                        @if($canManageFaculty)
                            <x-button
                                onclick="document.getElementById('assignFacultyModal-{{ $department->id }}').showModal()"
                                variant="secondary"
                                class="ml-4 text-sm font-medium flex items-center gap-2">
                                <i class="bx bx-user-plus"></i>
                                Add Faculty
                            </x-button>
                        @endif
                    </div>

                    {{-- CARD BODY --}}
                    <div class="p-6 space-y-6">

                        {{-- DEPARTMENT CHAIR --}}
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <i class="bx bxs-user-badge text-emerald-700 text-lg"></i>
                                <h3 class="text-xs uppercase tracking-[0.2em] text-emerald-800 font-semibold">
                                    Department Chair
                                </h3>
                            </div>

                            @if ($chairAssignments->get($department->id)?->first())
                                @php
                                    $chair = $chairAssignments->get($department->id)->first()->user;
                                @endphp

                                <div class="border border-emerald-200 rounded-lg p-4 bg-linear-to-br from-emerald-50 to-green-50 hover:from-emerald-100 hover:to-green-100 transition-colors flex justify-between items-start gap-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-emerald-900 leading-tight" title="{{ $chair->name }}">
                                            {{ $chair->name }}
                                        </p>
                                        <p class="text-xs text-emerald-700 mt-1.5">
                                            {{ $chair->email }}
                                        </p>
                                    </div>

                                    @if($canManageChair)
                                        <form action="{{ route('organizational.remove-chair') }}" method="POST" class="shrink-0">
                                            @csrf
                                            <input type="hidden" name="department_id" value="{{ $department->id }}">
                                            <input type="hidden" name="user_id" value="{{ $chair->id }}">

                                            <button type="submit"
                                                class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                                title="Remove chair">
                                                <i class="bx bx-trash text-base"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <div class="border border-dashed border-emerald-300 rounded-lg p-5 text-center bg-emerald-50 hover:bg-green-50 transition-colors">
                                    <i class="bx bx-user text-2xl text-emerald-400 mb-2 block"></i>
                                    <p class="text-emerald-700 text-sm font-medium mb-3">
                                        No chair assigned
                                    </p>
                                    @if ($canManageChair && $potentialChairs->count() > 0)
                                        <x-button
                                            onclick="document.getElementById('assignChairModal-{{ $department->id }}').showModal()"
                                            variant="secondary"
                                            class="text-sm font-medium">
                                            <i class="bx bx-user-plus mr-1"></i>
                                            Assign Chair
                                        </x-button>
                                    @elseif($canManageChair)
                                        <p class="text-xs text-emerald-600 font-medium">
                                            No available users to assign
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- FACULTY MEMBERS --}}
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <i class="bx bxs-group text-emerald-700 text-lg"></i>
                                    <h3 class="text-xs uppercase tracking-[0.2em] text-emerald-800 font-semibold">
                                        Faculty Members
                                    </h3>
                                </div>
                                @if ($facultyAssignments->get($department->id)?->count() > 0)
                                    <span class="inline-flex items-center justify-center min-w-7 h-7 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                                        {{ $facultyAssignments->get($department->id)->count() }}
                                    </span>
                                @endif
                            </div>

                            @if ($facultyAssignments->get($department->id)?->count() > 0)
                                <div class="border border-emerald-200 rounded-lg overflow-hidden bg-white">

                                    {{-- FACULTY LIST --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2">
                                        @foreach ($facultyAssignments->get($department->id) as $facultyAssignment)
                                            <div class="flex items-center justify-between px-4 py-3.5 border-b border-r border-emerald-100 last:border-b-0 md:last:border-r-0 md:even:border-r-0 md:odd:border-r hover:bg-emerald-50 transition-colors">

                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-slate-900 text-sm leading-tight" title="{{ $facultyAssignment->user->name }}">
                                                        {{ $facultyAssignment->user->name }}
                                                    </p>
                                                    <p class="text-xs text-slate-600 mt-1">
                                                        {{ $facultyAssignment->user->email }}
                                                    </p>
                                                    @if ($facultyAssignment->user->office)
                                                        <p class="text-xs text-emerald-600 mt-0.5">
                                                            <i class="bx bx-map text-xs mr-1"></i>{{ $facultyAssignment->user->office }}
                                                        </p>
                                                    @endif
                                                </div>

                                                @if($canManageFaculty)
                                                    <form action="{{ route('organizational.remove-faculty') }}"
                                                        method="POST" class="shrink-0 ml-3">
                                                        @csrf
                                                        <input type="hidden" name="department_id" value="{{ $department->id }}">
                                                        <input type="hidden" name="user_id" value="{{ $facultyAssignment->user->id }}">

                                                        <button type="submit"
                                                            class="p-1.5 text-rose-600 hover:bg-rose-50 rounded transition"
                                                            title="Remove faculty">
                                                            <i class="bx bx-trash text-base"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            @else
                                <div class="border border-dashed border-emerald-300 rounded-lg p-6 text-center bg-emerald-50 hover:bg-green-50 transition-colors">
                                    <i class="bx bxs-group text-2xl text-emerald-400 mb-2 block"></i>
                                    <p class="text-emerald-700 text-sm font-medium">
                                        No faculty assigned yet
                                    </p>
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- MODALS --}}
                    @if($canManageChair)
                        @include('OrganizationalHierarchy.modals.assignChairModal', [
                            'departmentId' => $department->id,
                            'departmentName' => $department->name,
                            'potentialChairs' => $potentialChairs,
                        ])
                    @endif

                    @if($canManageFaculty)
                        @include('OrganizationalHierarchy.modals.assignFacultyModal', [
                            'departmentId' => $department->id,
                            'departmentName' => $department->name,
                            'potentialFaculty' => $potentialFaculty,
                            'assignedFacultyIds' =>
                                $facultyAssignments->get($department->id)?->pluck('user_id')->toArray() ?? [],
                        ])
                    @endif

                </div>
            @endforeach

        </div>
    @endif

@endsection
