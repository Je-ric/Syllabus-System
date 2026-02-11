@extends('layouts.app')

@section('content')
<div
    class="flex justify-center items-center min-h-screen"
    x-data="{ mode: 'login' }"
>
    <div class="bg-white shadow-lg rounded-lg w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 overflow-hidden">

        <!-- LEFT -->
        <div class="p-10 bg-gray-50 flex flex-col justify-center">

            @include('includes.error-lists')
            @include('includes.session-success')

            <!-- LOGIN -->
            <div x-show="mode === 'login'" x-transition>
                <h2 class="text-2xl font-bold mb-6">Login</h2>

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <input type="email"
                        name="email"
                        placeholder="Email"
                        class="w-full border rounded px-3 py-2"
                        required>

                    <input type="password"
                        name="password"
                        placeholder="Password"
                        class="w-full border rounded px-3 py-2"
                        required>

                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                        <label for="remember" class="ml-2 block text-sm text-gray-600">
                            Remember me
                        </label>
                    </div>

                    <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                        Login
                    </button>
                </form>

                <p class="text-sm text-gray-600 mt-4">
                    Don’t have an account?
                    <button
                        type="button"
                        class="text-blue-600 hover:underline"
                        @click="mode = 'register'"
                    >
                        Sign up
                    </button>
                </p>

                <p class="text-sm text-gray-600 mt-2">
                    Already registered but need to verify email?
                    <a class="text-blue-600 hover:underline" href="{{ route('otp.resend') }}">
                        Resend OTP
                    </a>
                </p>
            </div>

            <!-- REGISTER -->
            <div x-show="mode === 'register'" x-transition>
                <h2 class="text-2xl font-bold mb-4">Sign Up</h2>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <input type="text"
                        name="name"
                        placeholder="Full Name"
                        class="w-full border rounded px-3 py-2"
                        required>

                    <input type="text"
                        name="phone_number"
                        placeholder="Phone Number"
                        class="w-full border rounded px-3 py-2"
                        required>

                    <input type="text"
                        name="office"
                        placeholder="Office / Department (where to find you)"
                        class="w-full border rounded px-3 py-2"
                        required>

                    <input type="email"
                        name="email"
                        placeholder="Email"
                        class="w-full border rounded px-3 py-2"
                        required>

                    <input type="password"
                        name="password"
                        placeholder="Password"
                        class="w-full border rounded px-3 py-2"
                        required>

                    <input type="password"
                        name="password_confirmation"
                        placeholder="Confirm Password"
                        class="w-full border rounded px-3 py-2"
                        required>

                    <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                        Create Account
                    </button>
                </form>

                <p class="text-sm text-gray-600 mt-4">
                    Already have an account?
                    <button
                        type="button"
                        class="text-blue-600 hover:underline"
                        @click="mode = 'login'"
                    >
                        Login
                    </button>
                </p>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="p-10 bg-blue-600 text-white flex flex-col justify-center">
            <h2 class="text-3xl font-bold mb-4">Welcome to CSMS</h2>
            <p class="mb-4">
                Central Luzon State University Content Management System helps you manage syllabi, programs, and courses
                efficiently.
            </p>
            <p class="mb-2">
                Sign up using your CLSU or CLSU2 email to get started.
            </p>
            <p class="mb-2">
                After signing up, you will receive an OTP to verify your email. Once verified, your account will wait
                for OLOI approval before you can access all features.
            </p>
            <p class="italic text-gray-200 mt-4">
                "Your account security and verification are important for proper access."
            </p>
        </div>

    </div>
</div>
@endsection
