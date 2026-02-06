@extends('layouts.app')

@section('content')
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium mb-6">
        ← Back to Dashboard
    </a>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">{{ $department->name }}</h1>
        <p class="text-slate-600 mt-2">Department Chair Overview</p>
    </div>

    {{-- Header Card --}}
    <div class="bg-linear-to-r from-purple-50 to-purple-100 rounded-lg shadow-md border border-slate-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600 font-medium">Your Role</p>
                <p class="text-2xl font-bold text-slate-900">Chair of {{ $department->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-slate-600 font-medium">Faculty Members</p>
                <p class="text-3xl font-bold text-purple-600">{{ $faculty->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Department Info --}}
    <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden mb-8">
        <div class="bg-linear-to-r from-slate-50 to-slate-100 border-b border-slate-200 px-6 py-4">
            <h2 class="text-xl font-semibold text-slate-900">Department Information</h2>
        </div>
        <div class="px-6 py-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-slate-600 font-medium">Department Name</p>
                    <p class="text-lg font-semibold text-slate-900 mt-2">{{ $department->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 font-medium">College</p>
                    <p class="text-lg font-semibold text-slate-900 mt-2">{{ $department->college->name }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Faculty Section --}}
    <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
        <div class="bg-linear-to-r from-green-50 to-green-100 border-b border-slate-200 px-6 py-4">
            <h2 class="text-xl font-semibold text-slate-900">Faculty Members</h2>
            <p class="text-sm text-slate-600 mt-1">{{ $faculty->count() }} faculty member(s)</p>
        </div>

        <div class="px-6 py-6">
            @if ($faculty->isEmpty())
                <div class="bg-slate-50 rounded-lg p-8 text-center">
                    <p class="text-slate-600 font-medium">No faculty members assigned yet</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($faculty as $member)
                        <div class="flex items-center justify-between p-5 bg-slate-50 rounded-lg border border-slate-200 hover:border-slate-300 hover:shadow-md transition-all">
                            <div class="flex-1">
                                <p class="font-semibold text-slate-900">{{ $member->user->name }}</p>
                                <p class="text-sm text-slate-500 mt-1">{{ $member->user->email }}</p>
                                @if ($member->user->office)
                                    <p class="text-sm text-slate-500">Office: {{ $member->user->office }}</p>
                                @endif
                                @if ($member->user->phone_number)
                                    <p class="text-sm text-slate-500">Phone: {{ $member->user->phone_number }}</p>
                                @endif
                            </div>
                            <div class="ml-4 shrink-0">
                                <span class="inline-block px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-semibold">Faculty</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
