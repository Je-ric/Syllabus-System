@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-target-lock"
        title="College Goals"
        desc="Define and manage strategic goals for each CLSU college">
        @if ($selectedCollegeId)
            <x-button variant="add-button"
                onclick="document.getElementById('addGoalModal').showModal()">
                <i class="bx bx-plus text-base leading-none"></i> Add Goal
            </x-button>
        @endif
    </x-page-header>

    <x-panel>
        @include('includes.error-lists')

        <div class="space-y-4">

            @if ($noAssignment)
                <x-feedback-status.alert type="warning" title="No college assigned"
                    message="You have the Dean role but are not assigned to any college. Contact an administrator to be assigned." />
            @endif

            @if ($colleges->count() > 1)
                <x-card-section title="Select College" icon="bx-buildings" class="max-w-md">
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
                </x-card-section>
            @elseif ($noAssignment)
                <x-card-section title="Select College" icon="bx-buildings" class="max-w-md">
                    <x-form.select disabled>
                        <option>— No college assigned —</option>
                    </x-form.select>
                </x-card-section>
            @endif

            <x-card-section
                title="Goals"
                icon="bx-target-lock"
                :subtitle="$selectedCollegeId ? $colleges->firstWhere('id', $selectedCollegeId)?->name : null"
                :count="$selectedCollegeId && $goals->count() ? $goals->count() : null">

                @if ($noAssignment)
                    <x-empty-state icon="bx-target-lock" title="No college assigned"
                        message="You are not assigned to any college. Contact an administrator." />
                @elseif (!$selectedCollegeId)
                    <x-empty-state icon="bx-target-lock" title="No college selected"
                        message="Select a college above to view its goals." />
                @elseif ($goals->isEmpty())
                    <x-empty-state icon="bx-target-lock" title="No goals yet"
                        message="No goals have been set for this college." />
                @else
                    <x-table.container>
                        <x-table.table>
                            <x-table.head>
                                <x-table.row>
                                    <x-table.th class="w-20">Code</x-table.th>
                                    <x-table.th>Goal Description</x-table.th>
                                    <x-table.th align="center" class="w-24">Actions</x-table.th>
                                </x-table.row>
                            </x-table.head>
                            <x-table.body>
                                @foreach ($goals as $goal)
                                    <x-table.row hover>
                                        <x-table.td class="align-top">
                                            <span class="inline-flex items-center font-mono text-[12px] font-bold text-[#166534]
                                                         bg-[#dcfce7] border border-[#86efac]
                                                         px-2 py-0.5 rounded-[8px] whitespace-nowrap">
                                                {{ $goal->college_goals_code }}
                                            </span>
                                        </x-table.td>
                                        <x-table.td class="text-[#3f3f46] leading-relaxed align-top">
                                            {{ $goal->goal_text }}
                                        </x-table.td>
                                        <x-table.td align="center" class="align-top">
                                            <div class="inline-flex items-center gap-0.5">
                                                <button type="button"
                                                    onclick="document.getElementById('updateGoalModal_{{ $goal->id }}').showModal()"
                                                    class="p-1.5 rounded-[8px] text-[#a1a1aa] hover:text-[#2563eb] hover:bg-[#eff6ff] transition-colors"
                                                    title="Edit goal">
                                                    <i class="bx bx-edit text-[15px] leading-none"></i>
                                                </button>
                                                <button type="button"
                                                    onclick="document.getElementById('deleteGoalModal_{{ $goal->id }}').showModal()"
                                                    class="p-1.5 rounded-[8px] text-[#a1a1aa] hover:text-[#e11d48] hover:bg-[#fff1f2] transition-colors"
                                                    title="Delete goal">
                                                    <i class="bx bx-trash text-[15px] leading-none"></i>
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

    @if ($selectedCollegeId)
        @include('GoalObjective.modals.addGoalModal')
    @endif

    @foreach ($goals as $goal)
        @include('GoalObjective.modals.updateGoalModal', ['goal' => $goal])
        @include('GoalObjective.modals.deleteGoalModal',  ['goal' => $goal])
    @endforeach

@endsection
