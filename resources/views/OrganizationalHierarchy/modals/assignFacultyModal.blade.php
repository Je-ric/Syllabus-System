@props([
    'departmentId',
    'departmentName',
    'potentialFaculty' => [],
    'assignedFacultyIds' => [],
])

<x-modal.dialog id="assignFacultyModal-{{ $departmentId }}" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <form method="POST" action="{{ route('organizational.assign-faculty') }}" class="flex flex-col">
        @csrf
        <input type="hidden" name="department_id" value="{{ $departmentId }}">

        <x-modal.header>
            <div>
                <h3 class="text-lg sm:text-xl font-bold text-slate-800">Assign Faculty</h3>
                <p class="text-gray-500 text-sm mt-1">{{ $departmentName }}</p>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-form.label for="assignFacultyUser{{ $departmentId }}">Select Faculty Member</x-form.label>
                    <x-form.select id="assignFacultyUser{{ $departmentId }}" name="user_id" class="mt-2" required>
                        <option value="">— Choose a user —</option>
                        @foreach ($potentialFaculty as $faculty)
                            @if (!in_array($faculty->id, $assignedFacultyIds))
                                <option value="{{ $faculty->id }}">{{ $faculty->name }} ({{ $faculty->email }})</option>
                            @endif
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
                <x-modal.close-button variant="cancel" :modalId="'assignFacultyModal-' . $departmentId" text="Cancel" />
                <x-button type="submit" variant="save" class="w-full sm:w-auto">
                    <i class="bx bx-user-check"></i> Assign Faculty
                </x-button>
            </div>
        </x-modal.footer>
    </form>
</x-modal.dialog>
