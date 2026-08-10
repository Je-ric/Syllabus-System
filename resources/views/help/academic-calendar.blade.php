{{-- Help: Academic Calendar Management --}}

<x-layout.accordion title="Overview" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        Academic Calendars define the date ranges for academic years and semesters. They are used to generate weekly coverage in syllabi and determine when specific events occur.
    </p>
    <p class="text-[13px] text-[#3f3f46] leading-relaxed mt-2">
        Access calendar management via <strong>Academic → Academic Calendar</strong> in the navigation menu. Each academic year contains two semesters (1st and 2nd).
    </p>
</x-layout.accordion>

<x-layout.accordion title="Creating an Academic Calendar" icon="plus-circle" color="emerald">
    <ol class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Click <strong>Add Academic Year</strong> on the index page.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Enter the <strong>Academic Year</strong> in YYYY-YYYY format (e.g., 2025-2026). The end year must be greater than the start year.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>Select start and end dates for <strong>1st Semester</strong> using the date picker.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">4</span>
            <span>Select start and end dates for <strong>2nd Semester</strong>. The 2nd semester must start after the 1st semester ends.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">5</span>
            <span>Click <strong>Create Calendar</strong> and confirm the action.</span>
        </li>
    </ol>
</x-layout.accordion>

<x-layout.accordion title="Managing Events" icon="calendar-event" color="blue">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>After creating a calendar, click <strong>Manage Events</strong> to add holidays, breaks, exams, and other events for each semester.</p>
        <p class="mt-1">Events control how weeks are generated in syllabi:</p>
        
        <div class="space-y-2 mt-2">
            <div class="flex items-start gap-3 p-2.5 rounded-lg bg-blue-50 border border-blue-200">
                <i class="bx bx-info-circle text-blue-600 shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-blue-800">Reference Events (Holiday, Other)</p>
                    <p class="text-[12px] text-blue-700 mt-0.5">Weeks are created normally and remain editable by faculty. Use for class suspensions, observances, deadlines.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-2.5 rounded-lg bg-amber-50 border border-amber-200">
                <i class="bx bx-skip-next text-amber-600 shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-amber-800">Skip Events (Break)</p>
                    <p class="text-[12px] text-amber-700 mt-0.5">Weeks are SKIPPED entirely. No syllabus week row is created. Use for Christmas break, semester breaks, health breaks.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-2.5 rounded-lg bg-red-50 border border-red-200">
                <i class="bx bx-lock text-red-600 shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-red-800">Lock Events (Exam, Non-Teaching)</p>
                    <p class="text-[12px] text-red-700 mt-0.5">Weeks are created but LOCKED. Faculty cannot edit the content. Use for exam periods, institutional events.</p>
                </div>
            </div>
        </div>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Week Generation Feedback" icon="bar-chart" color="purple">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>When faculty generate weekly coverage for a syllabus, the system now provides detailed feedback:</p>
        <ul class="space-y-1.5 mt-2">
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span><strong>Total weeks</strong> created for the semester</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span><strong>Skipped weeks</strong> due to break events</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span><strong>Locked weeks</strong> due to exam/non-teaching events</span></li>
        </ul>
        <p class="mt-2 text-[12px] text-[#71717a]">Example: "Weekly coverage generated (16 weeks, 2 skipped, 3 locked)."</p>
        <p class="mt-1 text-[12px] text-[#71717a]">This helps faculty understand the impact of calendar events on their syllabus structure.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Adding Events" icon="list-ol" color="slate">
    <ol class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Click <strong>Add Event</strong> on the events page.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Select the <strong>Event Type</strong> using the dropdown or quick buttons.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>Choose a date range (for breaks) or single date (for holidays/exams).</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">4</span>
            <span>Enter the <strong>Event Name</strong> (e.g., "Christmas Break", "Midterm Exam").</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">5</span>
            <span>Click <strong>Add Event</strong> to save.</span>
        </li>
    </ol>
</x-layout.accordion>

<x-layout.accordion title="Setting Active Calendar" icon="check-circle" color="emerald">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>The <strong>Active Calendar</strong> is the default calendar used for new syllabi. Set a calendar as active when it represents the current academic year.</p>
        <p class="mt-1">To set active:</p>
        <ol class="space-y-1 mt-2 pl-1">
            <li class="flex gap-2">
                <span class="shrink-0 w-4 text-[#a1a1aa] font-bold">1.</span>
                <span>Locate the academic year on the index page</span>
            </li>
            <li class="flex gap-2">
                <span class="shrink-0 w-4 text-[#a1a1aa] font-bold">2.</span>
                <span>Click <strong>Set Active</strong> button</span>
            </li>
            <li class="flex gap-2">
                <span class="shrink-0 w-4 text-[#a1a1aa] font-bold">3.</span>
                <span>The calendar will show a green "Active" indicator</span>
            </li>
        </ol>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Editing and Deleting" icon="edit" color="slate">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p><strong>Editing Calendars:</strong></p>
        <ul class="space-y-1 mt-1">
            <li class="flex gap-2"><i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i><span>Click the edit icon on a calendar card</span></li>
            <li class="flex gap-2"><i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i><span>You can only edit dates, not the academic year itself</span></li>
            <li class="flex gap-2"><i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i><span>Calendars with events are locked - remove events first to edit</span></li>
        </ul>
        
        <p class="mt-2"><strong>Deleting Calendars:</strong></p>
        <ul class="space-y-1 mt-1">
            <li class="flex gap-2"><i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i><span>Click the trash icon on a calendar card</span></li>
            <li class="flex gap-2"><i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i><span>Cannot delete if syllabi are linked to it</span></li>
            <li class="flex gap-2"><i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i><span>Cannot delete the active calendar - set another as active first</span></li>
        </ul>
        
        <p class="mt-2"><strong>Editing Events:</strong></p>
        <ul class="space-y-1 mt-1">
            <li class="flex gap-2"><i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i><span>Click on an event in the calendar view to edit it</span></li>
            <li class="flex gap-2"><i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i><span>You can change type, date, and name</span></li>
        </ul>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Tips" icon="bulb" color="amber">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Semesters can span calendar years (e.g., 2nd sem: Nov 2025 - Apr 2026).</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Use "Break" type for Christmas break to skip weeks in syllabi.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Use "Holiday" type for class suspensions - they remain as reference for faculty.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Use quick buttons in the event form for common event types.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Set the correct calendar as active before faculty create syllabi.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Review the calendar preview to ensure events are placed correctly.</span></li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Using "Holiday" for Christmas break - this won't skip the week. Use "Break" instead.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Forgetting to add break events - weeks will be generated incorrectly.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Not setting the active calendar - new syllabi will use the wrong dates.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Deleting a calendar with linked syllabi - this is blocked for data protection.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Entering invalid academic year format - must be YYYY-YYYY with end year greater than start.</span></li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Frequently Asked Questions" icon="question-mark" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46]">
        <div>
            <p class="font-semibold text-[#09090b]">What's the difference between Holiday and Break?</p>
            <p class="mt-0.5 text-[#52525b]">Holiday creates a week that faculty can edit (reference only). Break skips the week entirely - no syllabus week is created.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">Can a semester span multiple calendar years?</p>
            <p class="mt-0.5 text-[#52525b]">Yes. For example, 2nd semester can start in November 2025 and end in April 2026.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">What happens if I change calendar dates after syllabi are created?</p>
            <p class="mt-0.5 text-[#52525b]">You'll see a warning about stale weeks. Faculty will need to regenerate their weeks manually in the syllabus wizard.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">Can I have multiple events on the same date?</p>
            <p class="mt-0.5 text-[#52525b]">No. Each date can only have one event. Edit the existing event if you need to change it.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">Who can manage academic calendars?</p>
            <p class="mt-0.5 text-[#52525b]">Admins only. This is a system-level configuration that affects all syllabi.</p>
        </div>
    </div>
</x-layout.accordion>
