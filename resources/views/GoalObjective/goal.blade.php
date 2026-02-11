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

                        <x-form.label for="goalText" variant="description" isRequired>Add New Goal</x-form.label>

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

                        <x-button variant="primary" type="submit">
                            <i class="bx bx-plus"></i> Add Goal
                        </x-button>
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
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full border-collapse text-sm">
                                <thead class="bg-emerald-50 text-emerald-800">
                                    <tr>
                                        <th class="border border-slate-200 p-3 text-left text-xs uppercase tracking-[0.2em] font-semibold">Goal Code</th>
                                        <th class="border border-slate-200 p-3 text-left text-xs uppercase tracking-[0.2em] font-semibold">Goal Description</th>
                                        <th class="border border-slate-200 p-3 text-left text-xs uppercase tracking-[0.2em] font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($goals as $goal)
                                        <tr class="odd:bg-white even:bg-slate-50 hover:bg-emerald-50/60 transition">
                                            <td class="border border-slate-200 p-3 align-top font-mono text-emerald-700">
                                                {{ $goal->college_goals_code }}
                                            </td>
                                            <td class="border border-slate-200 p-3 text-slate-700 max-w-md">
                                                {{ $goal->goal_text }}
                                            </td>
                                            <td class="border border-slate-200 p-3">
                                                <div class="flex flex-wrap gap-2">
                                                <x-button
                                                    type="button"
                                                    variant="table-edit"
                                                    class="bg-amber-500 hover:bg-amber-600 text-white shadow-sm"
                                                    onclick="document.getElementById('updateGoalModal_{{ $goal->id }}').showModal()">
                                                    <i class="bx bx-edit-alt"></i> Edit
                                                </x-button>
                                                <x-button
                                                    type="button"
                                                    variant="table-danger"
                                                    class="bg-rose-600 hover:bg-rose-700 text-white shadow-sm"
                                                    onclick="document.getElementById('deleteGoalModal_{{ $goal->id }}').showModal()">
                                                    <i class="bx bx-trash"></i> Delete
                                                </x-button>
                                                </div>
                                            </td>
                                        </tr>
                                        @include('GoalObjective.modals.updateGoalModal', ['goal' => $goal])
                                        @include('GoalObjective.modals.deleteGoalModal', ['goal' => $goal])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
