<x-modal.dialog id="deleteEventModal_{{ $event->id }}" maxWidth="max-w-md" width="w-11/12">
    <x-modal.header>
        Confirm Delete Event
        <x-modal.x-button :modalId="'deleteEventModal_' . $event->id" />
    </x-modal.header>

    <x-modal.body>
        <div class="space-y-3">
            <p class="text-gray-700">Are you sure you want to delete this event?</p>
            <div class="bg-gray-50 p-3 rounded border border-gray-200">
                <p class="font-semibold text-sm">Event Details:</p>
                <ul class="text-sm mt-2 space-y-1">
                    <li><span class="font-medium">Type:</span> {{ ucfirst($event->type) }}</li>
                    <li><span class="font-medium">Name:</span> {{ $event->name }}</li>
                    <li><span class="font-medium">Date:</span>
                        {{ \Carbon\Carbon::parse($event->date)->format('F j, Y') }}</li>
                </ul>
            </div>
            <p class="text-red-600 text-sm font-medium">This action cannot be undone.</p>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button :modalId="'deleteEventModal_' . $event->id" text="Cancel" />

        <form action="{{ route('academic.calendar.events.destroy', $event) }}" method="POST"
            class="w-full flex gap-2 justify-end">
            @csrf
            @method('DELETE')

            <x-button type="submit" variant="table-danger">
                <i class="bx bx-trash"></i>
                Delete
            </x-button>
        </form>
    </x-modal.footer>
</x-modal.dialog>
