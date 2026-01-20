@extends('layouts.app')

@section('content')
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white shadow-lg rounded-lg w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 overflow-hidden">

            <!-- Left -->
            <div class="p-10 bg-gray-50 flex flex-col justify-center">

                @if (!session('verify_email') && !session('waiting_approval'))
                    <!-- LOGIN -->
                    
                    <h2 class="text-2xl font-bold mb-6">Login</h2>
                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="list-disc list-inside text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="text-green-600 text-sm mb-2">
                            {{ session('success') }}
                        </div>
                    @endif

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
                @endif

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

    <!-- OTP Modal -->
    @if (session('verify_email'))
        <div x-data="{ open: true }" x-show="open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4" x-transition>
                <div class="flex justify-between items-center border-b px-6 py-4">
                    <h2 class="text-lg font-semibold">Verify Your Email</h2>
                    <button @click="open = false" class="text-gray-500 hover:text-gray-700">&times;</button>
                </div>

                <div class="p-6">
                    <p class="mb-4 text-gray-700">
                        We sent a 6-digit OTP to <strong>{{ session('verify_email') }}</strong>.
                        <br>Enter it below to verify your account.
                    </p>

                    <form method="POST" action="{{ route('verify.otp') }}" class="space-y-4">
                        @csrf
                        <input type="hidden"
                                name="email"
                                value="{{ session('verify_email') }}">
                        <input type="text"
                                name="otp"
                                placeholder="Enter OTP"
                                class="w-full border rounded px-3 py-2"
                                required>
                        <button class="w-full bg-yellow-500 text-white py-2 rounded hover:bg-yellow-600">
                            Verify Email
                        </button>
                    </form>

                    <form method="POST" action="{{ route('request.otp') }}" class="mt-2">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('verify_email') }}">
                        <button class="text-blue-600 underline">Resend OTP</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection
