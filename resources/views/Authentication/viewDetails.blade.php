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
        @include('includes.session-success')

        <div class="flex items-center gap-6 border-b pb-6 mb-6">
            <div class="h-20 w-20 rounded-full bg-emerald-100 flex items-center justify-center text-2xl font-bold text-emerald-600">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <div>
                <h2 class="text-xl font-semibold text-slate-800">
                    {{ $user->name }}
                </h2>
                <div class="mt-2 flex flex-wrap gap-2">
                    @forelse ($user->roles as $role)
                        <x-feedback-status.status-indicator
                            :status="$role->name"
                            :label="ucfirst($role->name)"
                        />
                    @empty
                        <x-feedback-status.status-indicator
                            status="neutral"
                            label="No Role Assigned"
                        />
                    @endforelse
                </div>
            </div>

            @if($canOpenHierarchy)
                <div class="ml-auto">
                    <x-button href="{{ route('organizational.hierarchy') }}" variant="secondary">
                        <i class="bx bx-sitemap mr-1"></i> Organizational Hierarchy
                    </x-button>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="grid md:grid-cols-2 gap-6 text-sm text-slate-800">
                <div>
                    <x-form.label class="block">Name</x-form.label>
                    <x-form.input
                        type="text"
                        name="name"
                        class="mt-2"
                        :value="old('name', $user->name)"
                        required
                    />
                    @error('name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-form.label class="block">Email</x-form.label>
                    <x-form.input
                        type="email"
                        name="email"
                        class="mt-2"
                        :value="old('email', $user->email)"
                        required
                    />
                    @error('email')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-form.label class="block">Phone</x-form.label>
                    <x-form.input
                        type="text"
                        name="phone_number"
                        class="mt-2"
                        :value="old('phone_number', $user->phone_number)"
                    />
                    @error('phone_number')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-form.label class="block">Office</x-form.label>
                    <x-form.input
                        type="text"
                        name="office"
                        class="mt-2"
                        :value="old('office', $user->office)"
                    />
                    @error('office')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <p class="text-slate-500">Email Verified</p>
                    <p class="font-medium text-slate-700 mt-2">
                        {{ $user->email_verified_at ? $user->email_verified_at->format('F d, Y h:i A') : 'Not Verified' }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-button type="submit" variant="save">
                    <i class="bx bx-save mr-1"></i> Save Changes
                </x-button>
            </div>
        </form>

    </div>
</div>

@endsection
