{{-- Help: Step 4 — Weekly Coverage — Updated with latest system changes --}}

<x-layout.accordion title="What This Step Does" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13.5px] text-[#3f3f46] leading-relaxed">
        You fill in the topic, learning activities, assessment tasks, and CO mappings for each week of the semester. Weeks are auto-generated from the academic calendar you selected in Step 1.
    </p>
    <p class="text-[13.5px] text-[#3f3f46] leading-relaxed mt-2">
        This is the most detailed step of the syllabus wizard and typically takes the most time to complete.
    </p>
</x-layout.accordion>

<x-layout.accordion title="Generating Weeks" icon="calendar-plus" color="slate">
    <div class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <p>If no weeks exist yet, click <strong>Generate Weeks</strong>. This creates one row per 7-day block between the calendar's start and end dates.</p>
        <p class="mt-1">To regenerate (e.g. after changing the calendar), click <strong>Regenerate</strong> — a confirmation is required because all existing week content will be deleted.</p>
        <p class="mt-1 text-[12px] text-[#71717a]">Week generation respects calendar events: breaks are skipped, exam/non-teaching weeks are locked.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Week Types" icon="info-circle" color="blue">
    <div class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <div class="flex items-start gap-2.5 p-2 rounded-lg bg-[#f0fdf4] border border-[#d1fae5]">
            <i class="bx bx-flag text-[#16a34a] shrink-0 mt-0.5"></i>
            <div><p class="font-semibold text-[#166534]">Week 1 — MVGO</p><p class="text-[12.5px] mt-0.5">Always Mission-Vision-Goals-Objectives. The CO selector is replaced by an MVGO badge and cannot be changed.</p></div>
        </div>
        <div class="flex items-start gap-2.5 p-2 rounded-lg bg-[#fffbeb] border border-[#fde68a]">
            <i class="bx bx-lock text-[#d97706] shrink-0 mt-0.5"></i>
            <div><p class="font-semibold text-[#92400e]">Exam / Non-Teaching Weeks</p><p class="text-[12.5px] mt-0.5">Locked automatically from calendar events. Content is auto-filled and cannot be edited.</p></div>
        </div>
        <div class="flex items-start gap-2.5 p-2 rounded-lg bg-white border border-[#e4e4e7]">
            <i class="bx bx-edit text-[#52525b] shrink-0 mt-0.5"></i>
            <div><p class="font-semibold text-[#09090b]">Regular Weeks</p><p class="text-[12.5px] mt-0.5">Fully editable. Fill in topic, activities, assessment task, and CO mappings.</p></div>
        </div>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Filling in a Week" icon="list-ol" color="slate">
    <ol class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Click a week accordion to expand it.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Fill in the <strong>Topic</strong>, <strong>Learning Activities</strong>, and <strong>Assessment Task</strong> fields.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>Select COs that this week addresses using the CO selector.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">4</span>
            <span>Click <strong>Save Week</strong> to persist changes for that week.</span>
        </li>
    </ol>
</x-layout.accordion>

<x-layout.accordion title="CO Mapping" icon="link" color="purple">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Each week can be mapped to multiple COs. This connects your weekly content to the Course Outcomes you defined in Step 3.</p>
        <p class="mt-1">The CO selector shows all COs for this course. Click to toggle selection — selected COs are highlighted.</p>
        <p class="mt-1 text-[12px] text-[#71717a]">CO mapping is important for demonstrating coverage of program outcomes in accreditation reports.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Not saving each week individually — use the Save Week button at the bottom of each week.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Leaving topics blank — each week should have a meaningful topic description.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Not mapping COs to weeks — this is required for PO-PEO alignment analysis.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Forgetting to add assessment tasks — assessments without weights cannot be evaluated in Step 5.</span></li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Tips" icon="bulb" color="amber">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Save each week as you complete it — don't wait until you've filled all weeks.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Use specific, actionable learning activities that students can realistically complete.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Ensure assessment tasks match what you'll actually grade in Step 5.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Map COs broadly — most weeks should address 2-4 COs for good coverage.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Use the calendar info drawer to verify week dates and special events.</span></li>
    </ul>
</x-layout.accordion>
