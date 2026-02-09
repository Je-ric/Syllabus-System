@extends('layouts.app')

@section('content')

    <x-header-with-button
        title="College Goal Management"
        description="Set and manage goals for CLSU college programs"
    />

    <div class="clsu-shell p-6 max-w-7xl mx-auto space-y-6">

        @include('includes.error-lists')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Add Goal Section --}}
            <div class="clsu-card space-y-4">
                <form method="GET" action="{{ route('goal.index') }}" class="space-y-2">
                    <x-form.label for="collegeSelect" variant="title">Select College</x-form.label>
                    <select
                        id="collegeSelect"
                        name="college_id"
                        onchange="this.form.submit()"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-clsu-gold focus:border-clsu-gold transition">
                        <option value="">-- Choose College --</option>
                        @foreach ($colleges as $college)
                            <option value="{{ $college->id }}" @selected($selectedCollegeId == $college->id)>
                                {{ $college->name }}
                            </option>
                        @endforeach
                    </select>
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
                            class="resize-none border border-gray-300 focus:ring-clsu-gold focus:border-clsu-gold"
                            value="{{ old('goal_text') }}"
                            required>
                        </x-form.textarea>

                        <x-button variant="primary" type="submit">
                                <i class="bx bx-plus"></i> Add Goal
                        </x-button>
                    </form>
                @else
                    <p class="text-gray-600 text-sm mt-2">Please select a college to add goals.</p>
                @endif
            </div>

            {{-- Goals List --}}
            <div class="clsu-card">
                <div class="flex items-center gap-2 mb-4">
                    <i class="bx bx-target-lock text-2xl text-clsu-green"></i>
                    <h2 class="font-bold text-lg text-clsu-green">Goals</h2>
                    @if($selectedCollegeId)
                        <span class="text-md text-gray-600 underline">of {{ $colleges->firstWhere('id', $selectedCollegeId)->name }}</span>
                    @endif
                </div>

                @if($selectedCollegeId)
                    @if($goals->count())
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse text-sm">
                                <thead class="bg-clsu-gold/20 text-clsu-green">
                                    <tr>
                                        <th class="border p-3 text-left font-semibold">Goal Code</th>
                                        <th class="border p-3 text-left font-semibold">Goal Description</th>
                                        <th class="border p-3 text-left font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($goals as $goal)
                                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-clsu-gold/10 transition">
                                            <td class="border p-3 align-top font-mono text-clsu-green">
                                                {{ $goal->college_goals_code }}
                                            </td>
                                            <td class="border p-3 text-gray-800 max-w-md">
                                                {{ $goal->goal_text }}
                                            </td>
                                            <td class="border p-3 flex gap-2">
                                                <x-button
                                                    type="button"
                                                    variant="table-edit"
                                                    class="bg-clsu-gold hover:bg-clsu-gold-dark text-white"
                                                    onclick="document.getElementById('updateGoalModal_{{ $goal->id }}').showModal()">
                                                    <i class="bx bx-edit-alt"></i> Edit
                                                </x-button>
                                                <x-button
                                                    type="button"
                                                    variant="table-danger"
                                                    class="bg-red-500 hover:bg-red-600 text-white"
                                                    onclick="document.getElementById('deleteGoalModal_{{ $goal->id }}').showModal()">
                                                    <i class="bx bx-trash"></i> Delete
                                                </x-button>
                                            </td>
                                        </tr>
                                        @include('GoalObjective.modals.updateGoalModal', ['goal' => $goal])
                                        @include('GoalObjective.modals.deleteGoalModal', ['goal' => $goal])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600 text-center py-8">No goals have been set for this college yet.</p>
                    @endif
                @else
                    <p class="text-gray-600 text-center py-8">Select a college to view its goals.</p>
                @endif
            </div>

        </div>

    </div>
@endsection
