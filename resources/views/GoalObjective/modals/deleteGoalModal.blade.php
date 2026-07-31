<x-modal.dialog id="deleteGoalModal_{{ $goal->id }}" maxWidth="max-w-lg" width="w-11/12" variant="delete">
    <x-modal.header modalId="deleteGoalModal_{{ $goal->id }}" variant="delete">
        <span class="text-[#9f1239]">Delete Goal</span>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to delete this goal?</p>

            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-red-700">Goal to be deleted</p>
                    <span class="font-mono text-[11px] font-bold text-red-600 bg-rose-100 border border-rose-200 px-2 py-0.5 rounded-md shrink-0">
                        {{ $goal->college_goals_code }}
                    </span>
                </div>
                <p class="text-[13px] text-red-700 leading-relaxed">{{ $goal->goal_text }}</p>
            </div>

            <x-feedback-status.alert type="warning" :showTitle="false">
                This action cannot be undone. Remaining goals will be automatically re-sequenced.
            </x-feedback-status.alert>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'deleteGoalModal_' . $goal->id" text="Cancel" />
        <form action="{{ route('goal.destroy', $goal->id) }}" method="POST"
            x-data="{ submitting: false }"
            x-on:submit="submitting = true">
            @csrf
            @method('DELETE')
            <x-ui.button type="submit" variant="danger" ::disabled="submitting">
                <template x-if="!submitting">
                    <span class="inline-flex items-center gap-1.5"><i class="bx bx-trash"></i> Delete Goal</span>
                </template>
                <template x-if="submitting">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Deleting…
                    </span>
                </template>
            </x-ui.button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
