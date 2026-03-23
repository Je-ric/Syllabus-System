@php
    $cfg = [
        'approve' => ['icon' => 'bx-check-shield', 'circle' => 'bg-emerald-100', 'iconColor' => 'text-emerald-500', 'titleColor' => 'text-emerald-600', 'label' => 'Approve Account',  'question' => 'Are you sure you want to approve this account?'],
        'reject'  => ['icon' => 'bx-block',        'circle' => 'bg-rose-100',    'iconColor' => 'text-rose-500',    'titleColor' => 'text-rose-600',    'label' => 'Reject Account',   'question' => 'Are you sure you want to reject this account?'],
        'restore' => ['icon' => 'bx-revision',     'circle' => 'bg-indigo-100',  'iconColor' => 'text-indigo-500',  'titleColor' => 'text-indigo-600',  'label' => 'Restore Account',  'question' => 'Are you sure you want to restore this account?'],
        'disable' => ['icon' => 'bx-pause-circle', 'circle' => 'bg-slate-100',   'iconColor' => 'text-slate-500',   'titleColor' => 'text-slate-600',   'label' => 'Disable Account',  'question' => 'Are you sure you want to disable this account?'],
    ];
    $hc = $cfg[$action] ?? ['icon' => 'bx-user', 'circle' => 'bg-slate-100', 'iconColor' => 'text-slate-500', 'titleColor' => 'text-slate-600', 'label' => ucfirst($action), 'question' => 'Are you sure?'];
@endphp

<x-modal.dialog :id="$modalId" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <form method="POST" action="{{ route('account-approval.' . $action) }}" class="flex flex-col" wire:ignore.self>
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <x-modal.header>
            <h2 class="text-lg sm:text-xl font-bold {{ $hc['titleColor'] }} flex items-center gap-2">
                <i class="bx {{ $hc['icon'] }} text-2xl"></i>
                {{ $hc['label'] }}
            </h2>
        </x-modal.header>

        <x-modal.body>
            <div class="flex flex-col items-center text-center gap-4">
                <div class="{{ $hc['circle'] }} rounded-full w-12 h-12 flex items-center justify-center">
                    <i class="bx {{ $hc['icon'] }} text-2xl {{ $hc['iconColor'] }}"></i>
                </div>

                <h3 class="text-base sm:text-lg font-semibold {{ $hc['titleColor'] }}">{{ $hc['question'] }}</h3>

                {{-- User info card --}}
                <div class="bg-gray-50 rounded-lg p-4 w-full text-left">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700 text-sm">Name:</span>
                            <span class="text-sm text-gray-800">{{ $user->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700 text-sm">Email:</span>
                            <span class="text-sm text-gray-800">{{ $user->email }}</span>
                        </div>
                        @if ($user->office)
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700 text-sm">Office:</span>
                                <span class="text-sm text-gray-800">{{ $user->office }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-700 text-sm">Status:</span>
                            <x-feedback-status.status-indicator :status="$user->account_status" />
                        </div>
                    </div>
                </div>

                @if ($action === 'reject' || $action === 'disable')
                    <x-feedback-status.alert type="warning" title="The user will be notified via email." class="w-full" />
                @elseif ($action === 'approve')
                    <x-feedback-status.alert type="success" title="The user will be notified via email." class="w-full" />
                @elseif ($action === 'restore')
                    <x-feedback-status.alert type="info" title="The account will be restored to pending status." class="w-full" />
                @endif
            </div>
        </x-modal.body>

        <x-modal.footer>
            <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
                <x-modal.close-button :modalId="$modalId" text="Cancel" variant="cancel" />
                <x-button type="submit"
                    variant="{{ match($action) {
                        'approve' => 'add-button',
                        'reject'  => 'danger',
                        'restore' => 'save',
                        'disable' => 'cancel',
                        default   => 'primary',
                    } }}">
                    @if ($action === 'approve')     <i class="bx bx-check"></i>
                    @elseif ($action === 'reject')  <i class="bx bx-x"></i>
                    @elseif ($action === 'restore') <i class="bx bx-revision"></i>
                    @elseif ($action === 'disable') <i class="bx bx-pause"></i>
                    @endif
                    {{ ucfirst($action) }}
                </x-button>
            </div>
        </x-modal.footer>
    </form>
</x-modal.dialog>
