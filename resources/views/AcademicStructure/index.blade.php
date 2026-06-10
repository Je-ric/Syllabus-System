@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-buildings"
        title="Academic Structure Management"
        desc="Manage colleges, departments, and academic programs across the institution">
        <x-button variant="add-button"
                onclick="document.getElementById('addCollegeModal').showModal()">
            <i class="bx bx-plus"></i> Add College
        </x-button>
    </x-page-header>

    @if($colleges->count())

    <div x-data="{ selectedCollege: {{ $colleges->first()->id }} }">

        <x-panel>

            <div class="grid grid-cols-12 gap-5">

                {{-- LEFT PANEL --}}
                <div class="col-span-4">

                    <div class="border border-slate-200 rounded-xl overflow-visible shadow-sm">

                        <div class="px-4 py-2.5 border-b bg-slate-50 flex items-center justify-between">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Colleges</h3>
                            <span class="text-xs text-slate-400">{{ $colleges->count() }}</span>
                        </div>

                        <div class="divide-y divide-slate-100">

                            @foreach($colleges as $college)

                                @php
                                    $deptCount = $departments->where('college_id', $college->id)->count();
                                    $programCount = $departments->where('college_id', $college->id)->flatMap(fn($d) => $d->programs)->count();
                                @endphp

                                <button
                                    @click="selectedCollege = {{ $college->id }}"
                                    class="w-full text-left px-4 py-3 hover:bg-slate-50 transition-colors duration-100 group"
                                    :class="selectedCollege === {{ $college->id }}
                                        ? 'bg-green-50 border-l-[3px] border-green-600'
                                        : 'border-l-[3px] border-transparent'">

                                    <div class="flex items-center justify-between gap-2">

                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="shrink-0 w-7 h-7 rounded-lg flex items-center justify-center"
                                                :class="selectedCollege === {{ $college->id }} ? 'bg-green-100' : 'bg-slate-100'">
                                                <i class="bx bxs-school text-sm"
                                                   :class="selectedCollege === {{ $college->id }} ? 'text-green-700' : 'text-slate-500'"></i>
                                            </span>

                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-800 truncate leading-tight">
                                                    {{ $college->name }}
                                                </p>
                                                <p class="text-xs text-slate-400 mt-0.5">
                                                    {{ $deptCount }} dept{{ $deptCount !== 1 ? 's' : '' }}
                                                    · {{ $programCount }} program{{ $programCount !== 1 ? 's' : '' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="dropdown dropdown-end shrink-0" @click.stop>
                                            <label tabindex="0"
                                                class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200 cursor-pointer transition-colors">
                                                <i class="bx bx-dots-vertical-rounded text-base"></i>
                                            </label>
                                            <ul tabindex="0"
                                                class="dropdown-content z-100 menu p-1.5 shadow-lg bg-white rounded-lg border border-slate-200 w-36 text-sm">
                                                <li>
                                                    <a class="px-3 py-1.5 rounded hover:bg-slate-50"
                                                       onclick="document.getElementById('updateCollegeModal_{{ $college->id }}').showModal()">
                                                        <i class="bx bx-edit-alt text-slate-500"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="px-3 py-1.5 rounded text-rose-600 hover:bg-rose-50"
                                                       onclick="document.getElementById('deleteCollegeModal_{{ $college->id }}').showModal()">
                                                        <i class="bx bx-trash text-rose-500"></i> Delete
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

                {{-- RIGHT PANEL --}}
                <div class="col-span-8">

                    @foreach($colleges as $college)

                        <div x-show="selectedCollege === {{ $college->id }}" x-cloak>

                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h2 class="text-base font-bold text-slate-800">{{ $college->name }}</h2>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        {{ $departments->where('college_id', $college->id)->count() }} department(s)
                                    </p>
                                </div>
                                <x-button variant="add-button" size="sm"
                                    onclick="openAddDepartmentModal({{ $college->id }})">
                                    <i class="bx bx-plus"></i> Add Department
                                </x-button>
                            </div>

                            <div class="space-y-3">

                                @forelse($departments->where('college_id', $college->id) as $dept)

                                    <div class="border border-slate-200 rounded-xl shadow-sm overflow-visible">

                                        {{-- Department Header --}}
                                        <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-200">

                                            <div class="flex items-center gap-2.5">
                                                <span class="w-6 h-6 rounded-md bg-slate-200 flex items-center justify-center shrink-0">
                                                    <i class="bx bx-building text-slate-600 text-sm"></i>
                                                </span>
                                                <div>
                                                    <span class="text-sm font-semibold text-slate-800">{{ $dept->name }}</span>
                                                    <span class="ml-2 text-xs text-slate-400">
                                                        {{ $dept->programs->count() }} program{{ $dept->programs->count() !== 1 ? 's' : '' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-1.5">

                                                <x-button variant="sm-primary"
                                                    onclick="openAddProgramModal({{ $dept->id }})">
                                                    <i class="bx bx-plus"></i> Add Program
                                                </x-button>

                                                <div class="dropdown dropdown-end">
                                                    <label tabindex="0"
                                                        class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200 cursor-pointer transition-colors">
                                                        <i class="bx bx-dots-vertical-rounded text-base"></i>
                                                    </label>
                                                    <ul tabindex="0"
                                                        class="dropdown-content z-100 menu p-1.5 shadow-lg bg-white rounded-lg border border-slate-200 w-36 text-sm">
                                                        <li>
                                                            <a class="px-3 py-1.5 rounded hover:bg-slate-50"
                                                               onclick="document.getElementById('updateDepartmentModal_{{ $dept->id }}').showModal()">
                                                                <i class="bx bx-edit-alt text-slate-500"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="px-3 py-1.5 rounded text-rose-600 hover:bg-rose-50"
                                                               onclick="document.getElementById('deleteDepartmentModal_{{ $dept->id }}').showModal()">
                                                                <i class="bx bx-trash text-rose-500"></i> Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>

                                        </div>

                                        {{-- Programs --}}
                                        <div class="divide-y divide-slate-100">

                                            @forelse($dept->programs as $program)

                                                <div class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-50 transition-colors">

                                                    <div class="flex items-center gap-2.5 min-w-0">
                                                        <span class="w-5 h-5 rounded bg-emerald-100 flex items-center justify-center shrink-0">
                                                            <i class="bx bx-book-alt text-emerald-600 text-xs"></i>
                                                        </span>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-medium text-slate-800 truncate">
                                                                {{ $program->name }}
                                                            </p>
                                                            <p class="text-xs text-slate-400 mt-0.5">
                                                                BOR {{ $program->bor_approval_no }}
                                                                &middot;
                                                                {{ \Carbon\Carbon::parse($program->bor_approval_date)->format('M d, Y') }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="dropdown dropdown-end shrink-0">
                                                        <label tabindex="0"
                                                            class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200 cursor-pointer transition-colors">
                                                            <i class="bx bx-dots-vertical-rounded text-base"></i>
                                                        </label>
                                                        <ul tabindex="0"
                                                            class="dropdown-content z-100 menu p-1.5 shadow-lg bg-white rounded-lg border border-slate-200 w-36 text-sm">
                                                            <li>
                                                                <a class="px-3 py-1.5 rounded hover:bg-slate-50"
                                                                   onclick="document.getElementById('updateProgramModal_{{ $program->id }}').showModal()">
                                                                    <i class="bx bx-edit-alt text-slate-500"></i> Edit
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="px-3 py-1.5 rounded text-rose-600 hover:bg-rose-50"
                                                                   onclick="document.getElementById('deleteProgramModal_{{ $program->id }}').showModal()">
                                                                    <i class="bx bx-trash text-rose-500"></i> Delete
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                </div>

                                            @empty

                                                <div class="px-4 py-4 text-center">
                                                    <p class="text-xs text-slate-400 italic">No programs yet.</p>
                                                </div>

                                            @endforelse

                                        </div>

                                    </div>

                                @empty

                                    <x-empty-state
                                        icon="bx-building"
                                        title="No departments found"
                                        message="Create a department to get started." />

                                @endforelse

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </x-panel>

    </div>

    @else

    <x-empty-state
        icon="bxs-school"
        title="No colleges yet"
        message="Start by adding your first college.">

        <x-button
            variant="add-button"
            onclick="document.getElementById('addCollegeModal').showModal()">
            <i class="bx bx-plus"></i> Add College
        </x-button>

    </x-empty-state>

    @endif


    @include('AcademicStructure.modals.addCollegeModal')
    @include('AcademicStructure.modals.addDepartmentModal')
    @include('AcademicStructure.modals.addProgramModal')

    @foreach ($colleges as $college)
        @include('AcademicStructure.modals.updateCollegeModal', ['college' => $college])
        @include('AcademicStructure.modals.deleteCollegeModal', ['college' => $college])
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
