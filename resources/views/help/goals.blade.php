{{-- Help content: College Goals — Updated with latest system organization --}}

<x-layout.accordion title="Overview" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        College Goals define the strategic outcomes a college aims to achieve. They are referenced throughout the curriculum and appear in syllabi. Goals cascade down to department objectives and then to program outcomes.
    </p>
    <p class="text-[13px] text-[#3f3f46] leading-relaxed mt-2">
        Access goal management via <strong>CQI → Goals</strong> in the navigation menu. Goals are organized by college, and you can only manage goals for colleges you have access to.
    </p>
</x-layout.accordion>

<x-layout.accordion title="Step-by-Step Guide" icon="list-ol" color="slate">
    <ol class="space-y-2.5 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Select a college from the dropdown at the top of the page. Admins see all colleges; Deans see only their assigned college.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Click <strong>Add Goal</strong> to open the form modal.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>Enter the goal description and save. The code (a, b, c…) is assigned automatically based on the existing goals count.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">4</span>
            <span>Use the <i class="bx bx-edit text-[#2563eb]"></i> icon to edit or <i class="bx bx-trash text-[#e11d48]"></i> to delete a goal.</span>
        </li>
    </ol>
</x-layout.accordion>

<x-layout.accordion title="Goal Hierarchy" icon="pyramid" color="blue">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>College Goals are part of the CQI (Continuous Quality Improvement) hierarchy:</p>
        <div class="mt-2 space-y-1.5 pl-2 border-l-2 border-[#a1a1aa]">
            <div class="flex items-center gap-2">
                <span class="font-semibold text-[#09090b]">College Goals</span>
                <span class="text-[12px] text-[#71717a]">→</span>
                <span class="text-[12px] text-[#52525b]">Department Objectives</span>
            </div>
            <div class="flex items-center gap-2 pl-4">
                <span class="text-[12px] text-[#52525b]">Department Objectives</span>
                <span class="text-[12px] text-[#71717a]">→</span>
                <span class="text-[12px] text-[#52525b]">Program PEOs</span>
            </div>
            <div class="flex items-center gap-2 pl-6">
                <span class="text-[12px] text-[#52525b]">Program PEOs</span>
                <span class="text-[12px] text-[#71717a]">→</span>
                <span class="text-[12px] text-[#52525b]">Program Outcomes (POs)</span>
            </div>
        </div>
        <p class="mt-2 text-[12px] text-[#71717a]">Each level refines the broader goals into more specific, measurable outcomes.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Tips" icon="bulb" color="amber">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Keep goals broad and outcome-focused — they cascade down to department objectives.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Codes are resequenced automatically when a goal is deleted — no gaps.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Deans can only manage goals for their assigned college.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Use measurable language — goals should be specific enough to evaluate achievement.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Limit goals to 5-8 per college for better focus and measurability.</span>
        </li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Deleting a goal that is already referenced in syllabi — verify usage before deleting.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Adding duplicate goals with slightly different wording — review existing goals first.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Making goals too specific — save details for department objectives.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Using vague goals like "Improve quality" — be specific and measurable.</span>
        </li>
    </ul>
</x-layout.accordion>

<x-layout.accordion title="Frequently Asked Questions" icon="question-mark" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46]">
        <div>
            <p class="font-semibold text-[#09090b]">Can I reorder goals?</p>
            <p class="mt-0.5 text-[#52525b]">Not manually — goals are ordered by creation date. Codes are reassigned on delete to stay sequential.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">Who can manage goals?</p>
            <p class="mt-0.5 text-[#52525b]">Admins (all colleges) and Deans (their assigned college only).</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">What happens when I delete a goal?</p>
            <p class="mt-0.5 text-[#52525b]">The goal is removed and codes are resequenced. Check if the goal is used in syllabi before deleting.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">How many goals should a college have?</p>
            <p class="mt-0.5 text-[#52525b]">Typically 5-8 goals are recommended for focus and measurability, but this varies by institution.</p>
        </div>
    </div>
</x-layout.accordion>
