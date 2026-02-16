<x-modal.dialog id="updateEventModal_{{ $event->id }}" maxWidth="max-w-lg" width="w-11/12">
    <x-modal.header>
        Edit Event
        <x-modal.x-button :modalId="'updateEventModal_' . $event->id" />
    </x-modal.header>

    <form action="{{ route('academic.calendar.events.update', $event) }}" method="POST">
        @csrf
        @method('PUT')

        <x-modal.body>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1">Type</label>
                        <select name="type" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500">
                            <option value="holiday" {{ $event->type === 'holiday' ? 'selected' : '' }}>Holiday</option>
                            <option value="exam" {{ $event->type === 'exam' ? 'selected' : '' }}>Exam</option>
                            <option value="break" {{ $event->type === 'break' ? 'selected' : '' }}>Break</option>
                            <option value="other" {{ $event->type === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1">Date</label>
                        <input
                            type="date"
                            name="date"
                            value="{{ $event->date }}"
                            class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500"
                            min="{{ $event->calendar->start_date }}"
                            max="{{ $event->calendar->end_date }}"
                            required>
                        <p class="text-xs text-gray-500 mt-1">
                            Range: {{ \Carbon\Carbon::parse($event->calendar->start_date)->format('M j, Y') }}
                            - {{ \Carbon\Carbon::parse($event->calendar->end_date)->format('M j, Y') }}
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-sm text-gray-700 mb-1">Name</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ $event->name }}"
                        class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500"
                        required>
                </div>


            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'updateEventModal_' . $event->id" variant="cancel" text="Cancel" />

            <x-button type="submit" variant="save">
                <i class="bx bx-save"></i>
                Update Event
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>


{{-- UNUSED --}}
