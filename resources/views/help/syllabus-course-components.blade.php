{{-- Help: Step 2 — Course Components — Updated with latest system changes --}}

<x-layout.accordion title="What This Step Does" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        You fill in the class schedule and consultation hours for the LEC component — and for the LAB component if the course has one. Instructor profile details (name, email, phone, office) are pulled automatically from your account.
    </p>
    <p class="text-[13px] text-[#3f3f46] leading-relaxed mt-2">
        This step ensures the syllabus reflects accurate scheduling information for both students and administrators.
    </p>
</x-layout.accordion>

<x-layout.accordion title="LEC Component" icon="book-open" color="emerald">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p><strong>Instructor Profile</strong> — auto-populated from your account. To update it, edit your profile from the top navigation menu.</p>
        <p><strong>Class Hours & Passing Mark</strong> — read-only, set in the course settings via <strong>Academic → Courses</strong>.</p>
        <p><strong>Class Schedule</strong> — click <strong>Add</strong> to add a day + time range. You can add multiple rows for split schedules (e.g., MWF 9-10, TTh 1-2).</p>
        <p><strong>Consultation Hours</strong> — same format. A red warning appears if a consultation slot overlaps with a class schedule on the same day — fix the overlap before saving.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="LAB Component" icon="test-tube" color="blue">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Only visible if the course has a laboratory component (set in course settings).</p>
        <p><strong>Laboratory Instructor</strong> — select from the dropdown. The profile fields populate automatically after selection. This is required before the rest of the LAB section unlocks.</p>
        <p>The LAB instructor can be a different person from the LEC instructor — useful for courses with separate lab instructors.</p>
        <p>Class schedule and consultation hours work the same way as LEC, with the same overlap detection.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Saving" icon="save" color="slate">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Click <strong>Save All</strong> in the sticky footer bar to save both LEC and LAB sections together.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Clicking <strong>Next</strong> also triggers a save automatically before navigating.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>If consultation hours overlap a class schedule, saving is blocked until the conflict is resolved.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Missing fields are highlighted in red — fill all required fields before saving.</span></li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Not updating your profile before starting — outdated instructor information will appear in the syllabus.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Leaving consultation hour overlaps — this prevents saving and must be fixed.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Forgetting to select a lab instructor for lab courses — the LAB section won't unlock without this.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Using inconsistent time formats — follow the HH:MM AM/PM format shown in the examples.</span></li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Tips" icon="bulb" color="amber">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Update your profile (name, email, phone, office) via the top navigation before starting a syllabus.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Use split schedules if your class meets at different times on different days.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Set realistic consultation hours that don't conflict with your teaching schedule.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>The schedule information appears in the printed syllabus — ensure accuracy.</span></li>
    </ul>
</x-layout.accordion>
