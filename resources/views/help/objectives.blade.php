{{-- Help content: Department Objectives --}}

<x-accordion title="Overview" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        Department Objectives are specific, measurable outcomes that support the broader College Goals. They are scoped to a single department and feed into program-level PEOs.
    </p>
</x-accordion>

<x-accordion title="Step-by-Step Guide" icon="list-ol" color="slate">
    <ol class="space-y-2.5 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-[#dcfce7] text-[#166534] text-[10px] font-bold mt-0.5">1</span>
            <span>Select a college, then select a department from the dropdowns.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-[#dcfce7] text-[#166534] text-[10px] font-bold mt-0.5">2</span>
            <span>Click <strong>Add Objective</strong> to open the form.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-[#dcfce7] text-[#166534] text-[10px] font-bold mt-0.5">3</span>
            <span>Enter the objective text and save. The code is assigned automatically.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-[#dcfce7] text-[#166534] text-[10px] font-bold mt-0.5">4</span>
            <span>Use the <i class="bx bx-edit text-[#2563eb]"></i> icon to edit or <i class="bx bx-trash text-[#e11d48]"></i> to delete.</span>
        </li>
    </ol>
</x-accordion>

<x-accordion title="Tips" icon="bulb" color="amber">
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
    </ul>
</x-accordion>

<x-accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Confusing department objectives with program PEOs — objectives are department-wide, PEOs are program-specific.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Adding vague objectives like "Improve quality" — be specific and measurable.</span>
        </li>
    </ul>
</x-accordion>

<x-accordion title="Frequently Asked Questions" icon="question-mark" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46]">
        <div>
            <p class="font-semibold text-[#09090b]">Who can manage objectives?</p>
            <p class="mt-0.5 text-[#52525b]">Admins (all departments) and Chairs (their assigned department only).</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">How are objectives related to PEOs?</p>
            <p class="mt-0.5 text-[#52525b]">Objectives are department-level. PEOs are defined per program within that department and should align with these objectives.</p>
        </div>
    </div>
</x-accordion>
