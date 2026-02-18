@extends('layouts.app')

@section('content')

    <x-header-with-button
        title="College Goal Management"
        description="Set and manage goals for CLSU college programs"
    />

    <div class="mx-auto space-y-6 text-slate-800">

        @include('includes.error-lists')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Add Goal Section --}}
            <div class="clsu-card space-y-4 rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm">
                <form method="GET" action="{{ route('goal.index') }}" class="space-y-2">
                    <x-form.label for="collegeSelect" variant="title">Select College</x-form.label>
                    <x-form.select
                        id="collegeSelect"
                        name="college_id"
                        onchange="this.form.submit()">
                        <option value="">-- Choose College --</option>
                        @foreach ($colleges as $college)
                            <option value="{{ $college->id }}" @selected($selectedCollegeId == $college->id)>
                                {{ $college->name }}
                            </option>
                        @endforeach
                    </x-form.select>
                </form>

                @if($selectedCollegeId)
                    <form method="POST" action="{{ route('goal.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">

                        <x-form.label for="goalText"
                                    variant="description"
                                    isRequired>
                                    Add New Goal
                        </x-form.label>
                        <x-form.textarea
                            as="textarea"
                            id="goalText"
                            name="goal_text"
                            rows="3"
                            placeholder="Describe the college goal..."
                            class="resize-none"
                            value="{{ old('goal_text') }}"
                            required>
                        </x-form.textarea>

                        <div class="flex justify-end">
                            <x-button variant="primary" type="submit">
                                <i class="bx bx-plus"></i> Add Goal
                            </x-button>
                        </div>
                    </form>
                @else
                    <p class="text-slate-500 text-sm mt-2">Please select a college to add goals.</p>
                @endif
            </div>

            {{-- Goals List --}}
            <div class="clsu-card rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <i class="bx bx-target-lock text-2xl text-emerald-600"></i>
                    <h2 class="font-semibold text-lg text-slate-800">Goals</h2>
                    @if($selectedCollegeId)
                        <span class="text-sm text-slate-500">of {{ $colleges->firstWhere('id', $selectedCollegeId)->name }}</span>
                    @endif
                </div>

                @if($selectedCollegeId)
                    @if($goals->count())
                        <x-table.container class="rounded-xl">
                            <x-table.table>
                                <x-table.head>
                                    <tr>
                                        <x-table.th class="p-3">Code</x-table.th>
                                        <x-table.th class="p-3">Goal Description</x-table.th>
                                        <x-table.th class="p-3">Actions</x-table.th>
                                    </tr>
                                </x-table.head>
                                <x-table.body>
                                    @foreach ($goals as $goal)
                                        <x-table.row striped hover>
                                            <x-table.td class="p-3 align-top font-mono text-emerald-700">
                                                {{ $goal->college_goals_code }}
                                            </x-table.td>
                                            <x-table.td class="p-3 text-slate-700 max-w-md">
                                                {{ $goal->goal_text }}
                                            </x-table.td>
                                            <x-table.td class="p-3">
                                                <div class="flex items-center gap-2">
                                                <x-button
                                                    type="button"
                                                    variant="table-edit"
                                                    class="bg-amber-500 hover:bg-amber-600 text-white shadow-sm"
                                                    onclick="document.getElementById('updateGoalModal_{{ $goal->id }}').showModal()">
                                                    <i class="bx bx-edit-alt"></i>
                                                </x-button>
                                                <x-button
                                                    type="button"
                                                    variant="table-danger"
                                                    class="bg-rose-600 hover:bg-rose-700 text-white shadow-sm"
                                                    onclick="document.getElementById('deleteGoalModal_{{ $goal->id }}').showModal()">
                                                    <i class="bx bx-trash"></i>
                                                </x-button>
                                                </div>
                                            </x-table.td>
                                        </x-table.row>
                                        @include('GoalObjective.modals.updateGoalModal', ['goal' => $goal])
                                        @include('GoalObjective.modals.deleteGoalModal', ['goal' => $goal])
                                    @endforeach
                                </x-table.body>
                            </x-table.table>
                        </x-table.container>
                    @else
                        <p class="text-slate-500 text-center py-8">No goals have been set for this college yet.</p>
                    @endif
                @else
                    <p class="text-slate-500 text-center py-8">Select a college to view its goals.</p>
                @endif
            </div>

        </div>

    </div>
@endsection
