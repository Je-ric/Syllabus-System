@extends('layouts.app')

@section('content')

<x-layout.page-header
    icon="bx-user"
    title="My Profile"
    desc="View and manage your personal information" />

<x-layout.panel>
    @php
        $canOpenHierarchy = $user->hasRole('admin') || $user->hasRole('dean') || $user->hasRole('chair');
        $isAdmin = $user->hasRole('admin');
        $facultyAssignments = $user->assignments->where('context', 'faculty');
        $chairAssignments   = $user->assignments->where('context', 'chair');
        $deanAssignments    = $user->assignments->where('context', 'dean');
        $hasPendingPasswordOtp = session()->has('password_change_otp');

        $activityIcons = [
            'login'       => ['icon' => 'bx-log-in-circle',    'color' => '#16a34a'],
            'logout'      => ['icon' => 'bx-log-out-circle',   'color' => '#64748b'],
            'register'    => ['icon' => 'bx-user-plus',        'color' => '#0ea5e9'],
            'otp_verify'  => ['icon' => 'bx-envelope-check',   'color' => '#16a34a'],
            'profile_update' => ['icon' => 'bx-user-check',   'color' => '#7c3aed'],
            'password_change'=> ['icon' => 'bx-lock-alt',     'color' => '#d97706'],
        ];
        $defaultIcon = ['icon' => 'bx-time-five', 'color' => '#94a3b8'];
    @endphp

    @include('includes.session-success')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-2">

        {{-- ── LEFT: Profile Card ──────────────────────────────────── --}}
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden sticky top-20" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

                {{-- Green banner --}}
                <div class="h-20 w-full" style="background: linear-gradient(90deg, #002a0c 0%, #009639 100%);"></div>

                {{-- Avatar overlapping banner --}}
                <div class="flex flex-col items-center -mt-10 pb-5 px-5">
                    <div class="w-20 h-20 rounded-full border-4 border-white bg-[#f0fdf4] flex items-center justify-center text-2xl font-bold text-[#16a34a] shadow-md">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <h2 class="mt-3 text-[15px] font-bold text-[#0f172a] text-center">{{ $user->name }}</h2>
                    <p class="text-[12px] text-[#64748b] mt-0.5">{{ $user->office ?? '—' }}</p>

                    {{-- Role badges --}}
                    <div class="flex flex-wrap justify-center gap-1.5 mt-2">
                        @forelse ($user->roles as $role)
                            <x-feedback-status.status-indicator :status="$role->name" :label="ucfirst($role->name)" />
                        @empty
                            <x-feedback-status.status-indicator status="neutral" label="No Role" />
                        @endforelse
                    </div>

                    {{-- Email verified badge --}}
                    <div class="mt-2">
                        @if ($user->email_verified_at)
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#16a34a] bg-[#f0fdf4] border border-[#bbf7d0] px-2 py-0.5 rounded-full">
                                <i class="bx bx-check-circle"></i> Email Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#d97706] bg-[#fffbeb] border border-[#fde68a] px-2 py-0.5 rounded-full">
                                <i class="bx bx-error-circle"></i> Unverified
                            </span>
                        @endif
                    </div>

                    {{-- Info list --}}
                    <ul class="w-full mt-4 space-y-2 border-t border-[#f1f5f9] pt-4 text-[12px]">
                        <li class="flex items-center justify-between gap-2">
                            <span class="text-[#94a3b8] flex items-center gap-1"><i class="bx bx-envelope text-sm"></i> Email</span>
                            <span class="text-[#334155] font-medium truncate max-w-40" title="{{ $user->email }}">{{ $user->email }}</span>
                        </li>
                        <li class="flex items-center justify-between gap-2">
                            <span class="text-[#94a3b8] flex items-center gap-1"><i class="bx bx-phone text-sm"></i> Phone</span>
                            <span class="text-[#334155] font-medium">{{ $user->phone_number ?? '—' }}</span>
                        </li>
                        <li class="flex items-center justify-between gap-2">
                            <span class="text-[#94a3b8] flex items-center gap-1"><i class="bx bx-calendar text-sm"></i> Joined</span>
                            <span class="text-[#334155] font-medium">{{ $user->created_at->format('M Y') }}</span>
                        </li>
                        <li class="flex items-start justify-between gap-2">
                            <span class="text-[#94a3b8] flex items-center gap-1 shrink-0"><i class="bx bx-briefcase text-sm"></i> Status</span>
                            <span class="text-[#334155] font-medium capitalize text-right">{{ $user->status ?? 'active' }}</span>
                        </li>
                    </ul>

                    {{-- Assignments --}}
                    @if ($facultyAssignments->isNotEmpty() || $chairAssignments->isNotEmpty() || $deanAssignments->isNotEmpty())
                        <div class="w-full mt-4 border-t border-[#f1f5f9] pt-4 space-y-2 text-[12px]">
                            @foreach ($deanAssignments as $a)
                                <div class="flex items-start gap-1.5">
                                    <x-feedback-status.status-indicator status="dean" label="Dean" />
                                    <span class="text-[#475569]">{{ $a->college?->name ?? '—' }}</span>
                                </div>
                            @endforeach
                            @foreach ($chairAssignments as $a)
                                <div class="flex items-start gap-1.5">
                                    <x-feedback-status.status-indicator status="chair" label="Chair" />
                                    <span class="text-[#475569]">{{ $a->department?->name ?? '—' }}</span>
                                </div>
                            @endforeach
                            @foreach ($facultyAssignments as $a)
                                <div class="flex items-start gap-1.5">
                                    <x-feedback-status.status-indicator status="faculty" label="Faculty" />
                                    <span class="text-[#475569]">{{ $a->department?->name ?? '—' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Action buttons --}}
                    <div class="w-full mt-5 space-y-2">
                        @if ($canOpenHierarchy)
                            <a href="{{ route('user-assignments.hierarchy') }}"
                               class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-[13px] font-semibold border border-[#e2e8f0] text-[#475569] hover:bg-[#f8fafc] transition">
                                <i class="bx bx-sitemap"></i> Organizational Hierarchy
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: Details + Activity ──────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Admin notice --}}
            @if ($isAdmin)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-800 text-[13px]">
                    <i class="bx bx-info-circle mr-1"></i>
                    Admin profile editing is disabled on this page.
                </div>
            @endif

            <x-layout.card-section
                title="Profile Information"
                icon="bx-user-circle">

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="grid md:grid-cols-2 gap-4 text-sm text-[#334155]">
                        <div>
                            <x-form.label class="block">Name</x-form.label>
                            <x-form.input type="text" name="name" class="mt-1.5"
                                :value="old('name', $user->name)" required :disabled="$isAdmin"
                                pattern="[\p{L}\s]+"
                                title="Name must contain letters and spaces only" />
                            @error('name')<span class="text-rose-600 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <x-form.label class="block">Email</x-form.label>
                            <x-form.input type="email" name="email"
                                :value="old('email', $user->email)" required readonly
                                class="mt-1.5 bg-[#f4f4f5] text-[#71717a] cursor-not-allowed" />
                            <p class="mt-1 text-[11px] text-[#94a3b8]">Email cannot be changed here.</p>
                            @error('email')<span class="text-rose-600 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <x-form.label class="block">Phone</x-form.label>
                            <x-form.input type="text" name="phone_number" class="mt-1.5"
                                :value="old('phone_number', $user->phone_number)" :disabled="$isAdmin" />
                            @error('phone_number')<span class="text-rose-600 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <x-form.label class="block">Office</x-form.label>
                            <x-form.input type="text" name="office" class="mt-1.5"
                                :value="old('office', $user->office)" :disabled="$isAdmin" />
                            @error('office')<span class="text-rose-600 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <p class="text-[#94a3b8] text-xs uppercase tracking-wide font-semibold">Email Verified</p>
                            <p class="font-medium text-[#334155] mt-1 text-sm">
                                {{ $user->email_verified_at ? $user->email_verified_at->format('F d, Y h:i A') : 'Not Verified' }}
                            </p>
                        </div>
                    </div>
                    @unless ($isAdmin)
                        <div class="mt-4 flex justify-end">
                            <x-ui.button type="submit" variant="save">
                                <i class="bx bx-save mr-1"></i> Save Changes
                            </x-ui.button>
                        </div>
                    @endunless
                </form>
            </x-layout.card-section>

            {{-- Consultation Hours --}}
            {{-- <x-layout.card-section title="Consultation Hours" icon="bx-time">
                <x-slot:actions>
                    <button type="button" onclick="document.getElementById('profile-ch-modal').showModal()"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[12px] font-semibold
                               bg-[#f0fdf4] text-[#16a34a] border border-[#bbf7d0] hover:bg-[#dcfce7] transition-colors">
                        <i class="bx bx-plus text-sm"></i> Add
                    </button>
                </x-slot:actions>

                <div class="space-y-2">
                    @forelse ($user->consultationHours as $hour)
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-bold shrink-0">
                                    {{ substr($hour->day, 0, 3) }}
                                </span>
                                <span class="text-[13px] font-medium text-slate-700">{{ $hour->day }}</span>
                                <span class="text-[13px] text-slate-500">{{ $hour->time }}</span>
                            </div>
                            <form method="POST" action="{{ route('profile.consultation.destroy', $hour) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Remove this consultation hour?')"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition">
                                    <i class="bx bx-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-[13px] text-slate-400 italic">No consultation hours added yet.</p>
                    @endforelse
                </div>

                Add modal
                <x-modal.dialog id="profile-ch-modal" maxWidth="max-w-sm" variant="add">
                    <x-modal.header modalId="profile-ch-modal" variant="add">Add Consultation Hour</x-modal.header>
                    <form method="POST" action="{{ route('profile.consultation.store') }}">
                        @csrf
                        <x-modal.body class="space-y-4">
                            <div>
                                <x-modal.modal-label for="ch-day">Day</x-modal.modal-label>
                                <x-form.select id="ch-day" name="day">
                                    @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday'] as $d)
                                        <option value="{{ $d }}" {{ old('day') === $d ? 'selected' : '' }}>{{ $d }}</option>
                                    @endforeach
                                </x-form.select>
                                @error('day')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <x-modal.modal-label for="ch-time">Time</x-modal.modal-label>
                                <x-form.input id="ch-time" type="text" name="time" placeholder="01:00 PM – 03:00 PM" :value="old('time')" />
                                @error('time')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </x-modal.body>
                        <x-modal.footer>
                            <x-modal.close-button modalId="profile-ch-modal" />
                            <x-ui.button type="submit" variant="sm-add">
                                <i class="bx bx-plus"></i> Add
                            </x-ui.button>
                        </x-modal.footer>
                    </form>
                </x-modal.dialog>
                @if ($errors->hasAny(['day', 'time']))
                    <script>document.addEventListener('DOMContentLoaded', () => document.getElementById('profile-ch-modal')?.showModal());</script>
                @endif
            </x-layout.card-section> --}}

            <x-layout.card-section
                title="Recent Activity"
                icon="bx-history"
                :padded=false
                headerRight="Last 20 event's">

                <div class="overflow-y-auto" style="max-height: 360px;">
                    @if ($recentActivity->isEmpty())
                        <div class="text-center text-[13px] text-[#94a3b8]">No activity recorded yet.</div>
                    @else
                        <ul class="divide-[#f1f5f9]">
                            @foreach ($recentActivity as $log)
                                @php
                                    $key = strtolower($log->action);
                                    $ai  = $activityIcons[$key] ?? $defaultIcon;
                                @endphp
                                <li class="flex items-start gap-3 px-5 py-2.5">
                                    <span class="mt-0.5 shrink-0 text-[15px]" style="color: {{ $ai['color'] }};">
                                        <i class="bx {{ $ai['icon'] }}"></i>
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[12px] font-semibold text-[#1e293b] leading-snug">
                                            {{ $log->action }}
                                            @if ($log->description)
                                                <span class="font-normal text-[#64748b]"> — {{ $log->description }}</span>
                                            @endif
                                        </p>
                                        @if ($log->module)
                                            <p class="text-[11px] text-[#94a3b8]">{{ $log->module }}</p>
                                        @endif
                                    </div>
                                    <span class="shrink-0 text-[11px] text-[#94a3b8] whitespace-nowrap">
                                        {{ $log->timestamp->format('M d, g:i A') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

            </x-layout.card-section>

            {{-- Change Password --}}
            @unless ($isAdmin)
                <div class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
                    <div class="px-5 py-3 border-b border-[#e2e8f0] bg-[#f8fafc] flex items-center gap-2">
                        <i class="bx bx-lock-alt text-[#16a34a] text-base"></i>
                        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">Security — Change Password</p>
                    </div>
                    <div class="p-5 space-y-4">
                        <p class="text-[13px] text-[#64748b] leading-relaxed">
                            Enter your current password and set a new one. After clicking
                            <span class="font-medium text-[#334155]">Send OTP</span>,
                            a verification code will be sent to your email.
                        </p>

                        {{-- Step 1 --}}
                        <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-5"
                             id="change-password-step1">
                            <p class="text-[12px] font-semibold text-[#475569] mb-3">Step 1 — Set New Password</p>
                            <form method="POST" action="{{ route('profile.password.change') }}"
                                  class="grid md:grid-cols-2 gap-4 text-sm">
                                @csrf
                                <div class="md:col-span-2">
                                    <x-form.label>Current Password</x-form.label>
                                    <div x-data="{ show: false }" class="relative mt-1.5">
                                        <x-form.input name="current_password" x-bind:type="show ? 'text' : 'password'" class="pr-11 @error('current_password') border-rose-400 @enderror" required />
                                        <button type="button" @click="show = !show"
                                            class="absolute inset-y-0 right-0 flex items-center px-3 text-[#94a3b8] hover:text-[#475569] transition">
                                            <i :class="show ? 'bx bx-hide' : 'bx bx-show'" class="text-lg leading-none"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <p class="text-rose-600 text-xs mt-1 flex items-center gap-1"><i class="bx bx-error-circle"></i> {{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <x-form.label>New Password</x-form.label>
                                    <div x-data="{ show: false }" class="relative mt-1.5">
                                        <x-form.input name="password" x-bind:type="show ? 'text' : 'password'" class="pr-11 @error('password') border-rose-400 @enderror" required />
                                        <button type="button" @click="show = !show"
                                            class="absolute inset-y-0 right-0 flex items-center px-3 text-[#94a3b8] hover:text-[#475569] transition">
                                            <i :class="show ? 'bx bx-hide' : 'bx bx-show'" class="text-lg leading-none"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="text-rose-600 text-xs mt-1 flex items-center gap-1"><i class="bx bx-error-circle"></i> {{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <x-form.label>Confirm New Password</x-form.label>
                                    <div x-data="{ show: false }" class="relative mt-1.5">
                                        <x-form.input name="password_confirmation" x-bind:type="show ? 'text' : 'password'" class="pr-11 @error('password_confirmation') border-rose-400 @enderror" required />
                                        <button type="button" @click="show = !show"
                                            class="absolute inset-y-0 right-0 flex items-center px-3 text-[#94a3b8] hover:text-[#475569] transition">
                                            <i :class="show ? 'bx bx-hide' : 'bx bx-show'" class="text-lg leading-none"></i>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                        <p class="text-rose-600 text-xs mt-1 flex items-center gap-1"><i class="bx bx-error-circle"></i> {{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2 flex justify-end">
                                    <x-ui.button type="submit" variant="save">Send OTP</x-ui.button>
                                </div>
                            </form>
                        </div>

                        {{-- Step 2 --}}
                        @if ($hasPendingPasswordOtp)
                            <div class="rounded-xl border border-[#bbf7d0] bg-[#f0fdf4] p-5">
                                <p class="text-[12px] font-semibold text-[#166534] mb-2">Step 2 — Enter Verification Code</p>
                                <p class="text-[13px] text-[#166534] mb-4">A 6-digit OTP has been sent to your email.</p>
                                <form method="POST" action="{{ route('profile.password.verify-otp') }}"
                                      class="flex flex-col md:flex-row items-start md:items-end gap-4 text-sm">
                                    @csrf
                                    <div class="w-full md:w-56">
                                        <x-form.label>OTP Code</x-form.label>
                                        <x-form.input type="text" name="otp" maxlength="6"
                                            class="mt-1.5 text-center tracking-[0.4em] text-lg font-semibold" required />
                                        @error('otp')<span class="text-rose-600 text-xs">{{ $message }}</span>@enderror
                                    </div>
                                    <x-ui.button type="submit" variant="save">Confirm Password Change</x-ui.button>
                                </form>
                                <form method="POST" action="{{ route('profile.password.resend-otp') }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="text-[13px] text-[#0ea5e9] hover:underline">Resend OTP</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endunless

        </div>
        {{-- /.right --}}

    </div>
</x-layout.panel>

@endsection

@if ($errors->hasAny(['current_password', 'password', 'password_confirmation']))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('change-password-step1')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
</script>
@endif
