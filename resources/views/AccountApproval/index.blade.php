@extends('layouts.app')

@section('content')

    <x-header-with-button title="User Accounts"
                        description="Manage user access, statuses, and role assignments">
        <x-button variant="cancel" href="{{ route('dashboard') }}">Back</x-button>
    </x-header-with-button>

    <div class="overflow-x-auto rounded-2xl border border-slate-200/80 bg-white/90 shadow-sm">
        <table class="min-w-full border-collapse text-sm">
            <thead class="sticky top-0 z-10 bg-emerald-50 text-emerald-800">
                <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em]">
                    <th class="border border-slate-200 px-4 py-3">ID</th>
                    <th class="border border-slate-200 px-4 py-3">Name & Email</th>
                    <th class="border border-slate-200 px-4 py-3">Phone Number</th>
                    <th class="border border-slate-200 px-4 py-3">Office</th>
                    <th class="border border-slate-200 px-4 py-3">Account Status</th>
                    <th class="border border-slate-200 px-4 py-3">Role</th>
                    <th class="border border-slate-200 px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="odd:bg-white even:bg-slate-50 hover:bg-emerald-50/60 transition-colors group">
                        <td class="px-4 py-3 border border-slate-200 align-middle font-medium text-slate-700">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 border border-slate-200 text-slate-800">
                            <div class="font-semibold">{{ $user->name }}</div>
                            <div class="text-xs text-slate-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-4 py-3 border border-slate-200 text-slate-600">{{ $user->phone_number }}</td>
                        <td class="px-4 py-3 border border-slate-200 text-slate-600">{{ $user->office }}</td>
                        <td class="px-4 py-3 border border-slate-200 align-middle">
                            <x-feedback-status.status-indicator status="{{ $user->account_status }}" />
                        </td>

                        <td class="border border-slate-200 px-4 py-3">
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
                        <td class="px-4 py-3 border border-slate-200 align-middle">
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
