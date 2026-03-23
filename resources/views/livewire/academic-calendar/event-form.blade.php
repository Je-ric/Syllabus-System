{{--
    livewire/academic-calendar/event-form.blade.php
    ────────────────────────────────────────────────
    Two-column: form left, events table right.
    Alpine owns all form state — edit loads instantly (zero server call).
    Only saveEvent() and deleteEvent() hit the server.
--}}

<div
    x-data="{
        editingId: null,
        type:      'holiday',
        name:      '',
        date:      '',
        saving:    false,
        deletingId: null,

        loadForm(e) {
            this.editingId = e.detail.id;
            this.type      = e.detail.type;
            this.name      = e.detail.name;
            this.date      = e.detail.date;
        },

        reset() {
            this.editingId = null;
            this.type      = 'holiday';
            this.name      = '';
            this.date      = '';
            this.saving    = false;
        },

        async submit() {
            if (!this.name.trim() || !this.date) return;
            this.saving = true;
            await $wire.saveEvent(this.editingId, this.type, this.name, this.date);
            this.saving = false;
        },

        async remove(id) {
            this.deletingId = id;
            await $wire.deleteEvent(id);
            this.deletingId = null;
        }
    }"
    x-on:event-load-form.window="loadForm($event)"
    x-on:event-saved.window="reset()"
    class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

    {{-- ══ LEFT: Add / Edit form ══════════════════════════════════════════ --}}
    <div>
        {{-- Form header --}}
        <div class="flex items-center justify-between mb-3">
            <div>
                <span x-show="!editingId"
                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                    New Event
                </span>
                <span x-show="editingId" x-cloak
                    class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-amber-600">
                    <i class="bx bx-edit-alt text-xs leading-none"></i>
                    Editing Event
                </span>
            </div>
            <button type="button"
                x-show="editingId" x-cloak
                x-on:click="reset()"
                class="text-xs text-slate-400 hover:text-slate-700 underline underline-offset-2 transition-colors">
                Cancel
            </button>
        </div>

        {{-- Form card — border changes when editing --}}
        <div class="rounded-xl border p-4 space-y-3.5 transition-colors duration-150"
            x-bind:class="editingId
                ? 'border-amber-200 bg-amber-50/60'
                : 'border-slate-200 bg-slate-50/60'">

            {{-- Type --}}
            <div>
                <x-form.label isRequired>Type</x-form.label>
                <select x-model="type"
                    class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none transition-colors">
                    <option value="holiday">Holiday</option>
                    <option value="exam">Exam</option>
                    <option value="break">Break</option>
                    <option value="non_teaching">Non-Teaching</option>
                    <option value="other">Other</option>
                </select>
            </div>

            {{-- Date --}}
            <div>
                <x-form.label isRequired>Date</x-form.label>
                @if($semester)
                    <p class="text-[10px] text-slate-400 mb-1">
                        {{ \Carbon\Carbon::parse($semester->start_date)->format('M j') }}
                        – {{ \Carbon\Carbon::parse($semester->end_date)->format('M j, Y') }}
                    </p>
                @endif
                <input type="date" x-model="date"
                    min="{{ $semester?->start_date }}"
                    max="{{ $semester?->end_date }}"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm hover:border-slate-400 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none transition-colors" />
                <p x-show="saving && !date" x-cloak class="mt-1 text-xs text-rose-500 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> Date is required.
                </p>
                @error('date')
                    <p class="mt-1 text-xs text-rose-500 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Name --}}
            <div>
                <x-form.label isRequired>Event Name</x-form.label>
                <input x-model="name" type="text"
                    placeholder="e.g. Christmas Break"
                    class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 hover:border-slate-400 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 focus:outline-none transition-colors" />
                <p x-show="saving && !name.trim()" x-cloak class="mt-1 text-xs text-rose-500 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> Event name is required.
                </p>
                @error('name')
                    <p class="mt-1 text-xs text-rose-500 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            @error('type')
                <p class="text-xs text-rose-500 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> {{ $message }}
                </p>
            @enderror

            {{-- Submit button --}}
            <x-button type="button" variant="add-button"
                x-on:click="submit()"
                x-bind:disabled="saving"
                x-bind:class="editingId ? 'bg-amber-500! hover:!bg-amber-600! focus:!ring-amber-400/30!' : ''">
                <span x-show="!saving" class="inline-flex items-center gap-2">
                    <i x-show="editingId"  class="bx bx-save leading-none"></i>
                    <i x-show="!editingId" class="bx bx-plus leading-none"></i>
                    <span x-text="editingId ? 'Update Event' : 'Add Event'"></span>
                </span>
                <span x-show="saving" x-cloak class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Saving…
                </span>
            </x-button>
        </div>
    </div>

    {{-- ══ RIGHT: Events table ═════════════════════════════════════════════ --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                {{ $semester?->semester }} Semester Events
            </p>
            @if($semester)
                <span class="text-xs text-slate-500">
                    {{ \Carbon\Carbon::parse($semester->start_date)->format('M j') }}
                    – {{ \Carbon\Carbon::parse($semester->end_date)->format('M j, Y') }}
                </span>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <x-table.container>
                <x-table.table>
                    <x-table.head>
                        <x-table.row>
                            <x-table.th class="px-4 py-2.5">Date</x-table.th>
                            <x-table.th class="px-4 py-2.5">Type</x-table.th>
                            <x-table.th class="px-4 py-2.5">Name</x-table.th>
                            <x-table.th class="px-4 py-2.5 text-right">Actions</x-table.th>
                        </x-table.row>
                    </x-table.head>

                    <x-table.body>
                        @forelse($semester?->events ?? [] as $event)
                            @php
                                $typeVariant = match($event->type) {
                                    'holiday'      => 'emerald',
                                    'exam'         => 'amber',
                                    'break'        => 'blue',
                                    'non_teaching' => 'rose',
                                    default        => 'slate',
                                };
                            @endphp

                            <x-table.row striped hover
                                x-bind:class="editingId === {{ $event->id }} ? 'bg-amber-50!' : ''">

                                <x-table.td class="px-4 py-2.5 text-sm text-slate-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($event->date)->format('M j, Y') }}
                                    <span class="block text-[10px] text-slate-400">
                                        {{ \Carbon\Carbon::parse($event->date)->format('l') }}
                                    </span>
                                </x-table.td>

                                <x-table.td class="px-4 py-2.5">
                                    <x-feedback-status.status-indicator :variant="$typeVariant" size="sm" :dot="true">
                                        {{ ucfirst(str_replace('_', ' ', $event->type)) }}
                                    </x-feedback-status.status-indicator>
                                </x-table.td>

                                <x-table.td class="px-4 py-2.5 text-sm text-slate-700">
                                    {{ $event->name }}
                                </x-table.td>

                                <x-table.td class="px-4 py-2.5">
                                    <div class="flex items-center justify-end gap-0.5">

                                        {{-- Edit — pure Alpine, zero server call --}}
                                        <button type="button"
                                            x-on:click="$dispatch('event-load-form', @js(['id' => $event->id, 'type' => $event->type, 'name' => $event->name, 'date' => $event->date]))"
                                            x-bind:disabled="deletingId !== null || saving"
                                            title="Edit"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 disabled:opacity-40 transition-colors">
                                            <i class="bx bx-edit-alt text-sm leading-none"></i>
                                        </button>

                                        {{-- Delete --}}
                                        <button type="button"
                                            x-on:click="remove({{ $event->id }})"
                                            x-bind:disabled="deletingId !== null || saving"
                                            title="Delete"
                                            class="p-1.5 rounded-lg disabled:opacity-40 transition-colors">
                                            <i x-show="deletingId !== {{ $event->id }}"
                                                class="bx bx-trash text-sm leading-none text-slate-400 hover:text-rose-600"></i>
                                            <svg x-show="deletingId === {{ $event->id }}" x-cloak
                                                class="animate-spin h-3.5 w-3.5 text-rose-400"
                                                viewBox="0 0 24 24" fill="none">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </x-table.td>
                            </x-table.row>
                        @empty
                            <x-table.empty :colspan="4" message="No events added yet." class="py-8" />
                        @endforelse
                    </x-table.body>
                </x-table.table>
            </x-table.container>
        </div>
    </div>

</div>
