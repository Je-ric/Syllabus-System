{{-- Help: Step 4 — Weekly Coverage --}}

<x-accordion title="What This Step Does" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13.5px] text-[#3f3f46] leading-relaxed">
        You fill in the topic, learning activities, assessment tasks, and CO mappings for each week of the semester. Weeks are auto-generated from the academic calendar you selected in Step 1.
    </p>
</x-accordion>

<x-accordion title="Generating Weeks" icon="calendar-plus" color="slate">
    <div class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <p>If no weeks exist yet, click <strong>Generate Weeks</strong>. This creates one row per 7-day block between the calendar's start and end dates.</p>
        <p class="mt-1">To regenerate (e.g. after changing the calendar), click <strong>Regenerate</strong> — a confirmation is required because all existing week content will be deleted.</p>
    </div>
</x-accordion>

<x-accordion title="Week Types" icon="info-circle" color="blue">
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
</x-accordion>

<x-accordion title="Filling in a Week" icon="list-ol" color="slate">
    <ol class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Click a week accordion to expand it.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Fill in the <strong>Topic</strong>, <strong>Learning Activities</strong>, and <strong>Assessment Task</strong>.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>Select one or more <strong>Course Outcomes (COs)</strong> that this week addresses.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">4</span>
            <span>Collapse the week or click <strong>Save All</strong> — auto-save triggers on collapse.</span>
        </li>
    </ol>
    <p class="mt-2 text-[12.5px] text-[#71717a]">If the course has both LEC and LAB, use the tab switcher to fill in each component separately.</p>
</x-accordion>

<x-accordion title="Sidebar Tools" icon="settings" color="slate">
    <ul class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-time text-[#a1a1aa] shrink-0 mt-0.5"></i><span><strong>Schedule</strong> — opens a drawer showing the class schedule for reference.</span></li>
        <li class="flex gap-2"><i class="bx bx-calendar text-[#a1a1aa] shrink-0 mt-0.5"></i><span><strong>Calendar Info</strong> — shows the academic calendar events (exams, breaks, holidays).</span></li>
        <li class="flex gap-2"><i class="bx bx-expand-alt text-[#a1a1aa] shrink-0 mt-0.5"></i><span><strong>Expand All / Collapse All</strong> — opens or closes all week accordions at once.</span></li>
        <li class="flex gap-2"><i class="bx bx-skip-next text-[#d97706] shrink-0 mt-0.5"></i><span><strong>Next Incomplete</strong> — jumps to the first week that still has missing content.</span></li>
    </ul>
</x-accordion>

<x-accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Forgetting to generate weeks — the step is empty until you click Generate Weeks.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Regenerating weeks after filling content — all existing content is permanently deleted.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Leaving weeks with no CO selected — incomplete weeks are flagged with an amber dot and block submission.</span></li>
    </ul>
</x-accordion>
