<x-modal.dialog id="updateObjectiveModal_{{ $objective->id }}" maxWidth="max-w-lg" width="w-11/12">

    <x-modal.header modalId="updateObjectiveModal_{{ $objective->id }}">
        <div class="flex items-center gap-2">
            <i class="bx bx-edit text-slate-400"></i>
            Edit Objective
            <span class="text-xs font-mono font-normal text-emerald-600 bg-emerald-50
                         ring-1 ring-emerald-200 rounded-full px-2 py-0.5">
                {{ $objective->dept_obj_code }}
            </span>
        </div>
    </x-modal.header>

    <form action="{{ route('objective.update', $objective->id) }}" method="POST">
        @csrf
        @method('PUT')

        <x-modal.body>
            <div class="space-y-4">

                {{-- Code (read-only) --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                        Objective Code
                        <span class="ml-1 font-normal normal-case tracking-normal text-slate-400">— auto-generated</span>
                    </label>
                    <x-form.input
                        type="text"
                        value="{{ $objective->dept_obj_code }}"
                        disabled />
                </div>

                {{-- Description — slot content sets the existing value --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                        Objective Description
                    </label>
                    <x-form.textarea
                        name="objective_text"
                        rows="6"
                        placeholder="Describe the department objective…"
                        required>{{ $objective->objective_text }}</x-form.textarea>
                </div>

            </div>
        </x-modal.body>

        <x-modal.footer>
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
