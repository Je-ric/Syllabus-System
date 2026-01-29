<x-modal.dialog id="deleteGoalModal_{{ $goal->id }}" maxWidth="max-w-xl" width="w-11/12">
    <x-modal.header>
        Confirm Delete Goal
        <x-modal.x-button :modalId="'deleteGoalModal_' . $goal->id" />
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-3">
            <p class="text-gray-700">Are you sure you want to delete this goal?</p>
            <div class="bg-gray-50 p-3 rounded border border-gray-200">
                <p class="font-semibold text-sm">Goal Details:</p>
                <ul class="text-sm mt-2 space-y-1">
                    <li><span class="font-medium">Code:</span> {{ $goal->college_goals_code }}</li>
                    <li><span class="font-medium">Text:</span> {{ $goal->goal_text }}</li>
                </ul>
            </div>
            <p class="text-red-600 text-sm font-medium"><i class="bx bx-error"></i> This action cannot be undone. Goal codes will be automatically reindexed.</p>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'deleteGoalModal_' . $goal->id" text="Cancel" />

        <form action="{{ route('goal.destroy', $goal->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="table-danger">
                <i class="bx bx-trash"></i>
                Delete Goal
            </x-button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
