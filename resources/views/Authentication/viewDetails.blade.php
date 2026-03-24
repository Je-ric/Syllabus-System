@extends('layouts.app')

@section('content')

<x-page-header
    icon="bx-user"
    title="My Profile"
    desc="View and manage your personal information">
</x-page-header>

<x-panel>
    @php
        $canOpenHierarchy = $user->hasRole('admin')
                            || $user->hasRole('dean')
                            || $user->hasRole('chair');
        $isAdmin = $user->hasRole('admin');
        $facultyAssignments = $user->assignments->where('context', 'faculty');
        $chairAssignments = $user->assignments->where('context', 'chair');
        $deanAssignments = $user->assignments->where('context', 'dean');
        $hasPendingPasswordOtp = session()->has('password_change_otp');
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
    
    
            <div class="bg-white border border-slate-200 rounded-2xl shadow-lg p-8">
            <div class="mb-8">
                <h3 class="text-sm uppercase tracking-[0.2em] text-slate-500 mb-3">
                    Assignment Details
                </h3>
    
                <div class="grid md:grid-cols-3 gap-4 text-sm">
    
                    {{-- Faculty --}}
                    @if($facultyAssignments->isNotEmpty())
                        <div>
                            <div class="mb-2">
                                <x-feedback-status.status-indicator
                                    status="faculty"
                                    label="Faculty Assignment"
                                />
                            </div>
    
                            @foreach($facultyAssignments as $assignment)
                                <p class="text-slate-700">
                                    {{ $assignment->department?->name ?? 'Unassigned Department' }}
                                    @if($assignment->department?->college)
                                        <span class="text-slate-500">
                                            ({{ $assignment->department->college->name }})
                                        </span>
                                    @endif
                                </p>
                            @endforeach
                        </div>
                    @endif
    
    
                    {{-- Chair --}}
                    @if($chairAssignments->isNotEmpty())
                        <div>
                            <div class="mb-2">
                                <x-feedback-status.status-indicator
                                    status="chair"
                                    label="Chair Assignment"
                                />
                            </div>
    
                            @foreach($chairAssignments as $assignment)
                                <p class="text-slate-700">
                                    {{ $assignment->department?->name ?? 'Unassigned Department' }}
                                    @if($assignment->department?->college)
                                        <span class="text-slate-500">
                                            ({{ $assignment->department->college->name }})
                                        </span>
                                    @endif
                                </p>
                            @endforeach
                        </div>
                    @endif
    
    
                    {{-- Dean --}}
                    @if($deanAssignments->isNotEmpty())
                        <div>
                            <div class="mb-2">
                                <x-feedback-status.status-indicator
                                    status="dean"
                                    label="Dean Assignment"
                                />
                            </div>
    
                            @foreach($deanAssignments as $assignment)
                                <p class="text-slate-700">
                                    {{ $assignment->college?->name ?? 'Unassigned College' }}
                                </p>
                            @endforeach
                        </div>
                    @endif
    
                </div>
            </div>
    
                @if($isAdmin)
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 mb-6 text-amber-800 text-sm">
                        Admin profile editing is disabled on this page.
                    </div>
                @endif
    
                <form method="POST" action="{{ route('profile.update') }}"
                        class="border border-slate-200 rounded-xl p-6 bg-slate-50">
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
                                :disabled="$isAdmin"
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
                                :disabled="$isAdmin"
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
                                :disabled="$isAdmin"
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
                                :disabled="$isAdmin"
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
    
                    @unless($isAdmin)
                        <div class="mt-6 flex justify-end">
                            <x-button type="submit" variant="save">
                                <i class="bx bx-save mr-1"></i> Save Changes
                            </x-button>
                        </div>
                    @endunless
                </form>
            </div>
    
            @unless($isAdmin)
                <div class="mt-10">
    
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-lg p-8">
    
                        {{-- Header --}}
                        <div class="mb-6">
                            <h3 class="text-sm uppercase tracking-[0.2em] text-slate-500">
                                Security Settings
                            </h3>
    
                            <h2 class="text-lg font-semibold text-slate-800 mt-2">
                                Change Password
                            </h2>
    
                            <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                                Enter your current password and set a new one.
                                After clicking <span class="font-medium text-slate-700">Send OTP</span>,
                                a verification code will be sent to your registered email address. <br>
                                The OTP input field will appear below this form—enter the code there to complete your password change.
                            </p>
                        </div>
    
                        {{-- STEP 1 --}}
                        <div class="border border-slate-200 rounded-xl p-6 bg-slate-50">
                            <h4 class="text-sm font-semibold text-slate-700 mb-4">
                                Step 1 — Set New Password
                            </h4>
    
                            <form method="POST" action="{{ route('profile.password.change') }}"
                                class="grid md:grid-cols-2 gap-4 text-sm">
                                @csrf
    
                        <div class="md:col-span-2">
                            <x-form.label>Current Password</x-form.label>
                            <div x-data="{ show: false }" class="relative mt-2">
                                <x-form.input :type="'password'" name="current_password" x-bind:type="show ? 'text' : 'password'" class="pr-11" required />
                                <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors">
                                    <i :class="show ? 'bx bx-hide' : 'bx bx-show'" class="text-lg leading-none"></i>
                                </button>
                            </div>
                        </div>
    
                                <div>
                                    <x-form.label>New Password</x-form.label>
                                    <div x-data="{ show: false }" class="relative mt-2">
                                        <x-form.input :type="'password'" name="password" x-bind:type="show ? 'text' : 'password'" class="pr-11" required />
                                        <button type="button" @click="show = !show"
                                                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors">
                                            <i :class="show ? 'bx bx-hide' : 'bx bx-show'" class="text-lg leading-none"></i>
                                        </button>
                                    </div>
                                </div>
    
                                <div>
                                    <x-form.label>Confirm New Password</x-form.label>
                                    <div x-data="{ show: false }" class="relative mt-2">
                                        <x-form.input :type="'password'" name="password_confirmation" x-bind:type="show ? 'text' : 'password'" class="pr-11" required />
                                        <button type="button" @click="show = !show"
                                                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors">
                                            <i :class="show ? 'bx bx-hide' : 'bx bx-show'" class="text-lg leading-none"></i>
                                        </button>
                                    </div>
                                </div>
    
                                <div class="md:col-span-2 flex justify-end mt-2">
                                    <x-button type="submit" variant="save">
                                        Send OTP
                                    </x-button>
                                </div>
                            </form>
                        </div>
    
                        {{-- STEP 2 --}}
                        @if($hasPendingPasswordOtp)
                        <div class="mt-6 border border-emerald-200 bg-emerald-50 rounded-xl p-6">
    
                            <h4 class="text-sm font-semibold text-emerald-700 mb-3">
                                Step 2 — Enter Verification Code
                            </h4>
    
                            <p class="text-sm text-emerald-700 mb-4">
                                A 6-digit OTP has been sent to your email.
                                Enter the code below to confirm your password change.
                            </p>
    
                            <form method="POST" action="{{ route('profile.password.verify-otp') }}"
                                class="flex flex-col md:flex-row items-start md:items-end gap-4 text-sm">
                                @csrf
    
                                <div class="w-full md:w-64">
                                    <x-form.label>OTP Code</x-form.label>
                                    <x-form.input
                                        type="text"
                                        name="otp"
                                        maxlength="6"
                                        class="mt-2 text-center tracking-[0.4em] text-lg font-semibold"
                                        required
                                    />
                                </div>
    
                                <x-button type="submit" variant="save">
                                    Confirm Password Change
                                </x-button>
                            </form>
    
                            <form method="POST" action="{{ route('profile.password.resend-otp') }}" class="mt-3">
                                @csrf
                                <button type="submit" class="text-sm text-blue-600 hover:underline">
                                    Resend OTP
                                </button>
                            </form>
                        </div>
                        @endif
    
                    </div>
                </div>
                @endunless
    
        </div>
    </div>
</x-panel>

@endsection
