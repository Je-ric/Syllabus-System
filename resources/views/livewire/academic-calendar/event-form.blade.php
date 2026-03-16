{{--
    livewire/academic-calendar/event-form.blade.php
    ────────────────────────────────────────────────
    Rendered by App\Livewire\AcademicCalendar\AcademicCalendarEventForm.
    One instance mounted per semester tab.
    Handles both ADD (inline form) and EDIT (modal).
--}}

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
    {{-- ══ Add Event form ════════════════════════════════════════════════════ --}}
    <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl shadow-sm">
        <h2 class="font-semibold text-slate-800">Add Event</h2>

        <div class="grid grid-cols-2 gap-4">

            {{-- Type --}}
            <div>
                <x-form.label>Type</x-form.label>
                <x-form.select wire:model.live="type" class="mt-2">
                    <option value="holiday">Holiday</option>
                    <option value="exam">Exam</option>
                    <option value="break">Break</option>
                    <option value="other">Other</option>
                    <option value="non_teaching">Non-Teaching</option>
                </x-form.select>
                @error('type')
                    <p class="mt-1 flex items-center gap-1 text-xs text-rose-600">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Date --}}
            <div>
                <x-form.label isRequired>Date</x-form.label>
                <x-form.date-picker
                    name="add_date_{{ $semesterId }}"
                    wire:model.blur="date"
                    :value="$date"
                    :min="$semester?->start_date"
                    :max="$semester?->end_date"
                    class="mt-2" />
                @error('date')
                    <p class="mt-1 flex items-center gap-1 text-xs text-rose-600">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
                @if($semester)
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ \Carbon\Carbon::parse($semester->start_date)->format('M j') }}
                        – {{ \Carbon\Carbon::parse($semester->end_date)->format('M j, Y') }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Name --}}
        <div>
            <x-form.label isRequired>Name</x-form.label>
            <x-form.input
                wire:model.blur="name"
                :value="$name"
                placeholder="e.g. Christmas Break, Midterm Exam"
                class="mt-2" />
            @error('name')
                <p class="mt-1 flex items-center gap-1 text-xs text-rose-600">
                    <i class="bx bx-error-circle"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <div class="mt-4 flex justify-end">
            <x-button type="button" variant="save" wire:click="store" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="store"><i class="bx bx-plus"></i> Add Event</span>
                <span wire:loading wire:target="store">Adding…</span>
            </x-button>
        </div>
    </div>

    {{-- ══ Events list ════════════════════════════════════════════════════════ --}}
    <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl shadow-sm">
        <h2 class="font-semibold text-slate-800 mb-2">
            Events for {{ $semester?->semester }} Semester
        </h2>
        @if($semester)
            <p class="text-slate-500 text-sm mb-3">
                {{ \Carbon\Carbon::parse($semester->start_date)->format('F j, Y') }}
                – {{ \Carbon\Carbon::parse($semester->end_date)->format('F j, Y') }}
            </p>
        @endif

        <x-table.table class="border border-slate-200">
            <x-table.head>
                <tr class="bg-emerald-50 text-emerald-800">
                    <x-table.th class="px-3 py-2">Date</x-table.th>
                    <x-table.th class="px-3 py-2">Type</x-table.th>
                    <x-table.th class="px-3 py-2">Name</x-table.th>
                    <x-table.th class="px-3 py-2">Action</x-table.th>
                </tr>
            </x-table.head>
            <x-table.body>
                @forelse($semester?->events->sortBy('date') ?? [] as $event)
                    @php
                        $typeVariant = match($event->type) {
                            'holiday'      => 'emerald',
                            'exam'         => 'amber',
                            'break'        => 'blue',
                            'non_teaching' => 'rose',
                            default        => 'slate',
                        };
                    @endphp
                    <x-table.row striped hover>
                        <x-table.td class="px-3 py-2">
                            {{ \Carbon\Carbon::parse($event->date)->format('F j, Y') }}
                        </x-table.td>
                        <x-table.td class="px-3 py-2">
                            <x-feedback-status.status-indicator :variant="$typeVariant" size="sm" :dot="true">
                                {{ str_replace('_', ' ', (string) $event->type) }}
                            </x-feedback-status.status-indicator>
                        </x-table.td>
                        <x-table.td class="px-3 py-2">{{ $event->name }}</x-table.td>
                        <x-table.td class="px-3 py-2">
                            <div class="flex gap-2">
                                {{-- Edit button — calls Livewire startEdit() --}}
                                <x-button type="button" variant="table-edit" title="Edit"
                                    wire:click="startEdit({{ $event->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="startEdit({{ $event->id }})">
                                    <i class="bx bx-edit"></i>
                                </x-button>

                                {{-- Delete button — opens native confirm dialog modal --}}
                                <x-button type="button" variant="table-danger" title="Delete"
                                    onclick="document.getElementById('deleteEventModal_{{ $event->id }}').showModal()">
                                    <i class="bx bx-trash"></i>
                                </x-button>
                            </div>

                            {{-- Delete modal (plain HTML, no Livewire needed) --}}
                            <x-modal.dialog id="deleteEventModal_{{ $event->id }}" maxWidth="max-w-md" width="w-11/12">
                                <x-modal.header>
                                    Confirm Delete Event
                                    <x-modal.x-button :modalId="'deleteEventModal_' . $event->id" />
                                </x-modal.header>
                                <x-modal.body>
                                    <div class="space-y-3">
                                        <p class="text-gray-700">Are you sure you want to delete this event?</p>
                                        <div class="bg-gray-50 p-3 rounded border border-gray-200 text-sm">
                                            <p class="font-semibold mb-1">Event Details</p>
                                            <ul class="space-y-1">
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
                                        class="flex gap-2 justify-end">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" variant="table-danger">
                                            <i class="bx bx-trash"></i> Delete
                                        </x-button>
                                    </form>
                                </x-modal.footer>
                            </x-modal.dialog>
                        </x-table.td>
                    </x-table.row>
                @empty
                    <x-table.empty :colspan="4" message="No events yet." class="px-3 py-2" />
                @endforelse
            </x-table.body>
        </x-table.table>
    </div>

    {{-- ══ Edit Event Modal (Livewire-driven) ═══════════════════════════════ --}}
    @if ($showEditModal)
        <x-modal.dialog id="editEventModal_{{ $semesterId }}" maxWidth="max-w-lg" width="w-11/12"
            x-data
            x-init="$el.showModal()"
            x-on:close="$wire.closeEdit()">

            <x-modal.header>
                Edit Event
                <x-modal.x-button :modalId="'editEventModal_' . $semesterId" />
            </x-modal.header>

            <x-modal.body>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                        {{-- Type --}}
                        <div>
                            <x-form.label>Type</x-form.label>
                            <x-form.select wire:model.live="type" class="mt-2">
                                <option value="holiday">Holiday</option>
                                <option value="exam">Exam</option>
                                <option value="break">Break</option>
                                <option value="non_teaching">Non-Teaching</option>
                                <option value="other">Other</option>
                            </x-form.select>
                            @error('type')
                                <p class="mt-1 flex items-center gap-1 text-xs text-rose-600">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Date --}}
                        <div>
                            <x-form.label isRequired>Date</x-form.label>
                            <x-form.date-picker
                                name="edit_date_{{ $semesterId }}"
                                wire:model.blur="date"
                                :value="$date"
                                :min="$semester?->start_date"
                                :max="$semester?->end_date"
                                class="mt-2" />
                            @error('date')
                                <p class="mt-1 flex items-center gap-1 text-xs text-rose-600">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                            @if($semester)
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($semester->start_date)->format('M j') }}
                                    – {{ \Carbon\Carbon::parse($semester->end_date)->format('M j, Y') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Name --}}
                    <div>
                        <x-form.label isRequired>Name</x-form.label>
                        <x-form.input
                            wire:model.blur="name"
                            :value="$name"
                            class="mt-2" />
                        @error('name')
                            <p class="mt-1 flex items-center gap-1 text-xs text-rose-600">
                                <i class="bx bx-error-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </x-modal.body>

            <x-modal.footer>
                <x-modal.close-button :modalId="'editEventModal_' . $semesterId"
                    variant="cancel" text="Cancel"
                    wire:click="closeEdit" />

                <x-button type="button" variant="save"
                    wire:click="update" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="update"><i class="bx bx-save"></i> Update Event</span>
                    <span wire:loading wire:target="update">Saving…</span>
                </x-button>
            </x-modal.footer>
        </x-modal.dialog>
    @endif
</div>