{{-- Action Icon Legend --}}
<!-- <div class="flex flex-wrap items-center gap-x-3 gap-y-2 px-3 py-2.5 rounded-[10px] bg-[#f8fafc] border border-[#e8ecf0] mb-3">
    <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#94a3b8] mr-1">Legend</span>
    <div class="w-px h-3.5 bg-[#e2e8f0] mx-0.5"></div>
    <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5">

        <div class="flex items-center gap-1.5">
            <span class="inline-flex items-center justify-center rounded-full bg-[#D5FFF0] border border-[#6EE7B7]" style="width:18px;height:18px;">
                <i class="bx bx-check text-[9px] text-[#06754E]"></i>
            </span>
            <span class="text-[11.5px] font-semibold text-[#06754E]">Approve</span>
            <span class="text-[10.5px] text-[#b0b8c8]">/ Activate</span>
        </div>
        <div class="w-px h-3 bg-[#e2e8f0]"></div>

        <div class="flex items-center gap-1.5">
            <span class="inline-flex items-center justify-center rounded-full bg-[#FFE3E2] border border-[#FCA5A5]" style="width:18px;height:18px;">
                <i class="bx bx-x text-[9px] text-[#9D1F1A]"></i>
            </span>
            <span class="text-[11.5px] font-semibold text-[#9D1F1A]">Reject</span>
        </div>
        <div class="w-px h-3 bg-[#e2e8f0]"></div>

        <div class="flex items-center gap-1.5">
            <span class="inline-flex items-center justify-center rounded-full bg-[#F1F3F5] border border-[#D1D5DB]" style="width:18px;height:18px;">
                <i class="bx bx-pause text-[9px] text-[#72809E]"></i>
            </span>
            <span class="text-[11.5px] font-semibold text-[#72809E]">Disable</span>
        </div>
        <div class="w-px h-3 bg-[#e2e8f0]"></div>

        <div class="flex items-center gap-1.5">
            <span class="inline-flex items-center justify-center rounded-full bg-[#DBEAFE] border border-[#93C5FD]" style="width:18px;height:18px;">
                <i class="bx bx-revision text-[9px] text-[#1D4ED8]"></i>
            </span>
            <span class="text-[11.5px] font-semibold text-[#1D4ED8]">Restore</span>
        </div>
        <div class="w-px h-3 bg-[#e2e8f0]"></div>

        <div class="flex items-center gap-1.5">
            <span class="inline-flex items-center justify-center rounded-full bg-[#EDE9FE] border border-[#C4B5FD]" style="width:18px;height:18px;">
                <i class="bx bx-shield text-[9px] text-[#6D28D9]"></i>
            </span>
            <span class="text-[11.5px] font-semibold text-[#6D28D9]">Manage roles</span>
        </div>
        <div class="w-px h-3 bg-[#e2e8f0]"></div>

        <div class="flex items-center gap-1.5">
            <span class="inline-flex items-center justify-center rounded-full bg-[#F1F3F5] border border-[#D1D5DB]" style="width:18px;height:18px;">
                <i class="bx bx-edit text-[9px] text-[#475569]"></i>
            </span>
            <span class="text-[11.5px] font-semibold text-[#475569]">Edit</span>
        </div>
    </div>
    <div class="ml-auto flex items-center gap-1 text-[10.5px] text-[#b0b8c8]">
        <i class="bx bx-mouse text-xs"></i>
        <span>Hover icons for tooltips</span>
    </div>
</div> -->

<x-table.container>
    <x-table.table class="min-w-[700px]">
        <x-table.head :sticky="true">
            <x-table.row>
                <x-table.th class="w-12">
                    <input type="checkbox" x-model="selectAll"
                        class="w-4 h-4 rounded-[6px] border-[#ececee]"
                        style="accent-color: var(--clsu-green);">
                </x-table.th>
                <x-table.th>User</x-table.th>
                <x-table.th>Status</x-table.th>
                <x-table.th>Roles</x-table.th>
                <x-table.th>Phone</x-table.th>
                <x-table.th>Office</x-table.th>
                <x-table.th class="w-32 text-right">Actions</x-table.th>
            </x-table.row>
        </x-table.head>

        <x-table.body>
            @forelse($users as $user)
                @php
                    $avatarCls = match($user->account_status) {
                        'active'   => 'bg-[#D5FFF0] text-[#06754E]',
                        'pending'  => 'bg-[#FFF6E2] text-[#875200]',
                        'rejected' => 'bg-[#FFE3E2] text-[#9D1F1A]',
                        'disabled' => 'bg-[#F1F3F5] text-[#72809E]',
                        default    => 'bg-[#F1F3F5] text-[#72809E]',
                    };
                    $uid = (string) $user->id;
                    $counter = ($users->currentPage() - 1) * $users->perPage() + $loop->index + 1;
                    
                    // Get assignments
                    $collegeAssignments = $user->assignments->where('context', 'dean')->whereNotNull('college_id');
                    $deptAssignments = $user->assignments->where('context', 'chair')->whereNotNull('department_id');
                    $facultyAssignments = $user->assignments->where('context', 'faculty')->whereNotNull('department_id');
                @endphp

                {{-- Main row --}}
                <x-table.row striped hover @click="toggleExpand('{{ $uid }}')" style="cursor: pointer;">
                    <x-table.td @click.stop>
                        <input type="checkbox"
                            :checked="isSelected('{{ $uid }}')"
                            @change="toggleRow('{{ $uid }}')"
                            :disabled="!canSelect('{{ $uid }}') && !isSelected('{{ $uid }}')"
                            :title="!canSelect('{{ $uid }}') && !isSelected('{{ $uid }}') ? 'Only same-status users can be bulk-selected' : ''"
                            :class="(!canSelect('{{ $uid }}') && !isSelected('{{ $uid }}')) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'"
                            class="w-4 h-4 rounded-[6px] border-[#ececee]"
                            style="accent-color: var(--clsu-green);">
                    </x-table.td>

                    <x-table.td>
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full border border-[#ececee] font-bold text-[12px] {{ $avatarCls }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-[12px] font-medium text-[#71717a]">{{ $counter }}.</span>
                                    <p class="text-[13px] font-semibold text-[#09090b] truncate">{{ $user->name }}</p>
                                </div>
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
                            <i class="bx text-[#a1a1aa] text-sm shrink-0 transition-transform duration-200"
                               :class="expandedRows['{{ $uid }}'] ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                        </div>
                    </x-table.td>

                    <x-table.td>
                        <x-feedback-status.status-indicator :status="$user->account_status" />
                    </x-table.td>

                    <x-table.td>
                        <div class="flex flex-wrap gap-1">
                            @forelse($user->roles as $role)
                                <x-feedback-status.status-indicator :status="$role->name" />
                            @empty
                                <span class="text-[12px] text-[#a1a1aa] italic">—</span>
                            @endforelse
                        </div>
                    </x-table.td>

                    <x-table.td class="text-[13px] text-[#475569]">
                        {{ $user->phone_number ?: '—' }}
                    </x-table.td>

                    <x-table.td class="text-[13px] text-[#475569]">
                        {{ $user->office ?: '—' }}
                    </x-table.td>

                    <x-table.td class="text-right" @click.stop>
                        <div class="flex items-center justify-end gap-1">
                            @if($user->account_status === 'pending')
                                <x-ui.button variant="table-confirm" title="Approve account" onclick="document.getElementById('approveModal-{{ $user->id }}').showModal()">
                                    <i class="bx bx-check leading-none"></i>
                                </x-ui.button>
                                <x-ui.button variant="table-danger" title="Reject account" onclick="document.getElementById('rejectModal-{{ $user->id }}').showModal()">
                                    <i class="bx bx-x leading-none"></i>
                                </x-ui.button>
                            @elseif($user->account_status === 'disabled')
                                <x-ui.button variant="table-confirm" title="Activate account" onclick="document.getElementById('approveModal-{{ $user->id }}').showModal()">
                                    <i class="bx bx-check leading-none"></i>
                                </x-ui.button>
                            @elseif($user->account_status === 'rejected')
                                <x-ui.button variant="table-restore" title="Restore account" onclick="document.getElementById('restoreModal-{{ $user->id }}').showModal()">
                                    <i class="bx bx-revision leading-none"></i>
                                </x-ui.button>
                            @elseif($user->account_status === 'active')
                                <x-ui.button variant="table-disable" title="Disable account" onclick="document.getElementById('disableModal-{{ $user->id }}').showModal()">
                                    <i class="bx bx-pause leading-none"></i>
                                </x-ui.button>
                            @endif

                            @if($user->account_status === 'active')
                                <x-ui.button variant="table-manage" title="Manage roles" onclick="document.getElementById('assignRoleModal-{{ $user->id }}').showModal()">
                                    <i class="bx bx-shield leading-none"></i>
                                </x-ui.button>
                            @endif

                            <x-ui.button variant="table-edit" title="Edit user" onclick="document.getElementById('editUserModal-{{ $user->id }}').showModal()">
                                <i class="bx bx-edit leading-none"></i>
                            </x-ui.button>
                        </div>
                    </x-table.td>
                </x-table.row>

                {{-- Expanded detail row --}}
                <tr x-show="expandedRows['{{ $uid }}']"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-end="opacity-0"
                    class="bg-[#fafafa]">
                    <td colspan="7" class="px-4 py-4 border-t border-[#ececee]">
                        <div class="flex flex-wrap gap-0 divide-x divide-[#ececee]">

                            {{-- Contact Info --}}
                            <div class="flex-1 min-w-[160px] space-y-2 px-4 first:pl-1">
                                <h4 class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] flex items-center gap-1.5 pb-1.5 border-b border-[#ececee]">
                                    <i class="bx bx-phone text-sm"></i> Contact
                                </h4>
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <i class="bx bx-phone text-[#a1a1aa] text-xs shrink-0"></i>
                                        <span class="text-[13px] text-[#09090b]">{{ $user->phone_number ?: '—' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="bx bx-envelope text-[#a1a1aa] text-xs shrink-0"></i>
                                        <span class="text-[13px] text-[#52525b] break-all">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Office Info --}}
                            <div class="flex-1 min-w-[160px] space-y-2 px-4">
                                <h4 class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] flex items-center gap-1.5 pb-1.5 border-b border-[#ececee]">
                                    <i class="bx bx-buildings text-sm"></i> Office
                                </h4>
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <i class="bx bx-buildings text-[#a1a1aa] text-xs shrink-0"></i>
                                        <span class="text-[13px] text-[#09090b]">{{ $user->office ?: '—' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($user->email_verified_at)
                                            <i class="bx bx-check-circle text-xs shrink-0" style="color: var(--clsu-green);"></i>
                                            <span class="text-[13px] font-medium" style="color: var(--clsu-cobra, var(--clsu-green));">Email verified</span>
                                        @else
                                            <i class="bx bx-time text-[#f59e0b] text-xs shrink-0"></i>
                                            <span class="text-[13px] text-[#92400e] font-medium">Not verified</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Account Info --}}
                            <div class="flex-1 min-w-[160px] space-y-2 px-4">
                                <h4 class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] flex items-center gap-1.5 pb-1.5 border-b border-[#ececee]">
                                    <i class="bx bx-id-card text-sm"></i> Account
                                </h4>
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <i class="bx bx-calendar text-[#a1a1aa] text-xs shrink-0"></i>
                                        <span class="text-[13px] text-[#52525b]">{{ $user->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="bx bx-id-card text-[#a1a1aa] text-xs shrink-0"></i>
                                        <span class="text-[13px] text-[#52525b]">ID #{{ $user->id }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="bx bx-sync text-[#a1a1aa] text-xs shrink-0"></i>
                                        <span class="text-[13px] text-[#52525b]">
                                            @if($user->synced_at)
                                                Synced {{ $user->synced_at->format('M d, Y h:i A') }}
                                            @else
                                                <span class="italic text-[#a1a1aa]">Not synced</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Assignments --}}
                            <div class="flex-1 min-w-[160px] space-y-2 px-4 last:pr-1">
                                <h4 class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] flex items-center gap-1.5 pb-1.5 border-b border-[#ececee]">
                                    <i class="bx bx-briefcase text-sm"></i> Assignments
                                </h4>
                                <div class="space-y-1.5">
                                    {{-- Dean Assignments --}}
                                    @if($collegeAssignments->count() > 0)
                                        <div class="flex items-start gap-2">
                                            <i class="bx bx-crown text-[#a1a1aa] text-xs shrink-0 mt-0.5"></i>
                                            <div class="min-w-0">
                                                <span class="text-[12px] font-medium text-[#09090b]">Dean:</span>
                                                <div class="text-[12px] text-[#52525b]">
                                                    @foreach($collegeAssignments as $assignment)
                                                        {{ $assignment->college->name }}{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Chair Assignments --}}
                                    @if($deptAssignments->count() > 0)
                                        <div class="flex items-start gap-2">
                                            <i class="bx bx-user-voice text-[#a1a1aa] text-xs shrink-0 mt-0.5"></i>
                                            <div class="min-w-0">
                                                <span class="text-[12px] font-medium text-[#09090b]">Chair:</span>
                                                <div class="text-[12px] text-[#52525b]">
                                                    @foreach($deptAssignments as $assignment)
                                                        {{ $assignment->department->name }}{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Faculty Assignments --}}
                                    @if($facultyAssignments->count() > 0)
                                        <div class="flex items-start gap-2">
                                            <i class="bx bx-user text-[#a1a1aa] text-xs shrink-0 mt-0.5"></i>
                                            <div class="min-w-0">
                                                <span class="text-[12px] font-medium text-[#09090b]">Faculty:</span>
                                                <div class="text-[12px] text-[#52525b]">
                                                    @foreach($facultyAssignments as $assignment)
                                                        {{ $assignment->department->name }}{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- No Assignments --}}
                                    @if($collegeAssignments->count() === 0 && $deptAssignments->count() === 0 && $facultyAssignments->count() === 0)
                                        <div class="flex items-center gap-2">
                                            <i class="bx bx-x text-[#a1a1aa] text-xs shrink-0"></i>
                                            <span class="text-[12px] text-[#a1a1aa] italic">None</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </td>
                </tr>

            @empty
                <x-table.empty :colspan="7" message="No users found. Try adjusting your filters." class="py-10" />
            @endforelse
        </x-table.body>
    </x-table.table>
</x-table.container>

{{-- Pagination --}}
<x-pagination.custom :paginator="$users" />

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
