<div>
    <x-wizard.step-header
        title="Review & Submit"
        description="Review all details before submitting for approval." />

    <div class="space-y-4">

        {{-- ── Previews ──────────────────────────────────────────────────── --}}
        @include('livewire.syllabus.steps.review-partials.previews')

        {{-- ══ ACCORDION 1 — REVISION HISTORY ═══════════════════════════════ --}}
        @include('livewire.syllabus.steps.review-partials.revisions')

        {{-- ══════════════════════════════════════════════════════════════════
            ACCORDION 2 — REVIEW & APPROVAL
            Three sub-sections:
                1. Approved By    — exactly 1 dean
                2. Concurred By   — exactly 1 dean
                3. Reviewed By    — N additional reviewers (syllabus_reviewers table)
            Auto-saves concurred/approved on accordion close.
            addReviewer/removeReviewer still go via $parent on SyllabusWizard.
        ══════════════════════════════════════════════════════════════════════ --}}
        @include('livewire.syllabus.steps.review-partials.reviewers')

        {{-- ── Saved Versions ────────────────────────────────────────────── --}}
        
        @include('livewire.syllabus.steps.review-partials.saved-versions')

    </div>

    <div class="mt-6 flex flex-wrap items-center gap-4"
            x-data="{ saving: false }"
            x-on:wizard-save-done.window="saving = false">

        <button type="button"
            x-on:click="saving = true; $dispatch('wizard-save-as-done')"
            x-bind:disabled="saving"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                    bg-emerald-600 text-white text-sm font-semibold shadow-sm
                    hover:bg-emerald-700 active:bg-emerald-800
                    disabled:opacity-60 disabled:cursor-not-allowed
                    transition-colors duration-150">
            <span x-show="!saving" class="inline-flex items-center gap-2">
                <i class="bx bx-save text-base"></i> Save as Done
            </span>
            <span x-show="saving" x-cloak class="inline-flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Freezing version…
            </span>
        </button>

        <p x-show="saving" x-cloak class="text-xs text-slate-500 animate-pulse">
            Creating an immutable saved version — this may take a few seconds.
        </p>
    </div>

    <div class="mt-6">
        <x-feedback-status.alert
            type="warning"
            title="Before you submit"
            message="Once you submit, the syllabus will be sent for review by the department chair. Make sure all information is correct." />
    </div>
</div>
