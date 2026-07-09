@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-list-check"
        title="Department Objectives"
        desc="Define and manage learning objectives for each CLSU department">
        <x-ui.help-trigger />
        @if ($selectedCollegeId && $selectedDepartmentId)
            <x-button variant="add-button"
                onclick="document.getElementById('addObjectiveModal').showModal()">
                <i class="bx bx-plus text-base leading-none"></i> Add Objective
            </x-button>
        @endif
    </x-page-header>

    <x-help-panel module="objectives" />

    <x-panel>
        @include('includes.error-lists')

        <div class="space-y-4">

            @if ($noAssignment)
                <x-feedback-status.alert type="warning" title="No department assigned"
                    message="You have the Chair role but are not assigned to any department. Contact an administrator to be assigned." />
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @if ($colleges->count() > 1)
                    <x-card-section title="Select College" icon="bx-buildings">
                        <form method="GET" action="{{ route('objective.index') }}">
                            <x-form.select id="collegeSelect" name="college_id" onchange="this.form.submit()" :disabled="$noAssignment">
                                <option value="">— Choose College —</option>
                                @foreach ($colleges as $college)
                                    <option value="{{ $college->id }}" @selected($selectedCollegeId == $college->id)>
                                        {{ $college->name }}
                                    </option>
                                @endforeach
                            </x-form.select>
                        </form>
                    </x-card-section>
                @elseif ($noAssignment)
                    <x-card-section title="Select College" icon="bx-buildings">
                        <x-form.select disabled>
                            <option>— No department assigned —</option>
                        </x-form.select>
                    </x-card-section>
                @endif

                @if ($selectedCollegeId)
                    @if ($departments->count() > 1)
                        <x-card-section title="Select Department" icon="bx-sitemap">
                            <form method="GET" action="{{ route('objective.index') }}">
                                <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
                                <x-form.select id="departmentSelect" name="department_id" onchange="this.form.submit()" :disabled="$noAssignment">
                                    <option value="">— Choose Department —</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}" @selected($selectedDepartmentId == $dept->id)>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </x-form.select>
                            </form>
                        </x-card-section>
                    @elseif ($departments->count() === 0)
                        <x-feedback-status.alert type="info" title="No departments found"
                            message="This college has no departments configured yet." />
                    @endif
                @endif
            </div>

            <x-card-section
                title="Objectives"
                icon="bx-list-check"
                :subtitle="$selectedDepartmentId ? $departments->firstWhere('id', $selectedDepartmentId)?->name : null"
                :count="$selectedDepartmentId && $objectives->count() ? $objectives->count() : null">

                @if ($noAssignment)
                    <x-feedback-status.empty-state icon="bx-list-check" title="No department assigned"
                        message="You are not assigned to any department. Contact an administrator." />
                @elseif (!$selectedCollegeId)
                    <x-feedback-status.empty-state icon="bx-list-check" title="No college selected"
                        message="Select a college above to begin." />
                @elseif (!$selectedDepartmentId)
                    <x-feedback-status.empty-state icon="bx-list-check" title="No department selected"
                        message="Select a department to view its objectives." />
                @elseif ($objectives->isEmpty())
                    <x-feedback-status.empty-state icon="bx-list-check" title="No objectives yet"
                        message="No objectives have been set for this department." />
                @else
                    <x-table.container>
                        <x-table.table>
                            <x-table.head>
                                <x-table.row>
                                    <x-table.th class="w-20">Code</x-table.th>
                                    <x-table.th>Objective</x-table.th>
                                    <x-table.th align="center" class="w-24">Actions</x-table.th>
                                </x-table.row>
                            </x-table.head>
                            <x-table.body>
                                @foreach ($objectives as $objective)
                                    <x-table.row hover>
                                        <x-table.td class="align-top">
                                            <x-ui.code-badge>{{ $objective->dept_obj_code }}</x-ui.code-badge>
                                        </x-table.td>
                                        <x-table.td class="text-[#3f3f46] leading-relaxed align-top">
                                            {{ $objective->objective_text }}
                                        </x-table.td>
                                        <x-table.td align="center" class="align-top">
                                            <div class="inline-flex items-center gap-1">
                                                <button type="button"
                                                    onclick="document.getElementById('updateObjectiveModal_{{ $objective->id }}').showModal()"
                                                    class="p-2 rounded-lg text-[#a1a1aa] hover:text-[#2563eb] hover:bg-[#eff6ff] transition-colors"
                                                    title="Edit objective">
                                                    <i class="bx bx-edit text-base leading-none"></i>
                                                </button>
                                                <button type="button"
                                                    onclick="document.getElementById('deleteObjectiveModal_{{ $objective->id }}').showModal()"
                                                    class="p-2 rounded-lg text-[#a1a1aa] hover:text-[#e11d48] hover:bg-[#fff1f2] transition-colors"
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
            </x-card-section>

        </div>
    </x-panel>

    @if ($selectedCollegeId && $selectedDepartmentId)
        @include('GoalObjective.modals.addObjectiveModal')
    @endif

    @foreach ($objectives as $objective)
        @include('GoalObjective.modals.updateObjectiveModal', ['objective' => $objective])
        @include('GoalObjective.modals.deleteObjectiveModal',  ['objective' => $objective])
    @endforeach

@endsection
