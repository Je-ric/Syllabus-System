@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-list-check"
        title="Department Objectives"
        desc="Define and manage learning objectives for each CLSU department" />

    <x-panel>
        @include('includes.error-lists')

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- ── Left: Selectors + Add Form ─────────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- College selector --}}
                <div class="rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/60 flex items-center gap-2">
                        <i class="bx bx-buildings text-emerald-600 text-base"></i>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Select College</p>
                    </div>
                    <div class="p-4">
                        <form method="GET" action="{{ route('objective.index') }}">
                            <x-form.select id="collegeSelect" name="college_id" onchange="this.form.submit()">
                                <option value="">— Choose College —</option>
                                @foreach ($colleges as $college)
                                    <option value="{{ $college->id }}" @selected($selectedCollegeId == $college->id)>
                                        {{ $college->name }}
                                    </option>
                                @endforeach
                            </x-form.select>
                        </form>
                    </div>
                </div>

                {{-- Department selector (only when college chosen) --}}
                @if ($selectedCollegeId)
                    @if ($departments->count())
                        <div class="rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/60 flex items-center gap-2">
                                <i class="bx bx-sitemap text-emerald-600 text-base"></i>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Select Department</p>
                            </div>
                            <div class="p-4">
                                <form method="GET" action="{{ route('objective.index') }}">
                                    <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
                                    <x-form.select id="departmentSelect" name="department_id" onchange="this.form.submit()">
                                        <option value="">— Choose Department —</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}" @selected($selectedDepartmentId == $dept->id)>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </x-form.select>
                                </form>
                            </div>
                        </div>
                    @else
                        <x-feedback-status.alert
                            type="info"
                            title="No departments found"
                            message="This college has no departments configured yet." />
                    @endif
                @endif

                {{-- Add Objective form (only when both selected) --}}
                @if ($selectedCollegeId && $selectedDepartmentId)
                    <div class="rounded-2xl border border-emerald-200/70 bg-white/90 shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-emerald-100 bg-emerald-50/50 flex items-center gap-2">
                            <i class="bx bx-plus-circle text-emerald-600 text-base"></i>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Add New Objective</p>
                        </div>
                        <div class="p-4">
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
                                    <x-button type="submit" variant="add-button">
                                        <i class="bx bx-plus"></i> Add Objective
                                    </x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (!$selectedCollegeId)
                    <x-empty-state
                        icon="bx-sitemap"
                        title="No college selected"
                        message="Select a college above to get started." />
                @endif
            </div>

            {{-- ── Right: Objectives List ──────────────────────────────────────── --}}
            <div class="lg:col-span-3">
                <div class="rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm overflow-hidden">

                    {{-- Panel header --}}
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/60 flex items-center gap-2">
                        <i class="bx bx-list-check text-emerald-600 text-base"></i>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            Objectives
                            @if ($selectedDepartmentId)
                                <span class="normal-case tracking-normal font-normal text-slate-400">
                                    — {{ $departments->firstWhere('id', $selectedDepartmentId)?->name }}
                                </span>
                            @endif
                        </p>
                        @if ($selectedDepartmentId && $objectives->count())
                            <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5
                                         rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                                {{ $objectives->count() }}
                            </span>
                        @endif
                    </div>

                    <div class="p-4">
                        @if (!$selectedCollegeId)
                            <x-empty-state
                                icon="bx-list-check"
                                title="No college selected"
                                message="Select a college from the panel on the left to begin." />

                        @elseif (!$selectedDepartmentId)
                            <x-empty-state
                                icon="bx-list-check"
                                title="No department selected"
                                message="Select a department to view its objectives." />

                        @elseif ($objectives->isEmpty())
                            <x-empty-state
                                icon="bx-list-check"
                                title="No objectives yet"
                                message="No objectives have been set for this department. Use the form on the left to add the first one." />

                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50/70 text-slate-500 text-xs uppercase tracking-[0.12em]">
                                            <th class="px-4 py-2.5 text-left font-semibold w-20">Code</th>
                                            <th class="px-4 py-2.5 text-left font-semibold">Objective</th>
                                            <th class="px-4 py-2.5 text-center font-semibold w-20">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($objectives as $objective)
                                            <tr class="hover:bg-emerald-50/30 transition-colors group">
                                                <td class="px-4 py-3 align-top">
                                                    <span class="font-mono text-xs font-bold text-emerald-700
                                                                 bg-emerald-50 border border-emerald-200/70
                                                                 px-2 py-0.5 rounded-md whitespace-nowrap">
                                                        {{ $objective->dept_obj_code }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-slate-700 leading-relaxed">
                                                    {{ $objective->objective_text }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="inline-flex items-center gap-1">
                                                        <button type="button"
                                                                onclick="document.getElementById('updateObjectiveModal_{{ $objective->id }}').showModal()"
                                                                class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition"
                                                                title="Edit objective">
                                                            <i class="bx bx-edit text-base leading-none"></i>
                                                        </button>
                                                        <button type="button"
                                                                onclick="document.getElementById('deleteObjectiveModal_{{ $objective->id }}').showModal()"
                                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                                                title="Delete objective">
                                                            <i class="bx bx-trash text-base leading-none"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </x-panel>

    {{-- Modals --}}
    @foreach ($objectives as $objective)
        @include('GoalObjective.modals.updateObjectiveModal', ['objective' => $objective])
        @include('GoalObjective.modals.deleteObjectiveModal',  ['objective' => $objective])
    @endforeach

@endsection
