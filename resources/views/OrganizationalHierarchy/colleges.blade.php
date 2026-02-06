@extends('layouts.app')

@section('content')
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium mb-6">
        ← Back to Dashboard
    </a>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900">College Dean Management</h1>
        <p class="text-slate-600 mt-1">Assign and manage deans for each college</p>
    </div>

    @if ($colleges->isEmpty())
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-8 text-center">
            <p class="text-slate-600">No colleges found. Please create colleges first.</p>
        </div>
    @else
        <div class="grid gap-6">
            @foreach ($colleges as $college)
                <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                    <div class="bg-linear-to-r from-blue-50 to-blue-100 border-b border-slate-200 px-6 py-4">
                        <h2 class="text-xl font-semibold text-slate-900">{{ $college->name }}</h2>
                    </div>

                    <div class="flex">
                        {{-- Left Column: Actions --}}
                        <div class="w-1/3 border-r border-slate-200 px-6 py-6 bg-slate-50">
                            <h3 class="text-sm font-semibold text-slate-700 uppercase mb-4">Actions</h3>
                            <button
                                type="button"
                                onclick="document.getElementById('assignDeanModal-{{ $college->id }}').showModal()"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                + Assign Dean
                            </button>

                            <div class="mt-6 pt-6 border-t border-slate-200">
                                <p class="text-xs font-semibold text-slate-600 uppercase mb-3">Departments</p>
                                <p class="text-lg font-bold text-slate-900">{{ $college->departments->count() }}</p>
                            </div>

                            <a href="{{ route('organizational.departments.index', $college->id) }}"
                               class="block mt-6 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-300 transition-colors text-center">
                                Manage Departments →
                            </a>
                        </div>

                        {{-- Right Column: Dean List --}}
                        <div class="w-2/3 px-6 py-6">
                            <h3 class="text-sm font-semibold text-slate-700 uppercase mb-4">Assigned Deans</h3>

                            @php
                                $collegeDeans = $deanAssignments[$college->id] ?? [];
                            @endphp

                            @if (empty($collegeDeans))
                                <div class="bg-slate-50 rounded-lg p-4 text-center">
                                    <p class="text-slate-500 text-sm">No deans assigned yet</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($collegeDeans as $assignment)
                                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                                            <div class="flex-1">
                                                <p class="font-semibold text-slate-900">{{ $assignment->user->name }}</p>
                                                <p class="text-sm text-slate-600">{{ $assignment->user->email }}</p>
                                            </div>
                                            <form method="POST" action="{{ route('organizational.remove-dean') }}" class="inline" onsubmit="return confirm('Remove this dean assignment?');">
                                                @csrf
                                                <input type="hidden" name="college_id" value="{{ $college->id }}">
                                                <input type="hidden" name="user_id" value="{{ $assignment->user->id }}">
                                                <button type="submit" class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors font-medium">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Modal for assigning dean --}}
                @include('OrganizationalHierarchy.modals.assignDeanModal', [
                    'collegeId' => $college->id,
                    'collegeName' => $college->name,
                    'potentialDeans' => $potentialDeans,
                ])
            @endforeach
        </div>
    @endif
@endsection
