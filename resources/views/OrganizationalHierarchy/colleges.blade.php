@extends('layouts.app')

@section('content')

    {{-- Back --}}
    <div class="mb-6">
        <x-button variant="cancel" href="{{ route('dashboard') }}">
            ← Back to Dashboard
        </x-button>
    </div>

    {{-- Header --}}
    <div class="mb-8 border-b-4 border-yellow-500 pb-4">
        <h1 class="text-3xl font-bold text-green-900">CLSU College Dean Management</h1>
        <p class="text-green-800 mt-1 text-sm">
            Assign and manage deans for each college
        </p>
    </div>

    {{-- No Colleges --}}
    @if ($colleges->isEmpty())
        <div class="border border-green-800 rounded-lg p-10 text-center bg-green-50">
            <p class="text-green-900 font-medium">
                No colleges found. Please create colleges first.
            </p>
        </div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach ($colleges as $college)
                <div class="border border-green-900 rounded-xl bg-white overflow-hidden">

                    {{-- College Header --}}
                    <div class="bg-green-900 px-5 py-4">
                        <h2 class="text-lg font-bold text-white tracking-wide">
                            {{ $college->name }}
                        </h2>
                    </div>

                    <div class="p-5">

                        {{-- Current Dean --}}
                        @if ($deanAssignments->get($college->id)?->first())
                            <div class="border border-green-800 rounded-lg p-4 mb-4 bg-green-50">

                                <p class="text-xs uppercase tracking-wider text-green-800 font-semibold">
                                    College Dean
                                </p>

                                <p class="text-lg font-bold text-green-900 mt-1">
                                    {{ $deanAssignments->get($college->id)->first()->user->name }}
                                </p>

                                <p class="text-sm text-green-800 mb-4">
                                    {{ $deanAssignments->get($college->id)->first()->user->email }}
                                </p>

                                {{-- Delete --}}
                                <form action="{{ route('organizational.remove-dean') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="college_id" value="{{ $college->id }}">
                                    <input type="hidden" name="user_id"
                                        value="{{ $deanAssignments->get($college->id)->first()->user->id }}">

                                    <button type="submit"
                                        class="w-full py-2 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition">
                                        Remove Dean
                                    </button>
                                </form>

                            </div>
                        @else
                            <div class="border border-dashed border-green-800 rounded-lg p-6 text-center mb-4 bg-green-50">
                                <p class="text-green-900 text-sm font-medium">
                                    No dean assigned
                                </p>
                            </div>
                        @endif

                        {{-- Manage Departments --}}
                        <x-button href="{{ route('organizational.departments.index', $college->id) }}"
                            variant="secondary">
                            Manage Departments ({{ $college->departments->count() }})
                        </x-button>

                        {{-- Assign Dean --}}
                        @if (!$deanAssignments->get($college->id)?->first() && $potentialDeans->count() > 0)
                            <x-button onclick="document.getElementById('assignDeanModal-{{ $college->id }}').showModal()"
                                    variant="secondary">
                                Assign Dean
                            </x-button>
                        @endif

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
