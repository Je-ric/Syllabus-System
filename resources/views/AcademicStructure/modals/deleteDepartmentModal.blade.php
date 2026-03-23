<x-modal.dialog id="deleteDepartmentModal_{{ $dept->id }}" maxWidth="xl:max-w-xl lg:max-w-lg md:max-w-md sm:max-w-sm max-w-xs" width="w-full" maxHeight="max-h-[90vh]">
    <x-modal.header>
        <h2 class="text-lg sm:text-xl font-bold text-red-600 flex items-center gap-2">
            <i class="bx bx-trash text-2xl"></i> Delete Department
        </h2>
    </x-modal.header>

    <x-modal.body>
        <div class="flex flex-col items-center text-center gap-4">
            <div class="bg-red-100 rounded-full w-12 h-12 flex items-center justify-center">
                <i class="bx bx-building text-2xl text-red-500"></i>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-red-700">Are you sure you want to delete this department?</h3>

            <div class="bg-gray-50 rounded-lg p-4 w-full text-left space-y-2">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700 text-sm">Department:</span>
                    <span class="text-sm text-gray-800">{{ $dept->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700 text-sm">College:</span>
                    <span class="text-sm text-gray-800">{{ $dept->college->name }}</span>
                </div>
                @php $programCount = $dept->programs->count(); @endphp
                @if ($programCount > 0)
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700 text-sm">Programs:</span>
                        <span class="text-sm font-semibold text-red-600">{{ $programCount }}</span>
                    </div>
                @endif
            </div>

            <x-feedback-status.alert type="error"
                title="This will permanently delete the department and all its programs, courses, and syllabi." class="w-full" />
        </div>
    </x-modal.body>

    <x-modal.footer>
        <div class="flex gap-2 w-full justify-end flex-col sm:flex-row">
            <x-modal.close-button :modalId="'deleteDepartmentModal_' . $dept->id" text="Cancel" variant="cancel" />
            <form action="{{ route('department.destroy', $dept->id) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger" class="w-full sm:w-auto">
                    <i class="bx bx-trash"></i> Delete Department
                </x-button>
            </form>
        </div>
    </x-modal.footer>
</x-modal.dialog>
