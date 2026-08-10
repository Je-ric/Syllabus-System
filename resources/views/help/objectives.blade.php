{{-- Help content: Department Objectives — Updated with latest system organization --}}

<x-layout.accordion title="Overview" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        Department Objectives are specific, measurable outcomes that support the broader College Goals. They are scoped to a single department and feed into program-level PEOs.
    </p>
    <p class="text-[13px] text-[#3f3f46] leading-relaxed mt-2">
        Access objective management via <strong>CQI → Objectives</strong> in the navigation menu. Objectives are organized by college and department.
    </p>
</x-layout.accordion>

<x-layout.accordion title="Step-by-Step Guide" icon="list-ol" color="slate">
    <ol class="space-y-2.5 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Select a college, then select a department from the dropdowns. Admins see all departments; Chairs see only their assigned department.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Click <strong>Add Objective</strong> to open the form modal.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>Enter the objective text and save. The code is assigned automatically based on existing objectives count.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">4</span>
            <span>Use the <i class="bx bx-edit text-[#2563eb]"></i> icon to edit or <i class="bx bx-trash text-[#e11d48]"></i> to delete.</span>
        </li>
    </ol>
</x-layout.accordion>

<x-layout.accordion title="Objective Hierarchy" icon="pyramid" color="blue">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Department Objectives bridge College Goals and Program PEOs:</p>
        <div class="mt-2 space-y-1.5 pl-2 border-l-2 border-[#a1a1aa]">
            <div class="flex items-center gap-2">
                <span class="font-semibold text-[#09090b]">College Goals</span>
                <span class="text-[12px] text-[#71717a]">→</span>
                <span class="font-semibold text-[#09090b]">Department Objectives</span>
            </div>
            <div class="flex items-center gap-2 pl-4">
                <span class="text-[12px] text-[#52525b]">Department Objectives</span>
                <span class="text-[12px] text-[#71717a]">→</span>
                <span class="text-[12px] text-[#52525b]">Program PEOs</span>
            </div>
        </div>
        <p class="mt-2 text-[12px] text-[#71717a]">Objectives should be more specific than college goals but broader than program outcomes.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Tips" icon="bulb" color="amber">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Write objectives as measurable outcomes — start with action verbs (e.g. "Demonstrate", "Apply", "Analyze").</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Codes are resequenced automatically when an objective is deleted.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Chairs can only manage objectives for their assigned department.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Align objectives with college goals — each objective should support one or more college goals.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Limit objectives to 6-10 per department for focus and measurability.</span>
        </li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Confusing department objectives with program PEOs — objectives are department-wide, PEOs are program-specific.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Adding vague objectives like "Improve quality" — be specific and measurable.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Not aligning objectives with college goals — ensure each objective supports at least one college goal.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Writing objectives that are too specific to a single program — keep them department-wide.</span>
        </li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Frequently Asked Questions" icon="question-mark" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46]">
        <div>
            <p class="font-semibold text-[#09090b]">Who can manage objectives?</p>
            <p class="mt-0.5 text-[#52525b]">Admins (all departments) and Chairs (their assigned department only).</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">How are objectives related to PEOs?</p>
            <p class="mt-0.5 text-[#52525b]">Objectives are department-level. PEOs are defined per program within that department and should align with these objectives.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">Can I reference objectives in syllabi?</p>
            <p class="mt-0.5 text-[#52525b]">Objectives are primarily for curriculum planning. PEOs and POs are what appear in syllabi.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">What happens when I delete an objective?</p>
            <p class="mt-0.5 text-[#52525b]">The objective is removed and codes are resequenced. Verify if it's referenced in program planning before deleting.</p>
        </div>
    </div>
</x-layout.accordion>
