<x-modal.dialog id="deleteObjectiveModal_{{ $objective->id }}" maxWidth="max-w-lg" width="w-11/12" variant="delete">
    <x-modal.header modalId="deleteObjectiveModal_{{ $objective->id }}" variant="delete">
        <span class="text-[#9f1239]">Delete Objective</span>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to delete this objective?</p>

            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-red-700">Objective to be deleted</p>
                    <span class="font-mono text-[11px] font-bold text-red-600 bg-rose-100 border border-rose-200 px-2 py-0.5 rounded-md shrink-0">
                        {{ $objective->dept_obj_code }}
                    </span>
                </div>
                <p class="text-[13px] text-red-700 leading-relaxed">{{ $objective->objective_text }}</p>
            </div>

            <x-feedback-status.alert type="warning" :showTitle="false">
                This action cannot be undone. Remaining objectives will be automatically re-sequenced.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'deleteObjectiveModal_' . $objective->id" text="Cancel" />
        <form action="{{ route('objective.destroy', $objective->id) }}" method="POST"
            x-data="{ submitting: false }"
            x-on:submit="submitting = true">
            @csrf
            @method('DELETE')
            <x-ui.button type="submit" variant="danger"
                submitting="submitting" loadingText="Deleting…"
                ::disabled="submitting">
                <i class="bx bx-trash"></i> Delete Objective
            </x-ui.button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
