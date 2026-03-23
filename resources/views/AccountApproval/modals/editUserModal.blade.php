<x-modal.dialog :id="$modalId" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <form method="POST" action="{{ route('account-approval.edit-user') }}" class="flex flex-col">
        @csrf
        @method('PUT')
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <x-modal.header>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center shrink-0">
                    <span class="text-slate-600 font-bold text-sm uppercase">{{ substr($user->name, 0, 1) }}</span>
                </div>
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="bx bx-edit text-slate-500 text-2xl"></i>
                        Edit User
                    </h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-form.label isRequired>Full Name</x-form.label>
                    <x-form.input type="text" name="name" value="{{ old('name', $user->name) }}" required />
                </div>
                <div>
                    <x-form.label isRequired>Email</x-form.label>
                    <x-form.input type="email" name="email" value="{{ old('email', $user->email) }}" required />
                </div>
                <div>
                    <x-form.label>Phone Number</x-form.label>
                    <x-form.input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" />
                </div>
                <div>
                    <x-form.label>Office</x-form.label>
                    <x-form.input type="text" name="office" value="{{ old('office', $user->office) }}" />
                </div>

                <x-feedback-status.alert type="info" title="Changes take effect immediately." class="w-full" />
            </div>
        </x-modal.body>

        <x-modal.footer>
            <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
                <x-modal.close-button :modalId="$modalId" text="Cancel" variant="cancel" />
                <x-button type="submit" variant="save">
                    <i class="bx bx-save"></i> Save Changes
                </x-button>
            </div>
        </x-modal.footer>
    </form>
</x-modal.dialog>
