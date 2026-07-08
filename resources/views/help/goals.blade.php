{{-- Help content: College Goals --}}

<x-accordion title="Overview" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        College Goals define the strategic outcomes a college aims to achieve. They are referenced throughout the curriculum and appear in syllabi.
    </p>
</x-accordion>

<x-accordion title="Step-by-Step Guide" icon="list-ol" color="slate">
    <ol class="space-y-2.5 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Select a college from the dropdown at the top of the page.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Click <strong>Add Goal</strong> to open the form.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>Enter the goal description and save. The code (a, b, c…) is assigned automatically.</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">4</span>
            <span>Use the <i class="bx bx-edit text-[#2563eb]"></i> icon to edit or <i class="bx bx-trash text-[#e11d48]"></i> to delete a goal.</span>
        </li>
    </ol>
</x-accordion>

<x-accordion title="Tips" icon="bulb" color="amber">
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
    </ul>
</x-accordion>

<x-accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Deleting a goal that is already referenced in syllabi — verify usage before deleting.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Adding duplicate goals with slightly different wording — review existing goals first.</span>
        </li>
    </ul>
</x-accordion>

<x-accordion title="Frequently Asked Questions" icon="question-mark" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46]">
        <div>
            <p class="font-semibold text-[#09090b]">Can I reorder goals?</p>
            <p class="mt-0.5 text-[#52525b]">Not manually — goals are ordered by creation date. Codes are reassigned on delete to stay sequential.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">Who can manage goals?</p>
            <p class="mt-0.5 text-[#52525b]">Admins (all colleges) and Deans (their assigned college only).</p>
        </div>
    </div>
</x-accordion>
