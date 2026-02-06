@props([
    'collegeId',
    'collegeName',
    'potentialDeans' => [],
])

<x-modal.dialog id="assignDeanModal-{{ $collegeId }}" maxWidth="max-w-md">
    <form method="POST" action="{{ route('organizational.assign-dean') }}">
        @csrf
        <input type="hidden" name="college_id" value="{{ $collegeId }}">

        <x-modal.header class="bg-blue-50">
            <h3 class="text-lg font-semibold text-slate-900">Assign Dean</h3>
            <p class="text-sm text-slate-600 mt-1">{{ $collegeName }}</p>
        </x-modal.header>

        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Select Dean</label>
                <select name="user_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Choose a user --</option>
                    @foreach ($potentialDeans as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <x-modal.footer>
            <x-modal.close-button variant="cancel" :modalId="'assignDeanModal-' . $collegeId" text="Cancel" />
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                Assign Dean
            </button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
