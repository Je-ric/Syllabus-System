@php
    $headerConfig = [
        'approve' => ['icon' => 'bx-check-shield', 'color' => 'text-emerald-600', 'label' => 'Approve Account'],
        'reject'  => ['icon' => 'bx-block',        'color' => 'text-rose-600',    'label' => 'Reject Account'],
        'restore' => ['icon' => 'bx-revision',     'color' => 'text-indigo-600',  'label' => 'Restore Account'],
        'disable' => ['icon' => 'bx-pause-circle', 'color' => 'text-slate-600',   'label' => 'Disable Account'],
    ];
    $hc = $headerConfig[$action] ?? ['icon' => 'bx-user', 'color' => 'text-slate-600', 'label' => ucfirst($action)];
@endphp

<x-modal.dialog :id="$modalId" maxWidth="max-w-md" width="w-full">

    <form method="POST" action="{{ route('account-approval.' . $action) }}" class="flex flex-col" wire:ignore.self>
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <x-modal.header :modalId="$modalId">
            <span class="inline-flex items-center gap-2">
                <i class="bx {{ $hc['icon'] }} {{ $hc['color'] }} text-lg leading-none"></i>
                {{ $hc['label'] }}
            </span>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">
                <p class="text-sm text-slate-600">
                    Are you sure you want to
                    <span class="font-semibold capitalize">{{ $action }}</span> this account?
                </p>

                {{-- User info card --}}
                <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-600 font-bold text-sm uppercase">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-slate-800 text-sm truncate">{{ $user->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $user->email }}</p>
                        @if ($user->office)
                            <p class="text-xs text-slate-400 mt-0.5">{{ $user->office }}</p>
                        @endif
                    </div>
                    <div class="shrink-0">
                        <x-feedback-status.status-indicator :status="$user->account_status" />
                    </div>
                </div>

                @if ($action === 'reject' || $action === 'disable')
                    <x-feedback-status.alert type="warning"
                        title="The user will be notified via email." />
                @elseif ($action === 'approve')
                    <x-feedback-status.alert type="success"
                        title="The user will be notified via email." />
                @elseif ($action === 'restore')
                    <x-feedback-status.alert type="info"
                        title="The account will be restored to pending status." />
                @endif
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="$modalId" text="Cancel" variant="close" />

            <x-button type="submit"
                variant="{{ match($action) {
                    'approve' => 'add-button',
                    'reject'  => 'danger',
                    'restore' => 'save',
                    'disable' => 'cancel',
                    default   => 'primary',
                } }}">
                @if ($action === 'approve')      <i class="bx bx-check"></i>
                @elseif ($action === 'reject')   <i class="bx bx-x"></i>
                @elseif ($action === 'restore')  <i class="bx bx-revision"></i>
                @elseif ($action === 'disable')  <i class="bx bx-pause"></i>
                @endif
                {{ ucfirst($action) }}
            </x-button>
        </x-modal.footer>

    </form>

</x-modal.dialog>
