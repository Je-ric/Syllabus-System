<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.head-assets')
</head>
<body>
<div class="flex justify-center items-center min-h-screen px-4 py-8">
    <div class="w-full max-w-md">

        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
            <div class="h-1.5 w-full green-grad"></div>

            <div class="p-8">

                {{-- Icon --}}
                <div class="flex flex-col items-center text-center mb-7">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mb-4">
                        <i class="bx bx-check-shield text-4xl text-emerald-600"></i>
                    </div>
                    <h1 class="text-xl font-bold text-slate-800">Email Verified!</h1>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        Your email address has been successfully confirmed.
                    </p>
                </div>

                {{-- Status card --}}
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="bx bx-time-five text-xl text-amber-500 shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">Pending Admin Approval</p>
                            <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                                Your account is currently under review. An administrator will activate your account shortly. You'll receive an email notification once approved.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- What happens next --}}
                <div class="space-y-3 mb-7">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">What happens next</p>
                    @foreach ([
                        ['bx-envelope-open', 'Email notification', 'You will receive an email when your account is activated.'],
                        ['bx-user-check',    'Faculty role assigned', 'Approved accounts are granted the Faculty role by default.'],
                        ['bx-log-in',        'Log in and get started', 'Once active, sign in to access syllabi and programs.'],
                    ] as [$icon, $title, $desc])
                    <div class="flex items-start gap-3">
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 shrink-0 mt-0.5">
                            <i class="bx {{ $icon }} text-sm leading-none"></i>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-slate-700">{{ $title }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <a href="{{ route('auth.show') }}"
                   class="block w-full text-center auth-secondary text-white py-2.5 rounded-xl font-semibold shadow-sm transition">
                    Back to Login
                </a>

            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-5">
            Central Luzon State University · CSMS
        </p>
    </div>
</div>
@livewireScripts
</body>
</html>
