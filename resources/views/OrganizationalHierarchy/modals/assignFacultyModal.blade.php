@props([
    'departmentId',
    'departmentName',
    'potentialFaculty' => [],
    'assignedFacultyIds' => [],
])

<x-modal.dialog id="assignFacultyModal-{{ $departmentId }}" maxWidth="max-w-md">
    <form method="POST" action="{{ route('organizational.assign-faculty') }}">
        @csrf
        <input type="hidden" name="department_id" value="{{ $departmentId }}">

        <x-modal.header :modalId="'assignFacultyModal-' . $departmentId">
            <h3 class="text-base font-semibold text-slate-900">Assign Faculty</h3>
            <p class="text-sm text-slate-500 mt-0.5">{{ $departmentName }}</p>
        </x-modal.header>

        <x-modal.body>
            <div>
                <x-form.label for="assignFacultyUser{{ $departmentId }}" variant="user">Select Faculty Member</x-form.label>
                <x-form.select id="assignFacultyUser{{ $departmentId }}" name="user_id" class="mt-2" required>
                    <option value="">-- Choose a user --</option>
                    @foreach($potentialFaculty as $faculty)
                        @if(!in_array($faculty->id, $assignedFacultyIds))
                            <option value="{{ $faculty->id }}">{{ $faculty->name }} ({{ $faculty->email }})</option>
                        @endif
                    @endforeach
                </x-form.select>
                @error('user_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button variant="cancel" :modalId="'assignFacultyModal-' . $departmentId" text="Cancel" />
            <x-button type="submit" variant="save">
                Assign Faculty
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
