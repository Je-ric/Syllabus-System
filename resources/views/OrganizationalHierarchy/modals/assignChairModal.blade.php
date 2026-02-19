@props([
    'departmentId',
    'departmentName',
    'potentialChairs' => [],
])

<x-modal.dialog id="assignChairModal-{{ $departmentId }}" maxWidth="max-w-md">
    <form method="POST" action="{{ route('organizational.assign-chair') }}">
        @csrf
        <input type="hidden" name="department_id" value="{{ $departmentId }}">

        <x-modal.header>
            <h3 class="text-lg font-semibold text-slate-900">Assign Chair</h3>
            <p class="text-sm text-slate-600 mt-1">{{ $departmentName }}</p>
        </x-modal.header>

        <x-modal.body>
            <div>
                <x-form.label for="assignChairUser{{ $departmentId }}" variant="user">Select Chair</x-form.label>
                <x-form.select id="assignChairUser{{ $departmentId }}" name="user_id" class="mt-2" required>
                    <option value="">-- Choose a user --</option>
                    @foreach ($potentialChairs as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </x-form.select>
                @error('user_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button variant="cancel" :modalId="'assignChairModal-' . $departmentId" text="Cancel" />
            <x-button type="submit" variant="save">
                Assign Chair
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
