@extends('layouts.app')

@section('content')

    <x-header-with-button
        title="College Goal Management"
        description="Set and manage goals for CLSU college programs"
    />

    <div class="space-y-6 text-slate-800">

        @include('includes.error-lists')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ── Add Goal Panel ─────────────────────────────────────────── --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm space-y-5">

                <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 flex items-center gap-2">
                    <i class="bx bx-buildings text-emerald-500"></i>
                    College
                </h2>

                {{-- College selector (auto-submits) --}}
                <form method="GET" action="{{ route('goal.index') }}">
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

                {{-- Add Goal form (only when college is selected) --}}
                @if ($selectedCollegeId)
                    <div class="border-t border-slate-100 pt-5 space-y-3">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 flex items-center gap-2">
                            <i class="bx bx-plus-circle text-emerald-500"></i>
                            Add New Goal
                        </h2>

                        <form method="POST" action="{{ route('goal.store') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">

                            <x-form.textarea
                                name="goal_text"
                                rows="4"
                                placeholder="Describe the college goal…"
                                required>{{ old('goal_text') }}</x-form.textarea>

                            <div class="flex justify-end">
                                <x-button variant="primary" type="submit">
                                    <i class="bx bx-plus"></i> Add Goal
                                </x-button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 py-8 text-center">
                        <i class="bx bx-buildings text-3xl text-slate-300"></i>
                        <p class="mt-2 text-sm text-slate-400">Select a college above to add goals.</p>
                    </div>
                @endif
            </div>

            {{-- ── Goals List ─────────────────────────────────────────────── --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm">

                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <i class="bx bx-target-lock text-xl text-emerald-600"></i>
                    <h2 class="font-semibold text-slate-800">
                        Goals
                        @if ($selectedCollegeId)
                            <span class="text-sm font-normal text-slate-500">
                                of {{ $colleges->firstWhere('id', $selectedCollegeId)?->name }}
                            </span>
                        @endif
                    </h2>
                    @if ($selectedCollegeId && $goals->count())
                        <span class="ml-auto text-xs text-slate-400">{{ $goals->count() }} total</span>
                    @endif
                </div>

                @if (!$selectedCollegeId)
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 py-10 text-center">
                        <i class="bx bx-target-lock text-4xl text-slate-300"></i>
                        <p class="mt-2 text-sm text-slate-400">Select a college to view its goals.</p>
                    </div>

                @elseif ($goals->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 py-10 text-center">
                        <i class="bx bx-target-lock text-4xl text-slate-300"></i>
                        <p class="mt-2 text-sm text-slate-400">No goals have been set for this college yet.</p>
                    </div>

                @else
                    <x-table.container class="rounded-xl">
                        <x-table.table>
                            <x-table.head>
                                <tr>
                                    <x-table.th class="px-3 py-2">Code</x-table.th>
                                    <x-table.th class="px-3 py-2">Goal Description</x-table.th>
                                    <x-table.th class="px-3 py-2 text-right">Actions</x-table.th>
                                </tr>
                            </x-table.head>
                            <x-table.body>
                                @foreach ($goals as $goal)
                                    <x-table.row striped hover>
                                        <x-table.td class="px-3 py-2 align-top">
                                            <span class="font-mono text-xs font-bold text-emerald-700">
                                                {{ $goal->college_goals_code }}
                                            </span>
                                        </x-table.td>
                                        <x-table.td class="px-3 py-2 text-sm text-slate-700 max-w-sm">
                                            {{ $goal->goal_text }}
                                        </x-table.td>
                                        <x-table.td class="px-3 py-2 text-right">
                                            <div class="inline-flex items-center gap-1">
                                                <button
                                                    type="button"
                                                    onclick="document.getElementById('updateGoalModal_{{ $goal->id }}').showModal()"
                                                    class="p-1.5 text-slate-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition"
                                                    title="Edit">
                                                    <i class="bx bx-edit text-base"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    onclick="document.getElementById('deleteGoalModal_{{ $goal->id }}').showModal()"
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                                    title="Delete">
                                                    <i class="bx bx-trash text-base"></i>
                                                </button>
                                            </div>
                                        </x-table.td>
                                    </x-table.row>
                                    @include('GoalObjective.modals.updateGoalModal', ['goal' => $goal])
                                    @include('GoalObjective.modals.deleteGoalModal',  ['goal' => $goal])
                                @endforeach
                            </x-table.body>
                        </x-table.table>
                    </x-table.container>
                @endif
            </div>

        </div>
    </div>

@endsection
