@extends('layouts.app')

@section('content')
    @php $isAdmin = auth()->user()?->hasRole('admin'); @endphp

    <x-page-header
        icon="bx-info-circle"
        title="No Assignment Found"
        desc="You have not been assigned a role in the organizational hierarchy yet.">
        <x-button
            variant="cancel"
            href="{{ $isAdmin ? route('organizational.colleges.index') : route('profile.index') }}">
            <i class="bx bx-arrow-back"></i>
            {{ $isAdmin ? 'Back to Colleges' : 'Back to Profile' }}
        </x-button>
    </x-page-header>

    <x-panel>
        <x-feedback-status.empty-state
            icon="bx-user-x"
            title="No assignment found"
            message="You have not been assigned as a dean or chair yet. Please contact your administrator to receive an assignment." />
    </x-panel>

@endsection
