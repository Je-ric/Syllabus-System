@extends('layouts.app')

@section('content')
<div class="p-6 bg-white text-black max-w-7xl mx-auto space-y-6">

    <h1 class="text-2xl font-bold text-green-800">College Goal Management</h1>

    @include('includes.error-lists')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Add Goal Section --}}
        <div class="space-y-4 bg-green-50/40 p-6 rounded-xl border border-green-200 shadow-sm">
            <form method="GET" action="{{ route('goal.index') }}" class="space-y-2">
                <x-form.label for="collegeSelect" variant="title">Select College</x-form.label>
                <select
                    id="collegeSelect"
                    name="college_id"
                    onchange="this.form.submit()"
                    class="w-full border border-green-300 rounded-md px-3 py-2 text-gray-800 focus:outline-none focus:ring-1 focus:ring-green-600 focus:border-green-600 transition">
                    <option value="">-- Choose College --</option>

                    @foreach ($colleges as $college)
                        <option value="{{ $college->id }}" @selected(request('college_id') == $college->id)>
                            {{ $college->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if(request('college_id'))
                <form method="POST" action="{{ route('goal.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="college_id" value="{{ request('college_id') }}">

                    <x-form.label for="goalText" variant="description" isRequired>Add Goal</x-form.label>

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
                <p class="text-gray-600 text-sm mt-2">Select a college to add goals.</p>
            @endif
        </div>

        {{-- Goals List --}}
        <div class="bg-green-50/30 p-6 rounded-xl border border-green-200 shadow-sm">
            <h2 class="font-semibold text-green-800 mb-4">Goals</h2>

            @if(request('college_id'))
                @if($goals->count())
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead class="bg-green-100 text-green-800">
                                <tr>
                                    <th class="border p-3 text-left">Code</th>
                                    <th class="border p-3 text-left">Goal</th>
                                    <th class="border p-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($goals as $goal)
                                    <tr class="odd:bg-white even:bg-green-50">
                                        <td class="border p-2 align-top font-mono text-green-700">
                                            {{ $goal->college_goals_code }}
                                        </td>

                                        <td class="border p-2 text-gray-800">
                                            {{ $goal->goal_text }}
                                        </td>

                                        <td class="border p-2 flex gap-2">
                                            <x-button
                                                type="button"
                                                variant="table-edit"
                                                onclick="document.getElementById('updateGoalModal_{{ $goal->id }}').showModal()">
                                                <i class="bx bx-edit-alt"></i> Edit
                                            </x-button>

                                            <x-button
                                                type="button"
                                                variant="table-danger"
                                                onclick="document.getElementById('deleteGoalModal_{{ $goal->id }}').showModal()">
                                                <i class="bx bx-trash"></i> Delete
                                            </x-button>
                                        </td>
                                    </tr>

                                    {{-- Modals --}}
                                    @include('GoalObjective.modals.updateGoalModal', ['goal' => $goal])
                                    @include('GoalObjective.modals.deleteGoalModal', ['goal' => $goal])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">No goals found for this college.</p>
                @endif
            @else
                <p class="text-gray-600">Select a college to view its goals.</p>
            @endif
        </div>

    </div>

</div>
@endsection
