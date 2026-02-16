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
                <select id="assignChairUser{{ $departmentId }}" name="user_id" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" required>
                    <option value="">-- Choose a user --</option>
                    @foreach ($potentialChairs as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
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
