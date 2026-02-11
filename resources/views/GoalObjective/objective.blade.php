@extends('layouts.app')

@section('content')

<x-header-with-button
        title="Department Objective Management"
        description="Set and manage objectives"
    />
<div class="mx-auto space-y-6 text-slate-800">

    @include('includes.error-lists')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Add Objective Section --}}
        <div class="clsu-card space-y-4 rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm">

            {{-- College Selection --}}
            <form method="GET" action="{{ route('objective.index') }}" class="space-y-2">
                <x-form.label for="collegeSelect">Select College</x-form.label>
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

            {{-- Department Selection --}}
            @if($selectedCollegeId)
                @if($departments->count())
                    <form method="GET" action="{{ route('objective.index') }}" class="space-y-2">
                        <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">

                        <x-form.label for="departmentSelect">Select Department</x-form.label>
                        <x-form.select
                            id="departmentSelect"
                            name="department_id"
                            onchange="this.form.submit()">
                            <option value="">-- Choose Department --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" @selected($selectedDepartmentId == $dept->id)>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </x-form.select>
                    </form>

                    {{-- Add Objective Form --}}
                    @if($selectedDepartmentId)
                        <form method="POST" action="{{ route('objective.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="college_id" value="{{ $selectedCollegeId }}">
                            <input type="hidden" name="department_id" value="{{ $selectedDepartmentId }}">

                            <x-form.label for="objectiveText">Add Objective</x-form.label>
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
                        <p class="text-slate-500 text-sm mt-2">Select a department to add objectives.</p>
                    @endif
                @else
                    <p class="text-slate-500 text-sm">No departments found for this college.</p>
                @endif
            @else
                <p class="text-slate-500 text-sm">Select a college to view departments.</p>
            @endif
        </div>

        {{-- Objectives List --}}
        <div class="clsu-card rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm">
            <div class="flex flex-wrap items-center gap-2 mb-4 font-semibold text-lg">
                <i class="bx bx-target-lock text-2xl text-emerald-600"></i>
                <h2 class="text-slate-800">Objectives</h2>
                @if($selectedDepartmentId)
                    <span class="text-sm text-slate-500">of {{ $departments->firstWhere('id', $selectedDepartmentId)->name }}</span>
                @endif
            </div>

            @if($selectedCollegeId)
                @if($objectives->count())
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full border-collapse text-sm">
                            <thead class="bg-emerald-50 text-emerald-800">
                                <tr>
                                    <th class="border border-slate-200 p-3 text-left text-xs uppercase tracking-[0.2em] font-semibold">Code</th>
                                    <th class="border border-slate-200 p-3 text-left text-xs uppercase tracking-[0.2em] font-semibold">Objective</th>
                                    <th class="border border-slate-200 p-3 text-left text-xs uppercase tracking-[0.2em] font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($objectives as $objective)
                                    <tr class="odd:bg-white even:bg-slate-50 hover:bg-emerald-50/60 transition">
                                        <td class="border border-slate-200 p-2 align-top font-mono text-emerald-700">
                                            {{ $objective->dept_obj_code }}
                                        </td>

                                        <td class="border border-slate-200 p-2 text-slate-700">
                                            {{ $objective->objective_text }}
                                        </td>

                                        <td class="border border-slate-200 p-2">
                                            <div class="flex flex-wrap gap-2">
                                            <x-button
                                                type="button"
                                                variant="table-edit"
                                                class="bg-amber-500 hover:bg-amber-600 text-white shadow-sm"
                                                onclick="document.getElementById('updateObjectiveModal_{{ $objective->id }}').showModal()">
                                                <i class="bx bx-edit-alt"></i> Edit
                                            </x-button>

                                            <x-button
                                                type="button"
                                                variant="table-danger"
                                                class="bg-rose-600 hover:bg-rose-700 text-white shadow-sm"
                                                onclick="document.getElementById('deleteObjectiveModal_{{ $objective->id }}').showModal()">
                                                <i class="bx bx-trash"></i> Delete
                                            </x-button>
                                            </div>
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
                    <p class="text-slate-500">No objectives found for this college.</p>
                @endif
            @else
                <p class="text-slate-500">Select a college to view its objectives.</p>
            @endif
        </div>

    </div>

</div>
@endsection
