@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-target-lock"
        title="College Goals"
        desc="Define and manage strategic goals for each CLSU college" />

    <x-panel>
        @include('includes.error-lists')

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- ── Left: Selector + Add Form ──────────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-4">

                @if ($colleges->count() > 1)
                    <div class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                        <div class="px-5 py-3 border-b border-[#e2e8f0] bg-[#f8fafc] flex items-center gap-2">
                            <i class="bx bx-buildings text-[#16a34a] text-base"></i>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">Select College</p>
                        </div>
                        <div class="p-4">
                            <form method="GET" action="{{ route('goal.index') }}">
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
                    <div class="rounded-xl border border-[#bbf7d0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                        <div class="px-5 py-3 border-b border-[#bbf7d0] bg-[#f0fdf4] flex items-center gap-2">
                            <i class="bx bx-plus-circle text-[#16a34a] text-base"></i>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#166534]">Add New Goal</p>
                        </div>
                        <div class="p-4">
                            <form method="POST" action="{{ route('goal.store') }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
                                <x-form.textarea name="goal_text" rows="4"
                                    placeholder="Describe the college goal…"
                                    required>{{ old('goal_text') }}</x-form.textarea>
                                <div class="flex justify-end">
                                    <x-button variant="add-button" type="submit">
                                        <i class="bx bx-plus"></i> Add Goal
                                    </x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <x-empty-state icon="bx-target-lock" title="No college selected"
                        message="Select a college above to add or view its goals." />
                @endif
            </div>

            {{-- ── Right: Goals List ───────────────────────────────────────────── --}}
            <div class="lg:col-span-3">
                <div class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

                    <div class="px-5 py-3 border-b border-[#e2e8f0] bg-[#f8fafc] flex items-center gap-2">
                        <i class="bx bx-target-lock text-[#16a34a] text-base"></i>
                        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">
                            Goals
                            @if ($selectedCollegeId)
                                <span class="normal-case tracking-normal font-normal text-[#94a3b8]">
                                    — {{ $colleges->firstWhere('id', $selectedCollegeId)?->name }}
                                </span>
                            @endif
                        </p>
                        @if ($selectedCollegeId && $goals->count())
                            <x-feedback-status.status-indicator variant="brand" class="ml-auto">
                                {{ $goals->count() }}
                            </x-feedback-status.status-indicator>
                        @endif
                    </div>

                    <div class="p-4">
                        @if (!$selectedCollegeId)
                            <x-empty-state icon="bx-target-lock" title="No college selected"
                                message="Select a college from the panel on the left to view its goals." />
                        @elseif ($goals->isEmpty())
                            <x-empty-state icon="bx-target-lock" title="No goals yet"
                                message="No goals have been set for this college. Use the form on the left to add the first one." />
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
                    </div>
                </div>
            </div>

        </div>
    </x-panel>

    @foreach ($goals as $goal)
        @include('GoalObjective.modals.updateGoalModal', ['goal' => $goal])
        @include('GoalObjective.modals.deleteGoalModal',  ['goal' => $goal])
    @endforeach

@endsection
