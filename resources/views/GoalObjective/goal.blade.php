@extends('layouts.app')

@section('content')
<div class="p-6 bg-white text-black max-w-7xl mx-auto space-y-6">

    <h1 class="text-xl font-bold">College Goal Management</h1>

    @include('includes.error-lists')
    @include('includes.session-success')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-4 bg-gray-50 p-4 rounded border border-gray-200">
            <form method="GET" action="{{ route('goal.index') }}" class="space-y-2">
                <label class="block font-semibold">Select College</label>
                <select
                    name="college_id"
                    onchange="this.form.submit()"
                    class="w-full border rounded px-3 py-2">
                    <option value="">-- Choose College --</option>

                    @foreach ($colleges as $college)
                        <option
                            value="{{ $college->id }}"
                            @selected(request('college_id') == $college->id)
                        >
                            {{ $college->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if(request('college_id'))
                <form method="POST" action="{{ route('goal.store') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="college_id" value="{{ request('college_id') }}">

                    <div class="space-y-2">
                        <label class="block font-semibold">Add Goal</label>
                        {{-- <input
                            type="text"
                            name="college_goals_code"
                            value="{{ old('college_goals_code') }}"
                            placeholder="Code (e.g., a, b, c)"
                            class="w-full border rounded px-3 py-2"
                            required
                        > --}}
                        <input
                            type="text"
                            value="Goal Code (auto)"
                            class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-500"
                            disabled
                        >
                        <textarea
                            name="goal_text"
                            rows="3"
                            placeholder="Goal description"
                            class="w-full border rounded px-3 py-2"
                            required
                        >{{ old('goal_text') }}</textarea>
                    </div>

                    <x-button
                        variant="add-button"
                        type="submit">
                        <i class="bx bx-plus"></i>
                        Add Goal
                    </x-button>
                </form>
            @else
                <p class="text-gray-600 text-sm">Select a college to add goals.</p>
            @endif
        </div>

        <div class="bg-gray-50 p-4 rounded border border-gray-200">
            <h2 class="font-semibold mb-3">Goals</h2>

            @if(request('college_id'))
                @if($goals->count())
                    <table class="w-full border text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2 text-left">Code</th>
                                <th class="border p-2 text-left">Goal</th>
                                <th class="border p-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($goals as $goal)
                                <tr x-data="{ editing: false }" class="odd:bg-white even:bg-gray-50">
                                    {{-- when button clicked, the value of "editing" will be = true
                                        then all input fields will be shown --}}

                                    <form
                                        method="POST"
                                        action="{{ route('goal.update', $goal->id) }}"
                                        class="contents"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <td class="border p-2 align-top">
                                            <span>{{ $goal->college_goals_code }}</span>

                                            {{-- <input
                                                x-show="editing"
                                                type="text"
                                                name="college_goals_code"
                                                value="{{ $goal->college_goals_code }}"
                                                class="w-full border rounded px-2 py-1 text-sm resize-none overflow-hidden"> --}}
                                        </td>

                                        <td class="border p-2">
                                            <span x-show="!editing">{{ $goal->goal_text }}</span>

                                            <textarea
                                                x-show="editing"
                                                name="goal_text"
                                                rows="9"
                                                class="w-full border rounded px-2 py-1 text-sm">
                                                {{ $goal->goal_text }}
                                            </textarea>
                                        </td>

                                        <td class="border p-2 space-y-1">
                                            <x-button
                                                x-show="!editing"
                                                @click="editing = true"
                                                variant="table-edit">
                                                Edit
                                            </x-button>

                                            <x-button
                                                type="submit"
                                                x-show="editing"
                                                variant="table-confirm">
                                                Save
                                            </x-button>

                                            <x-button
                                                x-show="editing"
                                                @click="editing = false"
                                                variant="table-cancel">
                                                Cancel
                                            </x-button>
                                        </td>
                                    </form>

                                    <td class="border p-2">
                                        <x-button
                                            type="button"
                                            variant="table-danger"
                                            onclick="document.getElementById('deleteGoalModal_{{ $goal->id }}').showModal()">
                                            Delete
                                        </x-button>
                                    </td>
                                </tr>
                                @include('GoalObjective.modals.deleteGoalModal', ['goal' => $goal])
                                @endforeach

                        </tbody>
                    </table>
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
