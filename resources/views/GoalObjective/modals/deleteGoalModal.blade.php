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
        <form action="{{ route('goal.destroy', $goal->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="danger">
                <i class="bx bx-trash"></i> Delete Goal
            </x-button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
