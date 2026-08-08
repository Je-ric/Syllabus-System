@props([
    'collegeId',
    'collegeName',
    'potentialDeans' => [],
])

<x-modal.dialog id="assignDeanModal-{{ $collegeId }}" maxWidth="max-w-md" width="w-11/12" maxHeight="max-h-[90vh]" variant="assign">
    <form method="POST" action="{{ route('user-assignments.assign-dean') }}" class="flex flex-col"
        x-data="{ submitting: false, selectedUser: '' }"
        x-on:submit="submitting = true">
        @csrf
        <input type="hidden" name="college_id" value="{{ $collegeId }}">
        {{-- Always-present hidden input bound to Alpine state — not affected by disabled --}}
        <input type="hidden" name="user_id" x-bind:value="selectedUser">

        <x-modal.header :modalId="'assignDeanModal-' . $collegeId" variant="assign">
            <div class="min-w-0">
                <p class="text-[15px] font-bold text-[#0f172a]">Assign Dean</p>
                <p class="text-[13px] text-[#94a3b8] truncate">{{ $collegeName }}</p>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-modal.modal-label for="assignDeanUser{{ $collegeId }}" isRequired>Select Dean</x-modal.modal-label>
                    <x-form.select
                        id="assignDeanUser{{ $collegeId }}"
                        x-model="selectedUser"
                        class="mt-2"
                        ::disabled="submitting"
                        ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''">
                        <option value="">— Choose a user —</option>
                        @foreach ($potentialDeans as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </x-form.select>
                    @error('user_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'assignDeanModal-' . $collegeId" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Assigning…"
                ::disabled="submitting || !selectedUser">
                <i class="bx bx-user-check"></i> Assign Dean
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
