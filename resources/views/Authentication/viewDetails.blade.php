@extends('layouts.app')

@section('content')

<x-header-with-button
    title="My Profile"
    description="View and manage your personal information">
</x-header-with-button>

@php
    $canOpenHierarchy = $user->hasRole('admin') 
                        || $user->hasRole('dean') 
                        || $user->hasRole('chair');
@endphp

<div class="mt-6 max-w-7xl mx-auto">
    <div class="bg-white shadow-xl rounded-2xl border border-slate-200 p-8">

        <div class="flex items-center gap-6 border-b pb-6 mb-6">
            <div class="h-20 w-20 rounded-full bg-emerald-100 flex items-center justify-center text-2xl font-bold text-emerald-600">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <div>
                <h2 class="text-xl font-semibold text-slate-800">
                    {{ $user->name }}
                </h2>
                <p class="text-slate-500 text-sm">
                    {{ $user->roles->pluck('name')->join(', ') }}
                </p>
            </div>

            @if($canOpenHierarchy)
                <div class="ml-auto">
                    <x-button href="{{ route('organizational.hierarchy') }}" variant="secondary">
                        <i class="bx bx-sitemap mr-1"></i> Organizational Hierarchy
                    </x-button>
                </div>
            @endif
        </div>

        <div class="grid md:grid-cols-2 gap-6 text-sm">

            <div>
                <p class="text-slate-500">Email</p>
                <p class="font-medium text-slate-700">
                    {{ $user->email }}
                </p>
            </div>

            <div>
                <p class="text-slate-500">Email Verified</p>
                <p class="font-medium text-slate-700">
                    {{ $user->email_verified_at ? $user->email_verified_at->format('F d, Y h:i A') : 'Not Verified' }}
                </p>
            </div>

            <div>
                <p class="text-slate-500">Phone</p>
                <p class="font-medium text-slate-700">
                    {{ $user->phone_number ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-slate-500">Office</p>
                <p class="font-medium text-slate-700">
                    {{ $user->office ?? '-' }}
                </p>
            </div>

        </div>

    </div>
</div>

@endsection
