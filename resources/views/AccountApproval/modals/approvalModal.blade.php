@php
    /** @var string $action */
    /** @var \App\Models\User $user */
    /** @var string $modalId */
    $action = (string) ($action ?? '');
    $cfg = [
        'approve' => [
            'variant'     => 'approve',
            'label'       => 'Approve Account',
            'question'    => 'Grant this user access to the system?',
            'borderColor' => 'border-emerald-500',
            'btnVariant'  => 'add-button',
            'btnIcon'     => 'bx-check-shield',
            'alertType'   => 'success',
            'alertMsg'    => 'The user will be notified via email and immediately gain Faculty access.',
        ],
        'reject'  => [
            'variant'     => 'reject',
            'label'       => 'Reject Account',
            'question'    => 'Deny this users registration?',
            'borderColor' => 'border-rose-500',
            'btnVariant'  => 'danger',
            'btnIcon'     => 'bx-block',
            'alertType'   => 'error',
            'alertMsg'    => 'The user will be notified via email. All organizational assignments will be removed.',
        ],
        'restore' => [
            'variant'     => 'restore',
            'label'       => 'Restore Account',
            'question'    => 'Restore this account back to pending?',
            'borderColor' => 'border-blue-500',
            'btnVariant'  => 'save',
            'btnIcon'     => 'bx-revision',
            'alertType'   => 'info',
            'alertMsg'    => 'The account will be moved back to pending and await admin approval again.',
        ],
        'disable' => [
            'variant'     => 'disable',
            'label'       => 'Disable Account',
            'question'    => 'Suspend this users access?',
            'borderColor' => 'border-amber-500',
            'btnVariant'  => 'warning',
            'btnIcon'     => 'bx-pause-circle',
            'alertType'   => 'warning',
            'alertMsg'    => 'The user will be notified via email. All organizational assignments will be removed.',
        ],
    ];
    $hc = $cfg[$action] ?? [
        'variant' => null, 'label' => ucfirst($action), 'question' => 'Are you sure?',
        'borderColor' => 'border-slate-300', 'btnVariant' => 'primary', 'btnIcon' => 'bx-check',
        'alertType' => 'info', 'alertMsg' => '',
    ];

    $avatarColors = match($user->account_status) {
        'active'   => 'bg-[#dcfce7] text-[#166534]',
        'pending'  => 'bg-[#fef3c7] text-[#92400e]',
        'rejected' => 'bg-[#ffe4e6] text-[#9f1239]',
        'disabled' => 'bg-[#f1f5f9] text-[#475569]',
        default    => 'bg-[#f1f5f9] text-[#475569]',
    };
@endphp

<x-modal.dialog :id="$modalId" maxWidth="max-w-md" width="w-11/12" :variant="$hc['variant']">
    <form method="POST" action="{{ route('account-approval.' . $action) }}" class="flex flex-col" wire:ignore.self>
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <x-modal.header :modalId="$modalId" :variant="$hc['variant']">
            {{ $hc['label'] }}
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">

                <p class="text-[13px] text-[#475569]">{{ $hc['question'] }}</p>

                {{-- User profile card --}}
                <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] overflow-hidden">

                    {{-- Card header with avatar --}}
                    <div class="flex items-center gap-3 px-4 py-3 border-b border-[#e2e8f0]">
                        <span class="shrink-0 inline-flex items-center justify-center w-11 h-11 border rounded-full text-lg font-bold {{ $avatarColors }}">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-[14px] font-bold text-[#0f172a] truncate">{{ $user->name }}</p>
                            <p class="text-[12px] text-[#64748b] truncate">{{ $user->email }}</p>
                        </div>
                        <div class="ml-auto shrink-0">
                            <x-feedback-status.status-indicator :status="$user->account_status" />
                        </div>
                    </div>

                    {{-- Details grid --}}
                    <div class="grid grid-cols-2 divide-x divide-[#e2e8f0]">
                        <div class="px-4 py-2.5 space-y-0.5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Phone</p>
                            <p class="text-[13px] text-[#0f172a]">{{ $user->phone_number ?: '—' }}</p>
                        </div>
                        <div class="px-4 py-2.5 space-y-0.5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Office</p>
                            <p class="text-[13px] text-[#0f172a]">{{ $user->office ?: '—' }}</p>
                        </div>
                        <div class="px-4 py-2.5 space-y-0.5 border-t border-[#e2e8f0]">
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Registered</p>
                            <p class="text-[13px] text-[#0f172a]">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="px-4 py-2.5 space-y-0.5 border-t border-[#e2e8f0]">
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Email</p>
                            @if($user->email_verified_at)
                                <p class="text-[13px] text-[#16a34a] font-semibold flex items-center gap-1">
                                    <i class="bx bx-check-circle text-sm leading-none"></i> Verified
                                </p>
                            @else
                                <p class="text-[13px] text-[#92400e] font-semibold flex items-center gap-1">
                                    <i class="bx bx-time text-sm leading-none"></i> Unverified
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <x-feedback-status.alert :type="$hc['alertType']" :showTitle="false" :message="$hc['alertMsg']" />
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="$modalId" text="Cancel" />
            <x-ui.button type="submit" :variant="$hc['btnVariant']">
                <i class="bx {{ $hc['btnIcon'] }} leading-none"></i>
                {{ ucfirst($action) }}
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
