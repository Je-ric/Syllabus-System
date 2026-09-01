@extends('layouts.app')

@section('content')

    <x-layout.page-header
        icon="bx-target-lock"
        title="College Goals"
        desc="Define and manage strategic goals for each CLSU college">
        <x-ui.help-trigger />
        @if ($selectedCollegeId)
            <x-ui.button variant="add-button"
                onclick="document.getElementById('addGoalModal').showModal()">
                <i class="bx bx-plus text-base leading-none"></i> Add Goal
            </x-ui.button>
        @endif
    </x-layout.page-header>

    <x-layout.help-panel module="goals" />

    <x-layout.panel>
        <div class="space-y-4">

            @if ($noAssignment)
                <x-feedback-status.alert type="warning" title="No college assigned"
                    message="You have the Dean role but are not assigned to any college. Contact an administrator to be assigned." />
            @endif

            {{-- College selector — only shown when admin has multiple colleges --}}
            @if ($colleges->count() > 1)
                <x-layout.card-section title="Select College" icon="bx-buildings" class="max-w-md">
                    <form method="GET" action="{{ route('goal.index') }}">
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
            @elseif ($colleges->count() === 1)
                <x-layout.card-section title="College" icon="bx-buildings" class="max-w-md">
                    <div class="relative">
                        <div class="w-full rounded-[14px] border border-[#e4e4e7] bg-[#f4f4f5] px-3 py-2 pr-9 text-[13px] text-[#a1a1aa] cursor-not-allowed truncate">
                            {{ $colleges->first()->name }}
                        </div>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="bx bx-lock-alt text-[#d4d4d8] text-base"></i>
                        </div>
                    </div>
                </x-layout.card-section>
            @elseif ($noAssignment)
                <x-layout.card-section title="Select College" icon="bx-buildings" class="max-w-md">
                    <x-form.select disabled>
                        <option>— No college assigned —</option>
                    </x-form.select>
                </x-layout.card-section>
            @endif

            {{-- Goals list --}}
            <x-layout.card-section
                title="Goals"
                icon="bx-target-lock"
                :subtitle="$selectedCollegeId ? $colleges->firstWhere('id', $selectedCollegeId)?->name : null"
                :count="$selectedCollegeId && $goals->count() ? $goals->count() : null">

                @if ($noAssignment)
                    <x-feedback-status.empty-state
                        icon="bx-target-lock"
                        title="No college assigned"
                        message="You are not assigned to any college. Contact an administrator." />

                @elseif (!$selectedCollegeId)
                    <x-feedback-status.empty-state
                        icon="bx-buildings"
                        title="No college selected"
                        message="Select a college above to view its goals." />

                @elseif ($goals->isEmpty())
                    <x-feedback-status.empty-state
                        icon="bx-target-lock"
                        title="No goals yet"
                        message="No goals have been set for this college. Click Add Goal to get started.">
                        <x-ui.button variant="add-button"
                            onclick="document.getElementById('addGoalModal').showModal()">
                            <i class="bx bx-plus text-base leading-none"></i> Add Goal
                        </x-ui.button>
                    </x-feedback-status.empty-state>

                @else
                    <x-table.container>
                        <x-table.table>
                            <x-table.head>
                                <tr>
                                    <x-table.th class="w-24">Code</x-table.th>
                                    <x-table.th>Goal Description</x-table.th>
                                    <x-table.th align="right" class="w-28">Actions</x-table.th>
                                </tr>
                            </x-table.head>
                            <x-table.body>
                                @foreach ($goals as $goal)
                                    <tr class="border-b border-[#E3E8EB] last:border-0 hover:bg-[#F9FAFA] transition-colors duration-150">

                                        {{-- Code badge --}}
                                        <x-table.td class="align-top">
                                            <x-ui.code-badge>{{ $goal->college_goals_code }}</x-ui.code-badge>
                                        </x-table.td>

                                        {{-- Goal text --}}
                                        <x-table.td class="align-top">
                                            <p class="text-[13px] text-[#394056] leading-relaxed">
                                                {{ $goal->goal_text }}
                                            </p>
                                        </x-table.td>

                                        {{-- Actions --}}
                                        <x-table.td align="right" class="align-top">
                                            <div class="inline-flex items-center gap-0.5">
                                                <x-ui.button variant="table-light-edit"
                                                    onclick="document.getElementById('updateGoalModal_{{ $goal->id }}').showModal()"
                                                    title="Edit goal">
                                                    <i class="bx bx-edit leading-none"></i>
                                                </x-ui.button>
                                                <x-ui.button variant="table-light-delete"
                                                    onclick="document.getElementById('deleteGoalModal_{{ $goal->id }}').showModal()"
                                                    title="Delete goal">
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

    @if ($selectedCollegeId)
        @include('CQI.GoalObjective.modals.addGoalModal')
    @endif

    @foreach ($goals as $goal)
        @include('CQI.GoalObjective.modals.updateGoalModal', ['goal' => $goal])
        @include('CQI.GoalObjective.modals.deleteGoalModal',  ['goal' => $goal])
    @endforeach

@endsection
