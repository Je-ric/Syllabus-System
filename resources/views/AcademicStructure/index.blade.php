@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-buildings"
        title="Academic Structure Management"
        desc="Manage colleges, departments, and academic programs across the institution">
        <x-ui.button variant="add-button"
                onclick="document.getElementById('addCollegeModal').showModal()">
            <i class="bx bx-plus text-base leading-none"></i> Add College
        </x-ui.button>
    </x-page-header>

    @if($colleges->count())

    <div x-data="{ selectedCollege: {{ $colleges->first()->id }} }">

        <x-panel>
            <div class="grid grid-cols-12 gap-5">

                {{-- ── LEFT: College list ──────────────────────────────────────── --}}
                <div class="col-span-4">
                    <div class="rounded-[16px] border border-[#e4e4e7] bg-white overflow-hidden"
                         style="box-shadow: 0 1px 8px rgba(0,0,0,0.05);">

                        {{-- Header --}}
                        <div class="flex items-center justify-between gap-2 px-4 py-3 border-b border-[#e4e4e7] bg-[#f4f4f5]">
                            <div class="flex items-center gap-2">
                                <span class="flex items-center justify-center w-7 h-7 rounded-[10px] bg-[#dcfce7] text-[#16a34a]">
                                    <i class="bx bxs-school text-sm leading-none"></i>
                                </span>
                                <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#52525b]">Colleges</p>
                            </div>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-[#dcfce7] text-[#166534] border border-[#86efac]">
                                {{ $colleges->count() }}
                            </span>
                        </div>

                        {{-- College rows --}}
                        <div class="divide-y divide-[#f4f4f5]">
                            @foreach($colleges as $college)
                                @php
                                    $deptCount    = $departments->where('college_id', $college->id)->count();
                                    $programCount = $departments->where('college_id', $college->id)->flatMap(fn($d) => $d->programs)->count();
                                @endphp

                                <button
                                    @click="selectedCollege = {{ $college->id }}"
                                    class="w-full text-left px-4 py-3 transition-colors duration-100 border-l-[3px]"
                                    :class="selectedCollege === {{ $college->id }}
                                        ? 'bg-[#f0fdf4] border-l-[#16a34a]'
                                        : 'bg-white border-l-transparent hover:bg-[#fafafa]'">

                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="shrink-0 w-8 h-8 rounded-[10px] flex items-center justify-center transition-colors"
                                                :class="selectedCollege === {{ $college->id }} ? 'bg-[#dcfce7]' : 'bg-[#f4f4f5]'">
                                                <i class="bx bxs-school text-sm leading-none transition-colors"
                                                   :class="selectedCollege === {{ $college->id }} ? 'text-[#16a34a]' : 'text-[#71717a]'"></i>
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-[13px] font-semibold text-[#09090b] truncate leading-tight">{{ $college->name }}</p>
                                                <p class="text-[11px] text-[#a1a1aa] mt-0.5">
                                                    {{ $deptCount }} dept{{ $deptCount !== 1 ? 's' : '' }}
                                                    &middot; {{ $programCount }} program{{ $programCount !== 1 ? 's' : '' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="dropdown dropdown-end shrink-0" @click.stop>
                                            <label tabindex="0"
                                                class="w-7 h-7 rounded-[8px] flex items-center justify-center text-[#a1a1aa] hover:text-[#16a34a] hover:bg-[#dcfce7] cursor-pointer transition-colors">
                                                <i class="bx bx-dots-vertical-rounded text-base leading-none"></i>
                                            </label>
                                            <ul tabindex="0"
                                                class="dropdown-content z-100 menu p-1.5 bg-white rounded-[14px] border border-[#e4e4e7] w-36 text-sm"
                                                style="box-shadow: 0 4px 16px rgba(0,0,0,0.08);">
                                                <li>
                                                    <a class="flex items-center gap-2 px-3 py-1.5 rounded-[8px] hover:bg-[#f4f4f5] text-[#3f3f46] text-[13px]"
                                                       onclick="document.getElementById('updateCollegeModal_{{ $college->id }}').showModal()">
                                                        <i class="bx bx-edit-alt text-[#2563eb] text-sm"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="flex items-center gap-2 px-3 py-1.5 rounded-[8px] hover:bg-[#fff1f2] text-[#e11d48] text-[13px]"
                                                       onclick="document.getElementById('deleteCollegeModal_{{ $college->id }}').showModal()">
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
                <div class="col-span-8">
                    @foreach($colleges as $college)
                        <div x-show="selectedCollege === {{ $college->id }}" x-cloak>

                            <x-card-section icon="bx-sitemap" title="Departments & Programs"
                                :subtitle="$college->name">

                                <x-slot name="actions">
                                    <x-ui.button variant="sm-add"
                                        onclick="openAddDepartmentModal({{ $college->id }})">
                                        <i class="bx bx-plus text-base leading-none"></i> Add Department
                                    </x-ui.button>
                                </x-slot>

                                <div class="space-y-3">
                                    @forelse($departments->where('college_id', $college->id) as $dept)

                                        <div class="rounded-[14px] border border-[#e4e4e7] bg-white overflow-visible"
                                             style="box-shadow: 0 1px 4px rgba(0,0,0,0.04);">

                                            {{-- Department header --}}
                                            <div class="flex items-center justify-between px-4 py-3 bg-[#fafafa] border-b border-[#e4e4e7] rounded-t-[14px]">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-7 h-7 rounded-[10px] bg-[#dbeafe] flex items-center justify-center shrink-0">
                                                        <i class="bx bx-building text-[#2563eb] text-sm leading-none"></i>
                                                    </span>
                                                    <div>
                                                        <span class="text-[13px] font-semibold text-[#09090b]">{{ $dept->name }}</span>
                                                        <span class="ml-2 text-[11px] font-semibold px-1.5 py-0.5 rounded-full bg-[#eff6ff] text-[#2563eb] border border-[#bfdbfe]">
                                                            {{ $dept->programs->count() }} program{{ $dept->programs->count() !== 1 ? 's' : '' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="dropdown dropdown-end">
                                                    <label tabindex="0"
                                                        class="w-7 h-7 rounded-[8px] flex items-center justify-center text-[#a1a1aa] hover:text-[#2563eb] hover:bg-[#dbeafe] cursor-pointer transition-colors">
                                                        <i class="bx bx-dots-vertical-rounded text-base leading-none"></i>
                                                    </label>
                                                    <ul tabindex="0"
                                                        class="dropdown-content z-100 menu p-1.5 bg-white rounded-[14px] border border-[#e4e4e7] w-36 text-sm"
                                                        style="box-shadow: 0 4px 16px rgba(0,0,0,0.08);">
                                                        <li>
                                                            <a class="flex items-center gap-2 px-3 py-1.5 rounded-[8px] hover:bg-[#f4f4f5] text-[#3f3f46] text-[13px]"
                                                               onclick="document.getElementById('updateDepartmentModal_{{ $dept->id }}').showModal()">
                                                                <i class="bx bx-edit-alt text-[#2563eb] text-sm"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="flex items-center gap-2 px-3 py-1.5 rounded-[8px] hover:bg-[#fff1f2] text-[#e11d48] text-[13px]"
                                                               onclick="document.getElementById('deleteDepartmentModal_{{ $dept->id }}').showModal()">
                                                                <i class="bx bx-trash text-sm"></i> Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            {{-- Program rows --}}
                                            <div class="divide-y divide-[#f4f4f5]">
                                                @forelse($dept->programs as $program)
                                                    <div class="flex items-center justify-between px-4 py-2.5 hover:bg-[#fafafa] transition-colors">
                                                        <div class="flex items-center gap-2.5 min-w-0">
                                                            <span class="w-6 h-6 rounded-[8px] bg-[#dcfce7] flex items-center justify-center shrink-0">
                                                                <i class="bx bx-book-alt text-[#16a34a] text-xs leading-none"></i>
                                                            </span>
                                                            <div class="min-w-0">
                                                                <p class="text-[13px] font-medium text-[#09090b] truncate">{{ $program->name }}</p>
                                                                <p class="text-[11px] text-[#a1a1aa] mt-0.5">
                                                                    {{ $program->bor_approval_no }}
                                                                    &middot;
                                                                    {{ \Carbon\Carbon::parse($program->bor_approval_date)->format('M d, Y') }}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="dropdown dropdown-end shrink-0 relative">
                                                            <label tabindex="0"
                                                                class="w-7 h-7 rounded-[8px] flex items-center justify-center text-[#a1a1aa] hover:text-[#16a34a] hover:bg-[#dcfce7] cursor-pointer transition-colors">
                                                                <i class="bx bx-dots-vertical-rounded text-base leading-none"></i>
                                                            </label>
                                                            <ul tabindex="0"
                                                                class="dropdown-content z-150 menu p-1.5 bg-white rounded-[14px] border border-[#e4e4e7] w-36 text-sm"
                                                                style="box-shadow: 0 4px 16px rgba(0,0,0,0.08);">
                                                                <li>
                                                                    <a class="flex items-center gap-2 px-3 py-1.5 rounded-[8px] hover:bg-[#f4f4f5] text-[#3f3f46] text-[13px]"
                                                                       onclick="document.getElementById('updateProgramModal_{{ $program->id }}').showModal()">
                                                                        <i class="bx bx-edit-alt text-[#2563eb] text-sm"></i> Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="flex items-center gap-2 px-3 py-1.5 rounded-[8px] hover:bg-[#fff1f2] text-[#e11d48] text-[13px]"
                                                                       onclick="document.getElementById('deleteProgramModal_{{ $program->id }}').showModal()">
                                                                        <i class="bx bx-trash text-sm"></i> Delete
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="px-4 py-3 text-center text-[12px] text-[#a1a1aa] italic">
                                                        No programs yet.
                                                    </div>
                                                @endforelse
                                            </div>

                                            {{-- Add program footer --}}
                                            <button
                                                type="button"
                                                class="w-full flex items-center justify-center gap-1.5 py-2 text-[12px] font-medium
                                                       text-[#16a34a] hover:bg-[#f0fdf4] border-t border-dashed border-[#86efac]
                                                       transition-colors rounded-b-[14px]"
                                                onclick="openAddProgramModal({{ $dept->id }})">
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

                            </x-card-section>

                        </div>
                    @endforeach
                </div>

            </div>
        </x-panel>

    </div>

    @else

        <x-panel>
            <x-feedback-status.empty-state
                icon="bxs-school"
                title="No colleges yet"
                message="Start by adding your first college to build the academic structure.">
                <x-ui.button variant="add-button"
                    onclick="document.getElementById('addCollegeModal').showModal()">
                    <i class="bx bx-plus text-base leading-none"></i> Add College
                </x-ui.button>
            </x-feedback-status.empty-state>
        </x-panel>

    @endif


    @include('AcademicStructure.modals.addCollegeModal')
    @include('AcademicStructure.modals.addDepartmentModal')
    @include('AcademicStructure.modals.addProgramModal')

    @foreach ($colleges as $college)
        @include('AcademicStructure.modals.updateCollegeModal', ['college' => $college])
        @include('AcademicStructure.modals.deleteCollegeModal',  ['college' => $college])
        @foreach ($departments->where('college_id', $college->id) as $dept)
            @include('AcademicStructure.modals.updateDepartmentModal', ['dept' => $dept])
            @include('AcademicStructure.modals.deleteDepartmentModal', ['dept' => $dept])
            @foreach ($dept->programs as $program)
                @include('AcademicStructure.modals.updateProgramModal', ['program' => $program])
                @include('AcademicStructure.modals.deleteProgramModal', ['program' => $program])
            @endforeach
        @endforeach
    @endforeach

@endsection

@push('scripts')
<script>
function openAddDepartmentModal(collegeId) {
    const input = document.getElementById('addDepartment_college_id');
    if (input) input.value = collegeId;
    document.getElementById('addDepartmentModal').showModal();
}

function openAddProgramModal(departmentId) {
    const input = document.getElementById('addProgram_department_id');
    if (input) input.value = departmentId;
    document.getElementById('addProgramModal').showModal();
}
</script>
@endpush
