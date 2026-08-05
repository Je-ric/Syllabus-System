<div>
    <x-wizard.step-header
        title="Review & Submit"
        description="Review all details before submitting for department chair review." />

    <div class="space-y-4">

        {{-- ── Previews ──────────────────────────────────────────────────── --}}
        @include('livewire.syllabus.steps.review-partials.previews')

        {{-- ── F.003 Review Form (author panel) ─────────────────────────── --}}
        @include('livewire.syllabus.steps.review-partials.review-form', [
            'syllabus'   => $this->syllabus,
            'reviewForm' => $reviewForm,
        ]) 

        {{-- ── Review & Approval ─────────────────────────────────────────── --}}
        @include('livewire.syllabus.steps.review-partials.reviewers')

        {{-- ── Revision History ──────────────────────────────────────────── --}}
        @include('livewire.syllabus.steps.review-partials.revisions')


        {{-- ── Saved Versions (archive) ──────────────────────────────────── --}}
        @include('livewire.syllabus.steps.review-partials.saved-versions')

    </div>

</div>
