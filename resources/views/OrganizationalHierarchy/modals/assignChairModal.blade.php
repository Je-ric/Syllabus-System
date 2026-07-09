@props([
    'departmentId',
    'departmentName',
    'potentialChairs' => [],
])

<x-modal.dialog id="assignChairModal-{{ $departmentId }}" maxWidth="max-w-md" width="w-11/12" maxHeight="max-h-[90vh]">
    <form method="POST" action="{{ route('organizational.assign-chair') }}" class="flex flex-col">
        @csrf
        <input type="hidden" name="department_id" value="{{ $departmentId }}">

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
                    <x-form.select id="assignChairUser{{ $departmentId }}" name="user_id" class="mt-2" required>
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
            <x-modal.close-button :modalId="'assignChairModal-' . $departmentId" text="Cancel" />
            <x-ui.button type="submit" variant="save">
                <i class="bx bx-user-check"></i> Assign Chair
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
