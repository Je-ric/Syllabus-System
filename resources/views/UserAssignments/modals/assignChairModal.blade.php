@props([
    'departmentId',
    'departmentName',
    'potentialChairs' => [],
])

<x-modal.dialog id="assignChairModal-{{ $departmentId }}" maxWidth="max-w-md" width="w-11/12" maxHeight="max-h-[90vh]" variant="assign">
    <form method="POST" action="{{ route('user-assignments.assign-chair') }}" class="flex flex-col"
        x-data="{ submitting: false, selectedUser: '' }"
        x-on:submit="submitting = true">
        @csrf
        <input type="hidden" name="department_id" value="{{ $departmentId }}">
        {{-- Always-present hidden input bound to Alpine state — not affected by disabled --}}
        <input type="hidden" name="user_id" x-bind:value="selectedUser">

        <x-modal.header :modalId="'assignChairModal-' . $departmentId" variant="assign">
            <div class="min-w-0">
                <p class="text-[15px] font-bold text-[#0f172a]">Assign Chair</p>
                <p class="text-[13px] text-[#94a3b8] truncate">{{ $departmentName }}</p>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-modal.modal-label for="assignChairUser{{ $departmentId }}" isRequired>Select Chair</x-modal.modal-label>
                    <x-form.select
                        id="assignChairUser{{ $departmentId }}"
                        x-model="selectedUser"
                        class="mt-2"
                        ::disabled="submitting"
                        ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''">
                        <option value="">— Choose a user —</option>
                        @foreach ($potentialChairs as $user)
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
            <x-modal.close-button :modalId="'assignChairModal-' . $departmentId" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Assigning…"
                ::disabled="submitting || !selectedUser">
                <i class="bx bx-user-check"></i> Assign Chair
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
