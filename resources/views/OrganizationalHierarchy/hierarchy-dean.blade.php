@extends('layouts.app')

@section('content')

    <x-header-with-button title="{{ $college->name }}" description="Dean Overview & Organization Structure">
    </x-header-with-button>

    <div class="rounded-2xl border border-emerald-200 bg-linear-to-r from-emerald-700 to-emerald-600 p-5 md:p-6 mb-6 shadow-md flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <p class="text-sm text-emerald-100 font-medium">Your Role</p>
            <p class="text-xl md:text-2xl font-bold text-white mt-1">Dean of {{ $college->name }}</p>
            <div class="mt-3">
                <x-button href="{{ route('organizational.departments.index', $college->id) }}" variant="secondary">
                    <i class="bx bx-cog mr-1"></i> Manage Chair and Faculty
                </x-button>
            </div>
        </div>
        <div class="text-left md:text-right">
            <p class="text-sm text-emerald-100 font-medium">Total Departments</p>
            <p class="text-2xl md:text-3xl font-bold text-amber-200">{{ count($chairsWithFaculty) }}</p>
        </div>
    </div>

    <div class="space-y-4 md:space-y-6">
        @forelse ($chairsWithFaculty as $item)
            <div class="bg-white/90 rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-emerald-700 border-b border-emerald-700 px-4 md:px-6 py-3 md:py-4">
                    <h2 class="text-lg md:text-xl font-semibold text-white">{{ $item['department']->name }}</h2>
                </div>

                <div class="px-4 md:px-6 py-3 md:py-4 border-b border-emerald-200 bg-emerald-50">
                    @if ($item['chair'])
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 md:gap-4">
                            <div>
                                <p class="text-xs font-semibold text-emerald-800 uppercase tracking-[0.2em]">Department Chair</p>
                                <p class="text-lg font-bold text-slate-800 mt-1">{{ $item['chair']->user->name }}</p>
                                <p class="text-sm text-slate-600">{{ $item['chair']->user->email }}</p>
                            </div>
                            <div class="ml-0 sm:ml-4 mt-2 sm:mt-0">
                                <x-feedback-status.status-indicator status="chair" label="Chair" />
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3 md:py-4">
                            <p class="text-emerald-700 font-medium">No chair assigned</p>
                        </div>
                    @endif
                </div>

                @if ($item['chair'])
                    <div class="px-4 md:px-6 py-3 md:py-4">
                        <h3 class="text-xs font-semibold text-emerald-800 uppercase tracking-[0.2em] mb-3">Faculty Members</h3>

                        @if ($item['faculty']->isEmpty())
                            <div class="bg-emerald-50 rounded-xl p-3 text-center border border-emerald-200">
                                <p class="text-emerald-700 text-sm">No faculty assigned yet</p>
                            </div>
                        @else
                            <div class="space-y-2 md:space-y-3">
                                @foreach ($item['faculty'] as $member)
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-3 md:p-4 bg-emerald-50 rounded-xl border border-emerald-200 hover:border-emerald-400 transition-all gap-2 md:gap-3">
                                        <div class="flex-1">
                                            <p class="font-semibold text-slate-800">{{ $member->user->name }}</p>
                                            <p class="text-sm text-slate-600">{{ $member->user->email }}</p>
                                        </div>
                                        <div class="ml-0 sm:ml-4 mt-1 sm:mt-0">
                                            <x-feedback-status.status-indicator status="faculty" label="Faculty" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6 md:p-8 text-center">
                <p class="text-emerald-700">No departments found in {{ $college->name }}</p>
            </div>
        @endforelse
    </div>
@endsection
