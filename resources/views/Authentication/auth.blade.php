<!DOCTYPE html>
<html lang="en">

<head>
    @include('includes.head-assets')
</head>

<body>
    <div class="flex justify-center items-center min-h-screen px-4 py-8" x-data="{ mode: 'login' }">
        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 overflow-hidden rounded-3xl border border-slate-200/80 bg-white/90 shadow-xl backdrop-blur">

            <div class="p-8 md:p-10 bg-white flex flex-col justify-center">
                @include('includes.error-lists')
                @include('includes.session-success')

                <div x-show="mode === 'login'" x-transition>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">Account Access</p>
                    <h2 class="text-3xl font-semibold text-slate-800 mb-5 border-b-4 border-yellow-500 pb-4">Login</h2>

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf
                        <input type="email" name="email" placeholder="Email" class="auth-input" required>

                        <input type="password" name="password" placeholder="Password" class="auth-input" required>

                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-600">
                            <label for="remember" class="ml-2 block text-sm text-slate-600">
                                Remember me
                            </label>
                        </div>

                        <button class="w-full auth-secondary text-white py-2.5 rounded-xl font-semibold shadow-sm transition">
                            Login
                        </button>
                    </form>

                    <p class="text-sm text-slate-600 mt-4">
                        Don't have an account?
                        <button type="button" class="text-emerald-700 font-semibold hover:underline" @click="mode = 'register'">
                            Sign up
                        </button>
                    </p>

                    <p class="text-sm text-slate-600 mt-2">
                        Already registered but need to verify email?
                        <a class="text-emerald-700 font-semibold hover:underline" href="{{ route('otp.resend') }}">
                            Resend OTP
                        </a>
                    </p>
                </div>

                <div x-show="mode === 'register'" x-transition>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">New Account</p>
                    <h2 class="text-3xl font-semibold mb-4 text-slate-800">Sign Up</h2>

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf
                        <input type="text" name="name" placeholder="Full Name" class="auth-input" required>

                        <input type="text" name="phone_number" placeholder="Phone Number" class="auth-input" required>

                        <input type="text" name="office" placeholder="Office / Department (where to find you)" class="auth-input" required>

                        <input type="email" name="email" placeholder="Email" class="auth-input" required>

                        <input type="password" name="password" placeholder="Password" class="auth-input" required>

                        <input type="password" name="password_confirmation" placeholder="Confirm Password" class="auth-input" required>

                        <button class="w-full auth-primary text-white py-2.5 rounded-xl font-semibold shadow-sm transition">
                            Create Account
                        </button>
                    </form>

                    <p class="text-sm text-slate-600 mt-4">
                        Already have an account?
                        <button type="button" class="text-emerald-700 font-semibold hover:underline" @click="mode = 'login'">
                            Login
                        </button>
                    </p>
                </div>
            </div>

            <div class="p-8 md:p-10 text-white flex flex-col justify-center green-grad">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-100 mb-2">Central Luzon State University</p>
                <h2 class="text-3xl font-semibold mb-4">Welcome to CSMS</h2>
                <p class="mb-4 text-emerald-50/95">
                    Central Luzon State University Content Management System helps you manage syllabi, programs, and courses
                    efficiently.
                </p>
                <p class="mb-2 text-emerald-50/95">
                    Sign up using your CLSU or CLSU2 email to get started.
                </p>
                <p class="mb-2 text-emerald-50/95">
                    After signing up, you will receive an OTP to verify your email. Once verified, your account will wait
                    for OLOI approval before you can access all features.
                </p>
                <p class="italic text-emerald-100/90 mt-4">
                    "Your account security and verification are important for proper access."
                </p>
            </div>
        </div>
    </div>
    @livewireScripts
</body>

</html>
