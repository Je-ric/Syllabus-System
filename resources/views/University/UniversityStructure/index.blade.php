@extends('layouts.app')

@section('content')

    <x-layout.page-header
        icon="bx-buildings"
        title="Academic Structure Management"
        desc="Manage colleges, departments, and academic programs across the institution">
        <x-ui.button variant="add-button"
                onclick="document.getElementById('addCollegeModal').showModal()">
            <i class="bx bx-plus text-base leading-none"></i> Add College
        </x-ui.button>
    </x-layout.page-header>

    @if($colleges->count())

    <div x-data="{ selectedCollege: {{ $colleges->first()->id }} }">

        <x-layout.panel>
            <div class="grid grid-cols-12 gap-5">

                {{-- ── LEFT: College list ──────────────────────────────────────── --}}
                <div class="col-span-4">
                    <div class="rounded-[12px] border border-[#E3E8EB] bg-white overflow-hidden"
                         style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);">

                        {{-- Header --}}
                        <div class="flex items-center justify-between gap-2 px-4 py-3 border-b border-[#E3E8EB] bg-[#F1F3F5]">
                            <div class="flex items-center gap-2">
                                <span class="flex items-center justify-center w-7 h-7 rounded-[8px] bg-[#D5FFF0] text-[#06754E]">
                                    <i class="bx bxs-school text-sm leading-none"></i>
                                </span>
                                <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-[#394056]">Colleges</p>
                            </div>
                            <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-[#D5FFF0] text-[#06754E] border border-[#00965F]">
                                {{ $colleges->count() }}
                            </span>
                        </div>

                        {{-- College rows --}}
                        <div class="divide-y divide-[#F1F3F5]">
                            @foreach($colleges as $college)
                                @php
                                    $deptCount    = $departments->where('college_id', $college->id)->count();
                                    $programCount = $departments->where('college_id', $college->id)->flatMap(fn($d) => $d->programs)->count();
                                @endphp

                                <button
                                    @click="selectedCollege = {{ $college->id }}"
                                    class="w-full text-left px-4 py-3 transition-colors duration-150 border-l-[3px]"
                                    :class="selectedCollege === {{ $college->id }}
                                        ? 'bg-[#EDFFF8] border-l-[#00C075]'
                                        : 'bg-white border-l-transparent hover:bg-[#F9FAFA]'">

                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="shrink-0 w-8 h-8 rounded-[8px] flex items-center justify-center transition-colors"
                                                :class="selectedCollege === {{ $college->id }} ? 'bg-[#D5FFF0]' : 'bg-[#F1F3F5]'">
                                                <i class="bx bxs-school text-sm leading-none transition-colors"
                                                   :class="selectedCollege === {{ $college->id }} ? 'text-[#06754E]' : 'text-[#72809E]'"></i>
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-[13px] font-semibold text-[#394056] truncate leading-tight">{{ $college->name }}</p>
                                                <p class="text-[11px] text-[#93A1AF] mt-0.5">
                                                    {{ $deptCount }} dept{{ $deptCount !== 1 ? 's' : '' }}
                                                    &middot; {{ $programCount }} program{{ $programCount !== 1 ? 's' : '' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="dropdown dropdown-end shrink-0" @click.stop>
                                            <label tabindex="0"
                                                class="w-7 h-7 rounded-[7px] flex items-center justify-center text-[#B4C0CA] hover:text-[#06754E] hover:bg-[#D5FFF0] cursor-pointer transition-all duration-150">
                                                <i class="bx bx-dots-vertical-rounded text-base leading-none"></i>
                                            </label>
                                            <ul tabindex="0"
                                                class="dropdown-content z-100 menu p-1.5 bg-white rounded-[10px] border border-[#E3E8EB] w-36 text-sm"
                                                style="box-shadow: 0 4px 16px rgba(16,24,40,0.10);">
                                                <li>
                                                    <a class="flex items-center gap-2 px-3 py-1.5 rounded-[7px] hover:bg-[#F1F3F5] text-[#394056] text-[13px]"
                                                       onclick="document.getElementById('updateCollegeModal_{{ $college->id }}').showModal()">
                                                        <i class="bx bx-edit-alt text-[#3197D6] text-sm"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="flex items-center gap-2 px-3 py-1.5 rounded-[7px] hover:bg-[#FFE3E2] text-[#D21B14] text-[13px]"
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

                            <x-layout.card-section icon="bx-sitemap" title="Departments & Programs"
                                :subtitle="$college->name">

                                <x-slot name="actions">
                                    <x-ui.button variant="sm-add"
                                        onclick="openAddDepartmentModal({{ $college->id }})">
                                        <i class="bx bx-plus text-base leading-none"></i> Add Department
                                    </x-ui.button>
                                </x-slot>

                                <div class="space-y-3">
                                    @forelse($departments->where('college_id', $college->id) as $dept)

                                        <div class="rounded-[12px] border border-[#E3E8EB] bg-white overflow-visible"
                                             style="box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 1px 3px rgba(16,24,40,0.06);">

                                            {{-- Department header --}}
                                            <div class="flex items-center justify-between px-4 py-3 bg-[#F9FAFA] border-b border-[#E3E8EB] rounded-t-[12px]">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-7 h-7 rounded-[8px] bg-[#DAF1FF] flex items-center justify-center shrink-0">
                                                        <i class="bx bx-building text-[#143D57] text-sm leading-none"></i>
                                                    </span>
                                                    <div>
                                                        <span class="text-[13px] font-semibold text-[#394056]">{{ $dept->name }}</span>
                                                        <span class="ml-2 text-[10.5px] font-bold px-1.5 py-0.5 rounded-full bg-[#DAF1FF] text-[#143D57] border border-[#143D57]">
                                                            {{ $dept->programs->count() }} program{{ $dept->programs->count() !== 1 ? 's' : '' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="dropdown dropdown-end">
                                                    <label tabindex="0"
                                                        class="w-7 h-7 rounded-[7px] flex items-center justify-center text-[#B4C0CA] hover:text-[#143D57] hover:bg-[#DAF1FF] cursor-pointer transition-all duration-150">
                                                        <i class="bx bx-dots-vertical-rounded text-base leading-none"></i>
                                                    </label>
                                                    <ul tabindex="0"
                                                        class="dropdown-content z-100 menu p-1.5 bg-white rounded-[10px] border border-[#E3E8EB] w-36 text-sm"
                                                        style="box-shadow: 0 4px 16px rgba(16,24,40,0.10);">
                                                        <li>
                                                            <a class="flex items-center gap-2 px-3 py-1.5 rounded-[7px] hover:bg-[#F1F3F5] text-[#394056] text-[13px]"
                                                               onclick="document.getElementById('updateDepartmentModal_{{ $dept->id }}').showModal()">
                                                                <i class="bx bx-edit-alt text-[#3197D6] text-sm"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="flex items-center gap-2 px-3 py-1.5 rounded-[7px] hover:bg-[#FFE3E2] text-[#D21B14] text-[13px]"
                                                               onclick="document.getElementById('deleteDepartmentModal_{{ $dept->id }}').showModal()">
                                                                <i class="bx bx-trash text-sm"></i> Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            {{-- Program rows --}}
                                            <div class="divide-y divide-[#F1F3F5]">
                                                @forelse($dept->programs as $program)
                                                    @php
                                                        $role = $program->pivot->role;
                                                        $otherDepts = $program->departments->where('id', '!=', $dept->id);
                                                    @endphp
                                                    <div class="flex items-center justify-between px-4 py-2.5 hover:bg-[#F9FAFA] transition-colors duration-150">
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
                                                                        <span class="shrink-0 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-[#D5FFF0] text-[#06754E] border border-[#00965F]">
                                                                            Primary
                                                                        </span>
                                                                    @else
                                                                        <span class="shrink-0 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-[#FFF3CD] text-[#856404] border border-[#FFC107]">
                                                                            Supporting
                                                                        </span>
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
                                                                    {{-- @if($otherDepts->count())
                                                                        <span class="text-[10px] text-[#93A1AF]">&middot;</span>
                                                                        @foreach($otherDepts as $other)
                                                                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-[#F1F3F5] text-[#72809E] border border-[#E3E8EB]"
                                                                                  title="{{ $other->pivot->role === 'primary' ? 'Primary' : 'Supporting' }}: {{ $other->name }}">
                                                                                {{ Str::limit($other->name, 30) }}
                                                                                ({{ $other->pivot->role === 'primary' ? 'P' : 'S' }})
                                                                            </span>
                                                                        @endforeach
                                                                    @endif --}}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="dropdown dropdown-end shrink-0 relative">
                                                            <label tabindex="0"
                                                                class="w-7 h-7 rounded-[7px] flex items-center justify-center text-[#B4C0CA] hover:text-[#06754E] hover:bg-[#D5FFF0] cursor-pointer transition-all duration-150">
                                                                <i class="bx bx-dots-vertical-rounded text-base leading-none"></i>
                                                            </label>
                                                            <ul tabindex="0"
                                                                class="dropdown-content z-150 menu p-1.5 bg-white rounded-[10px] border border-[#E3E8EB] w-36 text-sm"
                                                                style="box-shadow: 0 4px 16px rgba(16,24,40,0.10);">
                                                                <li>
                                                                    <a class="flex items-center gap-2 px-3 py-1.5 rounded-[7px] hover:bg-[#F1F3F5] text-[#394056] text-[13px]"
                                                                       onclick="document.getElementById('updateProgramModal_{{ $program->id }}').showModal()">
                                                                        <i class="bx bx-edit-alt text-[#3197D6] text-sm"></i> Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="flex items-center gap-2 px-3 py-1.5 rounded-[7px] hover:bg-[#FFE3E2] text-[#D21B14] text-[13px]"
                                                                       onclick="document.getElementById('deleteProgramModal_{{ $program->id }}').showModal()">
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
                                                onclick="document.getElementById('addProgramModal').showModal()">
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


    @include('University.UniversityStructure.modals.addCollegeModal')
    @include('University.UniversityStructure.modals.addDepartmentModal')
    @include('University.UniversityStructure.modals.addProgramModal', ['allDepartments' => $allDepartments])

    @foreach ($colleges as $college)
        @include('University.UniversityStructure.modals.updateCollegeModal', ['college' => $college])
        @include('University.UniversityStructure.modals.deleteCollegeModal',  ['college' => $college])
        @foreach ($departments->where('college_id', $college->id) as $dept)
            @include('University.UniversityStructure.modals.updateDepartmentModal', ['dept' => $dept])
            @include('University.UniversityStructure.modals.deleteDepartmentModal', ['dept' => $dept])
        @endforeach
    @endforeach

    {{-- Program modals rendered once per unique program to avoid duplicates --}}
    @foreach ($programs as $program)
        @include('University.UniversityStructure.modals.updateProgramModal', ['program' => $program, 'allDepartments' => $allDepartments])
        @include('University.UniversityStructure.modals.deleteProgramModal', ['program' => $program])
    @endforeach

@endsection

@push('scripts')
<script>
function openAddDepartmentModal(collegeId) {
    const input = document.getElementById('addDepartment_college_id');
    if (input) input.value = collegeId;
    document.getElementById('addDepartmentModal').showModal();
}
</script>
@endpush
