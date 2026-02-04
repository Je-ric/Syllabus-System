@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">

        <div class="mb-6">
            <h1 class="text-2xl font-bold">Create Syllabus</h1>
            <p class="text-gray-600 mt-2">Step 1: Select program → Step 2: Choose course → Step 3: Fill details</p>
        </div>

        <!-- Program Selector -->
        <div class="mb-6 bg-gray-50 border rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Select Program</h2>
            <livewire:programs.program-selector :autoRedirect="false" />
        </div>

        <!-- Courses Display -->
        <livewire:syllabus.course-selector />

    </div>
@endsection
