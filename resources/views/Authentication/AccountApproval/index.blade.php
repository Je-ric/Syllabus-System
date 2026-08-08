@extends('layouts.app')

@section('content')

    <x-layout.page-header
        icon="bx-user-check"
        title="User Accounts"
        desc="Manage user access, statuses, and role assignments">
    </x-layout.page-header>

    <x-layout.panel>
        <livewire:account-approval.manage-queue />
    </x-layout.panel>
@endsection
