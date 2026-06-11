@php
    $cfg = [
        'approve' => ['variant' => 'approve', 'titleColor' => 'text-green-800',  'label' => 'Approve Account', 'question' => 'Approve this account?'],
        'reject'  => ['variant' => 'reject',  'titleColor' => 'text-red-800',    'label' => 'Reject Account',  'question' => 'Reject this account?'],
        'restore' => ['variant' => 'restore', 'titleColor' => 'text-blue-800',   'label' => 'Restore Account', 'question' => 'Restore this account to pending?'],
        'disable' => ['variant' => 'disable', 'titleColor' => 'text-amber-800',  'label' => 'Disable Account', 'question' => 'Disable this account?'],
    ];
    $hc = $cfg[$action] ?? ['variant' => null, 'titleColor' => 'text-[#475569]', 'label' => ucfirst($action), 'question' => 'Are you sure?'];
@endphp

<x-modal.dialog :id="$modalId" maxWidth="max-w-md" width="w-11/12">
    <form method="POST" action="{{ route('account-approval.' . $action) }}" class="flex flex-col" wire:ignore.self>
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <x-modal.header :modalId="$modalId" :variant="$hc['variant']">
            <span class="{{ $hc['titleColor'] }}">{{ $hc['label'] }}</span>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">
                <p class="text-[13px] text-[#475569]">{{ $hc['question'] }}</p>

                <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Name</span>
                        <span class="text-[13px] font-semibold text-[#0f172a]">{{ $user->name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Email</span>
                        <span class="text-[13px] text-[#475569]">{{ $user->email }}</span>
                    </div>
                    @if ($user->office)
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Office</span>
                            <span class="text-[13px] text-[#475569]">{{ $user->office }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">Status</span>
                        <x-feedback-status.status-indicator :status="$user->account_status" />
                    </div>
                </div>

                @if ($action === 'reject' || $action === 'disable')
                    <x-feedback-status.alert type="warning" :showTitle="false">The user will be notified via email.</x-feedback-status.alert>
                @elseif ($action === 'approve')
                    <x-feedback-status.alert type="success" :showTitle="false">The user will be notified via email.</x-feedback-status.alert>
                @elseif ($action === 'restore')
                    <x-feedback-status.alert type="info" :showTitle="false">The account will be restored to pending status.</x-feedback-status.alert>
                @endif
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="$modalId" text="Cancel" />
            <x-button type="submit"
                variant="{{ match($action) {
                    'approve' => 'add-button',
                    'reject'  => 'danger',
                    'restore' => 'save',
                    'disable' => 'warning',
                    default   => 'primary',
                } }}">
                @if ($action === 'approve')     <i class="bx bx-check"></i>
                @elseif ($action === 'reject')  <i class="bx bx-x"></i>
                @elseif ($action === 'restore') <i class="bx bx-revision"></i>
                @elseif ($action === 'disable') <i class="bx bx-pause"></i>
                @endif
                {{ ucfirst($action) }}
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
