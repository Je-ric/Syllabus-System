@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen bg-gray-50">
    <div class="bg-white p-10 rounded-lg shadow-md text-center max-w-lg">
        <h1 class="text-2xl font-bold mb-4">Account Verified!</h1>
        <p class="mb-4">Your email has been verified.</p>
        <p class="mb-4 text-yellow-700 font-semibold">
            Your account is pending approval by the OLOI. You cannot access other parts of the system yet.
        </p>
        <p class="text-sm text-gray-500">
            Once your account is approved, you will be able to login fully.
        </p>
        <button>
            <a href="{{ route('auth.show') }}" class="mt-6 inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Back
            </a>
        </button>
    </div>
</div>
@endsection
