@extends('layouts.app')

@section('content')

    <x-header-with-button title="{{ $department->name }}" description="Department Chair Overview">
        <x-button variant="cancel" href="{{ route('dashboard') }}">← Back to Dashboard</x-button>
    </x-header-with-button>

    {{-- Overview Card --}}
    <div class="bg-green-grad rounded-lg shadow-md border border-clsu-green p-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-0">
            <div>
                <p class="text-sm text-clsu-green-100 font-medium">Your Role</p>
                <p class="text-2xl font-bold text-clsu-cobra mt-1">Chair of {{ $department->name }}</p>
            </div>
            <div class="text-left md:text-right">
                <p class="text-sm text-clsu-green-100 font-medium">Faculty Members</p>
                <p class="text-3xl font-bold text-clsu-green">{{ $faculty->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Department Info --}}
    <div class="bg-white rounded-xl shadow-md border border-clsu-green overflow-hidden mb-8">
        <div class="bg-linear-to-r from-clsu-green-50 to-clsu-green-100 border-b border-clsu-green px-6 py-4">
            <h2 class="text-xl font-semibold text-clsu-cobra">Department Information</h2>
        </div>
        <div class="px-6 py-6">
            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-clsu-green-100 font-medium">Department Name</p>
                    <p class="text-lg font-semibold text-clsu-cobra mt-2">{{ $department->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-clsu-green-100 font-medium">College</p>
                    <p class="text-lg font-semibold text-clsu-cobra mt-2">{{ $department->college->name }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Faculty Section --}}
    <div class="bg-white rounded-xl shadow-md border border-clsu-green overflow-hidden">
        <div class="bg-linear-to-r from-clsu-green-50 to-clsu-green-100 border-b border-clsu-green px-6 py-4">
            <h2 class="text-xl font-semibold text-clsu-cobra">Faculty Members</h2>
            <p class="text-sm text-clsu-green-100 mt-1">{{ $faculty->count() }} faculty member(s)</p>
        </div>

        <div class="px-6 py-6">
            @if ($faculty->isEmpty())
                <div class="bg-clsu-green-50 rounded-lg p-8 text-center">
                    <p class="text-clsu-green-100 font-medium">No faculty members assigned yet</p>
                </div>
            @else
                <div class="space-y-3 max-h-125 overflow-y-auto">
                    @foreach ($faculty as $member)
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-5 bg-clsu-green-50 rounded-lg border border-clsu-green hover:border-clsu-cobra hover:shadow-md transition-all gap-3">

                            {{-- Faculty Info --}}
                            <div class="flex-1">
                                <p class="font-semibold text-clsu-cobra">{{ $member->user->name }}</p>
                                <p class="text-sm text-clsu-green-100 mt-1">{{ $member->user->email }}</p>
                                @if ($member->user->office)
                                    <p class="text-sm text-clsu-green-100">Office: {{ $member->user->office }}</p>
                                @endif
                                @if ($member->user->phone_number)
                                    <p class="text-sm text-clsu-green-100">Phone: {{ $member->user->phone_number }}</p>
                                @endif
                            </div>

                            {{-- Badge --}}
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
