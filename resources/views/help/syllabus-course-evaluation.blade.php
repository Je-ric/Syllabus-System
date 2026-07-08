{{-- Help: Step 5 — Course Evaluation --}}

<x-accordion title="What This Step Does" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        You assign a percentage weight to each assessment task that appeared in your weekly coverage. The weights must add up to exactly the required total for the syllabus to be submittable.
    </p>
</x-accordion>

<x-accordion title="Weight Totals Required" icon="bar-chart-alt-2" color="blue">
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
</x-accordion>

<x-accordion title="How to Complete" icon="list-ol" color="slate">
    <ol class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-[#dcfce7] text-[#166534] text-[10px] font-bold mt-0.5">1</span>
            <span>Each row shows an assessment task from your weekly coverage. Enter a percentage weight for each.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-[#dcfce7] text-[#166534] text-[10px] font-bold mt-0.5">2</span>
            <span>Watch the running total in the footer — adjust weights until the dot turns green.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-[#dcfce7] text-[#166534] text-[10px] font-bold mt-0.5">3</span>
            <span>Click <strong>Save Evaluation</strong> to persist.</span>
        </li>
    </ol>
</x-accordion>

<x-accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>The table is empty — this means no assessment tasks were entered in Weekly Coverage. Go back to Step 4 and add assessment tasks to your weeks.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Weights that don't add up to the required total — the syllabus cannot be submitted until totals are correct.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Leaving weights at 0 — every assessment task row must have a value greater than 0.</span></li>
    </ul>
</x-accordion>
