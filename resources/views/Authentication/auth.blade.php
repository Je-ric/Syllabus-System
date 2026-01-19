@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-50">
    <div class="bg-white shadow-lg rounded-lg w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 overflow-hidden">

        <div class="p-10 bg-gray-50 flex flex-col justify-center">

            @if(!session('verify_email') && !session('waiting_approval'))
                <h2 class="text-2xl font-bold mb-6">Login</h2>

                @if ($errors->any())
                    <div class="text-red-500 text-sm mb-2">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="text-green-600 text-sm mb-2">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <input type="email" name="email" placeholder="Email" class="w-full border rounded px-3 py-2" required>
                    <input type="password" name="password" placeholder="Password" class="w-full border rounded px-3 py-2" required>
                    <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>
                </form>

                <hr class="my-6 border-gray-300">

                <h2 class="text-2xl font-bold mb-4">Sign Up</h2>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <input type="text" name="name" placeholder="Full Name" class="w-full border rounded px-3 py-2" required>
                    <input type="email" name="email" placeholder="Email" class="w-full border rounded px-3 py-2" required>
                    <input type="password" name="password" placeholder="Password" class="w-full border rounded px-3 py-2" required>
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" class="w-full border rounded px-3 py-2" required>
                    <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Create Account</button>
                </form>
            @endif

            <!-- OTP Verification Form -->
            @if(session('verify_email'))
                <h2 class="text-2xl font-bold mb-4">Verify Your Email</h2>
                <p class="mb-4 text-gray-700">We sent a 6-digit OTP to <strong>{{ session('verify_email') }}</strong>. Enter it below to verify your account.</p>

                @if ($errors->has('otp'))
                    <div class="text-red-500 mb-2">{{ $errors->first('otp') }}</div>
                @endif

                <form method="POST" action="{{ route('verify.otp') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('verify_email') }}">
                    <input type="text" name="otp" placeholder="Enter OTP" class="w-full border rounded px-3 py-2" required>
                    <button class="w-full bg-yellow-500 text-white py-2 rounded hover:bg-yellow-600">Verify Email</button>
                </form>

                <form method="POST" action="{{ route('request.otp') }}" class="mt-2">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('verify_email') }}">
                    <button class="text-blue-600 underline">Resend OTP</button>
                </form>
            @endif

            @if(session('waiting_approval'))
                <h2 class="text-2xl font-bold mb-4">Account Verified!</h2>
                <p class="mb-4 text-gray-700">Your email has been verified. Please wait for the OLOI to approve your account. You will be notified once your account is active.</p>
            @endif
        </div>

        <div class="p-10 bg-blue-600 text-white flex flex-col justify-center">
            <h2 class="text-3xl font-bold mb-4">Welcome to CSMS</h2>
            <p class="mb-4">
                Central Luzon State University Content Management System helps you manage syllabi, programs, and courses efficiently.
            </p>
            <p class="mb-2">
                Sign up using your CLSU or CLSU2 email to get started.
            </p>
            <p class="mb-2">
                After signing up, you will receive an OTP to verify your email. Once verified, your account will wait for OLOI approval before you can access all features.
            </p>
            <p class="italic text-gray-200 mt-4">
                "Your account security and verification are important for proper access."
            </p>
        </div>

    </div>
</div>
@endsection
