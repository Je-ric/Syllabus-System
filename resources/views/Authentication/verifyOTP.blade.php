@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg w-full max-w-md p-8">
        <h2 class="text-2xl font-bold mb-4 text-center">Verify Your Email</h2>

        <p class="text-gray-600 text-center mb-6">
            We sent a 6-digit OTP to<br>
            <strong class="text-blue-600">{{ session('verify_email') }}</strong>
        </p>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
                <ul class="list-disc list-inside text-red-600 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-600 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verify.otp') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="email" value="{{ session('verify_email') }}">

            <input
                type="text"
                name="otp"
                maxlength="6"
                placeholder="Enter 6-digit OTP"
                class="w-full border border-gray-300 rounded px-3 py-2 text-center tracking-widest text-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
                autofocus
            >

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Verify OTP
            </button>
        </form>

        <form method="POST" action="{{ route('request.otp') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-blue-600 hover:underline">
                Resend OTP
            </button>
        </form>

        <p class="text-xs text-gray-500 text-center mt-6">
            Didn't receive the code? Check your spam folder or resend.
        </p>

        <div class="mt-2 text-center">
            <a href="{{ route('otp.resend') }}" class="text-sm text-blue-600 hover:underline">
                Resend using your email
            </a>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('auth.show') }}" class="text-sm text-gray-600 hover:text-gray-800">
                ← Back to Login
            </a>
        </div>
    </div>
</div>
@endsection
