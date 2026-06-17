{{-- Table container --}}
<div class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden"
     style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

    {{-- Column header --}}
    <div class="grid grid-cols-[2.5rem_1fr_auto] md:grid-cols-[2.5rem_2fr_1fr_1fr_auto] gap-x-3 items-center
                px-4 py-2.5 bg-[#f8fafc] border-b border-[#e2e8f0]
                text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8] select-none">
        <div class="flex items-center justify-center" @click.stop>
            <input type="checkbox" x-model="selectAll"
                class="w-4 h-4 rounded border-[#e2e8f0] text-[#16a34a] focus:ring-[#bbf7d0] cursor-pointer">
        </div>
        <div>User</div>
        <div class="hidden md:block">Status</div>
        <div class="hidden md:block">Roles</div>
        <div class="text-right pr-1">Actions</div>
    </div>

    {{-- Rows --}}
    <div class="divide-y divide-[#f1f5f9]">

    @forelse($users as $user)
    @php
        $avatarCls = match($user->account_status) {
            'active'   => 'bg-[#dcfce7] text-[#166534]',
            'pending'  => 'bg-[#fef3c7] text-[#92400e]',
            'rejected' => 'bg-[#ffe4e6] text-[#9f1239]',
            'disabled' => 'bg-[#f1f5f9] text-[#475569]',
            default    => 'bg-[#f1f5f9] text-[#475569]',
        };
        $borderCls = match($user->account_status) {
            'active'   => 'border-l-emerald-400',
            'pending'  => 'border-l-amber-400',
            'rejected' => 'border-l-rose-400',
            'disabled' => 'border-l-slate-300',
            default    => 'border-l-slate-300',
        };
        $uid = (string) $user->id;
    @endphp

        <div x-data="{ open: false }"
            @click="open = !open"
            class="cursor-pointer transition-colors select-none"
            :class="open ? '{{ $avatarCls }}' : 'bg-white hover:bg-[#fafafa]'">

            <div class="grid grid-cols-[2.5rem_1fr_auto] md:grid-cols-[2.5rem_2fr_1fr_1fr_auto] {{ $borderCls }} gap-x-3 items-center px-4 py-3">

                {{-- Checkbox --}}
                <div class="flex items-center justify-center" @click.stop>
                    <input type="checkbox"
                        :checked="isSelected('{{ $uid }}')"
                        @change="toggleRow('{{ $uid }}')"
                        :disabled="!canSelect('{{ $uid }}') && !isSelected('{{ $uid }}')"
                        :title="!canSelect('{{ $uid }}') && !isSelected('{{ $uid }}') ? 'Only same-status users can be bulk-selected' : ''"
                        :class="(!canSelect('{{ $uid }}') && !isSelected('{{ $uid }}')) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'"
                        class="w-4 h-4 rounded border-[#e2e8f0] text-[#16a34a] focus:ring-[#bbf7d0]">
                </div>

                {{-- Avatar + name --}}
                <div class="flex items-center gap-3 min-w-0">
                    <span class="{{ $avatarCls }} shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full border font-bold text-[13px] {{ $avatarCls }}">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-[13px] font-semibold text-[#0f172a] truncate">{{ $user->name }}</p>
                        <p class="text-[12px] text-[#94a3b8] truncate">{{ $user->email }}</p>
                    </div>
                    <i class="bx text-[#94a3b8] text-base shrink-0 ml-2 hidden sm:block transition-transform duration-200"
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
                        <span class="text-[12px] text-[#94a3b8] italic">—</span>
                    @endforelse
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-1 flex-wrap" @click.stop>
                    @if($user->account_status === 'pending')
                        <x-button variant="table-confirm" onclick="document.getElementById('approveModal-{{ $user->id }}').showModal()">
                            <i class="bx bx-check leading-none"></i> Approve
                        </x-button>
                        <x-button variant="table-danger" onclick="document.getElementById('rejectModal-{{ $user->id }}').showModal()">
                            <i class="bx bx-x leading-none"></i> Reject
                        </x-button>
                    @elseif($user->account_status === 'disabled')
                        <x-button variant="table-confirm" onclick="document.getElementById('approveModal-{{ $user->id }}').showModal()">
                            <i class="bx bx-check leading-none"></i> Activate
                        </x-button>
                    @elseif($user->account_status === 'rejected')
                        <x-button variant="table-restore" onclick="document.getElementById('restoreModal-{{ $user->id }}').showModal()">
                            <i class="bx bx-revision leading-none"></i> Restore
                        </x-button>
                    @elseif($user->account_status === 'active')
                        <x-button variant="table-disable" onclick="document.getElementById('disableModal-{{ $user->id }}').showModal()">
                            <i class="bx bx-pause leading-none"></i> Disable
                        </x-button>
                    @endif

                    @if($user->account_status === 'active')
                        <x-button variant="table-manage" onclick="document.getElementById('assignRoleModal-{{ $user->id }}').showModal()">
                            <i class="bx bx-shield leading-none"></i>
                        </x-button>
                    @endif

                    <x-button variant="table-edit" onclick="document.getElementById('editUserModal-{{ $user->id }}').showModal()">
                        <i class="bx bx-edit leading-none"></i>
                    </x-button>
                </div>

            </div>

            {{-- Expanded detail --}}
            <div x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-end="opacity-0"
                class="px-4 py-3 border-t border-[#e8f5e9]"
                @click.stop>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 py-2">

                    <x-card-section title="Contact" icon="bx-buildings">
                        <div class="flex items-center gap-2">
                            <i class="bx bx-phone text-[#64748b] text-sm shrink-0"></i>
                            <span class="text-[13px] text-[#0f172a]">{{ $user->phone_number ?: '—' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bx bx-envelope text-[#64748b] text-sm shrink-0"></i>
                            <span class="text-[13px] text-[#475569] break-all">{{ $user->email }}</span>
                        </div>
                    </x-card-section>

                    <x-card-section title="Office & Verification" icon="bx-buildings">
                        <div class="flex items-center gap-2">
                            <i class="bx bx-buildings text-[#64748b] text-sm shrink-0"></i>
                            <span class="text-[13px] text-[#0f172a]">{{ $user->office ?: '—' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($user->email_verified_at)
                                <i class="bx bx-check-circle text-[#16a34a] text-sm shrink-0"></i>
                                <span class="text-[13px] text-[#16a34a] font-medium">Email verified</span>
                            @else
                                <i class="bx bx-time text-[#f59e0b] text-sm shrink-0"></i>
                                <span class="text-[13px] text-[#92400e] font-medium">Not verified</span>
                            @endif
                        </div>
                    </x-card-section>

                    <x-card-section title="Account" icon="bx-buildings">
                        <div class="flex items-center gap-2">
                            <i class="bx bx-calendar text-[#64748b] text-sm shrink-0"></i>
                            <span class="text-[13px] text-[#475569]">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bx bx-id-card text-[#64748b] text-sm shrink-0"></i>
                            <span class="text-[13px] text-[#475569]">ID #{{ $user->id }}</span>
                        </div>
                    </x-card-section>

                </div>
            </div>

        </div>

    @empty
        <div class="py-12 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-[#f0fdf4] text-[#16a34a]">
                <i class="bx bx-user-x text-2xl leading-none"></i>
            </div>
            <p class="text-[14px] font-semibold text-[#0f172a]">No users found</p>
            <p class="text-[13px] text-[#94a3b8] mt-0.5">Try adjusting your filters.</p>
        </div>
    @endforelse

    </div>
</div>

{{-- Pagination --}}
@if($users->hasPages())
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <p class="text-[13px] text-[#475569]">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
        </p>
        {{ $users->links() }}
    </div>
@endif

{{-- Per-user modals --}}
@foreach($users as $user)
    @if($user->account_status === 'pending')
        @include('AccountApproval.modals.approvalModal', ['modalId' => 'approveModal-' . $user->id, 'user' => $user, 'action' => 'approve'])
        @include('AccountApproval.modals.approvalModal', ['modalId' => 'rejectModal-'  . $user->id, 'user' => $user, 'action' => 'reject'])
    @elseif($user->account_status === 'disabled')
        @include('AccountApproval.modals.approvalModal', ['modalId' => 'approveModal-' . $user->id, 'user' => $user, 'action' => 'approve'])
    @elseif($user->account_status === 'rejected')
        @include('AccountApproval.modals.approvalModal', ['modalId' => 'restoreModal-' . $user->id, 'user' => $user, 'action' => 'restore'])
    @elseif($user->account_status === 'active')
        @include('AccountApproval.modals.approvalModal', ['modalId' => 'disableModal-' . $user->id, 'user' => $user, 'action' => 'disable'])
    @endif

    @if($user->account_status === 'active')
        @include('AccountApproval.modals.assignRolesModal', ['modalId' => 'assignRoleModal-' . $user->id, 'user' => $user])
    @endif

    @include('AccountApproval.modals.editUserModal', ['modalId' => 'editUserModal-' . $user->id, 'user' => $user])
@endforeach
