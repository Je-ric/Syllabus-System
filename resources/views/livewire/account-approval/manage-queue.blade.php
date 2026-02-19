<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="md:col-span-2">
            <x-form.input
                type="text"
                wire:model.live.debounce.250ms="search"
                {{-- wire:model.live.debounce.300ms --}}
                placeholder="Search name, email, phone, office..."
            />
        </div>
        <div>
            <x-form.select wire:model.live="role">
                <option value="all">All Roles</option>
                <option value="admin">Admin</option>
                <option value="dean">Dean</option>
                <option value="chair">Chair</option>
                <option value="faculty">Faculty</option>
            </x-form.select>
        </div>
        <div>
            <x-form.select wire:model.live="status">
                <option value="all">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="disabled">Disabled</option>
                <option value="rejected">Rejected</option>
            </x-form.select>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Sort Options</p>
        <x-form.select
            wire:model.live="sort"
            class="w-auto min-w-[12rem] px-3 py-2"
        >
            <option value="name_asc">Name A-Z</option>
            <option value="name_desc">Name Z-A</option>
            <option value="email_asc">Email A-Z</option>
            <option value="email_desc">Email Z-A</option>
            <option value="status_asc">Status A-Z</option>
            <option value="status_desc">Status Z-A</option>
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
        </x-form.select>
    </div>

    <x-table.container>
        <x-table.table>
            <x-table.head :sticky="true">
                <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em]">
                    <x-table.th>#</x-table.th>
                    <x-table.th>Name & Email</x-table.th>
                    <x-table.th>Phone Number</x-table.th>
                    <x-table.th>Office</x-table.th>
                    <x-table.th>Account Status</x-table.th>
                    <x-table.th>Role</x-table.th>
                    <x-table.th>Actions</x-table.th>
                </tr>
            </x-table.head>
            <x-table.body>
                @forelse ($users as $user)
                    <x-table.row striped hover class="group">
                        <x-table.td class="align-middle font-medium text-slate-700">
                            {{ ($users->firstItem() ?? 0) + $loop->index }}
                        </x-table.td>
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
                            </div>
                        </x-table.td>
                    </x-table.row>

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
                @empty
                    <x-table.empty :colspan="7" message="No users found." />
                @endforelse
            </x-table.body>
        </x-table.table>
    </x-table.container>

    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <p class="text-sm text-slate-600">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
        </p>
        <div>
            {{ $users->links() }}
        </div>
    </div>
</div>
