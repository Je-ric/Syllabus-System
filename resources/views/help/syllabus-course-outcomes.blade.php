{{-- Help: Step 3 — Course Outcomes — Updated with latest system changes --}}

<x-layout.accordion title="What This Step Does" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13.5px] text-[#3f3f46] leading-relaxed">
        You define what students will be able to do after completing this course. Each Course Outcome (CO) is a measurable statement. COs are later linked to Program Outcomes (POs) in the weekly coverage step.
    </p>
    <p class="text-[13.5px] text-[#3f3f46] leading-relaxed mt-2">
        COs should be specific, measurable, and aligned with the course's content and assessment methods.
    </p>
</x-layout.accordion>

<x-layout.accordion title="Adding & Editing COs" icon="list-ol" color="slate">
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
</x-layout.accordion>

<x-layout.accordion title="Row State Indicators" icon="info-circle" color="amber">
    <ul class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2"><span class="shrink-0 w-2.5 h-2.5 rounded-full bg-emerald-400 mt-1.5"></span><span><strong>Green border</strong> — new unsaved row.</span></li>
        <li class="flex gap-2"><span class="shrink-0 w-2.5 h-2.5 rounded-full bg-amber-400 mt-1.5"></span><span><strong>Amber border</strong> — existing CO with unsaved edits.</span></li>
        <li class="flex gap-2"><span class="shrink-0 w-2.5 h-2.5 rounded-full bg-slate-300 mt-1.5"></span><span><strong>No highlight</strong> — saved and unchanged.</span></li>
    </ul>
    <p class="mt-2 text-[13.5px] text-[#3f3f46]">Click <strong>Revert</strong> to discard all staged changes and restore the last saved state.</p>
</x-layout.accordion>

<x-layout.accordion title="Reference Panels" icon="book-open" color="blue">
    <div class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <p>Click <strong>View POs</strong> to open a reference panel showing the Program Outcomes for this course. This helps you align COs with POs.</p>
        <p class="mt-1">COs are linked to POs in Step 4 (Weekly Coverage) — you'll select which POs each CO addresses.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Writing Good COs" icon="edit" color="purple">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Effective COs should be:</p>
        <ul class="space-y-1.5 mt-1">
            <li class="flex gap-2"><i class="bx bx-check text-[#6b21a8] shrink-0 mt-0.5"></i><span><strong>Measurable</strong> — students can demonstrate achievement</span></li>
            <li class="flex gap-2"><i class="bx bx-check text-[#6b21a8] shrink-0 mt-0.5"></i><span><strong>Specific</strong> — clear and unambiguous</span></li>
            <li class="flex gap-2"><i class="bx bx-check text-[#6b21a8] shrink-0 mt-0.5"></i><span><strong>Action-oriented</strong> — start with verbs (Demonstrate, Apply, Analyze, etc.)</span></li>
            <li class="flex gap-2"><i class="bx bx-check text-[#6b21a8] shrink-0 mt-0.5"></i><span><strong>Aligned</strong> — support course content and assessments</span></li>
        </ul>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Writing vague COs like "Understand the topic" — use measurable verbs instead.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Having too many COs — 5-8 is typically sufficient for most courses.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Not aligning COs with course content — ensure you can actually teach and assess each CO.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Navigating away without saving — unsaved COs are lost.</span></li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Tips" icon="bulb" color="amber">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Use Bloom's Taxonomy verbs for measurable outcomes (e.g., Define, Explain, Apply, Analyze, Evaluate, Create).</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Reference the course's POs when writing COs to ensure alignment.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Each CO should be assessable through your course evaluations.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>You need at least one CO to proceed to the next step.</span></li>
    </ul>
</x-layout.accordion>
