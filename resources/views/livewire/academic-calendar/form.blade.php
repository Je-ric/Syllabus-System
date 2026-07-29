{{--
    livewire/academic-calendar/form.blade.php
    ─────────────────────────────────────────
    CREATE / EDIT academic calendar.
    - Academic Year: plain text input (YYYY-YYYY format)
    - Semester dates: flatpickr range picker per semester
      (start + end in one picker, human-readable)
    - Real-time validation on blur/change via wire:model.blur
--}}

<div>
    {{-- ── Edit: current values notice ───────────────────────────────────── --}}
    @if ($isEdit)
        <x-feedback-status.alert type="warning" :showTitle="false" class="mb-5">
            <strong>Editing A.Y. {{ $academicYear }}</strong> — Changes will apply to both semesters. Events are not affected.
        </x-feedback-status.alert>
    @endif

    {{-- ── #11 Stale weeks warning ─────────────────────────────────────────── --}}
    @if ($showStaleWeeksWarning)
        <x-feedback-status.alert type="error" title="Syllabi with generated weeks exist for this calendar." class="mb-5">
            Changing the semester dates will make existing weekly coverage dates stale. Faculty will need to regenerate their weeks manually inside the syllabus wizard.
            <div class="flex gap-2 mt-3">
                <x-ui.button type="button" variant="danger" wire:click="update" wire:loading.attr="disabled">
                    <i class="bx bx-check"></i> Proceed Anyway
                </x-ui.button>
                <x-ui.button type="button" variant="cancel" wire:click="cancelStaleWarning">Cancel</x-ui.button>
            </div>
        </x-feedback-status.alert>
    @endif

    {{-- ── Academic Year ───────────────────────────────────────────────────── --}}
    <div class="mb-5 rounded-xl border border-[#e2e8f0] bg-white p-5" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
        <x-form.label for="academic_year" isRequired>Academic Year</x-form.label>
        <p class="text-[13px] text-[#94a3b8] mb-2">Format: YYYY-YYYY &nbsp;&middot;&nbsp; e.g. 2025-2026</p>

        <x-form.input
            wire:model.live.debounce.400ms="academic_year"
            id="academic_year"
            placeholder="e.g. 2025-2026"
            class="max-w-xs" />

        @error('academic_year')
            <p class="mt-1.5 flex items-center gap-1 text-[13px] text-rose-600">
                <i class="bx bx-error-circle"></i> {{ $message }}
            </p>
        @else
            @if ($isAcademicYearValid)
                <p class="mt-1.5 flex items-center gap-1 text-[13px] text-[#16a34a]">
                    <i class="bx bx-check-circle"></i> Looks good
                </p>
            @endif
        @enderror
    </div>

    {{-- ── Semester date ranges ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">

        {{-- 1st Semester --}}
        <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 flex flex-col gap-4" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
            <h3 class="text-[15px] font-bold text-[#0f172a] flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[13px] font-bold shrink-0">1</span>
                1st Semester
            </h3>

            {{-- Range picker: start --}}
            <div>
                <x-form.label isRequired>Start Date</x-form.label>
                <x-form.input type="date"
                    wire:model.blur="start_date_1"
                    :value="$start_date_1"
                    class="mt-1.5" />
                @error('start_date_1')
                    <p class="mt-1 flex items-center gap-1 text-xs text-rose-600">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Range picker: end --}}
            <div>
                <x-form.label isRequired>End Date</x-form.label>
                <x-form.input type="date"
                    wire:model.blur="end_date_1"
                    :value="$end_date_1"
                    :min="$start_date_1 ?: null"
                    class="mt-1.5" />
                @error('end_date_1')
                    <p class="mt-1 flex items-center gap-1 text-xs text-rose-600">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            @if ($start_date_1 && $end_date_1 && !$errors->has('start_date_1') && !$errors->has('end_date_1'))
                <p class="text-[13px] text-[#16a34a] flex items-center gap-1 mt-auto">
                    <i class="bx bx-check-circle"></i>
                    {{ \Carbon\Carbon::parse($start_date_1)->format('M j') }} – {{ \Carbon\Carbon::parse($end_date_1)->format('M j, Y') }}
                </p>
            @endif
        </div>

        <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 flex flex-col gap-4" style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
            <h3 class="text-[15px] font-bold text-[#0f172a] flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#eff6ff] text-[#1e40af] text-[13px] font-bold shrink-0">2</span>
                2nd Semester
            </h3>

            <div>
                <x-form.label isRequired>Start Date</x-form.label>
                <x-form.input type="date"
                    wire:model.blur="start_date_2"
                    :value="$start_date_2"
                    :min="$end_date_1 ?: null"
                    class="mt-1.5" />
                @error('start_date_2')
                    <p class="mt-1 flex items-center gap-1 text-xs text-rose-600">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <x-form.label isRequired>End Date</x-form.label>
                <x-form.input type="date"
                    wire:model.blur="end_date_2"
                    :value="$end_date_2"
                    :min="$start_date_2 ?: null"
                    class="mt-1.5" />
                @error('end_date_2')
                    <p class="mt-1 flex items-center gap-1 text-xs text-rose-600">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            @if ($start_date_2 && $end_date_2 && !$errors->has('start_date_2') && !$errors->has('end_date_2'))
                <p class="text-[13px] text-[#16a34a] flex items-center gap-1 mt-auto">
                    <i class="bx bx-check-circle"></i>
                    {{ \Carbon\Carbon::parse($start_date_2)->format('M j') }} – {{ \Carbon\Carbon::parse($end_date_2)->format('M j, Y') }}
                </p>
            @endif
        </div>
    </div>

    {{-- ── Actions ─────────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap justify-end gap-2">
        @if ($isEdit)
            <x-ui.button type="button" variant="save"
                wire:click="update"
                wire:loading.attr="disabled"
                wire:target="update"
                loading="Saving…">
                <i class="bx bx-save"></i> Update Calendar
            </x-ui.button>

            <x-ui.button type="button" variant="cancel"
                x-data
                x-on:click="$dispatch('open-cancel-edit-modal')">
                <i class="bx bx-x"></i> Cancel
            </x-ui.button>
        @else
            <x-ui.button type="button" variant="save"
                wire:click="requestCreate"
                wire:loading.attr="disabled"
                wire:target="requestCreate"
                loading="Validating…">
                <i class="bx bx-save"></i> Create Calendar
            </x-ui.button>
        @endif
    </div>

    {{-- ── Confirm-create modal ────────────────────────────────────────────── --}}
    @if (!$isEdit)
        <x-modal.dialog
            id="confirmAYModal"
            maxWidth="max-w-lg"
            width="w-11/12"
            variant="confirm"
            x-data
            x-on:open-confirm-ay-modal.window="$el.showModal()"
            x-bind:open="$wire.showConfirmModal">

            <x-modal.header>
                Confirm Academic Year Creation
                <x-modal.x-button modalId="confirmAYModal" />
            </x-modal.header>

            <x-modal.body>
                <div class="space-y-4">
                    <p class="text-slate-600 text-sm">Review the details before creating:</p>

                    <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] divide-y divide-[#e2e8f0] text-[13px]">
                        <div class="px-4 py-3">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-0.5">Academic Year</p>
                            <p class="font-bold text-[#0f172a] text-[15px]">{{ $academic_year ?: '—' }}</p>
                        </div>
                        <div class="grid grid-cols-2 divide-x divide-[#e2e8f0]">
                            <div class="px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-0.5">1st Semester</p>
                                <p class="font-semibold text-[#0f172a]">
                                    {{ $start_date_1 ? \Carbon\Carbon::parse($start_date_1)->format('M j, Y') : '—' }}
                                </p>
                                <p class="text-[13px] text-[#475569]">to {{ $end_date_1 ? \Carbon\Carbon::parse($end_date_1)->format('M j, Y') : '—' }}</p>
                            </div>
                            <div class="px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569] mb-0.5">2nd Semester</p>
                                <p class="font-semibold text-[#0f172a]">
                                    {{ $start_date_2 ? \Carbon\Carbon::parse($start_date_2)->format('M j, Y') : '—' }}
                                </p>
                                <p class="text-[13px] text-[#475569]">to {{ $end_date_2 ? \Carbon\Carbon::parse($end_date_2)->format('M j, Y') : '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-[13px] text-[#94a3b8] flex items-center gap-1">
                        <i class="bx bx-info-circle"></i>
                        You can add events after the calendar is created.
                    </p>
                </div>
            </x-modal.body>

            <x-modal.footer>
                <x-modal.close-button modalId="confirmAYModal" text="Review Again"
                    wire:click="cancelConfirm" />

                <x-ui.button type="button" variant="save"
                    wire:click="store"
                    wire:loading.attr="disabled"
                    loading="Creating…">
                    <i class="bx bx-check"></i> Confirm &amp; Create
                </x-ui.button>
            </x-modal.footer>
        </x-modal.dialog>
    @endif

    {{-- ── Cancel-edit modal ───────────────────────────────────────────────── --}}
    @if ($isEdit)
        <x-modal.dialog id="cancelEditModal" maxWidth="max-w-md" width="w-11/12" variant="warning"
            x-data
            x-on:open-cancel-edit-modal.window="$el.showModal()">

            <x-modal.header>
                Discard Changes?
                <x-modal.x-button modalId="cancelEditModal" />
            </x-modal.header>

            <x-modal.body>
                <div class="space-y-3">
                    <p class="text-[13px] text-[#0f172a]">You have unsaved changes. Are you sure you want to leave?</p>
                    <p class="text-[13px] text-[#92400e] font-medium flex items-center gap-1">
                        <i class="bx bx-error"></i> All changes will be lost.
                    </p>
                </div>
            </x-modal.body>

            <x-modal.footer>
                <x-modal.close-button modalId="cancelEditModal" text="Stay on Page" />
                <a href="{{ route('academic.calendars.index') }}"
                   wire:navigate
                   class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 transition-colors">
                    <i class="bx bx-x"></i> Discard Changes
                </a>
            </x-modal.footer>
        </x-modal.dialog>
    @endif
</div>
