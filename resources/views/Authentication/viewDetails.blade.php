@extends('layouts.app')

@section('content')
    {{-- Your Details --}}
    <x-header-with-button title="Your Details" description="View and manage your personal information">
    </x-header-with-button>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Email Verified At</th>
                <th>Role(s)</th>
                <th>Phone</th>
                <th>Office</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->email_verified_at }}</td>
                <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                <td>{{ $user->phone }}</td>
                <td>{{ $user->office }}</td>
            </tr>
        </tbody>
    </table>
@endsection
