@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-user-check"
        title="User Accounts"
        desc="Manage user access, statuses, and role assignments">
    </x-page-header>

    <x-panel>
        <livewire:account-approval.manage-queue />
    </x-panel>
@endsection
