{{-- Help: Step 3 — Course Outcomes --}}

<x-accordion title="What This Step Does" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13.5px] text-[#3f3f46] leading-relaxed">
        You define what students will be able to do after completing this course. Each Course Outcome (CO) is a measurable statement. COs are later linked to Program Outcomes (POs) in the weekly coverage step.
    </p>
</x-accordion>

<x-accordion title="Adding & Editing COs" icon="list-ol" color="slate">
    <ol class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Click <strong>Add Course Outcome</strong> — a new row appears with an empty text area.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Type the outcome description. The CO code (CO1, CO2…) is assigned automatically on save.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>Add as many COs as needed, then click <strong>Save All</strong> once to persist them all.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">4</span>
            <span>To delete a saved CO, click the <i class="bx bx-trash text-[#e11d48]"></i> icon. Codes are resequenced automatically.</span>
        </li>
    </ol>
</x-accordion>

<x-accordion title="Row State Indicators" icon="info-circle" color="amber">
    <ul class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2"><span class="shrink-0 w-2.5 h-2.5 rounded-full bg-emerald-400 mt-1.5"></span><span><strong>Green border</strong> — new unsaved row.</span></li>
        <li class="flex gap-2"><span class="shrink-0 w-2.5 h-2.5 rounded-full bg-amber-400 mt-1.5"></span><span><strong>Amber border</strong> — existing CO with unsaved edits.</span></li>
        <li class="flex gap-2"><span class="shrink-0 w-2.5 h-2.5 rounded-full bg-slate-300 mt-1.5"></span><span><strong>No highlight</strong> — saved and unchanged.</span></li>
    </ul>
    <p class="mt-2 text-[13.5px] text-[#3f3f46]">Click <strong>Revert</strong> to discard all staged changes and restore the last saved state.</p>
</x-accordion>

<x-accordion title="Reference Panels" icon="book-open" color="blue">
    <div class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <p>Two reference panels are available in the right sidebar while on this step:</p>
        <ul class="space-y-1.5 mt-1">
            <li class="flex gap-2"><i class="bx bx-book text-[#16a34a] shrink-0 mt-0.5"></i><span><strong>Course Info</strong> — shows the course code, title, description, units, and class hours.</span></li>
            <li class="flex gap-2"><i class="bx bx-list-check text-[#2563eb] shrink-0 mt-0.5"></i><span><strong>PO Reference</strong> — shows all Program Outcomes for this course's program, useful when writing COs that align to POs.</span></li>
        </ul>
    </div>
</x-accordion>

<x-accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Navigating away before clicking <strong>Save All</strong> — unsaved rows are lost.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Leaving a blank CO row — you'll be warned to fill it in before adding another or saving.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>At least one saved CO is required before you can submit the syllabus for review.</span></li>
    </ul>
</x-accordion>
