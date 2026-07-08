{{-- Help content: PEOs & POs (Programs module) --}}

<x-accordion title="Overview" icon="info-circle" color="emerald" :open="true">
    <div class="space-y-2 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>This module manages two things for a selected program:</p>
        <ul class="space-y-1.5 mt-2">
            <li class="flex gap-2">
                <span class="shrink-0 inline-flex items-center justify-center w-5 h-5 rounded bg-emerald-100 text-emerald-700 text-[10px] font-bold mt-0.5">PEO</span>
                <span><strong>Program Educational Objectives</strong> — what graduates are expected to be professionally, 3–5 years after graduation.</span>
            </li>
            <li class="flex gap-2">
                <span class="shrink-0 inline-flex items-center justify-center w-5 h-5 rounded bg-blue-100 text-blue-700 text-[10px] font-bold mt-0.5">PO</span>
                <span><strong>Program Outcomes</strong> — abilities and competencies students must have by the time of graduation.</span>
            </li>
        </ul>
        <p class="mt-2">Each PO can be mapped to one or more PEOs. The <strong>Matrix View</strong> tab shows the full mapping at a glance.</p>
    </div>
</x-accordion>

<x-accordion title="Step-by-Step Guide" icon="list-ol" color="slate">
    <div class="space-y-4 text-[13px] text-[#3f3f46]">

        <div>
            <p class="font-bold text-[#09090b] mb-2">1. Select a Program</p>
            <p>Use the college → department → program dropdowns at the top of the page. The page reloads with the selected program's data.</p>
        </div>

        <div>
            <p class="font-bold text-[#09090b] mb-2">2. Add PEOs (PEOs tab)</p>
            <ol class="space-y-1.5 pl-1">
                <li class="flex gap-2">
                    <span class="shrink-0 w-4 text-[#a1a1aa] font-bold">a.</span>
                    <span>Click <strong>Add PEO</strong> — a new row appears with an empty text area.</span>
                </li>
                <li class="flex gap-2">
                    <span class="shrink-0 w-4 text-[#a1a1aa] font-bold">b.</span>
                    <span>Type the PEO description. The code (PEO 1, PEO 2…) is assigned automatically on save.</span>
                </li>
                <li class="flex gap-2">
                    <span class="shrink-0 w-4 text-[#a1a1aa] font-bold">c.</span>
                    <span>Add as many PEOs as needed, then click <strong>Save All</strong> once. A confirmation dialog will appear.</span>
                </li>
            </ol>
        </div>

        <div>
            <p class="font-bold text-[#09090b] mb-2">3. Add POs (POs tab)</p>
            <ol class="space-y-1.5 pl-1">
                <li class="flex gap-2">
                    <span class="shrink-0 w-4 text-[#a1a1aa] font-bold">a.</span>
                    <span>Click <strong>Add PO</strong> — a new row appears.</span>
                </li>
                <li class="flex gap-2">
                    <span class="shrink-0 w-4 text-[#a1a1aa] font-bold">b.</span>
                    <span>Type the PO description, then click <strong>Save All</strong>.</span>
                </li>
            </ol>
        </div>

        <div>
            <p class="font-bold text-[#09090b] mb-2">4. Map POs to PEOs</p>
            <p>After saving a PO, a row of PEO chips appears below its text. Click a chip to toggle the mapping — it saves immediately without needing Save All. A spinning indicator shows while the save is in progress.</p>
            <p class="mt-1.5">Click <strong>View PEOs</strong> on any PO row to open a reference panel showing the full text of each PEO.</p>
        </div>

        <div>
            <p class="font-bold text-[#09090b] mb-2">5. Review the Matrix</p>
            <p>Switch to the <strong>Matrix View</strong> tab to see a read-only grid of all POs vs PEOs with mapping counts. This is useful for verifying coverage before creating syllabi.</p>
        </div>

    </div>
</x-accordion>

<x-accordion title="Unsaved Changes" icon="error-circle" color="amber">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>PEOs and POs use a <strong>batch save</strong> pattern — changes are staged locally until you click <strong>Save All</strong>.</p>
        <ul class="space-y-1.5 mt-2">
            <li class="flex gap-2">
                <span class="shrink-0 w-2 h-2 rounded-full bg-emerald-400 mt-1.5"></span>
                <span>Green border = new, unsaved row.</span>
            </li>
            <li class="flex gap-2">
                <span class="shrink-0 w-2 h-2 rounded-full bg-amber-400 mt-1.5"></span>
                <span>Amber border = existing row with unsaved edits.</span>
            </li>
            <li class="flex gap-2">
                <span class="shrink-0 w-2 h-2 rounded-full bg-slate-300 mt-1.5"></span>
                <span>No border highlight = saved and unchanged.</span>
            </li>
        </ul>
        <p class="mt-2">Click <strong>Revert</strong> to discard all staged changes and restore the last saved state.</p>
        <p class="mt-1">PEO–PO mappings (the chips) save <em>immediately</em> on toggle — no Save All needed.</p>
    </div>
</x-accordion>

<x-accordion title="Tips" icon="bulb" color="amber">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Add all PEOs before adding POs — PEO mapping chips only appear on saved POs, and only if PEOs already exist.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>A PO cannot be deleted if it is already mapped in an existing syllabus course outcome. Remove the mapping from the syllabus first.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>Codes (PEO 1, PEO 2 / PO a, PO b…) are auto-assigned and resequenced when a row is deleted — you cannot set them manually.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i>
            <span>A course must have at least one PO mapping before a syllabus can be created for it.</span>
        </li>
    </ul>
</x-accordion>

<x-accordion title="Common Mistakes" icon="error-circle" color="rose">
    <ul class="space-y-2 text-[13px] text-[#3f3f46]">
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Navigating away before clicking <strong>Save All</strong> — unsaved rows are lost.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Trying to map PEOs on an unsaved PO — the mapping chips are locked until the PO is saved.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Confusing PEOs (post-graduation, 3–5 years) with POs (at graduation). They serve different accreditation purposes.</span>
        </li>
        <li class="flex gap-2">
            <i class="bx bx-x-circle text-[#e11d48] shrink-0 mt-0.5"></i>
            <span>Adding a blank row and clicking Save All — you'll be warned to fill it in first.</span>
        </li>
    </ul>
</x-accordion>

<x-accordion title="Frequently Asked Questions" icon="question-mark" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46]">
        <div>
            <p class="font-semibold text-[#09090b]">Who can manage PEOs and POs?</p>
            <p class="mt-0.5 text-[#52525b]">Admins (all programs) and Chairs (programs within their assigned department only).</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">Can I reorder PEOs or POs?</p>
            <p class="mt-0.5 text-[#52525b]">No — they are ordered by creation date. Codes are resequenced automatically when a row is deleted.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">Why is the mapping chip locked?</p>
            <p class="mt-0.5 text-[#52525b]">The PO must be saved first. Unsaved POs show a lock icon — click Save All on the PO, then the chips become interactive.</p>
        </div>
        <div>
            <p class="font-semibold text-[#09090b]">What does the Matrix View show?</p>
            <p class="mt-0.5 text-[#52525b]">A read-only grid of every PO (rows) against every PEO (columns), with a checkmark where a mapping exists. The footer row shows how many POs map to each PEO.</p>
        </div>
    </div>
</x-accordion>
