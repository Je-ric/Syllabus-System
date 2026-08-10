{{-- Help: Step 1 — Academic Calendar — Updated with latest system changes --}}

<x-layout.accordion title="What This Step Does" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        You select the academic year and semester this syllabus covers. The calendar you choose determines the exact weeks that will be generated in Step 4 (Weekly Coverage).
    </p>
    <p class="text-[13px] text-[#3f3f46] leading-relaxed mt-2">
        Calendars are managed via <strong>Academic → Academic Calendar</strong> in the navigation menu. The selected calendar's events (breaks, exams, non-teaching days) affect how weeks are generated.
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
    <p class="mt-2 text-[12px] text-[#71717a]">Calendar events are managed by admins via the Academic Calendar module.</p>
</x-layout.accordion>

<x-layout.accordion title="Changing Calendars" icon="refresh" color="amber">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>If you need to change the calendar after weeks have been generated:</p>
        <ol class="space-y-1.5 mt-2 pl-1">
            <li class="flex gap-2">
                <span class="shrink-0 w-4 text-[#a1a1aa] font-bold">1.</span>
                <span>Go to Step 4 (Weekly Coverage)</span>
            </li>
            <li class="flex gap-2">
                <span class="shrink-0 w-4 text-[#a1a1aa] font-bold">2.</span>
                <span>Click <strong>Regenerate Weeks</strong></span>
            </li>
            <li class="flex gap-2">
                <span class="shrink-0 w-4 text-[#a1a1aa] font-bold">3.</span>
                <span>Confirm the action — <strong>this deletes all existing weekly content</strong></span>
            </li>
        </ol>
        <p class="mt-2 text-[12px] text-[#71717a]">Only do this if you're sure — all weekly topics, activities, and assessments will be lost.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Changing the calendar after weeks have been generated — this requires regenerating weeks, which deletes all existing weekly content.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Selecting the wrong semester — double-check the academic year and semester label before proceeding.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Not checking if the calendar has events set up — contact admin if the calendar appears incomplete.</span></li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Tips" icon="bulb" color="amber">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Verify the calendar dates match your institution's official academic calendar.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Contact your admin if the calendar is missing important events (breaks, exams).</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>The calendar selection is saved automatically — you won't lose it if you navigate between steps.</span></li>
    </ul>
</x-layout.accordion>
