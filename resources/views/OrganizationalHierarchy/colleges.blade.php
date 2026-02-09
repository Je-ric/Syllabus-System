@extends('layouts.app')

@section('content')

    <x-header-with-button title="CLSU College Dean Management" description="Assign and manage deans for each college">
        <x-button variant="cancel" href="{{ route('dashboard') }}">← Back to Dashboard</x-button>
    </x-header-with-button>

    {{-- No Colleges --}}
    @if ($colleges->isEmpty())
        <div class="border border-green-800 rounded-lg p-10 text-center bg-green-50">
            <p class="text-green-900 font-medium">
                No colleges found. Please create colleges first.
            </p>
        </div>
    @else
        {{-- COLLEGE GRID --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach ($colleges as $college)
                <div class="border border-green-900 rounded-xl bg-white shadow-sm overflow-hidden flex flex-col">

                    {{-- College Header --}}
                    <div class="bg-green-900 px-5 py-3 flex items-center gap-2">
                        <i class="bx bxs-school text-yellow-400 text-xl"></i>
                        <h2 class="text-lg font-bold text-white tracking-wide">
                            {{ $college->name }}
                        </h2>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-5 flex flex-col gap-4 flex-1">

                        {{-- Current Dean --}}
                        @if ($deanAssignments->get($college->id)?->first())
                            <div class="border border-green-800 rounded-lg p-4 bg-green-50">
                                <p
                                    class="text-xs uppercase tracking-wider text-green-800 font-semibold flex items-center gap-1">
                                    <i class="bx bxs-user-badge text-green-900"></i>
                                    College Dean
                                </p>

                                @if ($deanAssignments->get($college->id)?->first())
                                    @php
                                        $dean = $deanAssignments->get($college->id)->first()->user;
                                    @endphp

                                    <div class="flex justify-between items-start mt-2 gap-4">
                                        <div>
                                            <p class="text-lg font-bold text-green-900 leading-tight">
                                                {{ $dean->name }}
                                            </p>
                                            <p class="text-sm text-green-800 mb-3">
                                                {{ $dean->email }}
                                            </p>
                                        </div>

                                        <form action="{{ route('organizational.remove-dean') }}" method="POST"
                                            class="shrink-0">
                                            @csrf
                                            <input type="hidden" name="college_id" value="{{ $college->id }}">
                                            <input type="hidden" name="user_id" value="{{ $dean->id }}">

                                            <button type="submit"
                                                class="py-2 px-4 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition flex items-center gap-1">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p class="text-sm text-green-700 mt-2">No dean assigned yet.</p>
                                @endif
                            </div>
                        @else
                            <div class="border border-dashed border-green-800 rounded-lg p-6 text-center bg-green-50">
                                <p class="text-green-900 text-sm font-medium">
                                    No dean assigned
                                </p>
                            </div>
                        @endif

                        {{-- ACTIONS --}}
                        <div class="mt-auto space-y-2">

                            <x-button href="{{ route('organizational.departments.index', $college->id) }}"
                                variant="secondary" class="w-full">
                                <i class="bx bx-building-house mr-1"></i>
                                Manage Departments ({{ $college->departments->count() }})
                            </x-button>

                            @if (!$deanAssignments->get($college->id)?->first() && $potentialDeans->count() > 0)
                                <x-button
                                    onclick="document.getElementById('assignDeanModal-{{ $college->id }}').showModal()"
                                    variant="secondary" class="w-full">
                                    <i class="bx bx-user-plus mr-1"></i>
                                    Assign Dean
                                </x-button>
                            @endif

                        </div>

                    </div>
                </div>

                {{-- Modal --}}
                @include('OrganizationalHierarchy.modals.assignDeanModal', [
                    'collegeId' => $college->id,
                    'collegeName' => $college->name,
                    'potentialDeans' => $potentialDeans,
                ])
            @endforeach

        </div>
    @endif

@endsection
