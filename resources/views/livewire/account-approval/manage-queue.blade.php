<div class="space-y-4">

    {{-- ── Filters ─────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="md:col-span-2 relative">
            <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none"></i>
            <x-form.input
                type="text"
                wire:model.live.debounce.250ms="search"
                placeholder="Search name, email, phone, office…"
                class="pl-9" />
        </div>
        <x-form.select wire:model.live="role">
            <option value="all">All Roles</option>
            <option value="admin">Admin</option>
            <option value="dean">Dean</option>
            <option value="chair">Chair</option>
            <option value="faculty">Faculty</option>
        </x-form.select>
        <x-form.select wire:model.live="status">
            <option value="all">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="active">Active</option>
            <option value="disabled">Disabled</option>
            <option value="rejected">Rejected</option>
        </x-form.select>
    </div>

    {{-- ── Sort + count ────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between gap-3">
        <p class="text-xs text-slate-500">
            <span class="font-semibold text-slate-700">{{ $users->total() }}</span> user{{ $users->total() !== 1 ? 's' : '' }} found
        </p>
        <x-form.select wire:model.live="sort" class="w-auto min-w-[12rem]">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="name_asc">Name A–Z</option>
            <option value="name_desc">Name Z–A</option>
            <option value="email_asc">Email A–Z</option>
            <option value="email_desc">Email Z–A</option>
            <option value="status_asc">Status A–Z</option>
            <option value="status_desc">Status Z–A</option>
        </x-form.select>
    </div>

    {{-- ── Table ───────────────────────────────────────────────────────────── --}}
    <x-table.container>
        <x-table.table>
            <x-table.head :sticky="true">
                <x-table.row>
                    <x-table.th class="w-10">#</x-table.th>
                    <x-table.th>Name & Email</x-table.th>
                    <x-table.th class="hidden md:table-cell">Phone</x-table.th>
                    <x-table.th class="hidden lg:table-cell">Office</x-table.th>
                    <x-table.th>Status</x-table.th>
                    <x-table.th>Roles</x-table.th>
                    <x-table.th class="text-right">Actions</x-table.th>
                </x-table.row>
            </x-table.head>

            <x-table.body>
                @forelse ($users as $user)
                    <x-table.row striped hover>
                        <x-table.td class="text-slate-500 text-xs font-medium">
                            {{ ($users->firstItem() ?? 0) + $loop->index }}
                        </x-table.td>

                        <x-table.td>
                            <div class="font-semibold text-slate-800 text-sm">{{ $user->name }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $user->email }}</div>
                        </x-table.td>

                        <x-table.td class="hidden md:table-cell text-sm text-slate-600">
                            {{ $user->phone_number ?: '—' }}
                        </x-table.td>

                        <x-table.td class="hidden lg:table-cell text-sm text-slate-600">
                            {{ $user->office ?: '—' }}
                        </x-table.td>

                        <x-table.td>
                            <x-feedback-status.status-indicator :status="$user->account_status" />
                        </x-table.td>

                        <x-table.td>
                            <div class="flex flex-wrap gap-1">
                                @forelse ($user->roles as $role)
                                    <x-feedback-status.status-indicator
                                        :status="$role->name"
                                        :label="ucfirst($role->name)" />
                                @empty
                                    <span class="text-xs text-slate-400 italic">No role</span>
                                @endforelse
                            </div>
                        </x-table.td>

                        <x-table.td>
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">

                                @if ($user->account_status === 'pending')
                                    <x-button variant="table-confirm"
                                        onclick="document.getElementById('approveModal-{{ $user->id }}').showModal()">
                                        <i class="bx bx-check"></i> Approve
                                    </x-button>
                                    <x-button variant="table-danger"
                                        onclick="document.getElementById('rejectModal-{{ $user->id }}').showModal()">
                                        <i class="bx bx-x"></i> Reject
                                    </x-button>

                                @elseif ($user->account_status === 'disabled')
                                    <x-button variant="table-confirm"
                                        onclick="document.getElementById('approveModal-{{ $user->id }}').showModal()">
                                        <i class="bx bx-check"></i> Activate
                                    </x-button>

                                @elseif ($user->account_status === 'rejected')
                                    <x-button variant="table-restore"
                                        onclick="document.getElementById('restoreModal-{{ $user->id }}').showModal()">
                                        <i class="bx bx-revision"></i> Restore
                                    </x-button>

                                @elseif ($user->account_status === 'active')
                                    <x-button variant="table-disable"
                                        onclick="document.getElementById('disableModal-{{ $user->id }}').showModal()">
                                        <i class="bx bx-pause"></i> Disable
                                    </x-button>
                                @endif

                                @if ($user->account_status === 'active')
                                    <x-button variant="table-manage"
                                        onclick="document.getElementById('assignRoleModal-{{ $user->id }}').showModal()">
                                        <i class="bx bx-shield"></i> Roles
                                    </x-button>
                                @endif

                            </div>
                        </x-table.td>
                    </x-table.row>

                    {{-- Modals for this user --}}
                    @if ($user->account_status === 'pending')
                        @include('AccountApproval.modals.approvalModal', ['modalId' => 'approveModal-' . $user->id, 'user' => $user, 'action' => 'approve'])
                        @include('AccountApproval.modals.approvalModal', ['modalId' => 'rejectModal-'  . $user->id, 'user' => $user, 'action' => 'reject'])
                    @elseif ($user->account_status === 'disabled')
                        @include('AccountApproval.modals.approvalModal', ['modalId' => 'approveModal-' . $user->id, 'user' => $user, 'action' => 'approve'])
                    @elseif ($user->account_status === 'rejected')
                        @include('AccountApproval.modals.approvalModal', ['modalId' => 'restoreModal-' . $user->id, 'user' => $user, 'action' => 'restore'])
                    @elseif ($user->account_status === 'active')
                        @include('AccountApproval.modals.approvalModal', ['modalId' => 'disableModal-' . $user->id, 'user' => $user, 'action' => 'disable'])
                    @endif

                    @if ($user->account_status === 'active')
                        @include('AccountApproval.modals.assignRolesModal', ['modalId' => 'assignRoleModal-' . $user->id, 'user' => $user])
                    @endif

                @empty
                    <x-table.empty :colspan="7" message="No users match your filters." />
                @endforelse
            </x-table.body>
        </x-table.table>
    </x-table.container>

    {{-- ── Pagination ──────────────────────────────────────────────────────── --}}
    @if ($users->hasPages())
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <p class="text-xs text-slate-500">
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
            </p>
            {{ $users->links() }}
        </div>
    @endif

</div>
