@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 rounded-lg shadow-lg text-center max-w-lg">
        <div class="mb-6">
            <svg class="w-20 h-20 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold mb-4 text-gray-800">Email Verified!</h1>

        <p class="mb-4 text-gray-600">
            Your email has been successfully verified.
        </p>

        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
            <p class="text-yellow-800 font-semibold">
                ⏳ Pending Approval
            </p>
            <p class="text-sm text-yellow-700 mt-2">
                Your account is waiting for OLOI approval. You'll be able to access the system once approved.
            </p>
        </div>

        <p class="text-sm text-gray-500 mb-6">
            You will receive a notification once your account is activated.
        </p>

        <a href="{{ route('login') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
            Back to Login
        </a>
    </div>
</div>
@endsection
