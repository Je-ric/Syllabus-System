@extends('layouts.app')

@section('content')
<div class="p-6 bg-white text-black max-w-7xl mx-auto space-y-6">

    <h1 class="text-xl font-bold">Objective Management</h1>

    @include('includes.error-lists')
    @include('includes.session-success')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-4 bg-gray-50 p-4 rounded border border-gray-200">
            <form method="GET" action="{{ route('objective.index') }}" class="space-y-2">
                <label class="block font-semibold">Select College</label>
                <select
                    name="college_id"
                    onchange="this.form.submit()"
                    class="w-full border rounded px-3 py-2"
                >
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
                @if($departments->count())
                    <form method="GET" action="{{ route('objective.index') }}" class="space-y-2">
                        <input type="hidden" name="college_id" value="{{ request('college_id') }}">

                        <label class="block font-semibold">Select Department</label>
                        <select
                            name="department_id"
                            onchange="this.form.submit()"
                            class="w-full border rounded px-3 py-2">

                            <option value="">-- Choose Department --</option>

                            @foreach ($departments as $dept)
                                <option
                                    value="{{ $dept->id }}"
                                    @selected(request('department_id') == $dept->id)>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    @if(request('department_id'))
                        <form method="POST" action="{{ route('objective.store') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="college_id" value="{{ request('college_id') }}">
                            <input type="hidden" name="department_id" value="{{ request('department_id') }}">

                            <div class="space-y-2">
                                <label class="block font-semibold">Add Objective</label>

                                <input
                                    type="text"
                                    value="Goal Code (auto)"
                                    class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-500"
                                    disabled
                                >
                                <textarea
                                    name="objective_text"
                                    rows="3"
                                    placeholder="Objective description"
                                    class="w-full border rounded px-3 py-2"
                                    required
                                >{{ old('objective_text') }}</textarea>
                            </div>

                            <button type="submit"
                                    class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800">
                                Add Objective
                            </button>
                        </form>
                    @else
                        <p class="text-gray-600 text-sm">Select a department to add objectives.</p>
                    @endif
                @else
                    <p class="text-gray-600 text-sm">No departments found for this college.</p>
                @endif
            @else
                <p class="text-gray-600 text-sm">Select a college to view departments.</p>
            @endif
        </div>

        <div class="bg-gray-50 p-4 rounded border border-gray-200">
            <h2 class="font-semibold mb-3">Objectives</h2>

            @if(request('college_id'))
                @if($objectives->count())
                    <table class="w-full border text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2 text-left">Code</th>
                                <th class="border p-2 text-left">Objective</th>
                                <th class="border p-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($objectives as $objective)
                                <tr  x-data="{ editing: false }" class="odd:bg-white even:bg-gray-50">
                                    <form method="POST"
                                        action="{{ route('objective.update', $objective->id) }}"
                                        class="contents">
                                        @csrf
                                        @method('PUT')

                                        <td class="border p-2 align-top">
                                            <span>{{ $objective->dept_obj_code  }}</span>
                                        </td>

                                        <td class="border p-2">
                                            <span x-show="!editing">{{ $objective->objective_text }}</span>

                                            <textarea
                                                x-show="editing"
                                                name="objective_text"
                                                rows="3"
                                                placeholder="Objective description"
                                                class="w-full border rounded px-3 py-2"
                                                required
                                            >{{ old('objective_text', $objective->objective_text) }}</textarea>
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
                                            <form method="POST" action="{{ route('objective.destroy', $objective->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <x-button
                                                    type="submit"
                                                    variant="table-danger"
                                                    onclick="return confirm('Delete this objective?')">
                                                    Delete
                                                </x-button>
                                            </form>
                                        </td>
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
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
