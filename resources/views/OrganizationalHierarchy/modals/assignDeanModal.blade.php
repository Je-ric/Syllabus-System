@props([
    'collegeId',
    'collegeName',
    'potentialDeans' => [],
])

<x-modal.dialog id="assignDeanModal-{{ $collegeId }}" maxWidth="max-w-md" width="w-11/12" maxHeight="max-h-[90vh]">
    <form method="POST" action="{{ route('organizational.assign-dean') }}" class="flex flex-col">
        @csrf
        <input type="hidden" name="college_id" value="{{ $collegeId }}">

        <x-modal.header :modalId="'assignDeanModal-' . $collegeId">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#eef2ff] text-[#3730a3] shrink-0">
                    <i class="bx bx-medal text-base leading-none"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[15px] font-bold text-[#0f172a]">Assign Dean</p>
                    <p class="text-[13px] text-[#94a3b8] truncate">{{ $collegeName }}</p>
                </div>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-modal.modal-label for="assignDeanUser{{ $collegeId }}" isRequired>Select Dean</x-modal.modal-label>
                    <x-form.select id="assignDeanUser{{ $collegeId }}" name="user_id" class="mt-2" required>
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
            <x-modal.close-button :modalId="'assignDeanModal-' . $collegeId" text="Cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-user-check"></i> Assign Dean
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
