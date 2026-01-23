@extends('layouts.app')

@section('content')
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium">
        ← Back
    </a>

    <div class="mb-4">
        <p class="text-lg font-semibold text-slate-800">Accounts</p>
        <p class="text-sm text-slate-500">
            Manage user access, statuses, and role assignments
        </p>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm ring-1 ring-slate-200">
        <table class="min-w-full border-collapse">
            <thead class="sticky top-0 z-10 bg-slate-50">
                <tr class="text-left text-xs font-semibold text-slate-600 uppercase tracking-wide">
                    <th class="border px-3 py-2">ID</th>
                    <th class="border px-3 py-2">Name</th>
                    <th class="border px-3 py-2">Email</th>
                    <th class="border px-3 py-2">Account Status</th>
                    <th class="border px-3 py-2">Role</th>
                    <th class="border px-3 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-4 py-3 border border-slate-200 align-middle">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 border border-slate-200 font-medium text-slate-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 border border-slate-200 text-slate-600 text-sm">{{ $user->email }}</td>
                        <td class="px-4 py-3 border border-slate-200 align-middle">
                            <x-feedback-status.status-indicator status="{{ $user->account_status }}" />
                        </td>

                        <td class="border px-3 py-2">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($user->roles as $role)
                                    <x-feedback-status.status-indicator
                                        :status="$role->name"
                                        :label="ucfirst($role->name)"
                                    />
                                @empty
                                    <x-feedback-status.status-indicator
                                        status="neutral"
                                        label="No Role Assigned"
                                    />
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3 border-b border-slate-200 align-middle space-x-2">
                            @if ($user->account_status === 'pending')
                                <x-button variant='active'
                                        onclick="document.getElementById('approveModal-{{ $user->id }}').showModal();">
                                        Active
                                </x-button>

                                <x-button variant='reject'
                                        onclick="document.getElementById('rejectModal-{{ $user->id }}').showModal();">
                                        Reject
                                </x-button>
                            @elseif ($user->account_status === 'disabled')
                                <x-button variant='active'
                                        onclick="document.getElementById('approveModal-{{ $user->id }}').showModal();">
                                        Active
                                </x-button>
                            @elseif ($user->account_status === 'rejected')
                                <x-button variant='restore'
                                        onclick="document.getElementById('restoreModal-{{ $user->id }}').showModal();">
                                        Restore
                                </x-button>
                            @elseif ($user->account_status === 'active')
                                <x-button variant='disable'
                                        onclick="document.getElementById('disableModal-{{ $user->id }}').showModal();">
                                        Disable
                                </x-button>
                            @endif

                            @if ($user->account_status === 'active')
                                <x-button variant='assign-role'
                                        onclick="document.getElementById('assignRoleModal-{{ $user->id }}').showModal();">
                                        Assign Role
                                </x-button>
                            @endif
                        </td>
                    </tr>

                    {{-- Render modals for each user --}}
                    @if ($user->account_status === 'pending')
                        @include('AccountApproval.modals.approvalModal', [
                            'modalId' => 'approveModal-' . $user->id,
                            'user' => $user,
                            'action' => 'approve'
                        ])
                        @include('AccountApproval.modals.approvalModal', [
                            'modalId' => 'rejectModal-' . $user->id,
                            'user' => $user,
                            'action' => 'reject'
                        ])
                    @elseif ($user->account_status === 'disabled')
                        @include('AccountApproval.modals.approvalModal', [
                            'modalId' => 'approveModal-' . $user->id,
                            'user' => $user,
                            'action' => 'approve'
                        ])
                    @elseif ($user->account_status === 'rejected')
                        @include('AccountApproval.modals.approvalModal', [
                            'modalId' => 'restoreModal-' . $user->id,
                            'user' => $user,
                            'action' => 'restore'
                        ])
                    @elseif ($user->account_status === 'active')
                        @include('AccountApproval.modals.approvalModal', [
                            'modalId' => 'disableModal-' . $user->id,
                            'user' => $user,
                            'action' => 'disable'
                        ])
                    @endif

                    @if ($user->account_status === 'active')
                        @include('AccountApproval.modals.assignRolesModal', [
                            'modalId' => 'assignRoleModal-' . $user->id,
                            'user' => $user
                        ])
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
