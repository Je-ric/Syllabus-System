@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <a href="{{ route('organizational.colleges.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium mb-4">
            ← Back to Colleges
        </a>
        <h1 class="text-3xl font-bold text-slate-900">{{ $college->name }}</h1>
        <p class="text-slate-600 mt-1">Assign and manage chairs for each department</p>
    </div>

    @if ($college->departments->isEmpty())
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-8 text-center">
            <p class="text-slate-600">No departments found in this college.</p>
        </div>
    @else
        <div class="grid gap-6">
            @foreach ($college->departments as $department)
                <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                    <div class="bg-linear-to-r from-purple-50 to-purple-100 border-b border-slate-200 px-6 py-4">
                        <h2 class="text-xl font-semibold text-slate-900">{{ $department->name }}</h2>
                    </div>

                    <div class="flex">
                        {{-- Left Column: Actions --}}
                        <div class="w-1/3 border-r border-slate-200 px-6 py-6 bg-slate-50">
                            <h3 class="text-sm font-semibold text-slate-700 uppercase mb-4">Actions</h3>
                            <button
                                type="button"
                                onclick="document.getElementById('assignChairModal-{{ $department->id }}').showModal()"
                                class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors">
                                + Assign Chair
                            </button>
                            <p class="text-xs text-slate-600 mt-3">Add a new chair to this department</p>
                        </div>

                        {{-- Right Column: Chair & Faculty List --}}
                        <div class="w-2/3 px-6 py-6">
                            <h3 class="text-sm font-semibold text-slate-700 uppercase mb-4">Assigned Chairs</h3>

                            @php
                                $departmentChairs = $chairAssignments[$department->id] ?? [];
                            @endphp

                            @if (empty($departmentChairs))
                                <div class="bg-slate-50 rounded-lg p-4 text-center mb-6">
                                    <p class="text-slate-500 text-sm">No chairs assigned yet</p>
                                </div>
                            @else
                                <div class="space-y-3 mb-6">
                                    @foreach ($departmentChairs as $assignment)
                                        <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border border-purple-200">
                                            <div class="flex-1">
                                                <p class="font-semibold text-slate-900">{{ $assignment->user->name }}</p>
                                                <p class="text-sm text-slate-600">{{ $assignment->user->email }}</p>
                                            </div>
                                            <form method="POST" action="{{ route('organizational.remove-chair') }}" class="inline" onsubmit="return confirm('Remove this chair assignment?');">
                                                @csrf
                                                <input type="hidden" name="department_id" value="{{ $department->id }}">
                                                <input type="hidden" name="user_id" value="{{ $assignment->user->id }}">
                                                <button type="submit" class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors font-medium">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Faculty List --}}
                            <div class="pt-4 border-t border-slate-200">
                                <h4 class="text-sm font-semibold text-slate-700 uppercase mb-3">Faculty Members</h4>
                                @php
                                    $faculty = \App\Models\UserAssignment::where('department_id', $department->id)
                                        ->where('context', 'faculty')
                                        ->with('user')
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                @endphp

                                @if ($faculty->isEmpty())
                                    <p class="text-sm text-slate-500">No faculty assigned yet.</p>
                                @else
                                    <div class="space-y-2">
                                        @foreach ($faculty as $member)
                                            <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                                <p class="font-medium text-slate-800 text-sm">{{ $member->user->name }}</p>
                                                <p class="text-xs text-slate-600">{{ $member->user->email }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal for assigning chair --}}
                @include('OrganizationalHierarchy.modals.assignChairModal', [
                    'departmentId' => $department->id,
                    'departmentName' => $department->name,
                    'potentialChairs' => $potentialChairs,
                ])
            @endforeach
        </div>
    @endif

@endsection
