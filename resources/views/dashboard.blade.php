@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-10">


        @auth
        <div class="gap-6">

            {{-- Admin Tools --}}
            @if(auth()->user()->hasRole('admin'))
                <div class="p-4 rounded shadow hover:shadow-md bg-white border border-gray-200 text-center">
                    <a href="{{ route('accounts.approval') }}" class="font-semibold text-gray-800 hover:text-green-600">
                        User Management
                    </a>
                </div>

                <div class="p-4 rounded shadow hover:shadow-md bg-white border border-gray-200 text-center">
                    <a href="{{ route('academic.structure.index') }}" class="font-semibold text-gray-800 hover:text-green-600">
                        Academic Structure
                    </a>
                </div>

                <div class="p-4 rounded shadow hover:shadow-md bg-white border border-gray-200 text-center">
                    <a href="{{ route('organizational.colleges.index') }}" class="font-semibold text-gray-800 hover:text-green-600">
                        Organizational Hierarchy
                    </a>
                </div>

                <div class="p-4 rounded shadow hover:shadow-md bg-white border border-gray-200 text-center">
                    <a href="{{ route('academic.calendars.index') }}" class="font-semibold text-gray-800 hover:text-green-600">
                        Academic Calendars
                    </a>
                </div>
            @endif

            {{-- College Goals --}}
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('dean'))
                <div class="p-4 rounded shadow hover:shadow-md bg-white border border-gray-200 text-center">
                    <a href="{{ route('goal.index') }}" class="font-semibold text-gray-800 hover:text-green-600">
                        College Goals
                    </a>
                </div>
            @endif

            {{-- Department Management --}}
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('chair') || auth()->user()->hasRole('dean'))
                <div class="p-4 rounded shadow hover:shadow-md bg-white border border-gray-200 text-center">
                    <a href="{{ route('objective.index') }}" class="font-semibold text-gray-800 hover:text-green-600">
                        Department Objectives
                    </a>
                </div>

                <div class="p-4 rounded shadow hover:shadow-md bg-white border border-gray-200 text-center">
                    <a href="{{ route('programs.index') }}" class="font-semibold text-gray-800 hover:text-green-600">
                        PEOs & POs
                    </a>
                </div>

                <div class="p-4 rounded shadow hover:shadow-md bg-white border border-gray-200 text-center">
                    <a href="{{ route('courses.index') }}" class="font-semibold text-gray-800 hover:text-green-600">
                        Manage Courses
                    </a>
                </div>
            @endif

            {{-- Syllabi --}}
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('faculty'))
                <div class="p-4 rounded shadow hover:shadow-md bg-white border border-gray-200 text-center">
                    <a href="{{ route('syllabus.index') }}" class="font-semibold text-gray-800 hover:text-green-600">
                        Syllabi
                    </a>
                </div>
            @endif

        </div>
    @endauth

    <h1 class="text-2xl font-bold text-center">Button Component</h1>

    {{-- TABLE ACTION BUTTONS --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold">Table Action Buttons</h2>
        <div class="flex flex-wrap gap-2">
            <x-button variant="table-confirm">Confirm</x-button>
            <x-button variant="table-disable">Disable</x-button>
            <x-button variant="table-restore">Restore</x-button>
            <x-button variant="table-danger">Danger</x-button>
            <x-button variant="table-manage">Manage</x-button>
            <x-button variant="table-view">View</x-button>
            <x-button variant="table-edit">Edit</x-button>
            <x-button variant="table-cancel">Cancel</x-button>

        </div>
    </div>

    {{-- FORM / CRUD BUTTONS --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold">Form / CRUD Buttons</h2>
        <div class="flex flex-wrap gap-3">
            <x-button variant="add-button">Add</x-button>
            <x-button variant="cancel">Cancel</x-button>
            <x-button variant="save">Save</x-button>
            <x-button variant="danger">Danger</x-button>
            <x-button variant="primary">Primary</x-button>
            <x-button variant="secondary">Secondary</x-button>
            <x-button variant="soft">Soft</x-button>
            <x-button variant="outline">Outline</x-button>
        </div>
    </div>


</div>
@endsection
