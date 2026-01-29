@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-10">

    <h1 class="text-2xl font-bold text-center">Button Component</h1>

    {{-- TABLE ACTION BUTTONS --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold">Table Action Buttons</h2>
        <div class="flex flex-wrap gap-2">
            <x-button variant="table-confirm">Confirm</x-button>
            <x-button variant="table-disable">Disable</x-button>
            <x-button variant="table-restore">Restore</x-button>
            <x-button variant="table-danger">Danger</x-button>
            <x-button variant="table-manage">Manage</x-button>
            <x-button variant="table-view">View</x-button>
            <x-button variant="table-edit">Edit</x-button>
            <x-button variant="table-cancel">Cancel</x-button>

        </div>
    </div>

    {{-- FORM / CRUD BUTTONS --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold">Form / CRUD Buttons</h2>
        <div class="flex flex-wrap gap-3">
            <x-button variant="add-button">Add</x-button>
            <x-button variant="cancel">Cancel</x-button>
            <x-button variant="save">Save</x-button>
            <x-button variant="primary">Primary</x-button>
            <x-button variant="danger">Danger</x-button>
        </div>
    </div>


</div>
@endsection
