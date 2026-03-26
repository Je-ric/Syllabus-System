@extends('layouts.app')

@section('content')
    @php
        $canManageChair   = $canManageChair   ?? false;
        $canManageFaculty = $canManageFaculty ?? false;
        $isAdmin = auth()->user()?->hasRole('admin') ?? false;
    @endphp

    <x-page-header
        icon="bx-buildings"
        title="{{ $college->name }}"
        desc="Manage department leadership, faculty assignments, and academic structure">
        <x-button variant="cancel" href="{{ $isAdmin ? route('organizational.colleges.index') : route('organizational.hierarchy') }}">
            <i class="bx bx-left-arrow-alt"></i> Back
        </x-button>
    </x-page-header>

    <x-panel>

        @if ($college->departments->isEmpty())
            <x-empty-state
                icon="bxs-building"
                title="No departments found"
                message="Departments for this college will appear here once created." />
        @else
            <div class="space-y-5">

                @foreach ($college->departments as $department)
                    @php
                        $chair       = $chairAssignments->get($department->id)?->first()?->user;
                        $facultyList = $facultyAssignments->get($department->id) ?? collect();
                    @endphp

                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow overflow-hidden">

                        {{-- Department header --}}
                        <div class="flex items-center justify-between gap-3 px-5 py-4 bg-slate-50 border-b border-slate-200">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <span class="shrink-0 w-9 h-9 rounded-lg bg-emerald-800 flex items-center justify-center">
                                    <i class="bx bxs-buildings text-white text-lg leading-none"></i>
                                </span>
                                <div class="min-w-0">
                                    <h2 class="text-sm font-bold text-slate-800 truncate" title="{{ $department->name }}">
                                        {{ $department->name }}
                                    </h2>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $facultyList->count() }} faculty member{{ $facultyList->count() !== 1 ? 's' : '' }}
                                    </p>
                                </div>
                            </div>

                            @if ($canManageFaculty)
                                <x-button
                                    onclick="document.getElementById('assignFacultyModal-{{ $department->id }}').showModal()"
                                    variant="add-button"
                                    class="shrink-0 text-xs">
                                    <i class="bx bx-user-plus"></i> Add Faculty
                                </x-button>
                            @endif
                        </div>

                        {{-- Body: two-column on md+ --}}
                        <div class="p-4 grid md:grid-cols-2 gap-4">

                            {{-- Chair --}}
                            <x-card title="Department Chair" icon="user" color="slate" :shadow="false">
                                @if ($chair)
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 truncate" title="{{ $chair->name }}">
                                                {{ $chair->name }}
                                            </p>
                                            <p class="text-xs text-slate-500 mt-0.5 truncate" title="{{ $chair->email }}">
                                                {{ $chair->email }}
                                            </p>
                                        </div>
                                        @if ($canManageChair)
                                            <form action="{{ route('organizational.remove-chair') }}" method="POST" class="shrink-0">
                                                @csrf
                                                <input type="hidden" name="department_id" value="{{ $department->id }}">
                                                <input type="hidden" name="user_id" value="{{ $chair->id }}">
                                                <button type="submit"
                                                    class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-600 transition-colors"
                                                    title="Remove chair">
                                                    <i class="bx bx-trash text-base leading-none"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex flex-col items-center gap-3 py-1">
                                        <p class="text-xs text-slate-400 italic">No chair assigned yet.</p>
                                        @if ($canManageChair && $potentialChairs->count() > 0)
                                            <x-button
                                                onclick="document.getElementById('assignChairModal-{{ $department->id }}').showModal()"
                                                variant="cancel"
                                                class="text-xs">
                                                <i class="bx bx-user-plus"></i> Assign Chair
                                            </x-button>
                                        @elseif ($canManageChair)
                                            <p class="text-xs text-slate-400">No available users to assign</p>
                                        @endif
                                    </div>
                                @endif
                            </x-card>

                            {{-- Faculty --}}
                            <x-card color="slate" :shadow="false">
                                <x-slot name="title">Faculty Members</x-slot>
                                <x-slot name="action">
                                    @if ($facultyList->count() > 0)
                                        <span class="inline-flex items-center justify-center min-w-[1.4rem] h-5 px-1.5 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold">
                                            {{ $facultyList->count() }}
                                        </span>
                                    @endif
                                </x-slot>

                                @if ($facultyList->count() > 0)
                                    <div class="divide-y divide-slate-100">
                                        @foreach ($facultyList as $fa)
                                            <div class="flex items-center justify-between gap-3 py-2.5 first:pt-0 last:pb-0">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-slate-800 truncate" title="{{ $fa->user->name }}">
                                                        {{ $fa->user->name }}
                                                    </p>
                                                    <p class="text-xs text-slate-500 truncate">{{ $fa->user->email }}</p>
                                                </div>
                                                @if ($canManageFaculty)
                                                    <form action="{{ route('organizational.remove-faculty') }}" method="POST" class="shrink-0">
                                                        @csrf
                                                        <input type="hidden" name="department_id" value="{{ $department->id }}">
                                                        <input type="hidden" name="user_id" value="{{ $fa->user->id }}">
                                                        <button type="submit"
                                                            class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-600 transition-colors"
                                                            title="Remove faculty">
                                                            <i class="bx bx-trash text-base leading-none"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-slate-400 italic">No faculty assigned yet.</p>
                                @endif
                            </x-card>

                        </div>
                    </div>

                    @if ($canManageChair)
                        @include('OrganizationalHierarchy.modals.assignChairModal', [
                            'departmentId'    => $department->id,
                            'departmentName'  => $department->name,
                            'potentialChairs' => $potentialChairs,
                        ])
                    @endif

                    @if ($canManageFaculty)
                        @include('OrganizationalHierarchy.modals.assignFacultyModal', [
                            'departmentId'       => $department->id,
                            'departmentName'     => $department->name,
                            'potentialFaculty'   => $potentialFaculty,
                            'assignedFacultyIds' => $facultyList->pluck('user_id')->toArray(),
                        ])
                    @endif

                @endforeach

            </div>
        @endif

    </x-panel>

@endsection
