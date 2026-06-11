<div
    x-data="{
        selected: [],
        get selectAll() { return this.pageIds.length > 0 && this.pageIds.every(id => this.selected.includes(id)); },
        set selectAll(v) { this.selected = v ? [...this.pageIds] : []; },
        pageIds: @js($users->pluck('id')->map(fn($id) => (string)$id)->toArray()),
        toggleRow(id) { const i = this.selected.indexOf(id); i === -1 ? this.selected.push(id) : this.selected.splice(i, 1); },
        isSelected(id) { return this.selected.includes(id); },
        bulkModal: false,
        bulkAction: '',
        openBulk(action) { if (!this.selected.length) return; this.bulkAction = action; this.bulkModal = true; },
        syncAndExecute() {
            const ids = [...this.selected];
            const action = this.bulkAction;
            $wire.executeBulk(ids, action).then(() => {
                this.selected = [];
                this.bulkModal = false;
            });
        }
    }"
    class="space-y-4">

    {{-- ── Single-line filter bar ───────────────────────────────────────────── --}}
    <x-card-section icon="bx-filter-alt" title="Filters">
        <x-slot:actions>
            <button wire:click="$set('search',''); $set('role','all'); $set('status','all'); $set('sort','newest')"
                class="text-[11px] text-[#94a3b8] hover:text-rose-500 transition flex items-center gap-1 whitespace-nowrap">
                <i class="bx bx-reset text-xs leading-none"></i> Clear
            </button>
        </x-slot:actions>

        <div class="flex flex-wrap gap-2 items-center">

            {{-- Search --}}
            <div class="relative flex-1 min-w-45">
                <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-[#94a3b8] text-base pointer-events-none"></i>
                <x-form.input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search name, email, phone…" class="pl-9 py-2! text-[13px]" />
            </div>

            {{-- Role --}}
            <x-form.select wire:model.live="role" class="py-2! text-[13px] min-w-30">
                <option value="all">All Roles</option>
                <option value="admin">Admin</option>
                <option value="dean">Dean</option>
                <option value="chair">Chair</option>
                <option value="faculty">Faculty</option>
            </x-form.select>

            {{-- Status --}}
            <x-form.select wire:model.live="status" class="py-2! text-[13px] min-w-32.5">
                <option value="all">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="disabled">Disabled</option>
                <option value="rejected">Rejected</option>
            </x-form.select>

            {{-- Sort --}}
            <x-form.select wire:model.live="sort" class="py-2! text-[13px] min-w-32.5">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="name_asc">Name A–Z</option>
                <option value="name_desc">Name Z–A</option>
                <option value="status_asc">Status A–Z</option>
                <option value="status_desc">Status Z–A</option>
            </x-form.select>

        </div>
    </x-card-section>

    {{-- ── Count + bulk bar ────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between gap-3 flex-wrap min-h-8">
        <p class="text-[13px] text-[#475569]">
            <span class="font-semibold text-[#0f172a]">{{ $users->total() }}</span>
            user{{ $users->total() !== 1 ? 's' : '' }} found
            <span x-show="selected.length" class="text-[#16a34a] font-semibold">
                &mdash; <span x-text="selected.length"></span> selected
            </span>
        </p>

        <div x-show="selected.length" class="flex items-center gap-2 flex-wrap">
            <span class="text-[12px] text-[#475569] font-medium">Bulk:</span>
            <x-button variant="table-confirm" @click="openBulk('approve')">
                <i class="bx bx-check leading-none"></i> Approve
            </x-button>
            <x-button variant="table-restore" @click="openBulk('restore')">
                <i class="bx bx-revision leading-none"></i> Restore
            </x-button>
            <x-button variant="table-disable" @click="openBulk('disable')">
                <i class="bx bx-pause leading-none"></i> Disable
            </x-button>
            <x-button variant="table-danger" @click="openBulk('reject')">
                <i class="bx bx-x leading-none"></i> Reject
            </x-button>
        </div>
    </div>

    {{-- ── Bulk confirmation modal (Alpine-only, no Livewire round-trip to open) ── --}}
    <div x-show="bulkModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
         @click.self="bulkModal = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="bg-white rounded-xl w-full max-w-md mx-4 overflow-hidden"
             :class="{
                'border-t-4 border-emerald-500': bulkAction === 'approve',
                'border-t-4 border-rose-500':    bulkAction === 'reject',
                'border-t-4 border-amber-500':   bulkAction === 'disable',
                'border-t-4 border-blue-500':    bulkAction === 'restore',
             }"
             style="box-shadow: 0 8px 40px rgba(0,0,0,0.18);"
             @click.stop>

            <div class="flex items-center justify-between px-6 py-4 border-b border-[#e2e8f0]">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0 text-white"
                        :class="{
                            'bg-emerald-600': bulkAction === 'approve',
                            'bg-rose-600':    bulkAction === 'reject',
                            'bg-amber-500':   bulkAction === 'disable',
                            'bg-blue-600':    bulkAction === 'restore',
                        }">
                        <i class="text-base leading-none"
                           :class="{
                               'bx bx-check-shield': bulkAction === 'approve',
                               'bx bx-block':        bulkAction === 'reject',
                               'bx bx-pause-circle': bulkAction === 'disable',
                               'bx bx-revision':     bulkAction === 'restore',
                           }"></i>
                    </span>
                    <p class="text-[15px] font-bold text-[#0f172a]">
                        Bulk <span x-text="bulkAction.charAt(0).toUpperCase() + bulkAction.slice(1)"></span>
                    </p>
                </div>
                <button @click="bulkModal = false"
                    class="rounded-lg p-1.5 text-[#94a3b8] hover:bg-[#f8fafc] hover:text-[#475569] transition">
                    <i class="bx bx-x text-xl leading-none"></i>
                </button>
            </div>

            <div class="px-6 py-5 space-y-3">
                <p class="text-[14px] text-[#475569]">
                    You are about to
                    <strong class="text-[#0f172a]" x-text="bulkAction"></strong>
                    <strong class="text-[#0f172a]"><span x-text="selected.length"></span> user<span x-show="selected.length !== 1">s</span></strong>.
                </p>
                <div x-show="bulkAction === 'approve'" class="rounded-xl border border-[#bbf7d0] bg-[#f0fdf4] p-3 text-[13px] text-[#166534] flex items-center gap-2">
                    <i class="bx bx-check-circle text-base shrink-0"></i> Selected users will be activated and notified via email.
                </div>
                <div x-show="bulkAction === 'reject'" class="rounded-xl border border-[#fda4af] bg-[#fff1f2] p-3 text-[13px] text-[#9f1239] flex items-center gap-2">
                    <i class="bx bx-error-circle text-base shrink-0"></i> Users will be rejected and all assignments removed.
                </div>
                <div x-show="bulkAction === 'disable'" class="rounded-xl border border-[#fcd34d] bg-[#fffbeb] p-3 text-[13px] text-[#92400e] flex items-center gap-2">
                    <i class="bx bx-error text-base shrink-0"></i> Users will be disabled and all assignments removed.
                </div>
                <div x-show="bulkAction === 'restore'" class="rounded-xl border border-[#bfdbfe] bg-[#eff6ff] p-3 text-[13px] text-[#1e40af] flex items-center gap-2">
                    <i class="bx bx-info-circle text-base shrink-0"></i> Accounts will be restored to pending status.
                </div>
            </div>

            <div class="flex justify-end gap-3 px-6 py-4 bg-[#f8fafc] border-t border-[#e2e8f0]">
                <x-button variant="cancel" @click="bulkModal = false">Cancel</x-button>
                <button @click="syncAndExecute()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg shadow-sm
                           transition-all duration-150 active:scale-95 text-white disabled:opacity-50"
                    :class="{
                        'bg-emerald-600 hover:bg-emerald-700': bulkAction === 'approve',
                        'bg-rose-600 hover:bg-rose-700':       bulkAction === 'reject',
                        'bg-amber-500 hover:bg-amber-600':     bulkAction === 'disable',
                        'bg-blue-600 hover:bg-blue-700':       bulkAction === 'restore',
                    }">
                    <i class="text-base leading-none"
                       :class="{
                           'bx bx-check':    bulkAction === 'approve',
                           'bx bx-x':        bulkAction === 'reject',
                           'bx bx-pause':    bulkAction === 'disable',
                           'bx bx-revision': bulkAction === 'restore',
                       }"></i>
                    Confirm <span x-text="bulkAction.charAt(0).toUpperCase() + bulkAction.slice(1)"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Accordion rows ───────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-[#e2e8f0] bg-white overflow-hidden"
         style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

        {{-- Header --}}
        <div class="grid grid-cols-[2.5rem_1fr_auto] md:grid-cols-[2.5rem_2fr_1fr_1fr_auto] gap-x-3 items-center
                    px-4 py-2.5 bg-[#f8fafc] border-b border-[#e2e8f0]
                    text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8] select-none">
            <div class="flex items-center justify-center" @click.stop>
                <input type="checkbox"
                    x-model="selectAll"
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
            $uid = (string) $user->id;
        @endphp

            <div x-data="{ open: false }"
                 @click="open = !open"
                 class="cursor-pointer transition-colors select-none"
                 :class="open ? 'bg-[#f0fdf4]' : 'bg-white hover:bg-[#fafafa]'">

                {{-- Summary row --}}
                <div class="grid grid-cols-[2.5rem_1fr_auto] md:grid-cols-[2.5rem_2fr_1fr_1fr_auto] gap-x-3 items-center px-4 py-3">

                    {{-- Checkbox — stop propagation so clicking it doesn't toggle row --}}
                    <div class="flex items-center justify-center" @click.stop>
                        <input type="checkbox"
                            :checked="isSelected('{{ $uid }}')"
                            @change="toggleRow('{{ $uid }}')"
                            class="w-4 h-4 rounded border-[#e2e8f0] text-[#16a34a] focus:ring-[#bbf7d0] cursor-pointer">
                    </div>

                    {{-- Avatar + name --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full font-bold text-[13px] {{ $avatarCls }}">
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

                    {{-- Action buttons — stop propagation --}}
                    <div class="flex items-center justify-end gap-1 flex-wrap" @click.stop>

                        @if($user->account_status === 'pending')
                            <x-button variant="table-confirm"
                                onclick="document.getElementById('approveModal-{{ $user->id }}').showModal()">
                                <i class="bx bx-check leading-none"></i> Approve
                            </x-button>
                            <x-button variant="table-danger"
                                onclick="document.getElementById('rejectModal-{{ $user->id }}').showModal()">
                                <i class="bx bx-x leading-none"></i> Reject
                            </x-button>
                        @elseif($user->account_status === 'disabled')
                            <x-button variant="table-confirm"
                                onclick="document.getElementById('approveModal-{{ $user->id }}').showModal()">
                                <i class="bx bx-check leading-none"></i> Activate
                            </x-button>
                        @elseif($user->account_status === 'rejected')
                            <x-button variant="table-restore"
                                onclick="document.getElementById('restoreModal-{{ $user->id }}').showModal()">
                                <i class="bx bx-revision leading-none"></i> Restore
                            </x-button>
                        @elseif($user->account_status === 'active')
                            <x-button variant="table-disable"
                                onclick="document.getElementById('disableModal-{{ $user->id }}').showModal()">
                                <i class="bx bx-pause leading-none"></i> Disable
                            </x-button>
                        @endif

                        @if($user->account_status === 'active')
                            <x-button variant="table-manage"
                                onclick="document.getElementById('assignRoleModal-{{ $user->id }}').showModal()">
                                <i class="bx bx-shield leading-none"></i>
                            </x-button>
                        @endif

                        <x-button variant="table-edit"
                            onclick="document.getElementById('editUserModal-{{ $user->id }}').showModal()">
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
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="px-4 pb-4 pt-1 border-t border-[#e8f5e9]"
                     @click.stop>

                    <div class="ml-12 grid grid-cols-1 sm:grid-cols-3 gap-2.5">

                        <div class="rounded-lg bg-white border border-[#e2e8f0] px-3 py-2.5 space-y-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Contact</p>
                            <div class="flex items-center gap-2">
                                <i class="bx bx-phone text-[#64748b] text-sm shrink-0"></i>
                                <span class="text-[13px] text-[#0f172a]">{{ $user->phone_number ?: '—' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="bx bx-envelope text-[#64748b] text-sm shrink-0"></i>
                                <span class="text-[13px] text-[#475569] break-all">{{ $user->email }}</span>
                            </div>
                        </div>

                        <div class="rounded-lg bg-white border border-[#e2e8f0] px-3 py-2.5 space-y-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Office & Verification</p>
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
                        </div>

                        <div class="rounded-lg bg-white border border-[#e2e8f0] px-3 py-2.5 space-y-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Account</p>
                            <div class="flex items-center gap-2">
                                <i class="bx bx-calendar text-[#64748b] text-sm shrink-0"></i>
                                <span class="text-[13px] text-[#475569]">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="bx bx-id-card text-[#64748b] text-sm shrink-0"></i>
                                <span class="text-[13px] text-[#475569]">ID #{{ $user->id }}</span>
                            </div>
                        </div>

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

    {{-- ── Pagination ───────────────────────────────────────────────────────── --}}
    @if($users->hasPages())
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <p class="text-[13px] text-[#475569]">
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
            </p>
            {{ $users->links() }}
        </div>
    @endif

    {{-- ── Modals ───────────────────────────────────────────────────────────── --}}
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

</div>
