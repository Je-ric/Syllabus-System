@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-bullseye"
        title="Department Objective Management"
        desc="Set and manage objectives for CLSU departments"
    />

    <x-panel>
        <div class="space-y-6 text-slate-800">
    
            @include('includes.error-lists')
    
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
                {{-- ── Add Objective Panel ─────────────────────────────────────── --}}
                <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-lg space-y-5">
    
                    <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 flex items-center gap-2">
                        <i class="bx bx-buildings text-emerald-500"></i>
                        College &amp; Department
                    </h2>
    
                    {{-- College selector --}}
                    <form method="GET" action="{{ route('objective.index') }}">
                        <x-form.select
                            id="collegeSelect"
                            name="college_id"
                            onchange="this.form.submit()">
                            <option value="">— Choose College —</option>
                            @foreach ($colleges as $college)
                                <option value="{{ $college->id }}" @selected($selectedCollegeId == $college->id)>
                                    {{ $college->name }}
                                </option>
                            @endforeach
                        </x-form.select>
                    </form>
    
                    @if ($selectedCollegeId)
                        @if ($departments->count())
    
                            {{-- Department selector --}}
                            <form method="GET" action="{{ route('objective.index') }}">
                                <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
                                <x-form.select
                                    id="departmentSelect"
                                    name="department_id"
                                    onchange="this.form.submit()">
                                    <option value="">— Choose Department —</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}" @selected($selectedDepartmentId == $dept->id)>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </x-form.select>
                            </form>
    
                            {{-- Add Objective form --}}
                            @if ($selectedDepartmentId)
                                <div class="border-t border-slate-100 pt-5 space-y-3">
                                    <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 flex items-center gap-2">
                                        <i class="bx bx-plus-circle text-emerald-500"></i>
                                        Add New Objective
                                    </h2>
                                    <form method="POST" action="{{ route('objective.store') }}" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="college_id"    value="{{ $selectedCollegeId }}">
                                        <input type="hidden" name="department_id" value="{{ $selectedDepartmentId }}">
    
                                        <x-form.textarea
                                            name="objective_text"
                                            rows="4"
                                            placeholder="Describe the department objective…"
                                            required>{{ old('objective_text') }}</x-form.textarea>
    
                                        <div class="flex justify-end">
                                            <x-button type="submit" variant="primary">
                                                <i class="bx bx-plus"></i> Add Objective
                                            </x-button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <x-empty-state
                                    icon="bx-target-lock"
                                    title="No department selected"
                                    message="Please select a department from the dropdown to add objectives." />
                            @endif
    
                        @else
                            <x-feedback-status.alert type="info" title="No departments found"
                                message="This college has no departments configured yet." />
                        @endif
    
                    @else
                        <x-empty-state
                            icon="bx-sitemap"
                            title="No college selected"
                            message="Please select a college from the dropdown to view its departments." />
                    @endif
                </div>
    
                {{-- ── Objectives List ─────────────────────────────────────────── --}}
                <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-lg">
    
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <i class="bx bx-list-check text-xl text-emerald-600"></i>
                        <h2 class="font-semibold text-slate-800">
                            Objectives
                            @if ($selectedDepartmentId)
                                <span class="text-sm font-normal text-slate-500">
                                    of {{ $departments->firstWhere('id', $selectedDepartmentId)?->name }}
                                </span>
                            @endif
                        </h2>
                        @if ($selectedDepartmentId && $objectives->count())
                            <span class="ml-auto text-xs text-slate-400">{{ $objectives->count() }} total</span>
                        @endif
                    </div>
    
                    @if (!$selectedCollegeId)
                        <x-empty-state
                            icon="bx-list-check"
                            title="No college selected"
                            message="Select a college from the dropdown to view its objectives." />
    
                    @elseif (!$selectedDepartmentId)
                        <x-empty-state
                            icon="bx-list-check"
                            title="No department selected"
                            message="Select a department from the dropdown to view its objectives." />
    
                    @elseif ($objectives->isEmpty())
                        <x-empty-state
                            icon="bx-list-check"
                            title="No objectives set"
                            message="No objectives have been set for this department yet." /    >
    
                    @else
                        <x-table.container class="rounded-xl">
                            <x-table.table>
                                <x-table.head>
                                    <tr>
                                        <x-table.th class="px-3 py-2">Code</x-table.th>
                                        <x-table.th class="px-3 py-2">Objective</x-table.th>
                                        <x-table.th class="px-3 py-2 text-right">Actions</x-table.th>
                                    </tr>
                                </x-table.head>
                                <x-table.body>
                                    @foreach ($objectives as $objective)
                                        <x-table.row striped hover>
                                            <x-table.td class="px-3 py-2 align-top">
                                                <span class="font-mono text-xs font-bold text-emerald-700">
                                                    {{ $objective->dept_obj_code }}
                                                </span>
                                            </x-table.td>
                                            <x-table.td class="px-3 py-2 text-sm text-slate-700">
                                                {{ $objective->objective_text }}
                                            </x-table.td>
                                            <x-table.td class="px-3 py-2 text-right">
                                                <div class="inline-flex items-center gap-1">
                                                    <button
                                                        type="button"
                                                        onclick="document.getElementById('updateObjectiveModal_{{ $objective->id }}').showModal()"
                                                        class="p-1.5 text-slate-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition"
                                                        title="Edit">
                                                        <i class="bx bx-edit text-base"></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onclick="document.getElementById('deleteObjectiveModal_{{ $objective->id }}').showModal()"
                                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                                        title="Delete">
                                                        <i class="bx bx-trash text-base"></i>
                                                    </button>
                                                </div>
                                            </x-table.td>
                                        </x-table.row>
                                        @include('GoalObjective.modals.updateObjectiveModal', ['objective' => $objective])
                                        @include('GoalObjective.modals.deleteObjectiveModal',  ['objective' => $objective])
                                    @endforeach
                                </x-table.body>
                            </x-table.table>
                        </x-table.container>
                    @endif
                </div>
    
            </div>
        </div>
    </x-panel>

@endsection
