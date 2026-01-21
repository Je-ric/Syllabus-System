@extends('layouts.app')

@section('content')
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white shadow-lg rounded-lg w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 overflow-hidden">

            <!-- Left -->
            <div class="p-10 bg-gray-50 flex flex-col justify-center">
                <!-- LOGIN -->
                <h2 class="text-2xl font-bold mb-6">Login</h2>
                    @include('includes.error-lists')
                    @include('includes.session-success')

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
                        <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>
                    </form>

                    <hr class="my-6 border-gray-300">

                    <!-- REGISTER -->
                    <h2 class="text-2xl font-bold mb-4">Sign Up</h2>
                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf
                        <input type="text"
                                name="name"
                                placeholder="Full Name"
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
                        <button
                                class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                                Create Account
                        </button>
                    </form>

                    <p class="text-sm text-gray-600 mt-4">
                        Already registered but need to verify email? <a class="text-blue-600 hover:underline" href="{{ route('otp.resend') }}">Resend OTP</a>
                    </p>
            </div>

            <!-- Right -->
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
