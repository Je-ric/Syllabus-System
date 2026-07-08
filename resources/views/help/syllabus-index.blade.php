{{-- Help: Syllabi Index --}}

<x-accordion title="Overview" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        This page shows all your syllabi grouped by status. Each syllabus belongs to one course and one academic semester. You can have one syllabus per course — creating a duplicate redirects you to the existing one.
    </p>
</x-accordion>

<x-accordion title="Status Tabs" icon="list-ul" color="slate">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <div class="flex items-start gap-2.5 p-2 rounded-lg bg-[#fffbeb] border border-[#fde68a]">
            <span class="shrink-0 w-2.5 h-2.5 rounded-full bg-[#d97706] mt-1.5"></span>
            <div><p class="font-semibold text-[#92400e]">Draft</p><p class="text-[12px] mt-0.5">In progress. You can edit, preview, and delete drafts.</p></div>
        </div>
        <div class="flex items-start gap-2.5 p-2 rounded-lg bg-[#eff6ff] border border-[#bfdbfe]">
            <span class="shrink-0 w-2.5 h-2.5 rounded-full bg-[#2563eb] mt-1.5"></span>
            <div><p class="font-semibold text-[#1e40af]">Under Review</p><p class="text-[12px] mt-0.5">Submitted and awaiting chair/dean review. Read-only.</p></div>
        </div>
        <div class="flex items-start gap-2.5 p-2 rounded-lg bg-[#fff1f2] border border-[#fecdd3]">
            <span class="shrink-0 w-2.5 h-2.5 rounded-full bg-[#e11d48] mt-1.5"></span>
            <div><p class="font-semibold text-[#9f1239]">For Revision</p><p class="text-[12px] mt-0.5">Returned by a reviewer with comments. Click Continue to address feedback.</p></div>
        </div>
        <div class="flex items-start gap-2.5 p-2 rounded-lg bg-[#f0fdf4] border border-[#d1fae5]">
            <span class="shrink-0 w-2.5 h-2.5 rounded-full bg-[#16a34a] mt-1.5"></span>
            <div><p class="font-semibold text-[#166534]">Approved</p><p class="text-[12px] mt-0.5">Fully approved. View or preview only.</p></div>
        </div>
    </div>
</x-accordion>

<x-accordion title="Creating a Syllabus" icon="plus-circle" color="emerald">
    <ol class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-[#dcfce7] text-[#166534] text-[10px] font-bold mt-0.5">1</span>
            <span>Click <strong>Create Syllabus</strong> and select a program and course.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-[#dcfce7] text-[#166534] text-[10px] font-bold mt-0.5">2</span>
            <span>Complete all 6 wizard steps. You can navigate freely between steps — progress is saved when you move forward.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-[#dcfce7] text-[#166534] text-[10px] font-bold mt-0.5">3</span>
            <span>On the Review step, submit for review once all steps are complete.</span>
        </li>
    </ol>
</x-accordion>

<x-accordion title="Tips" icon="bulb" color="amber">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>The progress bar on each draft card shows which step you're on.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Only draft syllabi can be deleted. Submitted or approved syllabi cannot be removed.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Any faculty can create a syllabus for any course — there is no department restriction.</span></li>
        <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>The course must have at least one PO mapping before you can create a syllabus for it.</span></li>
    </ul>
</x-accordion>
