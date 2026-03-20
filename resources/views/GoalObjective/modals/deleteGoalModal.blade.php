<x-modal.dialog id="deleteGoalModal_{{ $goal->id }}" maxWidth="max-w-md" width="w-11/12">

    <x-modal.header modalId="deleteGoalModal_{{ $goal->id }}" class="bg-rose-50">
        <div class="flex items-center gap-2.5">
            <i class="bx bx-trash text-rose-600 text-lg leading-none"></i>
            <h2 class="text-base font-semibold text-rose-900 tracking-tight">Delete Goal</h2>
            <span class="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/70 px-2 py-0.5 rounded-md">
                {{ $goal->college_goals_code }}
            </span>
        </div>
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-3">
            <x-feedback-status.alert
                type="warning"
                message="This action cannot be undone. Remaining goals will be automatically re-sequenced." />

            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-2">Goal to be deleted</p>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $goal->goal_text }}</p>
            </div>
        </div>
    </x-modal.body>

    <x-modal.footer class="bg-rose-50">
        <x-modal.close-button :modalId="'deleteGoalModal_' . $goal->id" text="Cancel" variant="cancel" />
        <form action="{{ route('goal.destroy', $goal->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="danger">
                <i class="bx bx-trash"></i> Delete Goal
            </x-button>
        </form>
    </x-modal.footer>

</x-modal.dialog>
