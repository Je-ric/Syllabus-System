@extends('layouts.app')

@section('content')

    <x-layout.page-header
        icon="bx-history"
        title="Audit Logs"
        desc="Track system actions, changes, and approvals in real-time" />

    <x-layout.panel>
        <livewire:audit-log.audit-log />
    </x-layout.panel>

@endsection
