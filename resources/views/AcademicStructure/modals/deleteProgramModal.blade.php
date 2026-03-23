<x-modal.dialog id="deleteProgramModal_{{ $program->id }}" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <h2 class="text-lg sm:text-xl font-bold text-red-600 flex items-center gap-2">
            <i class="bx bx-trash text-2xl"></i> Delete Program
        </h2>
    </x-modal.header>

    <x-modal.body>
        <div class="flex flex-col items-center text-center gap-4">
            <div class="bg-red-100 rounded-full w-12 h-12 flex items-center justify-center">
                <i class="bx bx-book-open text-2xl text-red-500"></i>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-red-700">Are you sure you want to delete this program?</h3>

            <div class="bg-gray-50 rounded-lg p-4 w-full text-left space-y-2">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700 text-sm">Program:</span>
                    <span class="text-sm text-gray-800">{{ $program->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700 text-sm">BOR No:</span>
                    <span class="text-sm text-gray-800">{{ $program->bor_approval_no }}</span>
                </div>
                @php $courseCount = $program->courses->count(); @endphp
                @if ($courseCount > 0)
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700 text-sm">Courses:</span>
                        <span class="text-sm font-semibold text-red-600">{{ $courseCount }}</span>
                    </div>
                @endif
            </div>

            <x-feedback-status.alert type="error"
                title="This will permanently delete the program and all its courses and syllabi." class="w-full" />
        </div>
    </x-modal.body>

    <x-modal.footer>
        <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
            <x-modal.close-button :modalId="'deleteProgramModal_' . $program->id" text="Cancel" variant="cancel" />
            <form action="{{ route('program.destroy', $program->id) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger" class="w-full sm:w-auto">
                    <i class="bx bx-trash"></i> Delete Program
                </x-button>
            </form>
        </div>
    </x-modal.footer>
</x-modal.dialog>
