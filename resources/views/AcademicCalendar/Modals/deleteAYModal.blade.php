<x-modal.dialog id="deleteAYModal_{{ str_replace('-', '_', $year) }}" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <h2 class="text-lg sm:text-xl font-bold text-red-600 flex items-center gap-2">
            <i class="bx bx-trash text-2xl"></i>
            Delete Academic Year
        </h2>
    </x-modal.header>

    <x-modal.body>
        <div class="flex flex-col items-center text-center gap-4">
            <div class="bg-red-100 rounded-full w-12 h-12 flex items-center justify-center">
                <i class="bx bx-trash text-2xl text-red-500"></i>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-red-700">Are you sure you want to delete this academic year?</h3>
            <p class="text-sm text-gray-600">All semesters under this academic year will also be permanently removed.</p>

            <div class="bg-gray-50 rounded-lg p-4 w-full text-left">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700 text-sm">Academic Year:</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $year }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700 text-sm">Semesters:</span>
                        <span class="text-sm text-gray-800">{{ $semesters->count() }}</span>
                    </div>
                    @foreach ($semesters as $sem)
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500 ml-2">• {{ $sem->semester }} Semester</span>
                            <span class="text-xs text-gray-600">
                                {{ \Carbon\Carbon::parse($sem->start_date)->format('M j, Y') }}
                                – {{ \Carbon\Carbon::parse($sem->end_date)->format('M j, Y') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <x-feedback-status.alert type="error" title="This action cannot be undone." class="w-full" />
        </div>
    </x-modal.body>

    <x-modal.footer>
        <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
            <x-modal.close-button :modalId="'deleteAYModal_' . str_replace('-', '_', $year)" text="Cancel" variant="cancel" />
            <form action="{{ route('academic.calendars.destroy', $year) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger" class="w-full sm:w-auto">
                    <i class="bx bx-trash"></i> Delete A.Y.
                </x-button>
            </form>
        </div>
    </x-modal.footer>
</x-modal.dialog>
