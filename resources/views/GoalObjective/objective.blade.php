@extends('layouts.app')

@section('content')

<x-header-with-button
        title="Department Objective Management"
        description="Set and manage objectives"
    />
<div class="clsu-shell p-6 max-w-7xl mx-auto space-y-6">

    @include('includes.error-lists')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Add Objective Section --}}
        <div class="clsu-card space-y-4">

            {{-- College Selection --}}
            <form method="GET" action="{{ route('objective.index') }}" class="space-y-2">
                <x-form.label for="collegeSelect" variant="title">Select College</x-form.label>
                <select
                    id="collegeSelect"
                    name="college_id"
                    onchange="this.form.submit()"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:outline-none focus:ring-1 focus:ring-clsu-gold focus:border-clsu-gold transition"
                >
                    <option value="">-- Choose College --</option>
                    @foreach ($colleges as $college)
                        <option value="{{ $college->id }}" @selected($selectedCollegeId == $college->id)>
                            {{ $college->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            {{-- Department Selection --}}
            @if($selectedCollegeId)
                @if($departments->count())
                    <form method="GET" action="{{ route('objective.index') }}" class="space-y-2">
                        <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">

                        <x-form.label for="departmentSelect" variant="title">Select Department</x-form.label>
                        <select
                            id="departmentSelect"
                            name="department_id"
                            onchange="this.form.submit()"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:outline-none focus:ring-1 focus:ring-clsu-gold focus:border-clsu-gold transition"
                        >
                            <option value="">-- Choose Department --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" @selected($selectedDepartmentId == $dept->id)>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    {{-- Add Objective Form --}}
                    @if($selectedDepartmentId)
                        <form method="POST" action="{{ route('objective.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
                            <input type="hidden" name="department_id" value="{{ $selectedDepartmentId }}">

                            <x-form.label for="objectiveText" variant="description" isRequired>Add Objective</x-form.label>
                            <x-form.textarea
                                as="textarea"
                                id="objectiveText"
                                name="objective_text"
                                rows="3"
                                placeholder="Describe the department objective..."
                                class="resize-none"
                                value="{{ old('objective_text') }}"
                                required>
                            </x-form.textarea>

                            <x-button type="submit" variant="primary" class="flex items-center gap-1 bg-clsu-green hover:bg-clsu-green-dark text-white">
                                <i class="bx bx-plus"></i> Add Objective
                            </x-button>
                        </form>
                    @else
                        <p class="text-gray-600 text-sm mt-2">Select a department to add objectives.</p>
                    @endif
                @else
                    <p class="text-gray-600 text-sm">No departments found for this college.</p>
                @endif
            @else
                <p class="text-gray-600 text-sm">Select a college to view departments.</p>
            @endif
        </div>

        {{-- Objectives List --}}
        <div class="clsu-card">
            <div class="flex items-center gap-2 mb-4 font-bold text-lg ">
                <i class="bx bx-target-lock text-2xl text-clsu-green"></i>
                <h2 class="text-clsu-green">Objectives</h2>
                @if($selectedDepartmentId)
                    <span class="text-md text-gray-600 underline">of {{ $departments->firstWhere('id', $selectedDepartmentId)->name }}</span>
                @endif
            </div>

            @if($selectedCollegeId)
                @if($objectives->count())
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead class="bg-clsu-gold/20 text-clsu-green">
                                <tr>
                                    <th class="border p-3 text-left">Code</th>
                                    <th class="border p-3 text-left">Objective</th>
                                    <th class="border p-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($objectives as $objective)
                                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-clsu-gold/10 transition">
                                        <td class="border p-2 align-top font-mono text-clsu-green">
                                            {{ $objective->dept_obj_code }}
                                        </td>

                                        <td class="border p-2 text-gray-800">
                                            {{ $objective->objective_text }}
                                        </td>

                                        <td class="border p-2 flex gap-2">
                                            <x-button
                                                type="button"
                                                variant="table-edit"
                                                class="bg-clsu-gold hover:bg-clsu-gold-dark text-white"
                                                onclick="document.getElementById('updateObjectiveModal_{{ $objective->id }}').showModal()">
                                                <i class="bx bx-edit-alt"></i> Edit
                                            </x-button>

                                            <x-button
                                                type="button"
                                                variant="table-danger"
                                                class="bg-red-500 hover:bg-red-600 text-white"
                                                onclick="document.getElementById('deleteObjectiveModal_{{ $objective->id }}').showModal()">
                                                <i class="bx bx-trash"></i> Delete
                                            </x-button>
                                        </td>
                                    </tr>

                                    {{-- Modals --}}
                                    @include('GoalObjective.modals.updateObjectiveModal', ['objective' => $objective])
                                    @include('GoalObjective.modals.deleteObjectiveModal', ['objective' => $objective])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">No objectives found for this college.</p>
                @endif
            @else
                <p class="text-gray-600">Select a college to view its objectives.</p>
            @endif
        </div>

    </div>

</div>
@endsection
