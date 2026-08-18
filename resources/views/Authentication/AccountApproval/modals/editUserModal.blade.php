@php
    $avatarColors = match($user->account_status) {
        'active'   => 'bg-[#dcfce7] text-[#166534]',
        'pending'  => 'bg-[#fef3c7] text-[#92400e]',
        'rejected' => 'bg-[#ffe4e6] text-[#9f1239]',
        'disabled' => 'bg-[#f1f5f9] text-[#475569]',
        default    => 'bg-[#f1f5f9] text-[#475569]',
    };
@endphp

<x-modal.dialog :id="$modalId" maxWidth="max-w-lg" width="w-11/12" variant="edit">
    @php
        $hasEditErrors = $errors->hasAny(['name', 'email', 'phone_number', 'office'])
                      && (string) old('user_id') === (string) $user->id;
    @endphp

    <form method="POST" action="{{ route('account-approval.edit-user') }}" class="flex flex-col"
        x-data="{
            submitting: false,
            name: @js(old('name', $user->name)),
            email: @js(old('email', $user->email)),
            phone: @js(old('phone_number', $user->phone_number ?? '')),
            office: @js(old('office', $user->office ?? '')),
            origName: @js($user->name),
            origEmail: @js($user->email),
            origPhone: @js($user->phone_number ?? ''),
            origOffice: @js($user->office ?? ''),
            get hasChanged() {
                return this.name.trim()  !== this.origName.trim()
                    || this.email.trim() !== this.origEmail.trim()
                    || this.phone.trim() !== this.origPhone.trim()
                    || this.office.trim() !== this.origOffice.trim();
            },
            get canSubmit() {
                return this.hasChanged && this.name.trim() !== '' && this.email.trim() !== '';
            }
        }"
        x-on:submit="submitting = true"
        x-init="@js($hasEditErrors) && $nextTick(() => { const modal = document.getElementById(@js($modalId)); if (modal) modal.showModal(); })">
        @csrf
        @method('PUT')
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <x-modal.header :modalId="$modalId" variant="edit">
            <div class="min-w-0">
                <p class="text-[15px] font-bold text-[#0f172a]">Edit User</p>
                <p class="text-[12px] text-[#94a3b8] truncate">ID #{{ $user->id }}</p>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">

                {{-- Generic / catch-block errors --}}
                @if ($errors->has('error'))
                    <x-feedback-status.alert type="error" :showTitle="false" class="mb-4">
                        <strong>Something went wrong:</strong> {{ $errors->first('error') }}
                    </x-feedback-status.alert>
                @endif

                {{-- Validation summary --}}
                @if ($hasEditErrors)
                    <x-feedback-status.alert type="error" :showTitle="false">
                        Please fix the highlighted fields below before submitting.
                    </x-feedback-status.alert>
                @endif

                {{-- Identity strip --}}
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                    <span class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-full font-bold text-base {{ $avatarColors }}">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[13px] font-semibold text-[#0f172a] truncate">{{ $user->name }}</p>
                        <p class="text-[12px] text-[#94a3b8] truncate">{{ $user->email }}</p>
                    </div>
                    <x-feedback-status.status-indicator :status="$user->account_status" />
                </div>

                {{-- Form fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <x-modal.modal-label isRequired>Full Name</x-modal.modal-label>
                        <x-form.input type="text" name="name"
                            value="{{ old('name', $user->name) }}"
                            x-model="name"
                            pattern="[\p{L}\s]+"
                            title="Name must contain letters and spaces only"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                            required />
                        @if($hasEditErrors)
                            @error('name')
                                <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <x-modal.modal-label>Phone Number</x-modal.modal-label>
                        <x-form.input type="text" name="phone_number"
                            value="{{ old('phone_number', $user->phone_number) }}"
                            placeholder="e.g. 09XX XXX XXXX"
                            x-model="phone"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''" />
                        @if($hasEditErrors)
                            @error('phone_number')
                                <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <x-modal.modal-label isRequired>Email Address</x-modal.modal-label>
                        <x-form.input type="text" name="email"
                            value="{{ old('email', $user->email) }}"
                            x-model="email"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                            required />
                        @if($hasEditErrors)
                            @error('email')
                                <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>
                    <div class="sm:col-span-2">
                        <x-modal.modal-label>Office / Department</x-modal.modal-label>
                        <x-form.input type="text" name="office"
                            value="{{ old('office', $user->office) }}"
                            placeholder="e.g. College of Engineering"
                            x-model="office"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''" />
                        @if($hasEditErrors)
                            @error('office')
                                <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>
                </div>

                <x-feedback-status.alert type="warning" :showTitle="false"
                    message="Changes take effect immediately. Email changes will update the user's login credentials." />
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="$modalId" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Saving…"
                ::disabled="submitting || !canSubmit">
                <i class="bx bx-save leading-none"></i> Save Changes
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
