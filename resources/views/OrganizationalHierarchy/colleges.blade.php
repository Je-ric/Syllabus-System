@extends('layouts.app')

@section('content')

    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-black hover:underline text-sm font-medium">
            ← Back to Dashboard
        </a>
    </div>

    {{-- Header --}}
    <div class="mb-8 border-b pb-4">
        <h1 class="text-3xl font-bold text-black">College Dean Management</h1>
        <p class="text-gray-600 mt-1 text-sm">Assign and manage deans for each college</p>
    </div>

    {{-- No Colleges --}}
    @if ($colleges->isEmpty())
        <div class="border border-black rounded-lg p-10 text-center">
            <p class="text-gray-600">No colleges found. Please create colleges first.</p>
        </div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach ($colleges as $college)
                <div class="border border-black rounded-xl bg-white shadow-sm">

                    {{-- College Header --}}
                    <div class="border-b border-black px-5 py-4">
                        <h2 class="text-lg font-bold text-black">
                            {{ $college->name }}
                        </h2>
                    </div>

                    <div class="p-5">

                        {{-- Current Dean --}}
                        @if ($deanAssignments->get($college->id)?->first())
                            <div class="border border-black rounded-lg p-4 mb-4">

                                <p class="text-xs uppercase tracking-wide text-gray-500">
                                    Dean
                                </p>

                                <p class="text-lg font-semibold text-black mt-1">
                                    {{ $deanAssignments->get($college->id)->first()->user->name }}
                                </p>

                                <p class="text-sm text-gray-600 mb-4">
                                    {{ $deanAssignments->get($college->id)->first()->user->email }}
                                </p>

                                {{-- Remove --}}
                                <form action="{{ route('organizational.remove-dean') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="college_id" value="{{ $college->id }}">
                                    <input type="hidden" name="user_id"
                                        value="{{ $deanAssignments->get($college->id)->first()->user->id }}">

                                    <button type="submit"
                                        class="w-full py-2 bg-red-600 text-white rounded-md font-medium hover:bg-black transition">
                                        Delete Dean
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="border border-dashed border-black rounded-lg p-6 text-center mb-4">
                                <p class="text-gray-500 text-sm">No dean assigned</p>
                            </div>
                        @endif

                        {{-- Manage Departments --}}
                        <a href="{{ route('organizational.departments.index', $college->id) }}"
                            class="block w-full text-center border border-black py-2 rounded-md font-medium hover:bg-black hover:text-white transition mb-3">
                            Manage Departments ({{ $college->departments->count() }})
                        </a>

                        {{-- Assign Dean --}}
                        @if (!$deanAssignments->get($college->id)?->first() && $potentialDeans->count() > 0)
                            <button onclick="document.getElementById('assignDeanModal-{{ $college->id }}').showModal()"
                                class="w-full border border-black py-2 rounded-md font-medium bg-black text-white hover:bg-red-600 transition">
                                Assign Dean
                            </button>
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
