{{-- Help: Syllabi Index — Updated with latest system changes --}}

<x-layout.accordion title="Overview" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        This page shows all your syllabi grouped by status. Each syllabus belongs to one course and one academic semester. You can have one syllabus per course — creating a duplicate redirects you to the existing one.
    </p>
    <p class="text-[13px] text-[#3f3f46] leading-relaxed mt-2">
        Syllabi follow a structured workflow: <strong>Draft → Under Review → For Revision → Approved</strong>. Each status has specific permissions and actions available.
    </p>
</x-layout.accordion>

<x-layout.accordion title="Status Tabs" icon="list-ul" color="slate">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <div class="flex items-start gap-2.5 p-2 rounded-lg bg-[#fffbeb] border border-[#fde68a]">
            <span class="shrink-0 w-2.5 h-2.5 rounded-full bg-[#d97706] mt-1.5"></span>
            <div><p class="font-semibold text-[#92400e]">Draft</p><p class="text-[12px] mt-0.5">In progress. You can edit, preview, and delete drafts. Progress is saved automatically as you complete wizard steps.</p></div>
        </div>
        <div class="flex items-start gap-2.5 p-2 rounded-lg bg-[#eff6ff] border border-[#bfdbfe]">
            <span class="shrink-0 w-2.5 h-2.5 rounded-full bg-[#2563eb] mt-1.5"></span>
            <div><p class="font-semibold text-[#1e40af]">Under Review</p><p class="text-[12px] mt-0.5">Submitted and awaiting chair/dean review. Read-only. Use the Review Queue to track progress.</p></div>
        </div>
        <div class="flex items-start gap-2.5 p-2 rounded-lg bg-[#fff1f2] border border-[#fecdd3]">
            <span class="shrink-0 w-2.5 h-2.5 rounded-full bg-[#e11d48] mt-1.5"></span>
            <div><p class="font-semibold text-[#9f1239]">For Revision</p><p class="text-[12px] mt-0.5">Returned by a reviewer with comments. Click Continue to address feedback and resubmit.</p></div>
        </div>
        <div class="flex items-start gap-2.5 p-2 rounded-lg bg-[#f0fdf4] border border-[#d1fae5]">
            <span class="shrink-0 w-2.5 h-2.5 rounded-full bg-[#16a34a] mt-1.5"></span>
            <div><p class="font-semibold text-[#166534]">Approved</p><p class="text-[12px] mt-0.5">Fully approved. View or preview only. Cannot be edited without creating a new version.</p></div>
        </div>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Creating a Syllabus" icon="plus-circle" color="emerald">
    <ol class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Click <strong>Create Syllabus</strong> and select a program and course from the dropdowns.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Complete all 6 wizard steps. Navigate freely between steps — progress is saved when you move forward.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>On the Review step, assign signatories/reviewers and submit when all steps are complete.</span>
        </li>
    </ol>
</x-layout.accordion>

<x-layout.accordion title="Wizard Steps Overview" icon="list-check" color="blue">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <div class="flex items-start gap-2">
            <span class="shrink-0 font-bold text-[#2563eb]">1.</span>
            <div><strong>Academic Calendar</strong> — Select semester (determines weeks)</div>
        </div>
        <div class="flex items-start gap-2">
            <span class="shrink-0 font-bold text-[#2563eb]">2.</span>
            <div><strong>Course Components</strong> — Schedule, consultation hours, instructor info</div>
        </div>
        <div class="flex items-start gap-2">
            <span class="shrink-0 font-bold text-[#2563eb]">3.</span>
            <div><strong>Course Outcomes</strong> — Define measurable learning outcomes</div>
        </div>
        <div class="flex items-start gap-2">
            <span class="shrink-0 font-bold text-[#2563eb]">4.</span>
            <div><strong>Weekly Coverage</strong> — Topic, activities, assessments per week</div>
        </div>
        <div class="flex items-start gap-2">
            <span class="shrink-0 font-bold text-[#2563eb]">5.</span>
            <div><strong>Course Evaluation</strong> — Assign weight percentages to assessments</div>
        </div>
        <div class="flex items-start gap-2">
            <span class="shrink-0 font-bold text-[#2563eb]">6.</span>
            <div><strong>Review & Submit</strong> — Preview, assign reviewers, submit for approval</div>
        </div>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Tips" icon="bulb" color="amber">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>The progress bar on each draft card shows which step you're on.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Only draft syllabi can be deleted. Submitted or approved syllabi cannot be removed.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Any faculty can create a syllabus for any course — there is no department restriction.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>The course must have at least one PO mapping before you can create a syllabus for it.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Use the <strong>Review Queue</strong> to track syllabus approval progress and respond to feedback.</span></li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Not verifying course PO mappings before starting — check that the course has mapped POs first.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Skipping the Preview step before submitting — always preview to check formatting.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Forgetting to assign signatories before submission — this is required for approval.</span></li>
        <li class="flex gap-2"><i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i><span>Not addressing all reviewer comments when in "For Revision" status.</span></li>
    </ul>
</x-layout.accordion>
