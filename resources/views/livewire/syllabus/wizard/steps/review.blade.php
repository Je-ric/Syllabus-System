<div>
    <x-wizard.step-header
        title="Review & Submit"
        description="Review all details before submitting for review."
        :step="$stepNumber" />

    <div class="space-y-4">

        {{-- ── Previews ──────────────────────────────────────────────────── --}}
        @include('livewire.syllabus.wizard.steps.partials.review.previews')

        {{-- ── F.003 Review Form (author panel) ─────────────────────────── --}}
        {{-- @include('livewire.syllabus.wizard.steps.partials.review.review-form', [
            'syllabus'   => $this->syllabus,
            'reviewForm' => $reviewForm,
        ]) --}}

        {{-- ── Review & Approval ─────────────────────────────────────────── --}}
        @include('livewire.syllabus.wizard.steps.partials.review.reviewers')

        {{-- ── Revision History ──────────────────────────────────────────── --}}
        @include('livewire.syllabus.wizard.steps.partials.review.revisions')


        {{-- ── Saved Versions (archive) ──────────────────────────────────── --}}
        @include('livewire.syllabus.wizard.steps.partials.review.saved-versions')

    </div>

</div>
