@props([
    'departmentId',
    'departmentName',
    'potentialChairs' => [],
])

<x-modal.dialog id="assignChairModal-{{ $departmentId }}" maxWidth="max-w-md" width="w-11/12" maxHeight="max-h-[90vh]">
    <form method="POST" action="{{ route('organizational.assign-chair') }}" class="flex flex-col">
        @csrf
        <input type="hidden" name="department_id" value="{{ $departmentId }}">

        <x-modal.header :modalId="'assignChairModal-' . $departmentId">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#eff6ff] text-[#1d4ed8] shrink-0">
                    <i class="bx bx-user-pin text-base leading-none"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[15px] font-bold text-[#0f172a]">Assign Chair</p>
                    <p class="text-[13px] text-[#94a3b8] truncate">{{ $departmentName }}</p>
                </div>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-form.label for="assignChairUser{{ $departmentId }}" isRequired>Select Chair</x-form.label>
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
            <x-button type="submit" variant="save">
                <i class="bx bx-user-check"></i> Assign Chair
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
