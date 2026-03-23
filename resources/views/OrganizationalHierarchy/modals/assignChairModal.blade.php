@props([
    'departmentId',
    'departmentName',
    'potentialChairs' => [],
])

<x-modal.dialog id="assignChairModal-{{ $departmentId }}" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <form method="POST" action="{{ route('organizational.assign-chair') }}" class="flex flex-col">
        @csrf
        <input type="hidden" name="department_id" value="{{ $departmentId }}">

        <x-modal.header>
            <div>
                <h3 class="text-lg sm:text-xl font-bold text-slate-800">Assign Chair</h3>
                <p class="text-gray-500 text-sm mt-1">{{ $departmentName }}</p>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-form.label for="assignChairUser{{ $departmentId }}">Select Chair</x-form.label>
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
            <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
                <x-modal.close-button variant="cancel" :modalId="'assignChairModal-' . $departmentId" text="Cancel" />
                <x-button type="submit" variant="save" class="w-full sm:w-auto">
                    <i class="bx bx-user-check"></i> Assign Chair
                </x-button>
            </div>
        </x-modal.footer>
    </form>
</x-modal.dialog>
