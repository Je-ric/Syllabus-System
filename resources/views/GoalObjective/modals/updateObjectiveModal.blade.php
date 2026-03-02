<x-modal.dialog id="updateObjectiveModal_{{ $objective->id }}" maxWidth="max-w-lg" width="w-11/12">

    <x-modal.header modalId="updateObjectiveModal_{{ $objective->id }}"
        class="bg-blue-50">
        <h2 class="text-xl font-semibold text-blue-900 tracking-tight">Edit Objective</h2>
    </x-modal.header>

    <form action="{{ route('objective.update', $objective->id) }}" method="POST">
        @csrf
        @method('PUT')

        <x-modal.body>
            <div class="space-y-4">

                {{-- Code (read-only) --}}
                <div>
                    <x-form.label>Objective Code — auto-generated </x-form.label>
                    <x-form.input
                        type="text"
                        value="{{ $objective->dept_obj_code }}"
                        disabled />
                </div>

                {{-- Description — slot content sets the existing value --}}
                <div>
                    <x-form.label>Objective Description</x-form.label>
                    <x-form.textarea
                        name="objective_text"
                        rows="6"
                        placeholder="Describe the department objective…"
                        required>{{ $objective->objective_text }}</x-form.textarea>
                </div>

            </div>
        </x-modal.body>

        <x-modal.footer class="bg-blue-50">
            <x-modal.close-button
                :modalId="'updateObjectiveModal_' . $objective->id"
                text="Cancel"
                variant="cancel" />
            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i> Save Changes
            </x-button>
        </x-modal.footer>
    </form>

</x-modal.dialog>
