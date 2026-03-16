<x-modal.dialog id="deleteAYModal_{{ str_replace('-', '_', $year) }}" maxWidth="max-w-xl" width="w-11/12">
    <x-modal.header>
        Confirm Delete Academic Year
        <x-modal.x-button :modalId="'deleteAYModal_' . str_replace('-', '_', $year)" />
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-3">
            <p class="text-gray-700">Are you sure you want to delete this academic year and all its semesters?</p>
            <div class="bg-gray-50 p-3 rounded border border-gray-200 text-sm">
                <p class="font-semibold mb-1">Academic Year Details</p>
                <ul class="space-y-1">
                    <li><span class="font-medium">Year:</span> {{ $year }}</li>
                    <li><span class="font-medium">Semesters:</span> {{ $semesters->count() }}</li>
                    @foreach ($semesters as $sem)
                        <li class="ml-4">
                            • {{ $sem->semester }} Semester:
                            {{ \Carbon\Carbon::parse($sem->start_date)->format('M j, Y') }}
                            – {{ \Carbon\Carbon::parse($sem->end_date)->format('M j, Y') }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <p class="text-red-600 text-sm font-medium">
                <i class="bx bx-error"></i> This action cannot be undone.
            </p>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'deleteAYModal_' . str_replace('-', '_', $year)" text="Cancel" />
        <form action="{{ route('academic.calendars.destroy', $year) }}" method="POST"
            class="w-full flex gap-2 justify-end">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="table-danger">
                <i class="bx bx-trash"></i> Delete A.Y.
            </x-button>
        </form>
    </x-modal.footer>
</x-modal.dialog>