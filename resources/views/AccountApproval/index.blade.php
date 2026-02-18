@extends('layouts.app')

@section('content')

    <x-header-with-button title="User Accounts"
                        description="Manage user access, statuses, and role assignments">
    </x-header-with-button>

    <x-table.container>
        <x-table.table>
            <x-table.head :sticky="true">
                <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em]">
                    <x-table.th>ID</x-table.th>
                    <x-table.th>Name & Email</x-table.th>
                    <x-table.th>Phone Number</x-table.th>
                    <x-table.th>Office</x-table.th>
                    <x-table.th>Account Status</x-table.th>
                    <x-table.th>Role</x-table.th>
                    <x-table.th>Actions</x-table.th>
                </tr>
            </x-table.head>
            <x-table.body>
                @foreach ($users as $user)
                    <x-table.row striped hover class="group">
                        <x-table.td class="align-middle font-medium text-slate-700">{{ $loop->iteration }}</x-table.td>
                        <x-table.td class="text-slate-800">
                            <div class="font-semibold">{{ $user->name }}</div>
                            <div class="text-xs text-slate-500">{{ $user->email }}</div>
                        </x-table.td>
                        <x-table.td class="text-slate-600">{{ $user->phone_number }}</x-table.td>
                        <x-table.td class="text-slate-600">{{ $user->office }}</x-table.td>
                        <x-table.td class="align-middle">
                            <x-feedback-status.status-indicator status="{{ $user->account_status }}" />
                        </x-table.td>

                        <x-table.td>
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
                        </x-table.td>
                        <x-table.td class="align-middle">
                            <div class="flex flex-wrap gap-2">
                            @if ($user->account_status === 'pending')
                                <x-button variant='table-confirm'
                                        onclick="document.getElementById('approveModal-{{ $user->id }}').showModal();">
                                        Active
                                </x-button>

                                <x-button variant='table-danger'
                                        onclick="document.getElementById('rejectModal-{{ $user->id }}').showModal();">
                                        Reject
                                </x-button>
                            @elseif ($user->account_status === 'disabled')
                                <x-button variant='table-confirm'
                                        onclick="document.getElementById('approveModal-{{ $user->id }}').showModal();">
                                        Active
                                </x-button>
                            @elseif ($user->account_status === 'rejected')
                                <x-button variant='table-restore'
                                        onclick="document.getElementById('restoreModal-{{ $user->id }}').showModal();">
                                        Restore
                                </x-button>
                            @elseif ($user->account_status === 'active')
                                <x-button variant='table-disable'
                                        onclick="document.getElementById('disableModal-{{ $user->id }}').showModal();">
                                        Disable
                                </x-button>
                            @endif

                            @if ($user->account_status === 'active')
                                <x-button variant='table-manage'
                                        onclick="document.getElementById('assignRoleModal-{{ $user->id }}').showModal();">
                                        Assign Role
                                </x-button>
                            @endif

                            {{-- <x-button>Edit </x-button> --}}
                            </div>
                        </x-table.td>
                    </x-table.row>

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
            </x-table.body>
        </x-table.table>
    </x-table.container>
@endsection
