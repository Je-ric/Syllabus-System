@props([
    'collegeId',
    'collegeName',
    'potentialDeans' => [],
])

<x-modal.dialog id="assignDeanModal-{{ $collegeId }}" maxWidth="max-w-md">
    <form method="POST" action="{{ route('organizational.assign-dean') }}">
        @csrf
        <input type="hidden" name="college_id" value="{{ $collegeId }}">

        <x-modal.header :modalId="'assignDeanModal-' . $collegeId">
            <h3 class="text-base font-semibold text-slate-900">Assign Dean</h3>
            <p class="text-sm text-slate-500 mt-0.5">{{ $collegeName }}</p>
        </x-modal.header>

        <x-modal.body>
            <div>
                <x-form.label for="assignDeanUser{{ $collegeId }}" variant="user">Select Dean</x-form.label>
                <x-form.select id="assignDeanUser{{ $collegeId }}" name="user_id" class="mt-2" required>
                    <option value="">-- Choose a user --</option>
                    @foreach ($potentialDeans as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </x-form.select>
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
