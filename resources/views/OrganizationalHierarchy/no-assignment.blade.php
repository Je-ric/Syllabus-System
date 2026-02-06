@extends('layouts.app')

@section('content')
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium mb-6">
        ← Back to Dashboard
    </a>

    <div class="flex items-center justify-center min-h-96">
        <div class="text-center">
            <svg class="w-24 h-24 mx-auto text-slate-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>

            <h2 class="text-2xl font-bold text-slate-900 mb-2">No Assignment Found</h2>
            <p class="text-slate-600 max-w-md mx-auto">
                You haven't been assigned as a dean or chair yet.
                Please contact your administrator to receive an assignment.
            </p>
        </div>
    </div>
@endsection
