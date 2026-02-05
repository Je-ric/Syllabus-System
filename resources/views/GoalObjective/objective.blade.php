@extends('layouts.app')

@section('content')
<div class="p-6 bg-white text-black max-w-7xl mx-auto space-y-6">

    <h1 class="text-2xl font-bold text-green-800">Objective Management</h1>

    @include('includes.error-lists')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Add Objective Section --}}
        <div class="space-y-4 bg-green-50/40 p-6 rounded-xl border border-green-200 shadow-sm">

            {{-- College Selection --}}
            <form method="GET" action="{{ route('objective.index') }}" class="space-y-2">
                <x-form.label for="collegeSelect" variant="title">Select College</x-form.label>
                <select
                    id="collegeSelect"
                    name="college_id"
                    onchange="this.form.submit()"
                    class="w-full border border-green-300 rounded-md px-3 py-2 text-gray-800 focus:outline-none focus:ring-1 focus:ring-green-600 focus:border-green-600 transition"
                >
                    <option value="">-- Choose College --</option>
                    @foreach ($colleges as $college)
                        <option value="{{ $college->id }}" @selected(request('college_id') == $college->id)>
                            {{ $college->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            {{-- Department Selection --}}
            @if(request('college_id'))
                @if($departments->count())
                    <form method="GET" action="{{ route('objective.index') }}" class="space-y-2">
                        <input type="hidden" name="college_id" value="{{ request('college_id') }}">

                        <x-form.label for="departmentSelect" variant="title">Select Department</x-form.label>
                        <select
                            id="departmentSelect"
                            name="department_id"
                            onchange="this.form.submit()"
                            class="w-full border border-green-300 rounded-md px-3 py-2 text-gray-800 focus:outline-none focus:ring-1 focus:ring-green-600 focus:border-green-600 transition"
                        >
                            <option value="">-- Choose Department --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    {{-- Add Objective Form --}}
                    @if(request('department_id'))
                        <form method="POST" action="{{ route('objective.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="college_id" value="{{ request('college_id') }}">
                            <input type="hidden" name="department_id" value="{{ request('department_id') }}">

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

                            <x-button type="submit" variant="primary">
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
        <div class="bg-green-50/30 p-6 rounded-xl border border-green-200 shadow-sm">
            <h2 class="font-semibold text-green-800 mb-4">Objectives</h2>

            @if(request('college_id'))
                @if($objectives->count())
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead class="bg-green-100 text-green-800">
                                <tr>
                                    <th class="border p-3 text-left">Code</th>
                                    <th class="border p-3 text-left">Objective</th>
                                    <th class="border p-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($objectives as $objective)
                                    <tr class="odd:bg-white even:bg-green-50">
                                        <td class="border p-2 align-top font-mono text-green-700">
                                            {{ $objective->dept_obj_code }}
                                        </td>

                                        <td class="border p-2 text-gray-800">
                                            {{ $objective->objective_text }}
                                        </td>

                                        <td class="border p-2 flex gap-2">
                                            <x-button
                                                type="button"
                                                variant="table-edit"
                                                onclick="document.getElementById('updateObjectiveModal_{{ $objective->id }}').showModal()">
                                                <i class="bx bx-edit-alt"></i> Edit
                                            </x-button>

                                            <x-button
                                                type="button"
                                                variant="table-danger"
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
