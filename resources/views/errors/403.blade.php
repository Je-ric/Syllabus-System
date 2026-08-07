@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <h1 class="display-1">403</h1>

    <h3>Access Denied</h3>

    <p>
        You don't have permission to access this page.
    </p>

    <a href="{{ route('dashboard') }}" class="btn btn-primary">
        Return to Dashboard
    </a>
</div>
@endsection
