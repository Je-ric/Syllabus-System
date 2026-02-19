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
                        <x-form.label class="block mb-1">Type</x-form.label>
                        <x-form.select name="type">
                            <option value="holiday" {{ $event->type === 'holiday' ? 'selected' : '' }}>Holiday</option>
                            <option value="exam" {{ $event->type === 'exam' ? 'selected' : '' }}>Exam</option>
                            <option value="break" {{ $event->type === 'break' ? 'selected' : '' }}>Break</option>
                            <option value="other" {{ $event->type === 'other' ? 'selected' : '' }}>Other</option>
                        </x-form.select>
                    </div>
                    <div>
                        <x-form.label class="block mb-1">Date</x-form.label>
                        <x-form.date-picker
                            name="date"
                            value="{{ $event->date }}"
                            min="{{ $event->calendar->start_date }}"
                            max="{{ $event->calendar->end_date }}"
                            required
                        />
                        <p class="text-xs text-gray-500 mt-1">
                            Range: {{ \Carbon\Carbon::parse($event->calendar->start_date)->format('M j, Y') }}
                            - {{ \Carbon\Carbon::parse($event->calendar->end_date)->format('M j, Y') }}
                        </p>
                    </div>
                </div>

                <div>
                    <x-form.label class="block mb-1">Name</x-form.label>
                    <x-form.input
                        type="text"
                        name="name"
                        value="{{ $event->name }}"
                        required
                    />
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
