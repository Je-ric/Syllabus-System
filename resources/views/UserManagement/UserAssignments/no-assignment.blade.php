@extends('layouts.app')

@section('content')
    @php $isAdmin = auth()->user()?->hasRole('admin'); @endphp

    <x-layout.page-header
        icon="bx-info-circle"
        title="No Assignment Found"
        desc="You have not been assigned a role in the organizational hierarchy yet.">
        <x-ui.button
            variant="cancel"
            href="{{ $isAdmin ? route('user-assignments.colleges.index') : route('profile.index') }}">
            <i class="bx bx-arrow-back"></i>
            {{ $isAdmin ? 'Back to Colleges' : 'Back to Profile' }}
        </x-ui.button>
    </x-layout.page-header>

    <x-layout.panel>
        <x-feedback-status.empty-state
            icon="bx-user-x"
            title="No assignment found"
            message="You have not been assigned as a dean or chair yet. Please contact your administrator to receive an assignment." />
    </x-layout.panel>

@endsection
