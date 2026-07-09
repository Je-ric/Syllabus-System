{{-- Help content: Courses --}}

<x-layout.accordion title="Overview" icon="info-circle" color="emerald" :open="true">
    <div class="space-y-2 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>Courses are the individual subjects within a program. Each course belongs to a specific year level and semester, carries credit units, and is mapped to Program Outcomes (POs) using IED levels.</p>
        <p class="mt-1">A course must have at least one PO mapping before a syllabus can be created for it.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Browsing Courses" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46]">
        <p>Courses are grouped by <strong>Year Level → Semester</strong>. Each row shows:</p>
        <ul class="space-y-1.5 mt-1">
            <li class="flex gap-2">
                <i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i>
                <span><strong>Course Code & Title</strong></span>
            </li>
            <li class="flex gap-2">
                <i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i>
                <span><strong>Units</strong> — credit units for the course</span>
            </li>
            <li class="flex gap-2">
                <i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i>
                <span><strong>Type</strong> — LEC only, or LEC+LAB</span>
            </li>
            <li class="flex gap-2">
                <i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i>
                <span><strong>Class Hours</strong> — LEC hours and LAB hours (if applicable)</span>
            </li>
            <li class="flex gap-2">
                <i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i>
                <span><strong>PO columns</strong> — IED level badge per Program Outcome (I, E, or D)</span>
            </li>
        </ul>
        <p class="mt-2">Click <strong>Program Outcomes</strong> to open a reference panel showing the full text of each PO for the selected program.</p>
        <p class="mt-1">Use the <strong>Active / Archived</strong> toggle to switch between active courses and archived ones.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Adding a Course" icon="plus-circle" color="emerald">
    <ol class="space-y-2.5 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Select a program using the dropdowns at the top, then click <strong>Add Course</strong>.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Fill in the <strong>Course Code</strong>, <strong>Course Title</strong>, and optional description.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>Set <strong>Credit Units</strong>, <strong>Has Laboratory</strong> (Yes/No), <strong>Year Level</strong>, and <strong>Semester</strong>.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">4</span>
            <span>Set <strong>LEC Class Hours</strong> (and <strong>LAB Class Hours</strong> if the course has a lab component). Optionally set the <strong>Passing Mark</strong>, <strong>Prerequisite</strong>, and <strong>Corequisite</strong>.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">5</span>
            <span>In the <strong>Program Outcomes Mapping</strong> table, select an IED level (I, E, or D) for each applicable PO. Leave blank if the PO does not apply to this course.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">6</span>
            <span>Click <strong>Save Course</strong> — a confirmation dialog will appear. Review and click <strong>Confirm & Create</strong>.</span>
        </li>
    </ol>
</x-layout.accordion>

<x-layout.accordion title="IED Levels Explained" icon="info-circle" color="blue">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>IED indicates how deeply a course addresses each Program Outcome:</p>
        <div class="space-y-2 mt-2">
            <div class="flex items-start gap-3 p-2.5 rounded-lg bg-[#f0fdf4] border border-[#d1fae5]">
                <span class="shrink-0 inline-flex items-center justify-center w-6 h-6 rounded bg-emerald-600 text-white text-[11px] font-bold">I</span>
                <div>
                    <p class="font-semibold text-[#166534]">Introductory</p>
                    <p class="text-[12px] text-[#3f3f46] mt-0.5">The course introduces the concept — foundational exposure only.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-2.5 rounded-lg bg-[#eff6ff] border border-[#bfdbfe]">
                <span class="shrink-0 inline-flex items-center justify-center w-6 h-6 rounded bg-blue-600 text-white text-[11px] font-bold">E</span>
                <div>
                    <p class="font-semibold text-[#1e40af]">Enabling</p>
                    <p class="text-[12px] text-[#3f3f46] mt-0.5">The course builds on the concept — students develop the skill further.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-2.5 rounded-lg bg-[#fdf4ff] border border-[#e9d5ff]">
                <span class="shrink-0 inline-flex items-center justify-center w-6 h-6 rounded bg-purple-600 text-white text-[11px] font-bold">D</span>
                <div>
                    <p class="font-semibold text-[#6b21a8]">Demonstrating</p>
                    <p class="text-[12px] text-[#3f3f46] mt-0.5">The course requires full demonstration of the outcome — mastery level.</p>
                </div>
            </div>
        </div>
        <p class="mt-2 text-[12px] text-[#71717a]">Use <strong>Reset IED Levels</strong> on the form to clear all selections and start over.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Archive vs Delete" icon="error-circle" color="amber">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <div class="flex items-start gap-3 p-2.5 rounded-lg bg-[#fffbeb] border border-[#fde68a]">
            <i class="bx bx-archive text-[#d97706] text-base shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold text-[#92400e]">Archive</p>
                <p class="text-[12px] mt-0.5">Hides the course from the active list. The course and all its data are preserved. You can restore it at any time from the <strong>Archived</strong> tab.</p>
            </div>
        </div>
        <div class="flex items-start gap-3 p-2.5 rounded-lg bg-[#fff1f2] border border-[#fecdd3]">
            <i class="bx bx-trash text-[#e11d48] text-base shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold text-[#9f1239]">Delete</p>
                <p class="text-[12px] mt-0.5">Permanently removes the course and <strong>all associated syllabi</strong>, components, outcomes, weekly coverage, evaluations, and PO mappings. This cannot be undone.</p>
            </div>
        </div>
        <p class="mt-1 text-[12px] text-[#71717a]">Delete is only available to Admins and Chairs managing their own department's programs.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Tips" icon="bulb" color="amber">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Map at least one PO before creating a syllabus — the syllabus wizard will block creation otherwise.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>The <strong>Has Laboratory</strong> field is locked once syllabi exist for the course — delete all syllabi first if you need to change it.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Enter "None" in the Prerequisite or Corequisite fields if there are no requirements — don't leave them blank if the program requires a value.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Archive a course instead of deleting it when you want to retire it without losing historical syllabus data.</span>
        </li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Deleting a course that has existing syllabi — all syllabus data is permanently lost. Archive instead if unsure.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Saving a course with no PO mappings — faculty won't be able to create a syllabus for it until at least one PO is mapped.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Setting LAB class hours without enabling <strong>Has Laboratory</strong> — the LAB hours field only appears when "Yes" is selected.</span>
        </li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Frequently Asked Questions" icon="question-mark" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46]">
        <div>
            <p class="font-semibold text-[#09090b]">Who can manage courses?</p>
            <p class="mt-0.5 text-[#52525b]">Admins (all programs) and Chairs (programs within their assigned department only). Only Admins and Chairs can delete courses.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">Can I change the program a course belongs to?</p>
            <p class="mt-0.5 text-[#52525b]">No — the program is set when the course is created and cannot be changed. Delete and recreate the course under the correct program if needed.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">Why can't I see the Add Course button?</p>
            <p class="mt-0.5 text-[#52525b]">A program must be selected first. Use the college → department → program dropdowns at the top of the page.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">What happens to syllabi when a course is archived?</p>
            <p class="mt-0.5 text-[#52525b]">Nothing — syllabi are preserved. Archiving only hides the course from the active listing.</p>
        </div>
    </div>
</x-layout.accordion>
