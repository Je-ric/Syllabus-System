@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Create Academic Calendar</h1>

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('academic.calendars.store') }}" method="POST" class="grid grid-cols-2 gap-6">
        @csrf

        <div class="col-span-2">
            <label class="block font-semibold mb-1">Academic Year</label>
            <input type="text" name="academic_year" value="{{ old('academic_year') }}" placeholder="2025-2026"
                   class="border rounded px-2 py-1 w-full">
        </div>

        {{-- 1st Semester --}}
        <div class="border p-4 rounded">
            <h2 class="font-semibold mb-2">1st Semester</h2>
            <label>Start Date</label>
            <input type="date" name="start_date_1" value="{{ old('start_date_1') }}" class="border rounded px-2 py-1 w-full">
            <label class="mt-2 block">End Date</label>
            <input type="date" name="end_date_1" value="{{ old('end_date_1') }}" class="border rounded px-2 py-1 w-full">
        </div>

        {{-- 2nd Semester --}}
        <div class="border p-4 rounded">
            <h2 class="font-semibold mb-2">2nd Semester</h2>
            <label>Start Date</label>
            <input type="date" name="start_date_2" value="{{ old('start_date_2') }}" class="border rounded px-2 py-1 w-full">
            <label class="mt-2 block">End Date</label>
            <input type="date" name="end_date_2" value="{{ old('end_date_2') }}" class="border rounded px-2 py-1 w-full">
        </div>

        {{-- Submit --}}
        <div class="col-span-2">
            <x-button type="submit" variant="save">Create Calendar</x-button>
        </div>
    </form>
</div>
@endsection
