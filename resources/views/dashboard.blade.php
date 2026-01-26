@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-10">

    <h1 class="text-2xl font-bold text-center">Button Component Showcase</h1>

    {{-- STANDARD BUTTONS --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold">Standard Buttons</h2>
        <div class="flex flex-wrap gap-3">
            <x-button variant="primary">Primary</x-button>
            <x-button variant="success">Success</x-button>
            <x-button variant="danger">Danger</x-button>
            <x-button variant="warning">Warning</x-button>
            <x-button variant="info">Info</x-button>
            <x-button variant="manage">Manage</x-button>
        </div>
    </div>

    {{-- LINK BUTTON --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold">Anchor Button</h2>
        <x-button href="/dashboard" variant="primary">
            Go to Dashboard
        </x-button>
    </div>

    {{-- TABLE ACTION BUTTONS --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold">Table Action Buttons</h2>
        <div class="flex flex-wrap gap-2">
            <x-button variant="table-active">Active</x-button>
            <x-button variant="table-disable">Disabled</x-button>
            <x-button variant="table-restore">Restore</x-button>
            <x-button variant="table-reject">Reject</x-button>
            <x-button variant="table-assign-role">Assign Role</x-button>
        </div>
    </div>

    {{-- FORM / CRUD BUTTONS --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold">Form / CRUD Buttons</h2>
        <div class="flex flex-wrap gap-3">
            <x-button variant="add-button">Add</x-button>
            <x-button variant="table-save-input" type="submit">Save</x-button>
            <x-button variant="table-update">Update</x-button>
            <x-button variant="table-delete">Delete</x-button>
            <x-button variant="table-cancel">Cancel</x-button>
        </div>
    </div>

    {{-- ICON BUTTON --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold">Icon Button</h2>
        <x-button variant="table-active" class="tooltip" data-tip="View">
            <i class="bx bx-show"></i>
        </x-button>
    </div>

</div>
@endsection
