@extends('layouts.app')

@section('content')

    <x-header-with-button title="User Accounts"
                        description="Manage user access, statuses, and role assignments">
    </x-header-with-button>

    <livewire:account-approval.manage-queue />
@endsection
