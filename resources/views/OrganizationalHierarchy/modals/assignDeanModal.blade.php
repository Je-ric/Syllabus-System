@props([
    'collegeId',
    'collegeName',
    'potentialDeans' => [],
])

<x-modal.dialog id="assignDeanModal-{{ $collegeId }}" maxWidth="max-w-md">
    <form method="POST" action="{{ route('organizational.assign-dean') }}">
        @csrf
        <input type="hidden" name="college_id" value="{{ $collegeId }}">

        <x-modal.header>
            <h3 class="text-lg font-semibold text-slate-900">Assign Dean</h3>
            <p class="text-sm text-slate-600 mt-1">{{ $collegeName }}</p>
        </x-modal.header>

        <x-modal.body>
            <div>
                <x-form.label for="assignDeanUser{{ $collegeId }}" variant="user">Select Dean</x-form.label>
                <select id="assignDeanUser{{ $collegeId }}" name="user_id" class="mt-2 w-full rounded-xl border border-slate-300 bg-white/90 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" required>
                    <option value="">-- Choose a user --</option>
                    @foreach ($potentialDeans as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button variant="cancel" :modalId="'assignDeanModal-' . $collegeId" text="Cancel" />
            <x-button type="submit" variant="save">
                Assign Dean
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
