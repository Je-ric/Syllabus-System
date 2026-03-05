@extends('layouts.app')

@section('content')

    <x-page-header
        icon="bx-history"
        title="Audit Logs"
        desc="Track system actions, changes, and approvals in real-time" />

    <x-panel>
        <livewire:audit-log.audit-log />
    </x-panel>

@endsection
