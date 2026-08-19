@extends('layouts.app')

@section('content')
    @php
        $canManageChair   = $canManageChair   ?? false;
        $canManageFaculty = $canManageFaculty ?? false;
        $isAdmin = auth()->user()?->hasRole('admin') ?? false;
    @endphp

    <x-layout.page-header
        icon="bx-buildings"
        title="{{ $college->name }}"
        desc="Manage department leadership, faculty assignments, and academic structure">
        <x-ui.help-trigger />
        <x-ui.button variant="cancel" href="{{ $isAdmin ? route('user-assignments.colleges.index') : route('user-assignments.hierarchy') }}">
            <i class="bx bx-arrow-back"></i> Back
        </x-ui.button>
    </x-layout.page-header>

    <x-layout.help-panel module="user-assignments" />

    <script>
        function filterDepartments(searchTerm) {
            const term = searchTerm.toLowerCase();
            const cards = document.querySelectorAll('[data-department-name]');
            
            cards.forEach(card => {
                const name = card.getAttribute('data-department-name').toLowerCase();
                const chair = card.getAttribute('data-chair-name')?.toLowerCase() || '';
                const faculty = card.getAttribute('data-faculty-names')?.toLowerCase() || '';
                
                if (name.includes(term) || chair.includes(term) || faculty.includes(term)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>

    <x-layout.panel>

        {{-- Search and Filter --}}
        @if (!$college->departments->isEmpty())
            <div class="mb-4">
                <div class="relative">
                    <input
                        type="text"
                        id="departmentSearch"
                        placeholder="Search departments, chairs, or faculty..."
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-[#e2e8f0] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00c075] focus:border-transparent"
                        oninput="filterDepartments(this.value)"
                    >
                    <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
            </div>
        @endif

        @if ($college->departments->isEmpty())
            <x-feedback-status.empty-state
                icon="bxs-building"
                title="No departments found"
                message="Departments for this college will appear here once created." />
        @else
            <div class="space-y-5">

                @foreach ($college->departments as $department)
                    @php
                        $chair       = $chairAssignments->get($department->id)?->first()?->user;
                        $facultyList = $facultyAssignments->get($department->id) ?? collect();
                        $facultyNames = $facultyList->pluck('user.name')->implode(', ');
                    @endphp

                    <div class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);"
                         data-department-name="{{ $department->name }}"
                         data-chair-name="{{ $chair?->name ?? '' }}"
                         data-faculty-names="{{ $facultyNames }}">

                        {{-- Department header --}}
                        <div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#f8fafc] border-b border-[#e2e8f0]">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <span class="shrink-0 w-9 h-9 rounded-lg flex items-center justify-center" style="background: var(--clsu-green);">
                                    <i class="bx bxs-buildings text-white text-lg leading-none"></i>
                                </span>
                                <div class="min-w-0">
                                    <h2 class="text-sm font-bold text-[#0f172a] truncate" title="{{ $department->name }}">
                                        {{ $department->name }}
                                    </h2>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $facultyList->count() }} faculty member{{ $facultyList->count() !== 1 ? 's' : '' }}
                                    </p>
                                </div>
                            </div>

                            @if ($canManageFaculty)
                                <x-ui.button
                                    onclick="document.getElementById('bulkAssignFacultyModal-{{ $department->id }}').showModal()"
                                    variant="add-button"
                                    class="shrink-0 text-xs">
                                    <i class="bx bx-user-plus"></i> Add Faculty
                                </x-ui.button>
                            @endif
                        </div>

                        {{-- Body: two-column on md+ --}}
                        <div class="p-4 grid md:grid-cols-2 gap-4">

                            {{-- Chair --}}
                            <x-layout.card title="Department Chair" icon="user" color="slate" :shadow="false">
                                <x-slot name="action">
                                    <div class="group relative">
                                        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors">
                                            <i class="bx bx-info-circle text-sm"></i>
                                        </button>
                                        <div class="absolute right-0 top-full mt-2 w-64 p-3 bg-slate-800 text-white text-xs rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-[999]">
                                            <p class="font-semibold mb-1">Department Chair</p>
                                            <p class="text-slate-300">Leads a specific department within a college. Manages faculty, curriculum, and departmental operations.</p>
                                        </div>
                                    </div>
                                </x-slot>
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
                                            <button type="button"
                                                onclick="document.getElementById('removeChairModal-{{ $department->id }}').showModal()"
                                                class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-600 transition-colors"
                                                title="Remove chair">
                                                <i class="bx bx-trash text-base leading-none"></i>
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex flex-col items-center gap-3 py-1">
                                        <p class="text-xs text-slate-400 italic">No chair assigned yet.</p>
                                        @if ($canManageChair && $potentialChairs->count() > 0)
                                            <x-ui.button
                                                onclick="document.getElementById('assignChairModal-{{ $department->id }}').showModal()"
                                                variant="cancel"
                                                class="text-xs">
                                                <i class="bx bx-user-plus"></i> Assign Chair
                                            </x-ui.button>
                                        @elseif ($canManageChair)
                                            <p class="text-xs text-slate-400">No available users to assign</p>
                                        @endif
                                    </div>
                                @endif
                            </x-layout.card>

                            {{-- Faculty --}}
                            <x-layout.card color="slate" :shadow="false">
                                <x-slot name="title">Faculty Members</x-slot>
                                <x-slot name="action">
                                    <div class="group relative">
                                        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors">
                                            <i class="bx bx-info-circle text-sm"></i>
                                        </button>
                                        <div class="absolute right-0 top-full mt-2 w-64 p-3 bg-slate-800 text-white text-xs rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-[999]">
                                            <p class="font-semibold mb-1">Faculty Members</p>
                                            <p class="text-slate-300">Academic staff assigned to teach and conduct research within a department. Can be assigned to multiple departments.</p>
                                        </div>
                                    </div>
                                    @if ($facultyList->count() > 0)
                                        <span class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-slate-200 text-slate-600 text-xs font-bold">
                                            {{ $facultyList->count() }}
                                        </span>
                                    @endif
                                </x-slot>

                                @if ($facultyList->count() > 0)
                                    <div class="divide-y divide-slate-100 overflow-y-auto max-h-56 pr-1">
                                        @foreach ($facultyList as $fa)
                                            <div class="flex items-center justify-between gap-3 py-2.5 first:pt-0 last:pb-0">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-slate-800 truncate" title="{{ $fa->user->name }}">
                                                        {{ $fa->user->name }}
                                                    </p>
                                                    <p class="text-xs text-slate-500 truncate">{{ $fa->user->email }}</p>
                                                </div>
                                                @if ($canManageFaculty)
                                                    <button type="button"
                                                        onclick="document.getElementById('removeFacultyModal-{{ $department->id }}-{{ $fa->user->id }}').showModal()"
                                                        class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-600 transition-colors"
                                                        title="Remove faculty">
                                                        <i class="bx bx-trash text-base leading-none"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-slate-400 italic">No faculty assigned yet.</p>
                                @endif
                            </x-layout.card>

                        </div>
                    </div>

                    @if ($canManageChair)
                        @include('UserManagement.UserAssignments.modals.assignChairModal', [
                            'departmentId'    => $department->id,
                            'departmentName'  => $department->name,
                            'potentialChairs' => $potentialChairs,
                        ])
                    @endif

                    @if ($canManageFaculty)
                        @include('UserManagement.UserAssignments.modals.bulkAssignFacultyModal', [
                            'departmentId'       => $department->id,
                            'departmentName'     => $department->name,
                            'potentialFaculty'   => $potentialFaculty,
                            'assignedFacultyIds' => $facultyList->pluck('user_id')->toArray(),
                        ])
                    @endif

                    @if ($chair && $canManageChair)
                        @include('UserManagement.UserAssignments.modals.removeChairModal', [
                            'departmentId'   => $department->id,
                            'departmentName' => $department->name,
                            'userId'         => $chair->id,
                            'userName'       => $chair->name,
                        ])
                    @endif

                    @foreach ($facultyList as $fa)
                        @include('UserManagement.UserAssignments.modals.removeFacultyModal', [
                            'departmentId'   => $department->id,
                            'departmentName' => $department->name,
                            'userId'         => $fa->user->id,
                            'userName'       => $fa->user->name,
                        ])
                    @endforeach

                @endforeach

            </div>
        @endif

    </x-layout.panel>

@endsection