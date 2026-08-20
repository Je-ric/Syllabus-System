@extends('layouts.app')

@section('content')

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script>
        function openAddDepartmentModal(collegeId) {
            const input = document.getElementById('addDepartment_college_id');
            if (input) input.value = collegeId;
            const modal = document.getElementById('addDepartmentModal');
            if (modal) modal.showModal();
        }

        function tryOpenAddProgramModal() {
            const modal = document.getElementById('addProgramModal');
            if (modal) {
                modal.showModal();
            } else {
                console.error('addProgramModal not found');
            }
        }

    </script>

    <x-layout.page-header
        icon="bx-buildings"
        title="University Structure Management"
        desc="Manage colleges, departments, and academic programs across the institution">
        <x-ui.help-trigger />
        <x-ui.button variant="add-button"
                onclick="document.getElementById('addCollegeModal').showModal()">
            <i class="bx bx-plus text-base leading-none"></i> Add College
        </x-ui.button>
    </x-layout.page-header>

    <x-layout.help-panel module="university-structure" />

    <script>
        // Optimized search with better debouncing and minimal DOM queries
        let searchTimeout;
        function filterStructure(searchTerm) {
            clearTimeout(searchTimeout);
            
            if (!searchTerm.trim()) {
                // Show all elements when search is empty
                document.querySelectorAll('[data-college-search], [data-department-search], [data-program-search]')
                    .forEach(el => el.style.display = '');
                return;
            }

            searchTimeout = setTimeout(() => {
                const term = searchTerm.toLowerCase();
                
                // Cache queries to minimize DOM operations
                const collegeButtons = document.querySelectorAll('[data-college-search]');
                const departments = document.querySelectorAll('[data-department-search]');
                const programs = document.querySelectorAll('[data-program-search]');

                // Batch DOM updates using requestAnimationFrame for smoother rendering
                requestAnimationFrame(() => {
                    collegeButtons.forEach(button => {
                        const name = button.getAttribute('data-college-search').toLowerCase();
                        button.style.display = name.includes(term) ? '' : 'none';
                    });

                    departments.forEach(dept => {
                        const name = dept.getAttribute('data-department-search').toLowerCase();
                        dept.style.display = name.includes(term) ? '' : 'none';
                    });

                    programs.forEach(program => {
                        const name = program.getAttribute('data-program-search').toLowerCase();
                        program.style.display = name.includes(term) ? '' : 'none';
                    });
                });
            }, 300); // Increased debounce for better performance
        }
    </script>

    @if($colleges->count())

    <div x-data="{ 
        selectedCollege: {{ $colleges->first()->id }}, 
        searchTerm: '',
        collegeListOpen: true
    }">

        <x-layout.panel>
            {{-- Search bar --}}
            <div class="mb-4">
                <div class="relative">
                    <input
                        type="text"
                        x-model="searchTerm"
                        @input="filterStructure(searchTerm)"
                        placeholder="Search colleges, departments, or programs..."
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-[#e2e8f0] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00c075] focus:border-transparent"
                    >
                    <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

                {{-- ── LEFT: College list ──────────────────────────────────────── --}}
                <div class="col-span-1 md:col-span-5 lg:col-span-4">
                    <div class="rounded-[12px] border border-[#E3E8EB] bg-white overflow-visible"
                         style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);">

                        {{-- Header --}}
                        <div class="flex items-center justify-between gap-2 px-4 py-3 border-b border-[#E3E8EB] bg-[#F1F3F5] rounded-t-[12px] cursor-pointer md:cursor-default"
                             @click.md.prevent="" @click="collegeListOpen = !collegeListOpen">
                            <div class="flex items-center gap-2">
                                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-[#D5FFF0] text-[#06754E]">
                                    <i class="bx bxs-school text-sm leading-none"></i>
                                </span>
                                <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-[#394056]">Colleges</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-feedback-status.status-indicator variant="brand" size="sm">
                                    {{ $colleges->count() }}
                                </x-feedback-status.status-indicator>
                                <i class="bx text-[#72809E] text-sm leading-none md:hidden transition-transform duration-200"
                                   :class="collegeListOpen ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                            </div>
                        </div>

                        {{-- College rows --}}
                        <div class="divide-y divide-[#F1F3F5] overflow-hidden"
                             x-show="collegeListOpen"
                             x-transition:enter="transition-all duration-200 ease-out"
                             x-transition:enter-start="opacity-0 max-h-0"
                             x-transition:enter-end="opacity-100 max-h-[600px]"
                             x-transition:leave="transition-all duration-200 ease-in"
                             x-transition:leave-start="opacity-100 max-h-[600px]"
                             x-transition:leave-end="opacity-0 max-h-0">
                            @foreach($collegeData as $data)
                                @php $college = $data['college']; @endphp

                                <button
                                    @click="selectedCollege = {{ $college->id }}; collegeListOpen = window.innerWidth >= 768 ? true : false"
                                    class="w-full text-left px-4 py-3 transition-colors duration-150 border-l-[3px]"
                                    :class="selectedCollege === {{ $college->id }}
                                        ? 'bg-[#EDFFF8] border-l-[#00C075]'
                                        : 'bg-white border-l-transparent hover:bg-[#F9FAFA]'"
                                    x-show="!searchTerm || '{{ $college->name }}'.toLowerCase().includes(searchTerm.toLowerCase())">

                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                                                :class="selectedCollege === {{ $college->id }} ? 'bg-[#D5FFF0]' : 'bg-[#F1F3F5]'">
                                                <i class="bx bxs-school text-sm leading-none transition-colors"
                                                   :class="selectedCollege === {{ $college->id }} ? 'text-[#06754E]' : 'text-[#72809E]'"></i>
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-[13px] font-semibold text-[#394056] truncate leading-tight">{{ $college->name }}</p>
                                                <p class="text-[11px] text-[#93A1AF] mt-0.5">
                                                    {{ $data['dept_count'] }} dept{{ $data['dept_count'] !== 1 ? 's' : '' }}
                                                    &middot; {{ $data['program_count'] }} program{{ $data['program_count'] !== 1 ? 's' : '' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="dropdown dropdown-end shrink-0" onclick="event.stopPropagation()">
                                            <label tabindex="0"
                                                class="w-7 h-7 rounded-[7px] flex items-center justify-center text-[#B4C0CA] hover:text-[#06754E] hover:bg-[#D5FFF0] cursor-pointer transition-all duration-150">
                                                <i class="bx bx-dots-vertical-rounded text-base leading-none"></i>
                                            </label>
                                            <ul tabindex="0"
                                                class="dropdown-content z-100 menu p-1.5 bg-white rounded-[10px] border border-[#E3E8EB] w-36 text-sm"
                                                style="box-shadow: 0 4px 16px rgba(16,24,40,0.10);">
                                                <li>
                                                    <a class="flex items-center gap-2 px-3 py-1.5 rounded-[7px] hover:bg-[#F1F3F5] text-[#394056] text-[13px]"
                                                       onclick="openEditCollegeModal({{ $college->id }}, @js($college->name), '{{ url('university-structure/college') }}')">
                                                        <i class="bx bx-edit-alt text-[#3197D6] text-sm"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="flex items-center gap-2 px-3 py-1.5 rounded-[7px] hover:bg-[#FFE3E2] text-[#D21B14] text-[13px]"
                                                       onclick="openDeleteCollegeModal({{ $college->id }}, @js($college->name), {{ $college->departments_count }}, '{{ url('university-structure/college') }}')">
                                                        <i class="bx bx-trash text-sm"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                    </div>
                </div>

                {{-- ── RIGHT: Departments + Programs ──────────────────────────── --}}
                <div class="col-span-1 md:col-span-7 lg:col-span-8">
                    @foreach($collegeData as $data)
                        @php $college = $data['college']; @endphp
                        <div x-show="selectedCollege === {{ $college->id }} && !collegeListOpen || selectedCollege === {{ $college->id }} && window.innerWidth >= 768" x-cloak
                             @resize.window="$el.style.display = (selectedCollege === {{ $college->id }} && (!collegeListOpen || window.innerWidth >= 768)) ? '' : 'none'">

                            <x-layout.card-section icon="bx-sitemap" title="Departments & Programs"
                                :subtitle="$college->name">

                                <x-slot name="actions">
                                    <x-ui.button variant="sm-add"
                                        onclick="openAddDepartmentModal({{ $college->id }})">
                                        <i class="bx bx-plus text-base leading-none"></i> Add Department
                                    </x-ui.button>
                                </x-slot>

                                <div class="space-y-3">
                                    @forelse($data['departments'] as $dept)

                                        <div class="rounded-[12px] border border-[#E3E8EB] bg-white overflow-visible"
                                             style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);"
                                             data-department-search="{{ $dept->name }}">

                                            {{-- Department header --}}
                                            <div class="flex items-center justify-between px-4 py-3 bg-[#F9FAFA] border-b border-[#E3E8EB] rounded-t-[12px]">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-7 h-7 rounded-lg bg-[#DAF1FF] flex items-center justify-center shrink-0">
                                                        <i class="bx bx-building text-[#143D57] text-sm leading-none"></i>
                                                    </span>
                                                    <div>
                                                        <span class="text-[13px] font-semibold text-[#394056]">{{ $dept->name }}</span>
                                                        <x-feedback-status.status-indicator variant="blue" size="sm">
                                                            {{ $dept->programs->count() }}
                                                        </x-feedback-status.status-indicator>
                                                    </div>
                                                </div>

                                                <div class="dropdown dropdown-end" onclick="event.stopPropagation()">
                                                    <label tabindex="0"
                                                        class="w-7 h-7 rounded-[7px] flex items-center justify-center text-[#B4C0CA] hover:text-[#143D57] hover:bg-[#DAF1FF] cursor-pointer transition-all duration-150">
                                                        <i class="bx bx-dots-vertical-rounded text-base leading-none"></i>
                                                    </label>
                                                    <ul tabindex="0"
                                                        class="dropdown-content z-100 menu p-1.5 bg-white rounded-[10px] border border-[#E3E8EB] w-36 text-sm"
                                                        style="box-shadow: 0 4px 16px rgba(16,24,40,0.10);">
                                                        <li>
                                                            <a class="flex items-center gap-2 px-3 py-1.5 rounded-[7px] hover:bg-[#F1F3F5] text-[#394056] text-[13px]"
                                                               onclick="openEditDepartmentModal({{ $dept->id }}, @js($dept->name), {{ $dept->college_id }}, '{{ url('university-structure/department') }}')">
                                                                <i class="bx bx-edit-alt text-[#3197D6] text-sm"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="flex items-center gap-2 px-3 py-1.5 rounded-[7px] hover:bg-[#FFE3E2] text-[#D21B14] text-[13px]"
                                                               onclick="openDeleteDepartmentModal({{ $dept->id }}, @js($dept->name), @js($college->name), {{ $dept->programs_count }}, '{{ url('university-structure/department') }}')">
                                                                <i class="bx bx-trash text-sm"></i> Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            {{-- Program rows --}}
                                            <div class="divide-y divide-[#F1F3F5]">
                                                @forelse($dept->programs as $program)
                                                    @php $role = $program->pivot->role; @endphp
                                                    <div class="flex items-center justify-between px-4 py-2.5 hover:bg-[#F9FAFA] transition-colors duration-150"
                                                         data-program-search="{{ $program->name }}">
                                                        <div class="flex items-center gap-2.5 min-w-0">
                                                            <span class="w-6 h-6 rounded-[7px] flex items-center justify-center shrink-0
                                                                {{ $role === 'primary' ? 'bg-[#D5FFF0]' : 'bg-[#FFF3CD]' }}">
                                                                <i class="bx bx-book-alt text-xs leading-none
                                                                    {{ $role === 'primary' ? 'text-[#06754E]' : 'text-[#856404]' }}"></i>
                                                            </span>
                                                            <div class="min-w-0">
                                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                                    <p class="text-[13px] font-medium text-[#394056] truncate">{{ $program->name }}</p>
                                                                    @if($role === 'primary')
                                                                        <div class="group relative shrink-0">
                                                                            <x-feedback-status.status-indicator variant="brand" label="Primary" size="sm" class="cursor-help" />
                                                                            <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-48 p-2 bg-slate-800 text-white text-xs rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-[999]">
                                                                                <p class="font-semibold mb-1">Primary Department</p>
                                                                                <p class="text-slate-300">Main department responsible for program administration and curriculum.</p>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div class="group relative shrink-0">
                                                                            <x-feedback-status.status-indicator variant="amber" label="Supporting" size="sm" class="cursor-help" />
                                                                            <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-48 p-2 bg-slate-800 text-white text-xs rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-[999]">
                                                                                <p class="font-semibold mb-1">Supporting Department</p>
                                                                                <p class="text-slate-300">Collaborates on program but does not administer it. Provides interdisciplinary support.</p>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                                                    @if($program->bor_approval_no)
                                                                        <p class="text-[11px] text-[#93A1AF]">
                                                                            {{ $program->bor_approval_no }}
                                                                            @if($program->bor_approval_date)
                                                                                &middot; {{ \Carbon\Carbon::parse($program->bor_approval_date)->format('M d, Y') }}
                                                                            @endif
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="dropdown dropdown-end shrink-0 relative" onclick="event.stopPropagation()">
                                                            <label tabindex="0"
                                                                class="w-7 h-7 rounded-[7px] flex items-center justify-center text-[#B4C0CA] hover:text-[#06754E] hover:bg-[#D5FFF0] cursor-pointer transition-all duration-150">
                                                                <i class="bx bx-dots-vertical-rounded text-base leading-none"></i>
                                                            </label>
                                                            <ul tabindex="0"
                                                                class="dropdown-content z-150 menu p-1.5 bg-white rounded-[10px] border border-[#E3E8EB] w-36 text-sm"
                                                                style="box-shadow: 0 4px 16px rgba(16,24,40,0.10);">
                                                                <li>
                                                                    <a class="flex items-center gap-2 px-3 py-1.5 rounded-[7px] hover:bg-[#F1F3F5] text-[#394056] text-[13px]"
                                                                       onclick="openEditProgramModal({{ $program->id }}, @js($program->name), @js((string)($program->departments->where('pivot.role','primary')->first()?->id ?? '')), @js($program->departments->where('pivot.role','supporting')->pluck('id')->map(fn($id)=>(string)$id)->values()->all()), @js($program->bor_approval_no ?? ''), @js($program->bor_approval_date ?? ''), '{{ url('university-structure/program') }}')">
                                                                        <i class="bx bx-edit-alt text-[#3197D6] text-sm"></i> Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="flex items-center gap-2 px-3 py-1.5 rounded-[7px] hover:bg-[#FFE3E2] text-[#D21B14] text-[13px]"
                                                                       onclick="openDeleteProgramModal({{ $program->id }}, @js($program->name), @js($program->bor_approval_no ?? ''), {{ $program->courses_count }}, '{{ url('university-structure/program') }}')">
                                                                        <i class="bx bx-trash text-sm"></i> Delete
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="px-4 py-3 text-center text-[12px] text-[#93A1AF]">
                                                        No programs yet.
                                                    </div>
                                                @endforelse
                                            </div>

                                            {{-- Add program footer --}}
                                            <button
                                                type="button"
                                                class="w-full flex items-center justify-center gap-1.5 py-2 text-[12px] font-semibold
                                                       text-[#00965F] hover:bg-[#EDFFF8] border-t border-dashed border-[#00C075]
                                                       transition-all duration-150 rounded-b-[12px]"
                                                onclick="tryOpenAddProgramModal()">
                                                <i class="bx bx-plus text-sm leading-none"></i>
                                                Add Program
                                            </button>

                                        </div>

                                    @empty
                                        <x-feedback-status.empty-state
                                            icon="bx-building"
                                            title="No departments yet"
                                            message="Add a department to start building this college's structure." />
                                    @endforelse
                                </div>

                            </x-layout.card-section>

                        </div>
                    @endforeach
                </div>

            </div>
        </x-layout.panel>

    </div>

    @else

        <x-layout.panel>
            <x-feedback-status.empty-state
                icon="bxs-school"
                title="No colleges yet"
                message="Start by adding your first college to build the academic structure.">
                <x-ui.button variant="add-button"
                    onclick="document.getElementById('addCollegeModal').showModal()">
                    <i class="bx bx-plus text-base leading-none"></i> Add College
                </x-ui.button>
            </x-feedback-status.empty-state>
        </x-layout.panel>

    @endif


    {{-- Always loaded basic modals --}}
    @include('University.UniversityStructure.modals.addCollegeModal')
    @include('University.UniversityStructure.modals.addDepartmentModal')
    @include('University.UniversityStructure.modals.addProgramModal', ['allDepartments' => $allDepartments])

    {{-- Shared edit/delete modals — one per entity type, filled via JS --}}
    @include('University.UniversityStructure.modals.updateCollegeModal')
    @include('University.UniversityStructure.modals.deleteCollegeModal')
    @include('University.UniversityStructure.modals.updateDepartmentModal')
    @include('University.UniversityStructure.modals.deleteDepartmentModal')
    @include('University.UniversityStructure.modals.updateProgramModal', ['allDepartments' => $allDepartments])
    @include('University.UniversityStructure.modals.deleteProgramModal')

@endsection


