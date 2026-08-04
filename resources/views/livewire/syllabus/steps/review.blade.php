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

        {{-- ── Save as Done ──────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 overflow-hidden"
             style="box-shadow: 0 2px 12px rgba(0,0,0,.06);">
            <div class="flex items-center justify-between gap-4 px-5 py-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 shrink-0">
                        <i class="bx bx-cloud-upload text-base leading-none"></i>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Save as Done</p>
                        <p class="text-xs text-slate-500 mt-0.5">Create an immutable snapshot of this syllabus.</p>
                    </div>
                </div>
                <x-ui.button
                    type="button"
                    variant="add-button"
                    wire:click="$parent.saveAsDone"
                    wire:loading.attr="disabled"
                    wire:target="saveAsDone"
                    loading="Saving…">
                    <i class="bx bx-save text-base leading-none"></i> Create version
                </x-ui.button>
            </div>
        </div>

        {{-- ── Saved Versions (archive) ──────────────────────────────────── --}}
        @include('livewire.syllabus.steps.review-partials.saved-versions')

    </div>

    {{-- Submit section --}}
    {{-- <div class="mt-6 rounded-xl border border-[#e2e8f0] overflow-hidden"
         style="box-shadow: 0 2px 12px rgba(0,0,0,.07);">

        Warning bar
        <div class="px-5 py-4 border-b border-amber-200 bg-amber-50/80">
            <div class="flex items-start gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 text-amber-600 shrink-0 mt-0.5">
                    <i class="bx bx-error text-base leading-none"></i>
                </span>
                <div>
                    <p class="text-sm font-bold text-amber-800">Before you submit</p>
                    <p class="text-sm text-amber-700 mt-0.5 leading-relaxed">
                        Once submitted, this syllabus will be sent to the Program CQI Chair and Members for review.
                        Make sure all information is complete and accurate.
                    </p>
                </div>
            </div>
        </div>

        Submit footer
        <div class="flex items-center justify-between gap-4 px-5 py-4 bg-white">
            <p class="text-xs text-slate-400 hidden sm:block">
                You can still edit your draft if you need to make changes before submitting.
            </p>
            <x-ui.button
                variant="primary"
                wire:click="submitForReview"
                wire:loading.attr="disabled"
                wire:target="submitForReview"
                loading="Submitting…"
                class="ml-auto">
                <i class="bx bx-send text-base leading-none"></i> Submit for Review
            </x-ui.button>
        </div>

    </div> --}}
</div>
