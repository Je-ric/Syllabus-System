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

    <div class="mt-6 flex flex-wrap items-center gap-4">
        {{-- <x-button
            type="button"
            variant="add-button"
            wire:click="$dispatch('wizard-save-as-done')"
            loading="Freezing version…">
            <i class="bx bx-save text-base"></i> Save as Done
        </x-button> --}}
    </div>

    <div class="mt-6 space-y-4">
        <x-feedback-status.alert
            type="warning"
            title="Before you submit"
            message="Once you submit, the syllabus will be sent for review by the department chair. Make sure all information is correct." />

        <div class="flex justify-end">
            <x-button
                variant="primary"
                wire:click="submitForReview"
                wire:loading.attr="disabled"
                wire:target="submitForReview"
                loading="Submitting…">
                <i class="bx bx-send text-base leading-none"></i> Submit for Review
            </x-button>
        </div>
    </div>
</div>
