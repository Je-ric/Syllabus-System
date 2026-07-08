{{-- Help: Step 6 — Review & Submit --}}

<x-accordion title="What This Step Does" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        This is the final step. You review the complete syllabus, manage signatories and reviewers, create a saved version snapshot, and submit for review when everything is ready.
    </p>
</x-accordion>

<x-accordion title="Previews" icon="show" color="slate">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Two preview links are available:</p>
        <ul class="space-y-1.5 mt-1">
            <li class="flex gap-2"><i class="bx bx-file text-[#52525b] shrink-0 mt-0.5"></i><span><strong>Abridged</strong> — a condensed view of the syllabus.</span></li>
            <li class="flex gap-2"><i class="bx bx-file-blank text-[#52525b] shrink-0 mt-0.5"></i><span><strong>Complete</strong> — the full syllabus as it will appear when submitted.</span></li>
        </ul>
        <p class="mt-1 text-[12px] text-[#71717a]">Previews open in a new tab. Use them to check formatting before submitting.</p>
    </div>
</x-accordion>

<x-accordion title="Signatories & Reviewers" icon="user-check" color="blue">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Assign the required signatories (e.g. department chair, dean) and reviewers for this syllabus. These appear on the printed syllabus and in the review workflow.</p>
        <p class="mt-1 text-[12px] text-[#71717a]">Signatories and reviewers must be set before submitting.</p>
    </div>
</x-accordion>

<x-accordion title="Save as Done (Version Snapshot)" icon="cloud-upload" color="emerald">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Click <strong>Create Version</strong> to create an immutable snapshot of the syllabus in its current state. This:</p>
        <ul class="space-y-1.5 mt-1">
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Renders the syllabus to a PDF and freezes a version record.</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Does <strong>not</strong> submit for review — it only saves a snapshot.</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Previous versions are listed below and can be downloaded at any time.</span></li>
        </ul>
    </div>
</x-accordion>

<x-accordion title="Submission Gate" icon="check-shield" color="amber">
    <p class="text-[13px] text-[#3f3f46] mb-2">All of the following must be complete before you can submit:</p>
    <ul class="space-y-1.5 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Academic calendar selected (Step 1)</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Course components complete (Step 2)</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>At least one course outcome saved (Step 3)</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>At least one week exists (Step 4)</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Evaluation weights complete and totals correct (Step 5)</span></li>
    </ul>
    <p class="mt-2 text-[12px] text-[#71717a]">Incomplete steps are flagged with an amber dot in the step navigator on the left.</p>
</x-accordion>
