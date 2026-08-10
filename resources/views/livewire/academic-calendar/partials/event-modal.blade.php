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
        x-on:click.outside="if (!document.querySelector('.flatpickr-calendar.open')) closeForm()"
        class="w-full max-w-4xl rounded-2xl bg-white shadow-2xl overflow-hidden">

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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Left Column: Event Type Selection --}}
                <div class="space-y-4">
                    <x-form.label isRequired>Type</x-form.label>
                    
                    {{-- Quick Type Buttons - Reference Events --}}
                    <div class="space-y-2 mb-3">
                        <p class="text-[11px] font-semibold text-[#71717a] mb-1.5">Reference Events</p>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" 
                                x-on:click="type = 'holiday'; name = 'Class Suspension'"
                                class="text-[11px] px-2 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 flex items-center gap-1">
                                <i class="bx bx-info-circle text-xs"></i> Suspension
                            </button>
                            <button type="button" 
                                x-on:click="type = 'other'; name = 'Department Meeting'"
                                class="text-[11px] px-2 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 flex items-center gap-1">
                                <i class="bx bx-info-circle text-xs"></i> Meeting
                            </button>
                        </div>
                    </div>

                    {{-- Quick Type Buttons - Skip Events --}}
                    <div class="space-y-2 mb-3">
                        <p class="text-[11px] font-semibold text-[#71717a] mb-1.5">Skip Events</p>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" 
                                x-on:click="type = 'break'; name = 'Christmas Break'"
                                class="text-[11px] px-2 py-1 rounded bg-amber-100 text-amber-700 hover:bg-amber-200 flex items-center gap-1">
                                <i class="bx bx-skip-next text-xs"></i> Christmas Break
                            </button>
                            <button type="button" 
                                x-on:click="type = 'break'; name = 'Semester Break'"
                                class="text-[11px] px-2 py-1 rounded bg-amber-100 text-amber-700 hover:bg-amber-200 flex items-center gap-1">
                                <i class="bx bx-skip-next text-xs"></i> Semester Break
                            </button>
                            <button type="button" 
                                x-on:click="type = 'break'; name = 'Health Break'"
                                class="text-[11px] px-2 py-1 rounded bg-amber-100 text-amber-700 hover:bg-amber-200 flex items-center gap-1">
                                <i class="bx bx-skip-next text-xs"></i> Health Break
                            </button>
                        </div>
                    </div>

                    {{-- Quick Type Buttons - Lock Events --}}
                    <div class="space-y-2 mb-3">
                        <p class="text-[11px] font-semibold text-[#71717a] mb-1.5">Lock Events</p>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" 
                                x-on:click="type = 'exam'; name = 'First Term Exam'"
                                class="text-[11px] px-2 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 flex items-center gap-1">
                                <i class="bx bx-lock text-xs"></i> 1st Term Exam
                            </button>
                            <button type="button" 
                                x-on:click="type = 'exam'; name = 'Midterm Exam'"
                                class="text-[11px] px-2 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 flex items-center gap-1">
                                <i class="bx bx-lock text-xs"></i> Midterm Exam
                            </button>
                            <button type="button" 
                                x-on:click="type = 'exam'; name = 'Final Exam'"
                                class="text-[11px] px-2 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 flex items-center gap-1">
                                <i class="bx bx-lock text-xs"></i> Final Exam
                            </button>
                            <button type="button" 
                                x-on:click="type = 'non_teaching'; name = 'Institutional Event'"
                                class="text-[11px] px-2 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 flex items-center gap-1">
                                <i class="bx bx-lock text-xs"></i> Non-Teaching
                            </button>
                        </div>
                    </div>

                    <select x-model="type"
                        class="w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                               focus:border-[#16a34a] focus:outline-none transition-colors"
                        style="box-shadow:none"
                        onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                        onblur="this.style.boxShadow='none'">
                        <option value="holiday">📌 Holiday (Reference)</option>
                        <option value="other">📌 Other (Reference)</option>
                        <option value="break">⏭️ Break (Skip Week)</option>
                        <option value="exam">🔒 Exam (Lock Week)</option>
                        <option value="non_teaching">🔒 Non-Teaching (Lock Week)</option>
                    </select>

                    {{-- Dynamic guidance based on selection --}}
                    <div x-show="type === 'holiday'" x-cloak class="mt-2 text-[11px] text-blue-600 flex items-center gap-1">
                        <i class="bx bx-info-circle"></i> Reference only: Week will be created and editable by faculty.
                    </div>
                    <div x-show="type === 'other'" x-cloak class="mt-2 text-[11px] text-blue-600 flex items-center gap-1">
                        <i class="bx bx-info-circle"></i> Reference only: Week will be created and editable by faculty.
                    </div>
                    <div x-show="type === 'break'" x-cloak class="mt-2 text-[11px] text-amber-600 flex items-center gap-1">
                        <i class="bx bx-skip-next"></i> This will SKIP the week entirely. No syllabus week will be created.
                    </div>
                    <div x-show="type === 'exam'" x-cloak class="mt-2 text-[11px] text-red-600 flex items-center gap-1">
                        <i class="bx bx-lock"></i> This will LOCK the week as "Exam Week". Faculty cannot edit it.
                    </div>
                    <div x-show="type === 'non_teaching'" x-cloak class="mt-2 text-[11px] text-red-600 flex items-center gap-1">
                        <i class="bx bx-lock"></i> This will LOCK the week as "Non-Teaching Week". Faculty cannot edit it.
                    </div>

                    @error('type')
                        <p class="mt-1 text-[13px] text-rose-500 flex items-center gap-1">
                            <i class="bx bx-error-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Right Column: Date and Name --}}
                <div class="space-y-4">
                    <div>
                        <x-form.label isRequired>
                            <span x-text="editingId ? 'Date' : 'Date Range'"></span>
                        </x-form.label>

                        <div class="mt-1.5 flex gap-3">
                            {{-- Start date (always shown) --}}
                            <div class="flex-1">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-[#94a3b8] mb-1"
                                   x-show="!editingId">Start</p>
                                <input type="text" id="ev-date-start-{{ $semesterId }}" readonly
                                    placeholder="Select a date"
                                    class="w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                                           placeholder:text-[#94a3b8] hover:border-[#bbf7d0] focus:border-[#16a34a] focus:outline-none
                                           transition-colors cursor-pointer"
                                    style="box-shadow:none"
                                    onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                                    onblur="this.style.boxShadow='none'" />
                            </div>

                            {{-- End date (hidden when editing a single event) --}}
                            <div class="flex-1" x-show="!editingId" x-cloak>
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-[#94a3b8] mb-1">End</p>
                                <input type="text" id="ev-date-end-{{ $semesterId }}" readonly
                                    placeholder="Select a date"
                                    class="w-full rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-[13px] text-[#0f172a]
                                           placeholder:text-[#94a3b8] hover:border-[#bbf7d0] focus:border-[#16a34a] focus:outline-none
                                           transition-colors cursor-pointer"
                                    style="box-shadow:none"
                                    onfocus="this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.25)'"
                                    onblur="this.style.boxShadow='none'" />
                            </div>
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
