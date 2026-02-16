@extends('layouts.app')

@section('content')

    <x-header-with-button title="{{ $department->name }}" description="Department Chair Overview">
        <x-button variant="cancel" href="{{ route('dashboard') }}">
            <i class="bx bx-left-arrow-alt"></i> Back to Dashboard
        </x-button>
    </x-header-with-button>

    <div class="rounded-2xl border border-emerald-200 bg-linear-to-r from-emerald-700 to-emerald-600 p-6 mb-8 shadow-md">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <p class="text-sm text-emerald-100 font-medium">Your Role</p>
                <p class="text-2xl font-bold text-white mt-1">Chair of {{ $department->name }}</p>
            </div>
            <div class="text-left md:text-right">
                <p class="text-sm text-emerald-100 font-medium">Faculty Members</p>
                <p class="text-3xl font-bold text-amber-200">{{ $faculty->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white/90 rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
        <div class="bg-emerald-50 border-b border-emerald-200 px-6 py-4">
            <h2 class="text-xl font-semibold text-slate-800">Department Information</h2>
        </div>
        <div class="px-6 py-6">
            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500 font-semibold">Department Name</p>
                    <p class="text-lg font-semibold text-slate-800 mt-2">{{ $department->name }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500 font-semibold">College</p>
                    <p class="text-lg font-semibold text-slate-800 mt-2">{{ $department->college->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white/90 rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-emerald-50 border-b border-emerald-200 px-6 py-4">
            <h2 class="text-xl font-semibold text-slate-800">Faculty Members</h2>
            <p class="text-sm text-slate-600 mt-1">{{ $faculty->count() }} faculty member(s)</p>
        </div>

        <div class="px-6 py-6">
            @if ($faculty->isEmpty())
                <div class="bg-emerald-50 rounded-xl p-8 text-center border border-emerald-200">
                    <p class="text-emerald-700 font-medium">No faculty members assigned yet</p>
                </div>
            @else
                <div class="space-y-3 max-h-125 overflow-y-auto">
                    @foreach ($faculty as $member)
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-5 bg-emerald-50/70 rounded-xl border border-emerald-200 hover:border-emerald-400 hover:shadow-sm transition-all gap-3">
                            <div class="flex-1">
                                <p class="font-semibold text-slate-800">{{ $member->user->name }}</p>
                                <p class="text-sm text-slate-600 mt-1">{{ $member->user->email }}</p>
                                @if ($member->user->office)
                                    <p class="text-sm text-emerald-700">Office: {{ $member->user->office }}</p>
                                @endif
                                @if ($member->user->phone_number)
                                    <p class="text-sm text-emerald-700">Phone: {{ $member->user->phone_number }}</p>
                                @endif
                            </div>

                            <div class="ml-0 sm:ml-4">
                                <x-feedback-status.status-indicator status="faculty" label="Faculty" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
