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

    <div
        x-data="{ savingSnapshot: false }"
        x-on:wizard-save-done.window="savingSnapshot = false"
        x-on:lw-toast.window="savingSnapshot = false"
        class="mt-6 flex flex-wrap items-center gap-4">
        <x-button
            type="button"
            variant="add-button"
            x-on:click="savingSnapshot = true"
            wire:click="$dispatch('wizard-save-as-done')"
            x-bind:disabled="savingSnapshot"
            wireTarget="wizard-save-as-done"
            loading="Freezing version…">
            <span x-show="!savingSnapshot" x-cloak>
                <i class="bx bx-save text-base"></i> Save as Done
            </span>
            <span x-show="savingSnapshot" x-cloak class="inline-flex items-center gap-2">
                <span class="inline-block h-3.5 w-3.5 rounded-full border-2 border-white/80 border-t-transparent animate-spin"></span>
                Freezing version...
            </span>
        </x-button>

        <p x-show="savingSnapshot" x-cloak class="text-xs text-slate-500 animate-pulse">
            Creating an immutable saved version - this may take a few seconds.
        </p>
    </div>

    <div class="mt-6">
        <x-feedback-status.alert
            type="warning"
            title="Before you submit"
            message="Once you submit, the syllabus will be sent for review by the department chair. Make sure all information is correct." />
    </div>
</div>
