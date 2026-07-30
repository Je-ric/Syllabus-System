{{-- livewire/academic-calendar/partials/event-modal.blade.php --}}

<div
    x-show="showForm"
    x-cloak
    x-on:keydown.escape.window="closeForm()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background:rgba(0,0,0,0.45);">

    <div
        x-show="showForm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-on:click.outside="closeForm()"
        class="w-full max-w-xl rounded-2xl bg-white shadow-2xl overflow-hidden">

        <div class="flex items-center justify-between px-5 py-4 border-b border-[#e2e8f0] bg-[#f8fafc]">
            <div>
                <p x-show="!editingId" class="text-[13px] font-bold text-[#0f172a]">Add Event</p>
                <p x-show="editingId" x-cloak class="text-[13px] font-bold text-[#0f172a] inline-flex items-center gap-1.5">
                    <i class="bx bx-edit-alt"></i> Edit Event
                </p>
                @php $semester = $this->semester; @endphp
                @if($semester)
                    <p class="text-[11px] text-[#94a3b8] mt-0.5">
                        Semester: {{ \Carbon\Carbon::parse($semester->start_date)->format('M j') }}
                        – {{ \Carbon\Carbon::parse($semester->end_date)->format('M j, Y') }}
                    </p>
                @endif
            </div>
            <button type="button" x-on:click="closeForm()"
                class="w-7 h-7 flex items-center justify-center rounded-lg text-[#94a3b8]
                       hover:bg-[#f1f5f9] hover:text-[#0f172a] transition-colors">
                <i class="bx bx-x text-lg"></i>
            </button>
        </div>

        <div class="p-5 flex flex-col gap-4">
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
                @error('type')
                    <p class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <x-form.label isRequired>
                    <span x-text="editingId ? 'Date' : 'Date Range'"></span>
                </x-form.label>

                <div class="mt-1.5 flex gap-3">
                    <div class="flex-1">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-[#94a3b8] mb-1"
                           x-show="!editingId">Start</p>
                        <input type="date" x-model="dateStart"
                            x-on:change="if (!editingId && (!dateEnd || dateEnd < dateStart)) dateEnd = dateStart"
                            min="{{ $semester?->start_date }}"
                            max="{{ $semester?->end_date }}"
                            class="w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                                   hover:border-[#bbf7d0] focus:border-[#16a34a] focus:outline-none transition-colors"
                            style="box-shadow:none"
                            onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                            onblur="this.style.boxShadow='none'" />
                    </div>

                    <template x-if="!editingId">
                        <div class="flex-1">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-[#94a3b8] mb-1">End</p>
                            <input type="date" x-model="dateEnd"
                                :min="dateStart || '{{ $semester?->start_date }}'"
                                max="{{ $semester?->end_date }}"
                                class="w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                                       hover:border-[#bbf7d0] focus:border-[#16a34a] focus:outline-none transition-colors"
                                style="box-shadow:none"
                                onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                                onblur="this.style.boxShadow='none'" />
                        </div>
                    </template>
                </div>

                <div x-show="isRange" x-cloak class="mt-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#166534] bg-[#dcfce7] rounded-full px-2.5 py-0.5">
                        <i class="bx bx-calendar-check text-xs"></i>
                        <span x-text="rangeLabel"></span>
                        <span class="text-[10px] font-normal text-[#16a34a]" x-text="'(' + datesInRange().length + ' days)'"></span>
                    </span>
                </div>

                <p x-show="hasAttempted && !dateStart" x-cloak class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> Date is required.
                </p>
                @error('date')
                    <p class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <x-form.label isRequired>Event Name</x-form.label>
                <input x-model="name" type="text"
                    placeholder="e.g. Christmas Break"
                    class="mt-1.5 w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                           placeholder:text-[#94a3b8] hover:border-[#bbf7d0] focus:border-[#16a34a] focus:outline-none transition-colors"
                    style="box-shadow:none"
                    onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                    onblur="this.style.boxShadow='none'" />
                <p x-show="hasAttempted && !name.trim()" x-cloak class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                    <i class="bx bx-error-circle"></i> Event name is required.
                </p>
                @error('name')
                    <p class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-[#e2e8f0] bg-[#f8fafc]">
            <button type="button" x-on:click="closeForm()"
                class="px-4 py-2 rounded-lg text-[13px] font-semibold text-[#475569]
                       border border-[#e2e8f0] bg-white hover:bg-[#f1f5f9] transition-colors">
                Cancel
            </button>
            <x-ui.button type="button" variant="add-button"
                x-on:click="submit()"
                x-bind:disabled="saving">
                <span x-show="!saving" class="inline-flex items-center gap-1.5">
                    <i x-show="editingId"  class="bx bx-save leading-none"></i>
                    <i x-show="!editingId" class="bx bx-plus leading-none"></i>
                    <span x-text="editingId ? 'Update Event' : (isRange ? 'Add ' + datesInRange().length + ' Events' : 'Add Event')"></span>
                </span>
                <span x-show="saving" x-cloak class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Saving…
                </span>
            </x-ui.button>
        </div>
    </div>
</div>
