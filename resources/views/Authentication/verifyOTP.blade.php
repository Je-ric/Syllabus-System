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

        {{-- Card --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">

            {{-- Top accent --}}
            <div class="h-1.5 w-full green-grad"></div>

            <div class="p-8">

                {{-- Icon + heading --}}
                <div class="flex flex-col items-center text-center mb-7">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center mb-4">
                        <i class="bx bx-envelope-open text-3xl text-emerald-600"></i>
                    </div>
                    <h1 class="text-xl font-bold text-slate-800">Check your email</h1>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        We sent a 6-digit verification code to<br>
                        <strong class="text-slate-700">{{ session('verify_email', 'your email') }}</strong>
                    </p>
                </div>

                @include('includes.error-lists')
                @include('includes.session-success')

                {{-- OTP form --}}
                <form method="POST" action="{{ route('otp.verify') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('verify_email') }}">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 text-center">
                            Enter 6-digit OTP
                        </label>
                        <input type="number" name="otp" maxlength="6"
                               placeholder="• • • • • •"
                               class="auth-input text-center tracking-[0.5em] text-xl font-bold"
                               required autofocus autocomplete="one-time-code">
                        <p class="text-[11px] text-slate-400 text-center mt-1.5">
                            Code expires in 10 minutes. Check your spam folder if not received.
                        </p>
                    </div>

                    <button type="submit"
                            class="w-full auth-secondary text-white py-2.5 rounded-xl font-semibold shadow-sm transition">
                        Verify Email
                    </button>
                </form>

                {{-- Resend --}}
                <div class="mt-5 pt-5 border-t border-slate-100 text-center space-y-2">
                    <p class="text-sm text-slate-500">Didn't receive the code?</p>
                    <form method="POST" action="{{ route('otp.resend.email') }}" class="inline">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('verify_email') }}">
                        <button type="submit"
                                class="text-sm text-emerald-700 font-semibold hover:underline">
                            Resend OTP
                        </button>
                    </form>
                    <span class="text-slate-300 mx-2">·</span>
                    <a href="{{ route('otp.resend') }}"
                       class="text-sm text-emerald-700 font-semibold hover:underline">
                        Use a different email
                    </a>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('auth.show') }}"
                       class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="bx bx-arrow-back text-sm leading-none"></i>
                        <span>Back to Login</span>
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
