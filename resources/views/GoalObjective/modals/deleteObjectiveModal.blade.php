<x-modal.dialog id="deleteObjectiveModal_{{ $objective->id }}" maxWidth="max-w-xl" width="w-11/12">

    <x-modal.header modalId="deleteObjectiveModal_{{ $objective->id }}"
        class="bg-rose-50">
        <h2 class="text-xl font-semibold text-rose-900 tracking-tight">Delete Objective</h2>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">

            <x-feedback-status.alert
                type="warning"
                message="Are you sure you want to delete this objective? This action cannot be undone." />

            {{-- Objective details card --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 space-y-1.5">
                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500 mb-2">
                    Objective Details
                </p>
                <div class="flex items-start gap-2 text-sm">
                    <span class="shrink-0 font-semibold text-slate-600 w-12">Code</span>
                    <span class="font-mono text-emerald-700 font-semibold">{{ $objective->dept_obj_code }}</span>
                </div>
                <div class="flex items-start gap-2 text-sm">
                    <span class="shrink-0 font-semibold text-slate-600 w-12">Text</span>
                    <span class="text-slate-700">{{ $objective->objective_text }}</span>
                </div>
            </div>

            <x-feedback-status.alert
                type="warning"
                title="Codes will be re-indexed"
                message="Remaining objectives will be automatically re-sequenced after deletion." />

        </div>
    </x-modal.body>

    <x-modal.footer class="bg-rose-50">
        <x-modal.close-button
            :modalId="'deleteObjectiveModal_' . $objective->id"
            text="Cancel"
            variant="cancel" />

        <form action="{{ route('objective.destroy', $objective->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="danger">
                <i class="bx bx-trash"></i> Delete Objective
            </x-button>
        </form>
    </x-modal.footer>

</x-modal.dialog>
