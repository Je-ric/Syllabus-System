{{-- livewire/academic-calendar/event-form.blade.php --}}

<div
    x-data="{
        editingId: null,
        type:      'holiday',
        name:      '',
        date:      '',
        saving:    false,
        deletingId: null,
        confirmDeleteId: null,

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
            this.confirmDeleteId = null;
            this.deletingId = id;
            await $wire.deleteEvent(id);
            this.deletingId = null;
        }
    }"
    x-on:event-load-form.window="loadForm($event)"
    x-on:event-saved.window="reset()"
    class="grid grid-cols-1 lg:grid-cols-2 gap-6"
    style="align-items: start;">

    {{-- ══ LEFT: Add / Edit form (sticky) ══════════════════════════════════ --}}
    <div style="position: sticky; top: 5rem; align-self: flex-start;">

        {{-- Form header --}}
        <div class="flex items-center justify-between mb-3">
            <div>
                <span x-show="!editingId"
                    class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]">
                    New Event
                </span>
                <span x-show="editingId" x-cloak
                    class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">
                    <i class="bx bx-edit-alt text-xs leading-none"></i>
                    Editing Event
                </span>
            </div>
            <button type="button"
                x-show="editingId" x-cloak
                x-on:click="reset()"
                class="text-[13px] text-[#94a3b8] hover:text-[#0f172a] underline underline-offset-2 transition-colors">
                Cancel
            </button>
        </div>

        {{-- Form card --}}
        <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4 flex flex-col gap-3.5"
             style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

            {{-- Type --}}
            <div>
                <x-form.label isRequired>Type</x-form.label>
                <select x-model="type"
                    class="mt-1.5 w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                           focus:border-[#16a34a] focus:outline-none transition-colors"
                    style="box-shadow:none"
                    onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                    onblur="this.style.boxShadow='none'">
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
                    <p class="text-[13px] text-[#94a3b8] mb-1">
                        {{ \Carbon\Carbon::parse($semester->start_date)->format('M j') }}
                        – {{ \Carbon\Carbon::parse($semester->end_date)->format('M j, Y') }}
                    </p>
                @endif
                <input type="date" x-model="date"
                    min="{{ $semester?->start_date }}"
                    max="{{ $semester?->end_date }}"
                    class="w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                           hover:border-[#bbf7d0] focus:border-[#16a34a] focus:outline-none transition-colors"
                    style="box-shadow:none"
                    onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                    onblur="this.style.boxShadow='none'" />
                <p x-show="saving && !date" x-cloak class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> Date is required.
                </p>
                @error('date')
                    <p class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Name --}}
            <div>
                <x-form.label isRequired>Event Name</x-form.label>
                <input x-model="name" type="text"
                    placeholder="e.g. Christmas Break"
                    class="mt-1.5 w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                           placeholder:text-[#94a3b8] hover:border-[#bbf7d0] focus:border-[#16a34a] focus:outline-none transition-colors"
                    style="box-shadow:none"
                    onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                    onblur="this.style.boxShadow='none'" />
                <p x-show="saving && !name.trim()" x-cloak class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> Event name is required.
                </p>
                @error('name')
                    <p class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            @error('type')
                <p class="text-[13px] text-rose-500 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> {{ $message }}
                </p>
            @enderror

            {{-- Submit --}}
            <div class="flex justify-end mt-auto">
                <x-button type="button" variant="add-button"
                    x-on:click="submit()"
                    x-bind:disabled="saving">
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
    </div>

    {{-- ══ RIGHT: Events table ══════════════════════════════════════════════ --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]">
                {{ $semester?->semester }} Semester Events
            </p>
            @if($semester)
                <span class="text-[13px] text-[#94a3b8]">
                    {{ \Carbon\Carbon::parse($semester->start_date)->format('M j') }}
                    – {{ \Carbon\Carbon::parse($semester->end_date)->format('M j, Y') }}
                </span>
            @endif
        </div>

        <x-table.container>
            <x-table.table>
                <x-table.head>
                    <x-table.row>
                        <x-table.th>Date</x-table.th>
                        <x-table.th>Type</x-table.th>
                        <x-table.th>Name</x-table.th>
                        <x-table.th align="right">Actions</x-table.th>
                    </x-table.row>
                </x-table.head>

                <x-table.body>
                    @forelse($semester?->events ?? [] as $event)
                        @php
                            $typeVariant = match($event->type) {
                                'holiday'      => 'brand',
                                'exam'         => 'amber',
                                'break'        => 'lab',
                                'non_teaching' => 'rose',
                                default        => 'slate',
                            };
                        @endphp

                        <x-table.row striped hover
                            x-bind:class="editingId === {{ $event->id }} ? 'bg-[#f0fdf4]' : ''">

                            <x-table.td class="whitespace-nowrap">
                                <p class="text-[13px] font-medium text-[#0f172a]">
                                    {{ \Carbon\Carbon::parse($event->date)->format('M j, Y') }}
                                </p>
                                <p class="text-[11px] text-[#94a3b8]">
                                    {{ \Carbon\Carbon::parse($event->date)->format('l') }}
                                </p>
                            </x-table.td>

                            <x-table.td>
                                <x-feedback-status.status-indicator :variant="$typeVariant" :dot="true">
                                    {{ ucfirst(str_replace('_', ' ', $event->type)) }}
                                </x-feedback-status.status-indicator>
                            </x-table.td>

                            <x-table.td class="text-[#475569]">
                                {{ $event->name }}
                            </x-table.td>

                            <x-table.td align="right">
                                <div class="flex items-center justify-end gap-0.5">
                                    <button type="button"
                                        x-on:click="$dispatch('event-load-form', @js(['id' => $event->id, 'type' => $event->type, 'name' => $event->name, 'date' => $event->date]))"
                                        x-bind:disabled="deletingId !== null || saving"
                                        title="Edit"
                                        class="p-1.5 rounded-lg text-[#94a3b8] hover:text-[#1e40af] hover:bg-[#eff6ff] disabled:opacity-40 transition-colors">
                                        <i class="bx bx-edit-alt text-sm leading-none"></i>
                                    </button>
                                    <button type="button"
                                        x-on:click="remove({{ $event->id }})"
                                        x-bind:disabled="deletingId !== null || saving"
                                        title="Delete"
                                        class="p-1.5 rounded-lg disabled:opacity-40 transition-colors">
                                        <i x-show="deletingId !== {{ $event->id }}"
                                            class="bx bx-trash text-sm leading-none text-[#94a3b8] hover:text-rose-600"></i>
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
                        <x-table.empty :colspan="4" message="No events added yet." />
                    @endforelse
                </x-table.body>
            </x-table.table>
        </x-table.container>
    </div>

</div>
