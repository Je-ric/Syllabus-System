<x-modal.dialog id="addDepartmentModal" maxWidth="max-w-xl" width="w-11/12">
    <x-modal.header>
        Add New Department
        <x-modal.x-button :modalId="'addDepartmentModal'" />
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-3">
            <p class="text-gray-700">Please review before creating:</p>
            <div class="bg-gray-50 p-3 rounded border border-gray-200">
                <p class="font-semibold text-sm">Goal Details:</p>
                {{-- <ul class="text-sm mt-2 space-y-1">
                    <li><span class="font-medium">Code:</span> {{ $goal->college_goals_code }}</li>
                    <li><span class="font-medium">Text:</span> {{ $goal->goal_text }}</li>
                </ul> --}}
            </div>
            <p class="text-red-600 text-sm font-medium"><i class="bx bx-error"></i> This action cannot be undone. Goal codes will be automatically reindexed.</p>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'addDepartmentModal'" text="Cancel" />


    </x-modal.footer>
</x-modal.dialog>
