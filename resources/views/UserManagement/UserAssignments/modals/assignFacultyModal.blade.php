@props([
    'departmentId',
    'departmentName',
    'potentialFaculty' => [],
    'assignedFacultyIds' => [],
])

<x-modal.dialog id="assignFacultyModal-{{ $departmentId }}" maxWidth="max-w-md" width="w-11/12" maxHeight="max-h-[90vh]" variant="assign">
    <form method="POST" action="{{ route('user-assignments.assign-faculty') }}" class="flex flex-col"
        x-data="{ submitting: false, selectedUser: '' }"
        x-on:submit="submitting = true">
        @csrf
        <input type="hidden" name="department_id" value="{{ $departmentId }}">
        {{-- Always-present hidden input bound to Alpine state — not affected by disabled --}}
        <input type="hidden" name="user_id" x-bind:value="selectedUser">

        <x-modal.header :modalId="'assignFacultyModal-' . $departmentId" variant="assign">
            <div class="min-w-0">
                <p class="text-[15px] font-bold text-[#0f172a]">Assign Faculty</p>
                <p class="text-[13px] text-[#94a3b8] truncate">{{ $departmentName }}</p>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-modal.modal-label for="assignFacultyUser{{ $departmentId }}" isRequired>Select Faculty Member</x-modal.modal-label>
                    <x-form.select
                        id="assignFacultyUser{{ $departmentId }}"
                        x-model="selectedUser"
                        class="mt-2"
                        ::disabled="submitting"
                        ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''">
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
            <x-modal.close-button :modalId="'assignFacultyModal-' . $departmentId" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Assigning…"
                ::disabled="submitting || !selectedUser">
                <i class="bx bx-user-check"></i> Assign Faculty
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
