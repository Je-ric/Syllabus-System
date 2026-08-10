{{-- Help: Step 5 — Course Evaluation — Updated with latest system changes --}}

<x-layout.accordion title="What This Step Does" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        You assign a percentage weight to each assessment task that appeared in your weekly coverage. The weights must add up to exactly the required total for the syllabus to be submittable.
    </p>
    <p class="text-[13px] text-[#3f3f46] leading-relaxed mt-2">
        This ensures the assessment structure is balanced and meets institutional requirements for credit distribution.
    </p>
</x-layout.accordion>

<x-layout.accordion title="Weight Totals Required" icon="bar-chart-alt-2" color="blue">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>The required totals depend on whether the course has a lab component:</p>
        <div class="space-y-1.5 mt-1">
            <div class="flex items-center gap-3 p-2 rounded-lg bg-[#f0fdf4] border border-[#d1fae5]">
                <span class="shrink-0 w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span><strong>LEC only</strong> — weights must total <strong>100%</strong>.</span>
            </div>
            <div class="flex items-center gap-3 p-2 rounded-lg bg-[#eff6ff] border border-[#bfdbfe]">
                <span class="shrink-0 w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                <span><strong>LEC + LAB</strong> — LEC must total <strong>67%</strong>, LAB must total <strong>33%</strong>.</span>
            </div>
        </div>
        <p class="mt-2 text-[12px] text-[#71717a]">The sticky footer bar shows your running totals in real time. The dot turns green when the total is correct, red when it is not.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="How to Complete" icon="list-ol" color="slate">
    <ol class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Each row shows an assessment task from your weekly coverage. Enter a percentage weight for each.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Watch the running total in the footer — adjust weights until the dot turns green.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>Click <strong>Save Evaluation</strong> to persist the weights.</span>
        </li>
    </ol>
</x-layout.accordion>

<x-layout.accordion title="Where Assessments Come From" icon="help-circle" color="purple">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Assessment tasks are automatically pulled from Step 4 (Weekly Coverage). Any task you entered in the "Assessment Task" field for a week appears here.</p>
        <p class="mt-1">If you need to add or change assessments, go back to Step 4 and modify the weekly coverage.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Not reaching the exact required total — the dot must be green to submit.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Using decimal weights that don't add up correctly — stick to whole numbers for simplicity.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Forgetting to save after adjusting weights — the save button must be clicked to persist changes.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Leaving assessment weights at 0% — all assessments should have meaningful weights.</span></li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Tips" icon="bulb" color="amber">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Use the running total indicator in the footer to track your progress.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Major assessments (exams, projects) typically have higher weights than quizzes/homework.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>For LEC+LAB courses, ensure the 67/33 split is maintained as required.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Weights should reflect the relative importance and effort of each assessment.</span></li>
    </ul>
</x-layout.accordion>
