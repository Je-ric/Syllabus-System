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
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <i class="bx bx-info-circle text-amber-500 text-lg shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold">Editing A.Y. {{ $academicYear }}</p>
                <p class="text-amber-700 text-xs mt-0.5">Changes will apply to both semesters. Events are not affected.</p>
            </div>
        </div>
    @endif

    {{-- ── Academic Year ───────────────────────────────────────────────────── --}}
    <div class="mb-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <x-form.label for="academic_year" isRequired variant="title">Academic Year</x-form.label>
        <p class="text-xs text-slate-400 mb-2">Format: YYYY-YYYY &nbsp;·&nbsp; e.g. 2025-2026</p>

        <x-form.input
            wire:model.live.debounce.400ms="academic_year"
            id="academic_year"
            placeholder="e.g. 2025-2026"
            class="max-w-xs" />

        @error('academic_year')
            <p class="mt-1.5 flex items-center gap-1 text-sm text-rose-600">
                <i class="bx bx-error-circle"></i> {{ $message }}
            </p>
        @else
            @if ($academic_year && preg_match('/^\d{4}-\d{4}$/', $academic_year))
                <p class="mt-1.5 flex items-center gap-1 text-sm text-emerald-600">
                    <i class="bx bx-check-circle"></i> Looks good
                </p>
            @endif
        @enderror
    </div>

    {{-- ── Semester date ranges ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">

        {{-- 1st Semester --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col gap-4">
            <h3 class="font-semibold text-slate-800 flex items-center gap-2 text-sm">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold shrink-0">1</span>
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
                <p class="text-xs text-emerald-600 flex items-center gap-1 mt-auto">
                    <i class="bx bx-check-circle"></i>
                    {{ \Carbon\Carbon::parse($start_date_1)->format('M j') }} – {{ \Carbon\Carbon::parse($end_date_1)->format('M j, Y') }}
                </p>
            @endif
        </div>

        {{-- 2nd Semester --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col gap-4">
            <h3 class="font-semibold text-slate-800 flex items-center gap-2 text-sm">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold shrink-0">2</span>
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
                <p class="text-xs text-emerald-600 flex items-center gap-1 mt-auto">
                    <i class="bx bx-check-circle"></i>
                    {{ \Carbon\Carbon::parse($start_date_2)->format('M j') }} – {{ \Carbon\Carbon::parse($end_date_2)->format('M j, Y') }}
                </p>
            @endif
        </div>
    </div>

    {{-- ── Actions ─────────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap justify-end gap-2">
        @if ($isEdit)
            <x-button type="button" variant="save"
                wire:click="update"
                wire:loading.attr="disabled"
                wire:target="update"
                loading="Saving…">
                <i class="bx bx-save"></i> Update Calendar
            </x-button>

            <x-button type="button" variant="cancel"
                x-data
                x-on:click="$dispatch('open-cancel-edit-modal')">
                <i class="bx bx-x"></i> Cancel
            </x-button>
        @else
            <x-button type="button" variant="save"
                wire:click="requestCreate"
                wire:loading.attr="disabled"
                wire:target="requestCreate"
                loading="Validating…">
                <i class="bx bx-save"></i> Create Calendar
            </x-button>
        @endif
    </div>

    {{-- ── Confirm-create modal ────────────────────────────────────────────── --}}
    @if (!$isEdit)
        <x-modal.dialog
            id="confirmAYModal"
            maxWidth="max-w-lg"
            width="w-11/12"
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

                    <div class="rounded-xl border border-slate-200 bg-slate-50 divide-y divide-slate-200 text-sm">
                        <div class="px-4 py-3">
                            <p class="text-xs font-medium text-slate-500 mb-0.5">Academic Year</p>
                            <p class="font-bold text-slate-800 text-base">{{ $academic_year ?: '—' }}</p>
                        </div>
                        <div class="grid grid-cols-2 divide-x divide-slate-200">
                            <div class="px-4 py-3">
                                <p class="text-xs font-medium text-slate-500 mb-0.5">1st Semester</p>
                                <p class="font-semibold text-slate-700">
                                    {{ $start_date_1 ? \Carbon\Carbon::parse($start_date_1)->format('M j, Y') : '—' }}
                                </p>
                                <p class="text-slate-500">to {{ $end_date_1 ? \Carbon\Carbon::parse($end_date_1)->format('M j, Y') : '—' }}</p>
                            </div>
                            <div class="px-4 py-3">
                                <p class="text-xs font-medium text-slate-500 mb-0.5">2nd Semester</p>
                                <p class="font-semibold text-slate-700">
                                    {{ $start_date_2 ? \Carbon\Carbon::parse($start_date_2)->format('M j, Y') : '—' }}
                                </p>
                                <p class="text-slate-500">to {{ $end_date_2 ? \Carbon\Carbon::parse($end_date_2)->format('M j, Y') : '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-slate-400 flex items-center gap-1">
                        <i class="bx bx-info-circle"></i>
                        You can add events after the calendar is created.
                    </p>
                </div>
            </x-modal.body>

            <x-modal.footer>
                <x-modal.close-button modalId="confirmAYModal" text="Review Again"
                    wire:click="cancelConfirm" />

                <x-button type="button" variant="save"
                    wire:click="store"
                    wire:loading.attr="disabled"
                    loading="Creating…">
                    <i class="bx bx-check"></i> Confirm &amp; Create
                </x-button>
            </x-modal.footer>
        </x-modal.dialog>
    @endif

    {{-- ── Cancel-edit modal ───────────────────────────────────────────────── --}}
    @if ($isEdit)
        <x-modal.dialog id="cancelEditModal" maxWidth="max-w-md" width="w-11/12"
            x-data
            x-on:open-cancel-edit-modal.window="$el.showModal()">

            <x-modal.header>
                Discard Changes?
                <x-modal.x-button modalId="cancelEditModal" />
            </x-modal.header>

            <x-modal.body>
                <div class="space-y-3">
                    <p class="text-slate-700 text-sm">You have unsaved changes. Are you sure you want to leave?</p>
                    <p class="text-amber-600 text-sm font-medium flex items-center gap-1">
                        <i class="bx bx-error"></i> All changes will be lost.
                    </p>
                </div>
            </x-modal.body>

            <x-modal.footer>
                <x-modal.close-button modalId="cancelEditModal" text="Stay on Page" />
                <x-button type="button" variant="danger"
                    wire:navigate
                    href="{{ route('academic.calendars.index') }}">
                    <i class="bx bx-x"></i> Discard Changes
                </x-button>
            </x-modal.footer>
        </x-modal.dialog>
    @endif
</div>
