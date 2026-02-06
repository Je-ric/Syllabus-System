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

        <x-modal.header class="bg-green-50">
            <h3 class="text-lg font-semibold text-slate-900">Assign Faculty</h3>
            <p class="text-sm text-slate-600 mt-1">{{ $departmentName }}</p>
        </x-modal.header>

        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Select Faculty Member</label>
                <select name="user_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    <option value="">-- Choose a user --</option>
                    @foreach($potentialFaculty as $faculty)
                        @if(!in_array($faculty->id, $assignedFacultyIds))
                            <option value="{{ $faculty->id }}">{{ $faculty->name }} ({{ $faculty->email }})</option>
                        @endif
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <x-modal.footer>
            <x-modal.close-button variant="cancel" :modalId="'assignFacultyModal-' . $departmentId" text="Cancel" />
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                Assign Faculty
            </button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
