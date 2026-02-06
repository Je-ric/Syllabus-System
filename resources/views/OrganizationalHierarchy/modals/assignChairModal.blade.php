@props([
    'departmentId',
    'departmentName',
    'potentialChairs' => [],
])

<x-modal.dialog id="assignChairModal-{{ $departmentId }}" maxWidth="max-w-md">
    <form method="POST" action="{{ route('organizational.assign-chair') }}">
        @csrf
        <input type="hidden" name="department_id" value="{{ $departmentId }}">

        <x-modal.header class="bg-purple-50">
            <h3 class="text-lg font-semibold text-slate-900">Assign Chair</h3>
            <p class="text-sm text-slate-600 mt-1">{{ $departmentName }}</p>
        </x-modal.header>

        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Select Chair</label>
                <select name="user_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    <option value="">-- Choose a user --</option>
                    @foreach ($potentialChairs as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <x-modal.footer>
            <x-modal.close-button variant="cancel" :modalId="'assignChairModal-' . $departmentId" text="Cancel" />
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                Assign Chair
            </button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
