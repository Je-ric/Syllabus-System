<x-modal.dialog id="deleteGoalModal_{{ $goal->id }}" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header modalId="deleteGoalModal_{{ $goal->id }}">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#ffe4e6] text-[#e11d48] shrink-0">
                <i class="bx bx-trash text-base leading-none"></i>
            </span>
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-[#9f1239]">Delete Goal</span>
                <span class="font-mono text-[11px] font-bold text-[#166534] bg-[#f0fdf4] border border-[#bbf7d0] px-2 py-0.5 rounded-md shrink-0">
                    {{ $goal->college_goals_code }}
                </span>
            </div>
        </div>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-4">
            <p class="text-[13px] text-[#475569]">Are you sure you want to delete this goal?</p>

            <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4">
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8] mb-2">Goal to be deleted</p>
                <p class="text-[13px] text-[#475569] leading-relaxed">{{ $goal->goal_text }}</p>
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
