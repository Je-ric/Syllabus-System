@extends('layouts.app')

@section('content')

    <x-header-with-button title="{{ $college->name }}" description="Dean Overview & Organization Structure">
        <x-button variant="cancel" href="{{ route('dashboard') }}">← Back to Dashboard</x-button>
    </x-header-with-button>

    {{-- Overview Card --}}
    <div class="bg-linear-to-r from-clsu-green to-clsu-cobra rounded-lg shadow-md border border-clsu-green p-5 md:p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 md:gap-0">
        <div>
            <p class="text-sm text-clsu-green-100 font-medium">Your Role</p>
            <p class="text-xl md:text-2xl font-bold text-white mt-1">Dean of {{ $college->name }}</p>
        </div>
        <div class="text-left md:text-right">
            <p class="text-sm text-clsu-green-100 font-medium">Total Departments</p>
            <p class="text-2xl md:text-3xl font-bold text-clsu-yellow">{{ count($chairsWithFaculty) }}</p>
        </div>
    </div>

    {{-- Departments --}}
    <div class="space-y-4 md:space-y-6">
        @forelse ($chairsWithFaculty as $item)
            <div class="bg-white rounded-xl shadow-sm border border-clsu-green overflow-hidden">

                {{-- Department Header --}}
                <div class="bg-linear-to-r from-clsu-green to-clsu-cobra border-b border-clsu-green px-4 md:px-6 py-3 md:py-4">
                    <h2 class="text-lg md:text-xl font-semibold text-white">{{ $item['department']->name }}</h2>
                </div>

                {{-- Chair Section --}}
                <div class="px-4 md:px-6 py-3 md:py-4 border-b border-clsu-green bg-clsu-green-50">
                    @if ($item['chair'])
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 md:gap-4">
                            <div>
                                <p class="text-xs font-semibold text-clsu-cobra uppercase tracking-wide">Department Chair</p>
                                <p class="text-lg font-bold text-clsu-cobra mt-1">{{ $item['chair']->user->name }}</p>
                                <p class="text-sm text-clsu-green-100">{{ $item['chair']->user->email }}</p>
                            </div>
                            <div class="ml-0 sm:ml-4 mt-2 sm:mt-0">
                                <x-feedback-status.status-indicator status="chair" label="Chair" />
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3 md:py-4">
                            <p class="text-clsu-green-100 font-medium">No chair assigned</p>
                        </div>
                    @endif
                </div>

                {{-- Faculty Section --}}
                @if ($item['chair'])
                    <div class="px-4 md:px-6 py-3 md:py-4">
                        <h3 class="text-xs font-semibold text-clsu-cobra uppercase tracking-wide mb-3">Faculty Members</h3>

                        @if ($item['faculty']->isEmpty())
                            <div class="bg-clsu-green-50 rounded-lg p-3 text-center">
                                <p class="text-clsu-green-100 text-sm">No faculty assigned yet</p>
                            </div>
                        @else
                            <div class="space-y-2 md:space-y-3">
                                @foreach ($item['faculty'] as $member)
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-3 md:p-4 bg-clsu-green-50 rounded-lg border border-clsu-green hover:border-clsu-cobra transition-all gap-2 md:gap-3">
                                        <div class="flex-1">
                                            <p class="font-semibold text-clsu-cobra">{{ $member->user->name }}</p>
                                            <p class="text-sm text-clsu-green-100">{{ $member->user->email }}</p>
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
            <div class="bg-clsu-green-50 border border-clsu-green rounded-lg p-6 md:p-8 text-center">
                <p class="text-clsu-green-100">No departments found in {{ $college->name }}</p>
            </div>
        @endforelse
    </div>
@endsection
