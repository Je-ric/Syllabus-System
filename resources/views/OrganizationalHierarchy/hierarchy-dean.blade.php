@extends('layouts.app')

@section('content')
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium mb-6">
        ← Back to Dashboard
    </a>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">{{ $college->name }}</h1>
        <p class="text-slate-600 mt-2">Dean Overview & Organization Structure</p>
    </div>

    {{-- Header Card --}}
    <div class="bg-linear-to-r from-blue-50 to-blue-100 rounded-lg shadow-md border border-slate-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600 font-medium">Your Role</p>
                <p class="text-2xl font-bold text-slate-900">Dean of {{ $college->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-slate-600 font-medium">Total Departments</p>
                <p class="text-3xl font-bold text-blue-600">{{ count($chairsWithFaculty) }}</p>
            </div>
        </div>
    </div>

    {{-- Departments and Organization --}}
    <div class="space-y-6">
        @forelse ($chairsWithFaculty as $item)
            <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                {{-- Department Header --}}
                <div class="bg-linear-to-r from-purple-50 to-purple-100 border-b border-slate-200 px-6 py-4">
                    <h2 class="text-xl font-semibold text-slate-900">{{ $item['department']->name }}</h2>
                </div>

                {{-- Chair Section --}}
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    @if ($item['chair'])
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Department Chair</p>
                                <p class="text-lg font-semibold text-slate-900 mt-1">{{ $item['chair']->user->name }}</p>
                                <p class="text-sm text-slate-500">{{ $item['chair']->user->email }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">Chair</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-slate-500 font-medium">No chair assigned to this department</p>
                        </div>
                    @endif
                </div>

                {{-- Faculty Section --}}
                @if ($item['chair'])
                    <div class="px-6 py-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4">Faculty</h3>

                        @if ($item['faculty']->isEmpty())
                            <div class="bg-slate-50 rounded-lg p-4 text-center">
                                <p class="text-slate-500">No faculty assigned yet</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($item['faculty'] as $member)
                                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200 hover:border-slate-300 transition-colors">
                                        <div class="flex-1">
                                            <p class="font-semibold text-slate-900">{{ $member->user->name }}</p>
                                            <p class="text-sm text-slate-500">{{ $member->user->email }}</p>
                                        </div>
                                        <div class="ml-4 text-right">
                                            <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Faculty</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-8 text-center">
                <p class="text-slate-600">No departments found in {{ $college->name }}</p>
            </div>
        @endforelse
    </div>
@endsection
