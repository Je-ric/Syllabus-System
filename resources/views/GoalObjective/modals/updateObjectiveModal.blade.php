<x-modal.dialog id="updateObjectiveModal_{{ $objective->id }}" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <div>
            <h3 class="text-lg sm:text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="bx bx-edit text-blue-500 text-2xl"></i>
                Edit Objective
                <span class="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/70 px-2 py-0.5 rounded-md">
                    {{ $objective->dept_obj_code }}
                </span>
            </h3>
            <p class="text-gray-500 text-sm mt-1">Update the description of this department objective.</p>
        </div>
    </x-modal.header>

    <form action="{{ route('objective.update', $objective->id) }}" method="POST" class="flex flex-col">
        @csrf
        @method('PUT')

        <x-modal.body>
            <div class="space-y-4">
                <div>
                    <x-form.label for="objective_text_{{ $objective->id }}">Objective Description</x-form.label>
                    <x-form.textarea
                        id="objective_text_{{ $objective->id }}"
                        name="objective_text"
                        rows="6"
                        placeholder="Describe the department objective…"
                        required>{{ $objective->objective_text }}</x-form.textarea>
                </div>
            </div>
        </x-modal.body>

        <x-modal.footer>
            <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
                <x-modal.close-button :modalId="'updateObjectiveModal_' . $objective->id" text="Cancel" variant="cancel" />
                <x-button type="submit" variant="save" class="w-full sm:w-auto">
                    <i class="bx bx-save"></i> Save Changes
                </x-button>
            </div>
        </x-modal.footer>
    </form>
</x-modal.dialog>
