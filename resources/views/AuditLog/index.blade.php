@extends('layouts.app')

@section('content')

    <x-header-with-button
        title="Audit Logs"
        description="Track system actions, changes, and approvals in real-time" />

    <livewire:audit-log.audit-log />

@endsection
