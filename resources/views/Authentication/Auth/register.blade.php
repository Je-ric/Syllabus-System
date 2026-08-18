<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.head-assets')
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body
    class="relative min-h-screen bg-cover bg-center bg-no-repeat"
>
@if (session('toast'))
    <x-feedback-status.toast :message="session('toast')['message']" :type="session('toast')['type']" />
@endif
<x-feedback-status.toast />
@livewireScripts
<div
    class="relative flex justify-center items-center min-h-screen px-4 py-8 overflow-hidden"
>

    <div class="w-full max-w-5xl
            grid grid-cols-1 lg:grid-cols-2
            overflow-hidden
            rounded-3xl
            border border-white/60
            bg-white/92
            backdrop-blur-xl
            shadow-[0_30px_80px_rgba(0,0,0,.28)]">

        {{-- ── Left: Forms ── --}}
        <div class="p-8 md:p-10 flex flex-col justify-center bg-white">

            @include('includes.session-success')

            @error('name')
                <div class="mb-3 p-3 rounded-lg bg-rose-50 border border-rose-200">
                    <p class="text-[13px] text-rose-600 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                </div>
            @enderror
            @error('phone_number')
                <div class="mb-3 p-3 rounded-lg bg-rose-50 border border-rose-200">
                    <p class="text-[13px] text-rose-600 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                </div>
            @enderror
            @error('office')
                <div class="mb-3 p-3 rounded-lg bg-rose-50 border border-rose-200">
                    <p class="text-[13px] text-rose-600 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                </div>
            @enderror
            @error('email')
                <div class="mb-3 p-3 rounded-lg bg-rose-50 border border-rose-200">
                    <p class="text-[13px] text-rose-600 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                </div>
            @enderror
            @error('password')
                <div class="mb-3 p-3 rounded-lg bg-rose-50 border border-rose-200">
                    <p class="text-[13px] text-rose-600 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                </div>
            @enderror
            @error('password_confirmation')
                <div class="mb-3 p-3 rounded-lg bg-rose-50 border border-rose-200">
                    <p class="text-[13px] text-rose-600 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                </div>
            @enderror

            {{-- ════ REGISTER ════ --}}
            <div>

                <div class="mb-6">
                    <p class="text-[11px] uppercase tracking-[0.3em] text-slate-400 mb-1">New Account</p>
                    <h2 class="text-2xl font-bold text-slate-800">Create your account</h2>
                    <p class="text-sm text-slate-500 mt-1">Use your official CLSU email to register.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="_mode" value="register">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="e.g. Juan dela Cruz"
                               pattern="[\p{L}\s]+"
                               title="Name must contain letters and spaces only"
                               class="auth-input" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Phone Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                               placeholder="e.g. 09XX-XXX-XXXX"
                               class="auth-input" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Office / Department</label>
                        <input type="text" name="office" value="{{ old('office') }}"
                               placeholder="Where can we find you?"
                               class="auth-input" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="you@clsu.edu.ph or you@clsu2.edu.ph"
                               class="auth-input" required>
                        <p class="text-[11px] text-slate-400 mt-1">Must be a valid @clsu.edu.ph or @clsu2.edu.ph address.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="register-password"
                                   placeholder="Minimum 8 characters"
                                   class="auth-input pr-11" required>
                            <button type="button" onclick="togglePassword('register-password', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="bx bx-show text-lg leading-none"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="register-password-confirm"
                                   placeholder="Re-enter your password"
                                   class="auth-input pr-11" required>
                            <button type="button" onclick="togglePassword('register-password-confirm', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="bx bx-show text-lg leading-none"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full auth-primary text-white py-2.5 rounded-xl font-semibold shadow-sm transition">
                        Create Account
                    </button>
                </form>

                <p class="text-sm text-slate-500 mt-4">
                    Already have an account?
                    <a href="{{ route('auth.login') }}" class="text-emerald-700 font-semibold hover:underline">
                        Sign in
                    </a>
                </p>
            </div>
        </div>

        {{-- ── Right: Branding panel ── --}}
        <div class="p-8 md:p-10 text-white flex flex-col justify-between green-grad">
            <div>
                <div class="flex flex-col items-center text-center mb-7">
                    <div class="flex items-center justify-center shrink-0 overflow-hidden mb-4">
                        <img src="{{ asset('assets/clsu-logo-green.png') }}" alt="CLSU Logo" class="w-36 h-36 object-contain">
                    </div>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-emerald-100 mb-2">CLSU · CSMS</p>
                    <h2 class="text-2xl font-bold leading-snug">
                        Course Syllabus<br>Management System
                    </h2>
                </div>

                {{-- How it works --}}
                <div class="space-y-3 mb-6">
                    @foreach ([
                        ['bx-user-plus',   '1', 'Register',        'Sign up with your CLSU email.'],
                        ['bx-time',        '2', 'Await Approval',   'An admin reviews and activates your account.'],
                        ['bx-check-shield','3', 'Access Granted',   'Log in and start managing syllabi.'],
                    ] as [$icon, $num, $title, $desc])
                    <div class="flex items-start gap-3">
                        <span class="flex items-center justify-center w-7 h-7 rounded-full bg-white/20 text-xs font-bold shrink-0 mt-0.5">{{ $num }}</span>
                        <div>
                            <p class="text-sm font-semibold leading-tight">{{ $title }}</p>
                            <p class="text-xs text-emerald-100/80 mt-0.5">{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'bx bx-show text-lg leading-none' : 'bx bx-hide text-lg leading-none';
}
</script>
</body>
</html>
