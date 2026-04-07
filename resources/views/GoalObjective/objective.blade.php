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

                @if ($colleges->count() > 1)
                    <div class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                        <div class="px-5 py-3 border-b border-[#e2e8f0] bg-[#f8fafc] flex items-center gap-2">
                            <i class="bx bx-buildings text-[#16a34a] text-base"></i>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">Select College</p>
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
                @endif

                @if ($selectedCollegeId)
                    @if ($departments->count() > 1)
                        <div class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                            <div class="px-5 py-3 border-b border-[#e2e8f0] bg-[#f8fafc] flex items-center gap-2">
                                <i class="bx bx-sitemap text-[#16a34a] text-base"></i>
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">Select Department</p>
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
                    @elseif ($departments->count() === 0)
                        <x-feedback-status.alert type="info" title="No departments found"
                            message="This college has no departments configured yet." />
                    @endif
                @endif

                @if ($selectedCollegeId && $selectedDepartmentId)
                    <div class="rounded-xl border border-[#bbf7d0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                        <div class="px-5 py-3 border-b border-[#bbf7d0] bg-[#f0fdf4] flex items-center gap-2">
                            <i class="bx bx-plus-circle text-[#16a34a] text-base"></i>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#166534]">Add New Objective</p>
                        </div>
                        <div class="p-4">
                            <form method="POST" action="{{ route('objective.store') }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="college_id"    value="{{ $selectedCollegeId }}">
                                <input type="hidden" name="department_id" value="{{ $selectedDepartmentId }}">
                                <x-form.textarea name="objective_text" rows="4"
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
                    <x-empty-state icon="bx-sitemap" title="No college selected"
                        message="Select a college above to get started." />
                @endif
            </div>

            {{-- ── Right: Objectives List ──────────────────────────────────────── --}}
            <div class="lg:col-span-3">
                <div class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

                    <div class="px-5 py-3 border-b border-[#e2e8f0] bg-[#f8fafc] flex items-center gap-2">
                        <i class="bx bx-list-check text-[#16a34a] text-base"></i>
                        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">
                            Objectives
                            @if ($selectedDepartmentId)
                                <span class="normal-case tracking-normal font-normal text-[#94a3b8]">
                                    — {{ $departments->firstWhere('id', $selectedDepartmentId)?->name }}
                                </span>
                            @endif
                        </p>
                        @if ($selectedDepartmentId && $objectives->count())
                            <x-feedback-status.status-indicator variant="brand" class="ml-auto">
                                {{ $objectives->count() }}
                            </x-feedback-status.status-indicator>
                        @endif
                    </div>

                    <div class="p-4">
                        @if (!$selectedCollegeId)
                            <x-empty-state icon="bx-list-check" title="No college selected"
                                message="Select a college from the panel on the left to begin." />
                        @elseif (!$selectedDepartmentId)
                            <x-empty-state icon="bx-list-check" title="No department selected"
                                message="Select a department to view its objectives." />
                        @elseif ($objectives->isEmpty())
                            <x-empty-state icon="bx-list-check" title="No objectives yet"
                                message="No objectives have been set for this department. Use the form on the left to add the first one." />
                        @else
                            <x-table.container>
                                <x-table.table>
                                    <x-table.head>
                                        <x-table.row>
                                            <x-table.th class="w-20">Code</x-table.th>
                                            <x-table.th>Objective</x-table.th>
                                            <x-table.th align="center" class="w-20">Actions</x-table.th>
                                        </x-table.row>
                                    </x-table.head>
                                    <x-table.body>
                                        @foreach ($objectives as $objective)
                                            <x-table.row hover>
                                                <x-table.td class="align-top">
                                                    <span class="font-mono text-[13px] font-bold text-[#166534]
                                                                 bg-[#f0fdf4] border border-[#bbf7d0]
                                                                 px-2 py-0.5 rounded-md whitespace-nowrap">
                                                        {{ $objective->dept_obj_code }}
                                                    </span>
                                                </x-table.td>
                                                <x-table.td class="text-[#475569] leading-relaxed align-top">
                                                    {{ $objective->objective_text }}
                                                </x-table.td>
                                                <x-table.td align="center" class="align-top">
                                                    <div class="inline-flex items-center gap-1">
                                                        <button type="button"
                                                            onclick="document.getElementById('updateObjectiveModal_{{ $objective->id }}').showModal()"
                                                            class="p-1.5 rounded-lg text-[#94a3b8] hover:text-[#1e40af] hover:bg-[#eff6ff] transition"
                                                            title="Edit objective">
                                                            <i class="bx bx-edit text-base leading-none"></i>
                                                        </button>
                                                        <button type="button"
                                                            onclick="document.getElementById('deleteObjectiveModal_{{ $objective->id }}').showModal()"
                                                            class="p-1.5 rounded-lg text-[#94a3b8] hover:text-rose-600 hover:bg-rose-50 transition"
                                                            title="Delete objective">
                                                            <i class="bx bx-trash text-base leading-none"></i>
                                                        </button>
                                                    </div>
                                                </x-table.td>
                                            </x-table.row>
                                        @endforeach
                                    </x-table.body>
                                </x-table.table>
                            </x-table.container>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </x-panel>

    @foreach ($objectives as $objective)
        @include('GoalObjective.modals.updateObjectiveModal', ['objective' => $objective])
        @include('GoalObjective.modals.deleteObjectiveModal',  ['objective' => $objective])
    @endforeach

@endsection
