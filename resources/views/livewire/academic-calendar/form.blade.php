{{--
    livewire/academic-calendar/form.blade.php
    ─────────────────────────────────────────
    Rendered by App\Livewire\AcademicCalendar\AcademicCalendarForm.
    Real-time validation: every field uses wire:model.blur so the
    rule runs the moment focus leaves the field. Errors show inline.
--}}

<div>
    {{-- ── Edit: show current values strip ──────────────────────────────── --}}
    @if ($isEdit)
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-sm text-amber-900">
            <p class="text-xs uppercase tracking-[0.2em] font-semibold text-amber-700 mb-2">Current Values</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <p class="font-semibold">Academic Year</p>
                    <p>{{ $academicYear ?: '—' }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Academic Year ───────────────────────────────────────────────── --}}
    <div class="mb-6 bg-white/90 border border-slate-200/80 rounded-2xl p-5 shadow-sm">
        <x-form.label for="academic_year" isRequired variant="title">
            Academic Year
        </x-form.label>
        <x-form.input
            wire:model.blur="academic_year"
            id="academic_year"
            placeholder="e.g. 2025-2026"
            class="mt-2" />

        @error('academic_year')
            <p class="mt-1 flex items-center gap-1 text-sm text-rose-600">
                <i class="bx bx-error-circle"></i> {{ $message }}
            </p>
        @enderror

        <p class="mt-1 text-xs text-slate-400">Format: YYYY-YYYY (e.g. 2025-2026)</p>
    </div>

    {{-- ── Semester dates grid ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        {{-- 1st Semester --}}
        <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl shadow-sm space-y-4">
            <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">1</span>
                1st Semester
            </h2>

            <div>
                <x-form.label isRequired>Start Date</x-form.label>
                <x-form.date-picker
                    name="start_date_1"
                    wire:model.blur="start_date_1"
                    :value="$start_date_1"
                    class="mt-2" />
                @error('start_date_1')
                    <p class="mt-1 flex items-center gap-1 text-sm text-rose-600">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <x-form.label isRequired>End Date</x-form.label>
                <x-form.date-picker
                    name="end_date_1"
                    wire:model.blur="end_date_1"
                    :value="$end_date_1"
                    class="mt-2" />
                @error('end_date_1')
                    <p class="mt-1 flex items-center gap-1 text-sm text-rose-600">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- 2nd Semester --}}
        <div class="border border-slate-200/80 bg-white/90 p-5 rounded-2xl shadow-sm space-y-4">
            <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">2</span>
                2nd Semester
            </h2>

            <div>
                <x-form.label isRequired>Start Date</x-form.label>
                <x-form.date-picker
                    name="start_date_2"
                    wire:model.blur="start_date_2"
                    :value="$start_date_2"
                    class="mt-2" />
                @error('start_date_2')
                    <p class="mt-1 flex items-center gap-1 text-sm text-rose-600">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <x-form.label isRequired>End Date</x-form.label>
                <x-form.date-picker
                    name="end_date_2"
                    wire:model.blur="end_date_2"
                    :value="$end_date_2"
                    class="mt-2" />
                @error('end_date_2')
                    <p class="mt-1 flex items-center gap-1 text-sm text-rose-600">
                        <i class="bx bx-error-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>
        </div>
    </div>

    {{-- ── Action buttons ───────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-2">
        @if ($isEdit)
            <x-button type="button" variant="save" wire:click="update" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="update"><i class="bx bx-save"></i> Update Calendar</span>
                <span wire:loading wire:target="update">Saving…</span>
            </x-button>

            <x-button type="button" variant="cancel"
                x-data
                x-on:click="$dispatch('open-cancel-edit-modal')">
                <i class="bx bx-x"></i> Cancel
            </x-button>
        @else
            <x-button type="button" variant="save" wire:click="requestCreate" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="requestCreate"><i class="bx bx-save"></i> Create Calendar</span>
                <span wire:loading wire:target="requestCreate">Validating…</span>
            </x-button>
        @endif
    </div>

    {{-- ── Confirm-create modal (Livewire-driven) ──────────────────────── --}}
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
                    <p class="text-gray-700 font-medium">Review the details before creating:</p>
                    <div class="bg-gray-50 p-4 rounded border border-gray-200 space-y-3">
                        <div>
                            <p class="font-semibold text-sm text-gray-600">Academic Year</p>
                            <p class="text-base font-bold">{{ $academic_year ?: '—' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="font-semibold text-gray-600">1st Semester</p>
                                <p>{{ $start_date_1 ? \Carbon\Carbon::parse($start_date_1)->format('M d, Y') : '—' }}
                                   – {{ $end_date_1 ? \Carbon\Carbon::parse($end_date_1)->format('M d, Y') : '—' }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-600">2nd Semester</p>
                                <p>{{ $start_date_2 ? \Carbon\Carbon::parse($start_date_2)->format('M d, Y') : '—' }}
                                   – {{ $end_date_2 ? \Carbon\Carbon::parse($end_date_2)->format('M d, Y') : '—' }}</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-blue-600 text-sm">✓ Make sure all dates are correct before proceeding.</p>
                </div>
            </x-modal.body>

            <x-modal.footer>
                <x-modal.close-button modalId="confirmAYModal" text="Review Again"
                    wire:click="cancelConfirm" />

                <x-button type="button" variant="save"
                    wire:click="store"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store"><i class="bx bx-check"></i> Confirm &amp; Create</span>
                    <span wire:loading wire:target="store">Creating…</span>
                </x-button>
            </x-modal.footer>
        </x-modal.dialog>
    @endif

    {{-- ── Cancel-edit modal ───────────────────────────────────────────── --}}
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
                    <p class="text-gray-700">You have unsaved changes. Are you sure you want to leave?</p>
                    <p class="text-amber-600 text-sm font-medium">
                        <i class="bx bx-error"></i> All changes will be lost.
                    </p>
                </div>
            </x-modal.body>

            <x-modal.footer>
                <div class="w-full flex gap-2 justify-end">
                    <x-modal.close-button modalId="cancelEditModal" text="Stay on Page" />
                    <x-button type="button" variant="table-danger"
                        wire:navigate
                        href="{{ route('academic.calendars.index') }}">
                        <i class="bx bx-x"></i> Discard Changes
                    </x-button>
                </div>
            </x-modal.footer>
        </x-modal.dialog>
    @endif
</div>