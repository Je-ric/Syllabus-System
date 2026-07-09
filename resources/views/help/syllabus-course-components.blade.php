{{-- Help: Step 2 — Course Components --}}

<x-layout.accordion title="What This Step Does" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        You fill in the class schedule and consultation hours for the LEC component — and for the LAB component if the course has one. Instructor profile details (name, email, phone, office) are pulled automatically from your account.
    </p>
</x-layout.accordion>

<x-layout.accordion title="LEC Component" icon="book-open" color="emerald">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p><strong>Instructor Profile</strong> — auto-populated from your account. To update it, edit your profile from the top navigation.</p>
        <p><strong>Class Hours & Passing Mark</strong> — read-only, set in the course settings.</p>
        <p><strong>Class Schedule</strong> — click <strong>Add</strong> to add a day + time range. You can add multiple rows for split schedules.</p>
        <p><strong>Consultation Hours</strong> — same format. A red warning appears if a consultation slot overlaps with a class schedule on the same day — fix the overlap before saving.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="LAB Component" icon="test-tube" color="blue">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Only visible if the course has a laboratory component.</p>
        <p><strong>Laboratory Instructor</strong> — select from the dropdown. The profile fields populate automatically after selection. This is required before the rest of the LAB section unlocks.</p>
        <p>The LAB instructor can be a different person from the LEC instructor.</p>
        <p>Class schedule and consultation hours work the same way as LEC, with the same overlap detection.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Saving" icon="save" color="slate">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Click <strong>Save All</strong> in the sticky footer bar to save both LEC and LAB sections together.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Clicking <strong>Next</strong> also triggers a save automatically before navigating.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>If consultation hours overlap a class schedule, saving is blocked until the conflict is resolved.</span></li>
    </ul>
</x-layout.accordion>
