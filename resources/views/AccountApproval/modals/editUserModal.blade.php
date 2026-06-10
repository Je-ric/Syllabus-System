<x-modal.dialog :id="$modalId" maxWidth="max-w-md" width="w-11/12">
    <form method="POST" action="{{ route('account-approval.edit-user') }}" class="flex flex-col">
        @csrf
        @method('PUT')
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <x-modal.header :modalId="$modalId">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#e2e8f0] text-[#475569] shrink-0">
                    <i class="bx bx-edit text-base leading-none"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[15px] font-bold text-[#0f172a]">Edit User</p>
                    <p class="text-[13px] text-[#94a3b8] truncate">{{ $user->email }}</p>
                </div>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-modal.modal-label isRequired>Full Name</x-modal.modal-label>
                    <x-form.input type="text" name="name" value="{{ old('name', $user->name) }}" required />
                </div>
                <div>
                    <x-modal.modal-label isRequired>Email</x-modal.modal-label>
                    <x-form.input type="email" name="email" value="{{ old('email', $user->email) }}" required />
                </div>
                <div>
                    <x-modal.modal-label>Phone Number</x-modal.modal-label>
                    <x-form.input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" />
                </div>
                <div>
                    <x-modal.modal-label>Office</x-modal.modal-label>
                    <x-form.input type="text" name="office" value="{{ old('office', $user->office) }}" />
                </div>
                <x-feedback-status.alert type="info" :showTitle="false">Changes take effect immediately.</x-feedback-status.alert>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="$modalId" text="Cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i> Save Changes
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
