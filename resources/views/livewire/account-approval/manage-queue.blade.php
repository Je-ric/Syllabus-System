<div class="space-y-4">

    {{-- ── Filters ─────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="md:col-span-2 relative">
            <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-[#94a3b8] text-base pointer-events-none"></i>
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
        <p class="text-[13px] text-[#475569]">
            <span class="font-semibold text-[#0f172a]">{{ $users->total() }}</span>
            user{{ $users->total() !== 1 ? 's' : '' }} found
        </p>
        <x-form.select wire:model.live="sort" class="w-auto min-w-48">
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
                    <x-table.th class="w-8">#</x-table.th>
                    <x-table.th>User</x-table.th>
                    <x-table.th class="hidden lg:table-cell">Contact & Office</x-table.th>
                    <x-table.th>Status</x-table.th>
                    <x-table.th>Roles</x-table.th>
                    <x-table.th align="right">Actions</x-table.th>
                </x-table.row>
            </x-table.head>

            <x-table.body>
                @forelse ($users as $user)
                    <x-table.row striped hover>

                        {{-- # --}}
                        <x-table.td class="text-[#94a3b8] font-medium w-8">
                            {{ ($users->firstItem() ?? 0) + $loop->index }}
                        </x-table.td>

                        {{-- User: avatar + name + email + verified badge --}}
                        <x-table.td>
                            <div class="flex items-center gap-3 min-w-0">
                                {{-- Avatar --}}
                                <span class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full
                                             bg-[#dcfce7] text-[#166534] text-[13px] font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-semibold text-[#0f172a] truncate">{{ $user->name }}</p>
                                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                        <span class="text-[13px] text-[#475569] truncate">{{ $user->email }}</span>
                                        @if ($user->email_verified_at)
                                            <x-feedback-status.status-indicator variant="brand">
                                                <i class="bx bx-check text-[11px]"></i> Verified
                                            </x-feedback-status.status-indicator>
                                        @else
                                            <x-feedback-status.status-indicator variant="amber">
                                                <i class="bx bx-time text-[11px]"></i> Unverified
                                            </x-feedback-status.status-indicator>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </x-table.td>

                        {{-- Contact & Office (grouped) --}}
                        <x-table.td class="hidden lg:table-cell">
                            <div class="space-y-0.5">
                                <p class="text-[13px] text-[#0f172a]">
                                    {{ $user->phone_number ?: '—' }}
                                </p>
                                <p class="text-[13px] text-[#475569]">
                                    {{ $user->office ?: '—' }}
                                </p>
                            </div>
                        </x-table.td>

                        {{-- Status --}}
                        <x-table.td>
                            <x-feedback-status.status-indicator :status="$user->account_status" />
                        </x-table.td>

                        {{-- Roles --}}
                        <x-table.td>
                            <div class="flex flex-wrap gap-1">
                                @forelse ($user->roles as $role)
                                    <x-feedback-status.status-indicator
                                        :status="$role->name"
                                        :label="ucfirst($role->name)" />
                                @empty
                                    <span class="text-[13px] text-[#94a3b8] italic">No role</span>
                                @endforelse
                            </div>
                        </x-table.td>

                        {{-- Actions --}}
                        <x-table.td align="right">
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

                                <x-button variant="table-edit"
                                    onclick="document.getElementById('editUserModal-{{ $user->id }}').showModal()">
                                    <i class="bx bx-edit"></i>
                                </x-button>

                            </div>
                        </x-table.td>
                    </x-table.row>

                    {{-- Modals --}}
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

                    @include('AccountApproval.modals.editUserModal', ['modalId' => 'editUserModal-' . $user->id, 'user' => $user])

                @empty
                    <x-table.empty :colspan="6" message="No users match your filters." />
                @endforelse
            </x-table.body>
        </x-table.table>
    </x-table.container>

    {{-- ── Pagination ──────────────────────────────────────────────────────── --}}
    @if ($users->hasPages())
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <p class="text-[13px] text-[#475569]">
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
            </p>
            {{ $users->links() }}
        </div>
    @endif

</div>
