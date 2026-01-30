<x-modal.dialog id="updateObjectiveModal_{{ $objective->id }}" maxWidth="max-w-lg" width="w-11/12">
    <x-modal.header>
        Edit Objective
        <x-modal.x-button :modalId="'updateObjectiveModal_' . $objective->id" />
    </x-modal.header>

    <form action="{{ route('objective.update', $objective->id) }}" method="POST">
        @csrf
        @method('PUT')

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <label class="block font-medium text-sm text-gray-700 mb-2">Objective Code (Auto-generated)</label>
                    <input
                        type="text"
                        value="{{ $objective->dept_obj_code }}"
                        class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-500"
                        disabled>
                </div>

                <div>
                    <label class="block font-medium text-sm text-gray-700 mb-2">Objective Description</label>
                    <textarea
                        name="objective_text"
                        rows="6"
                        placeholder="Objective description"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
                        required>{{ $objective->objective_text }}</textarea>
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'updateObjectiveModal_' . $objective->id" text="Cancel" variant="cancel"/>

            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i>
                Save Changes
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
