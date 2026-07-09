{{-- Help: Step 1 — Academic Calendar --}}

<x-layout.accordion title="What This Step Does" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        You select the academic year and semester this syllabus covers. The calendar you choose determines the exact weeks that will be generated in Step 4 (Weekly Coverage).
    </p>
</x-layout.accordion>

<x-layout.accordion title="How to Complete" icon="list-ol" color="slate">
    <ol class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Choose an academic calendar from the dropdown — each entry shows the academic year and semester (e.g. <em>2024–2025 — 1st Semester</em>).</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Click <strong>Next</strong> — the selection is saved automatically.</span>
        </li>
    </ol>
</x-layout.accordion>

<x-layout.accordion title="How the Calendar Affects Weeks" icon="calendar" color="blue">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Weeks are generated in 7-day blocks from the calendar's start to end date.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span><strong>Break</strong> events skip the week entirely — no row is created for that week.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span><strong>Exam</strong> events lock the week as "Exam Week" — content is auto-filled and cannot be edited.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span><strong>Non-teaching</strong> events lock the week as "Non-Teaching Week" — same as exam weeks.</span></li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Changing the calendar after weeks have been generated — this requires regenerating weeks, which deletes all existing weekly content.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Selecting the wrong semester — double-check the academic year and semester label before proceeding.</span></li>
    </ul>
</x-layout.accordion>
