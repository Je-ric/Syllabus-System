<x-modal.dialog id="deleteGoalModal_{{ $goal->id }}" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <h2 class="text-lg sm:text-xl font-bold text-red-600 flex items-center gap-2">
            <i class="bx bx-trash text-2xl"></i>
            Delete Goal
            <span class="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/70 px-2 py-0.5 rounded-md">
                {{ $goal->college_goals_code }}
            </span>
        </h2>
    </x-modal.header>

    <x-modal.body>
        <div class="flex flex-col items-center text-center gap-4">
            <div class="bg-red-100 rounded-full w-12 h-12 flex items-center justify-center">
                <i class="bx bx-trash text-2xl text-red-500"></i>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-red-700">Are you sure you want to delete this goal?</h3>

            <div class="bg-gray-50 rounded-lg p-4 w-full text-left">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">Goal to be deleted</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $goal->goal_text }}</p>
            </div>

            <x-feedback-status.alert type="warning" title="This action cannot be undone. Remaining goals will be automatically re-sequenced." class="w-full" />
        </div>
    </x-modal.body>

    <x-modal.footer>
        <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
            <x-modal.close-button :modalId="'deleteGoalModal_' . $goal->id" text="Cancel" variant="cancel" />
            <form action="{{ route('goal.destroy', $goal->id) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger" class="w-full sm:w-auto">
                    <i class="bx bx-trash"></i> Delete Goal
                </x-button>
            </form>
        </div>
    </x-modal.footer>
</x-modal.dialog>
