<x-modal.dialog :id="$modalId" maxWidth="max-w-2xl" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <h3 class="text-lg font-semibold text-slate-800">
            Confirm {{ ucfirst($action) }} Action
        </h3>
    </x-modal.header>

    <x-modal.body>
        <p class="text-slate-700">
            Are you sure you want to <span class="font-semibold capitalize">{{ $action }}</span> this account?
        </p>
        <div class="bg-gray-50 rounded p-2 w-full mt-2">
            <span class="font-semibold text-gray-800">{{ $user->name }}</span>
            <span class="text-gray-600 text-sm block">{{ $user->email }}</span>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <form method="POST" action="{{ route('account-approval.' . $action) }}" class="w-full flex gap-2 justify-end">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <x-modal.close-button :modalId="$modalId" text="Cancel" variant="cancel" />
            <x-button type="submit"
                variant="{{ $action === 'approve' ? 'active' :
                            ($action === 'reject' ? 'reject' :
                            ($action === 'restore' ? 'restore' : 'disable')
                            )
                        }}"
                >
                {{ ucfirst($action) }}
            </x-button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
