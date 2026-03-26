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

                {{-- College selector card (hidden if only one college available) --}}
                @if ($colleges->count() > 1)
                <div class="rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/60 flex items-center gap-2">
                        <i class="bx bx-buildings text-emerald-600 text-base"></i>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Select College</p>
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

                {{-- Add Goal form --}}
                @if ($selectedCollegeId)
                    <div class="rounded-2xl border border-emerald-200/70 bg-white/90 shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-emerald-100 bg-emerald-50/50 flex items-center gap-2">
                            <i class="bx bx-plus-circle text-emerald-600 text-base"></i>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Add New Goal</p>
                        </div>
                        <div class="p-4">
                            <form method="POST" action="{{ route('goal.store') }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
                                <x-form.textarea
                                    name="goal_text"
                                    rows="4"
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
                    <x-empty-state
                        icon="bx-target-lock"
                        title="No college selected"
                        message="Select a college above to add or view its goals." />
                @endif
            </div>

            {{-- ── Right: Goals List ───────────────────────────────────────────── --}}
            <div class="lg:col-span-3">
                <div class="rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm overflow-hidden">

                    {{-- Panel header --}}
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/60 flex items-center gap-2">
                        <i class="bx bx-target-lock text-emerald-600 text-base"></i>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            Goals
                            @if ($selectedCollegeId)
                                <span class="normal-case tracking-normal font-normal text-slate-400">
                                    — {{ $colleges->firstWhere('id', $selectedCollegeId)?->name }}
                                </span>
                            @endif
                        </p>
                        @if ($selectedCollegeId && $goals->count())
                            <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5
                                         rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                                {{ $goals->count() }}
                            </span>
                        @endif
                    </div>

                    <div class="p-4">
                        @if (!$selectedCollegeId)
                            <x-empty-state
                                icon="bx-target-lock"
                                title="No college selected"
                                message="Select a college from the panel on the left to view its goals." />

                        @elseif ($goals->isEmpty())
                            <x-empty-state
                                icon="bx-target-lock"
                                title="No goals yet"
                                message="No goals have been set for this college. Use the form on the left to add the first one." />

                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50/70 text-slate-500 text-xs uppercase tracking-[0.12em]">
                                            <th class="px-4 py-2.5 text-left font-semibold w-20">Code</th>
                                            <th class="px-4 py-2.5 text-left font-semibold">Goal Description</th>
                                            <th class="px-4 py-2.5 text-center font-semibold w-20">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($goals as $goal)
                                            <tr class="hover:bg-emerald-50/30 transition-colors group">
                                                <td class="px-4 py-3 align-top">
                                                    <span class="font-mono text-xs font-bold text-emerald-700
                                                                 bg-emerald-50 border border-emerald-200/70
                                                                 px-2 py-0.5 rounded-md whitespace-nowrap">
                                                        {{ $goal->college_goals_code }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-slate-700 leading-relaxed">
                                                    {{ $goal->goal_text }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="inline-flex items-center gap-1">
                                                        <button type="button"
                                                                onclick="document.getElementById('updateGoalModal_{{ $goal->id }}').showModal()"
                                                                class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition"
                                                                title="Edit goal">
                                                            <i class="bx bx-edit text-base leading-none"></i>
                                                        </button>
                                                        <button type="button"
                                                                onclick="document.getElementById('deleteGoalModal_{{ $goal->id }}').showModal()"
                                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                                                title="Delete goal">
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
    @foreach ($goals as $goal)
        @include('GoalObjective.modals.updateGoalModal', ['goal' => $goal])
        @include('GoalObjective.modals.deleteGoalModal',  ['goal' => $goal])
    @endforeach

@endsection
