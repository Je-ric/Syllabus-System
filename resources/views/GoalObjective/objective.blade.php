@extends('layouts.app')

@section('content')

    <x-layout.page-header
        icon="bx-list-check"
        title="Department Objectives"
        desc="Define and manage learning objectives for each CLSU department">
        <x-ui.help-trigger />
        @if ($selectedCollegeId && $selectedDepartmentId)
            <x-ui.button variant="add-button"
                onclick="document.getElementById('addObjectiveModal').showModal()">
                <i class="bx bx-plus text-base leading-none"></i> Add Objective
            </x-ui.button>
        @endif
    </x-layout.page-header>

    <x-layout.help-panel module="objectives" />

    <x-layout.panel>
        @include('includes.error-lists')

        <div class="space-y-4">

            @if ($noAssignment)
                <x-feedback-status.alert type="warning" title="No department assigned"
                    message="You have the Chair role but are not assigned to any department. Contact an administrator to be assigned." />
            @endif

            {{-- College + Department selectors --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                @if ($colleges->count() > 1)
                    <x-layout.card-section title="Select College" icon="bx-buildings">
                        <form method="GET" action="{{ route('objective.index') }}">
                            <x-form.select
                                id="collegeSelect"
                                name="college_id"
                                onchange="this.form.submit()"
                                :disabled="$noAssignment">
                                <option value="">— Choose College —</option>
                                @foreach ($colleges as $college)
                                    <option
                                        value="{{ $college->id }}"
                                        @selected($selectedCollegeId == $college->id)>
                                        {{ $college->name }}
                                    </option>
                                @endforeach
                            </x-form.select>
                        </form>
                    </x-layout.card-section>

                @elseif ($noAssignment)
                    <x-layout.card-section title="Select College" icon="bx-buildings">
                        <x-form.select disabled>
                            <option>— No department assigned —</option>
                        </x-form.select>
                    </x-layout.card-section>
                @endif

                @if ($selectedCollegeId)
                    @if ($departments->count() > 1)
                        <x-layout.card-section title="Select Department" icon="bx-sitemap">
                            <form method="GET" action="{{ route('objective.index') }}">
                                <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
                                <x-form.select
                                    id="departmentSelect"
                                    name="department_id"
                                    onchange="this.form.submit()"
                                    :disabled="$noAssignment">
                                    <option value="">— Choose Department —</option>
                                    @foreach ($departments as $dept)
                                        <option
                                            value="{{ $dept->id }}"
                                            @selected($selectedDepartmentId == $dept->id)>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </x-form.select>
                            </form>
                        </x-layout.card-section>

                    @elseif ($departments->count() === 0)
                        <x-feedback-status.alert type="info" title="No departments found"
                            message="This college has no departments configured yet." />
                    @endif
                @endif

            </div>

            {{-- Objectives list --}}
            <x-layout.card-section
                title="Objectives"
                icon="bx-list-check"
                :subtitle="$selectedDepartmentId ? $departments->firstWhere('id', $selectedDepartmentId)?->name : null"
                :count="$selectedDepartmentId && $objectives->count() ? $objectives->count() : null">

                @if ($noAssignment)
                    <x-feedback-status.empty-state
                        icon="bx-list-check"
                        title="No department assigned"
                        message="You are not assigned to any department. Contact an administrator." />

                @elseif (!$selectedCollegeId)
                    <x-feedback-status.empty-state
                        icon="bx-buildings"
                        title="No college selected"
                        message="Select a college above to begin." />

                @elseif (!$selectedDepartmentId)
                    <x-feedback-status.empty-state
                        icon="bx-sitemap"
                        title="No department selected"
                        message="Select a department to view its objectives." />

                @elseif ($objectives->isEmpty())
                    <x-feedback-status.empty-state
                        icon="bx-list-check"
                        title="No objectives yet"
                        message="No objectives have been set for this department. Click Add Objective to get started.">
                        <x-ui.button variant="add-button"
                            onclick="document.getElementById('addObjectiveModal').showModal()">
                            <i class="bx bx-plus text-base leading-none"></i> Add Objective
                        </x-ui.button>
                    </x-feedback-status.empty-state>

                @else
                    <x-table.container>
                        <x-table.table>
                            <x-table.head>
                                <tr>
                                    <x-table.th class="w-24">Code</x-table.th>
                                    <x-table.th>Objective</x-table.th>
                                    <x-table.th align="right" class="w-28">Actions</x-table.th>
                                </tr>
                            </x-table.head>
                            <x-table.body>
                                @foreach ($objectives as $objective)
                                    <tr class="border-b border-[#E3E8EB] last:border-0 hover:bg-[#F9FAFA] transition-colors duration-150">

                                        {{-- Code badge --}}
                                        <x-table.td class="align-top">
                                            <x-ui.code-badge>{{ $objective->dept_obj_code }}</x-ui.code-badge>
                                        </x-table.td>

                                        {{-- Objective text --}}
                                        <x-table.td class="align-top">
                                            <p class="text-[13px] text-[#394056] leading-relaxed">
                                                {{ $objective->objective_text }}
                                            </p>
                                        </x-table.td>

                                        {{-- Actions --}}
                                        <x-table.td align="right" class="align-top">
                                            <div class="inline-flex items-center gap-0.5">
                                                <x-ui.button variant="table-light-edit"
                                                    onclick="document.getElementById('updateObjectiveModal_{{ $objective->id }}').showModal()"
                                                    title="Edit objective">
                                                    <i class="bx bx-edit leading-none"></i>
                                                </x-ui.button>
                                                <x-ui.button variant="table-light-delete"
                                                    onclick="document.getElementById('deleteObjectiveModal_{{ $objective->id }}').showModal()"
                                                    title="Delete objective">
                                                    <i class="bx bx-trash leading-none"></i>
                                                </x-ui.button>
                                            </div>
                                        </x-table.td>

                                    </tr>
                                @endforeach
                            </x-table.body>
                        </x-table.table>
                    </x-table.container>
                @endif

            </x-layout.card-section>

        </div>
    </x-layout.panel>

    @if ($selectedCollegeId && $selectedDepartmentId)
        @include('GoalObjective.modals.addObjectiveModal')
    @endif

    @foreach ($objectives as $objective)
        @include('GoalObjective.modals.updateObjectiveModal', ['objective' => $objective])
        @include('GoalObjective.modals.deleteObjectiveModal',  ['objective' => $objective])
    @endforeach

@endsection
