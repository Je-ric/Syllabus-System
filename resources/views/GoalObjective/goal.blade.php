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

            {{-- College selector (admin sees all; dean sees only theirs, hidden if single) --}}
            @if ($colleges->count() > 1)
                <x-card-section title="Select College" icon="bx-buildings" class="max-w-md">
                    <form method="GET" action="{{ route('goal.index') }}">
                        <x-form.select
                            id="collegeSelect"
                            name="college_id"
                            onchange="this.form.submit()">
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
            @endif

            {{-- Goals table --}}
            <x-card-section
                title="Goals"
                icon="bx-target-lock"
                :subtitle="$selectedCollegeId ? $colleges->firstWhere('id', $selectedCollegeId)?->name : null"
                :count="$selectedCollegeId && $goals->count() ? $goals->count() : null">

                @if (!$selectedCollegeId)
                    <x-empty-state icon="bx-target-lock" title="No college selected"
                        message="Select a college above to view its goals." />
                @elseif ($goals->isEmpty())
                    <x-empty-state icon="bx-target-lock" title="No goals yet"
                        message="No goals have been set for this college. Click \"Add Goal\" to create the first one." />
                @else
                    <x-table.container>
                        <x-table.table>
                            <x-table.head>
                                <x-table.row>
                                    <x-table.th class="w-20">Code</x-table.th>
                                    <x-table.th>Goal Description</x-table.th>
                                    <x-table.th align="center" class="w-20">Actions</x-table.th>
                                </x-table.row>
                            </x-table.head>
                            <x-table.body>
                                @foreach ($goals as $goal)
                                    <x-table.row hover>
                                        <x-table.td class="align-top">
                                            <span class="font-mono text-[13px] font-bold text-[#166534]
                                                         bg-[#f0fdf4] border border-[#bbf7d0]
                                                         px-2 py-0.5 rounded-md whitespace-nowrap">
                                                {{ $goal->college_goals_code }}
                                            </span>
                                        </x-table.td>
                                        <x-table.td class="text-[#475569] leading-relaxed align-top">
                                            {{ $goal->goal_text }}
                                        </x-table.td>
                                        <x-table.td align="center" class="align-top">
                                            <div class="inline-flex items-center gap-1">
                                                <button type="button"
                                                    onclick="document.getElementById('updateGoalModal_{{ $goal->id }}').showModal()"
                                                    class="p-1.5 rounded-lg text-[#94a3b8] hover:text-[#1e40af] hover:bg-[#eff6ff] transition"
                                                    title="Edit goal">
                                                    <i class="bx bx-edit text-base leading-none"></i>
                                                </button>
                                                <button type="button"
                                                    onclick="document.getElementById('deleteGoalModal_{{ $goal->id }}').showModal()"
                                                    class="p-1.5 rounded-lg text-[#94a3b8] hover:text-rose-600 hover:bg-rose-50 transition"
                                                    title="Delete goal">
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

    @if ($selectedCollegeId)
        @include('GoalObjective.modals.addGoalModal')
    @endif

    @foreach ($goals as $goal)
        @include('GoalObjective.modals.updateGoalModal', ['goal' => $goal])
        @include('GoalObjective.modals.deleteGoalModal',  ['goal' => $goal])
    @endforeach

@endsection
