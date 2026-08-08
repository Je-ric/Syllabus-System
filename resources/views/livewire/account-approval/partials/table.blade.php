{{-- Table container --}}
<div class="rounded-sm border border-[#ececee] bg-white overflow-hidden"
     style="box-shadow: rgba(0, 0, 0, 0.04) 0px 4px 12px 0px;">

    {{-- Column header --}}
    <div class="grid grid-cols-[2.5rem_1fr_auto] md:grid-cols-[2.5rem_2fr_1fr_1fr_auto] gap-x-3 items-center
                px-5 py-3 bg-[#fafafa] border-b border-[#ececee]
                text-[11px] font-bold uppercase tracking-[0.14em] text-[#a1a1aa] select-none">
        <div class="flex items-center justify-center" @click.stop>
            <input type="checkbox" x-model="selectAll"
                class="w-4 h-4 rounded-[6px] border-[#ececee]"
                style="accent-color: var(--clsu-green);">
        </div>
        <div>User</div>
        <div class="hidden md:block">Status</div>
        <div class="hidden md:block">Roles</div>
        <div class="text-right pr-1">Actions</div>
    </div>

    {{-- Rows --}}
    <div class="divide-y divide-[#f4f4f5] empty:py-12">

    @forelse($users as $user)
    @php
        $avatarCls = match($user->account_status) {
            'active'   => 'bg-[color-mix(in_srgb,var(--clsu-green)_14%,white)] text-[color:var(--clsu-cobra,var(--clsu-green))]',
            'pending'  => 'bg-[#fef3c7] text-[#92400e]',
            'rejected' => 'bg-[#ffe4e6] text-[#9f1239]',
            'disabled' => 'bg-[#f4f4f5] text-[#71717a]',
            default    => 'bg-[#f4f4f5] text-[#71717a]',
        };
        $uid = (string) $user->id;
    @endphp

        <div x-data="{ open: false }"
            @click="open = !open"
            class="cursor-pointer transition-colors select-none"
            :class="open ? 'bg-[color-mix(in_srgb,var(--clsu-green)_6%,white)]' : 'bg-white hover:bg-[#fafafa]'">

            <div class="grid grid-cols-[2.5rem_1fr_auto] md:grid-cols-[2.5rem_2fr_1fr_1fr_auto] gap-x-3 items-center px-5 py-3.5">

                {{-- Checkbox --}}
                <div class="flex items-center justify-center" @click.stop>
                    <input type="checkbox"
                        :checked="isSelected('{{ $uid }}')"
                        @change="toggleRow('{{ $uid }}')"
                        :disabled="!canSelect('{{ $uid }}') && !isSelected('{{ $uid }}')"
                        :title="!canSelect('{{ $uid }}') && !isSelected('{{ $uid }}') ? 'Only same-status users can be bulk-selected' : ''"
                        :class="(!canSelect('{{ $uid }}') && !isSelected('{{ $uid }}')) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'"
                        class="w-4 h-4 rounded-[6px] border-[#ececee]"
                        style="accent-color: var(--clsu-green);">
                </div>

                {{-- Avatar + name --}}
                <div class="flex items-center gap-3 min-w-0">
                    <span class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full border border-[#ececee] font-bold text-[13px] {{ $avatarCls }}">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-[13px] font-semibold text-[#09090b] truncate">{{ $user->name }}</p>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <p class="text-[12px] text-[#a1a1aa] truncate">{{ $user->email }}</p>
                            @if($user->email_verified_at)
                                <x-feedback-status.status-indicator variant="emerald" class="text-[10px] px-1.5 py-0.5">
                                    <i class="bx bx-check-circle text-[10px]"></i> Verified
                                </x-feedback-status.status-indicator>
                            @else
                                <x-feedback-status.status-indicator variant="amber" class="text-[10px] px-1.5 py-0.5">
                                    <i class="bx bx-time text-[10px]"></i> Unverified
                                </x-feedback-status.status-indicator>
                            @endif
                        </div>
                    </div>
                    <i class="bx text-[#a1a1aa] text-base shrink-0 ml-2 hidden sm:block transition-transform duration-200"
                       :class="open ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                </div>

                {{-- Status --}}
                <div class="hidden md:flex items-center">
                    <x-feedback-status.status-indicator :status="$user->account_status" />
                </div>

                {{-- Roles --}}
                <div class="hidden md:flex flex-wrap gap-1">
                    @forelse($user->roles as $role)
                        <x-feedback-status.status-indicator :status="$role->name" />
                    @empty
                        <span class="text-[12px] text-[#a1a1aa] italic">—</span>
                    @endforelse
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-1 flex-wrap" @click.stop>
                    @if($user->account_status === 'pending')
                        <x-ui.button variant="table-confirm" onclick="document.getElementById('approveModal-{{ $user->id }}').showModal()">
                            <i class="bx bx-check leading-none"></i> Approve
                        </x-ui.button>
                        <x-ui.button variant="table-danger" onclick="document.getElementById('rejectModal-{{ $user->id }}').showModal()">
                            <i class="bx bx-x leading-none"></i> Reject
                        </x-ui.button>
                    @elseif($user->account_status === 'disabled')
                        <x-ui.button variant="table-confirm" onclick="document.getElementById('approveModal-{{ $user->id }}').showModal()">
                            <i class="bx bx-check leading-none"></i> Activate
                        </x-ui.button>
                    @elseif($user->account_status === 'rejected')
                        <x-ui.button variant="table-restore" onclick="document.getElementById('restoreModal-{{ $user->id }}').showModal()">
                            <i class="bx bx-revision leading-none"></i> Restore
                        </x-ui.button>
                    @elseif($user->account_status === 'active')
                        <x-ui.button variant="table-disable" onclick="document.getElementById('disableModal-{{ $user->id }}').showModal()">
                            <i class="bx bx-pause leading-none"></i> Disable
                        </x-ui.button>
                    @endif

                    @if($user->account_status === 'active')
                        <x-ui.button variant="table-manage" onclick="document.getElementById('assignRoleModal-{{ $user->id }}').showModal()">
                            <i class="bx bx-shield leading-none"></i>
                        </x-ui.button>
                    @endif

                    <x-ui.button variant="table-edit" onclick="document.getElementById('editUserModal-{{ $user->id }}').showModal()">
                        <i class="bx bx-edit leading-none"></i>
                    </x-ui.button>
                </div>

            </div>

            {{-- Expanded detail --}}
            <div x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-end="opacity-0"
                class="px-5 pb-5 pt-2 border-t border-[#ececee] bg-[#fafafa]"
                @click.stop>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">

                    <x-layout.card-section title="Contact" icon="bx-phone">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <i class="bx bx-phone text-[#a1a1aa] text-sm shrink-0"></i>
                                <span class="text-[13px] text-[#09090b]">{{ $user->phone_number ?: '—' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="bx bx-envelope text-[#a1a1aa] text-sm shrink-0"></i>
                                <span class="text-[13px] text-[#52525b] break-all">{{ $user->email }}</span>
                            </div>
                        </div>
                    </x-layout.card-section>

                    <x-layout.card-section title="Office" icon="bx-buildings">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <i class="bx bx-buildings text-[#a1a1aa] text-sm shrink-0"></i>
                                <span class="text-[13px] text-[#09090b]">{{ $user->office ?: '—' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($user->email_verified_at)
                                    <i class="bx bx-check-circle text-sm shrink-0" style="color: var(--clsu-green);"></i>
                                    <span class="text-[13px] font-medium" style="color: var(--clsu-cobra, var(--clsu-green));">Email verified</span>
                                @else
                                    <i class="bx bx-time text-[#f59e0b] text-sm shrink-0"></i>
                                    <span class="text-[13px] text-[#92400e] font-medium">Not verified</span>
                                @endif
                            </div>
                        </div>
                    </x-layout.card-section>

                    <x-layout.card-section title="Account" icon="bx-id-card">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <i class="bx bx-calendar text-[#a1a1aa] text-sm shrink-0"></i>
                                <span class="text-[13px] text-[#52525b]">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="bx bx-id-card text-[#a1a1aa] text-sm shrink-0"></i>
                                <span class="text-[13px] text-[#52525b]">ID #{{ $user->id }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="bx bx-sync text-[#a1a1aa] text-sm shrink-0"></i>
                                <span class="text-[13px] text-[#52525b]">
                                    @if($user->synced_at)
                                        Synced {{ $user->synced_at->format('M d, Y h:i A') }}
                                    @else
                                        <span class="italic text-[#a1a1aa]">Not synced</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </x-layout.card-section>

                </div>
            </div>

        </div>

    @empty
        <div class="px-5 py-10">
            <x-feedback-status.empty-state icon="bx-user-x" title="No users found" message="Try adjusting your filters." />
        </div>
    @endforelse

    </div>
</div>

{{-- Pagination --}}
@if($users->hasPages())
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <p class="text-[13px] text-[#71717a]">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
        </p>
        {{ $users->links() }}
    </div>
@endif

{{-- Per-user modals --}}
@foreach($users as $user)
    @if($user->account_status === 'pending')
        @include('Authentication.AccountApproval.modals.approvalModal', ['modalId' => 'approveModal-' . $user->id, 'user' => $user, 'action' => 'approve'])
        @include('Authentication.AccountApproval.modals.approvalModal', ['modalId' => 'rejectModal-'  . $user->id, 'user' => $user, 'action' => 'reject'])
    @elseif($user->account_status === 'disabled')
        @include('Authentication.AccountApproval.modals.approvalModal', ['modalId' => 'approveModal-' . $user->id, 'user' => $user, 'action' => 'approve'])
    @elseif($user->account_status === 'rejected')
        @include('Authentication.AccountApproval.modals.approvalModal', ['modalId' => 'restoreModal-' . $user->id, 'user' => $user, 'action' => 'restore'])
    @elseif($user->account_status === 'active')
        @include('Authentication.AccountApproval.modals.approvalModal', ['modalId' => 'disableModal-' . $user->id, 'user' => $user, 'action' => 'disable'])
    @endif

    @if($user->account_status === 'active')
        @include('Authentication.AccountApproval.modals.assignRolesModal', ['modalId' => 'assignRoleModal-' . $user->id, 'user' => $user])
    @endif

    @include('Authentication.AccountApproval.modals.editUserModal', ['modalId' => 'editUserModal-' . $user->id, 'user' => $user])
@endforeach
