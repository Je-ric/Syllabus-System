@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen">
    <div class="bg-white shadow rounded p-6 w-full max-w-md">

        <h2 class="text-xl font-bold mb-4 text-center">Resend Verification Code</h2>

        @include('includes.error-lists')
        @include('includes.session-success')

        <form method="POST" action="{{ route('otp.resend.email') }}">
            @csrf
            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                class="w-full border rounded px-3 py-2 mb-4"
                required
            >

            <button class="w-full bg-blue-600 text-white py-2 rounded">
                Send OTP
            </button>
        </form>
    </div>
</div>
@endsection
