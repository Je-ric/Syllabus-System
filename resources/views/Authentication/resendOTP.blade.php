<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.head-assets')
</head>
<body>
@if (session('toast'))
    <x-feedback-status.toast :message="session('toast')['message']" :type="session('toast')['type']" />
@endif
<div class="flex justify-center items-center min-h-screen px-4 py-8">
    <div class="w-full max-w-md">

        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
            <div class="h-1.5 w-full green-grad"></div>

            <div class="p-8">

                <div class="flex flex-col items-center text-center mb-7">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center mb-4">
                        <i class="bx bx-refresh text-3xl text-amber-600"></i>
                    </div>
                    <h1 class="text-xl font-bold text-slate-800">Resend Verification Code</h1>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        Enter your registered CLSU email and we'll send you a fresh 6-digit OTP.
                    </p>
                </div>

                @include('includes.error-lists')
                @include('includes.session-success')

                <form method="POST" action="{{ route('otp.resend.email') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email Address</label>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="you@clsu.edu.ph"
                               class="auth-input" required autofocus>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Only works if your email is not yet verified.
                        </p>
                    </div>

                    <button type="submit"
                            class="w-full auth-secondary text-white py-2.5 rounded-xl font-semibold shadow-sm transition">
                        Send Verification Code
                    </button>
                </form>

                <div class="mt-5 text-center">
                    <a href="{{ route('auth.show') }}"
                       class="text-xs text-slate-400 hover:text-slate-600 transition-colors">
                        ← Back to Login
                    </a>
                </div>

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
