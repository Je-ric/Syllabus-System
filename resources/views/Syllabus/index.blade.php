@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">

        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold">My Syllabi</h1>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            <a href="{{ route('syllabus.create') }}"
                class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg h-48 bg-white shadow hover:shadow-lg hover:bg-gray-50 transition cursor-pointer">
                <span class="text-4xl text-gray-400 font-bold">+</span>
                <span class="mt-2 text-gray-600 font-medium">Create Syllabus</span>
            </a>
        </div>

        @if ($syllabi->count() > 0)
            @foreach ($syllabi as $syllabus)
                <h1>{{ $syllabus->course->course_code }} - {{ $syllabus->course->course_title }}</h1>
            @endforeach
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed">
                <p class="text-gray-500 mb-4">No syllabi created yet</p>
                <a href="{{ route('syllabus.create') }}" class="text-blue-600 hover:underline font-medium">
                    Create your first syllabus
                </a>
            </div>
        @endif

    </div>
@endsection
