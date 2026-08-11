{{-- Help: University Structure Management --}}

<x-layout.accordion title="Overview" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        The University Structure module manages the academic hierarchy of your institution. It organizes colleges, departments, and programs in a hierarchical relationship that defines how courses, faculty, and curriculum are structured.
    </p>
    <p class="text-[13px] text-[#3f3f46] leading-relaxed mt-2">
        Access structure management via <strong>University → University Structure</strong> in the navigation menu. The interface uses a two-panel layout with colleges on the left and departments/programs on the right.
    </p>
</x-layout.accordion>

<x-layout.accordion title="Academic Hierarchy" icon="building" color="blue">
    <div class="space-y-3 text-[13px] text-[#3f3f46]">
        <div class="flex items-start gap-3 p-2.5 rounded-lg bg-green-50 border border-green-200">
            <i class="bx bxs-school text-green-600 shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold text-green-800">College</p>
                <p class="text-[12px] text-green-700 mt-0.5">Top-level academic unit. Colleges contain multiple departments and oversee broad academic disciplines (e.g., College of Engineering, College of Arts).</p>
            </div>
        </div>
        <div class="flex items-start gap-3 p-2.5 rounded-lg bg-blue-50 border border-blue-200">
            <i class="bx bx-building text-blue-600 shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold text-blue-800">Department</p>
                <p class="text-[12px] text-blue-700 mt-0.5">Mid-level unit within a college. Departments offer specific programs and manage faculty in a specialized field (e.g., Department of Computer Science).</p>
            </div>
        </div>
        <div class="flex items-start gap-3 p-2.5 rounded-lg bg-amber-50 border border-amber-200">
            <i class="bx bx-book text-amber-600 shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold text-amber-800">Program</p>
                <p class="text-[12px] text-amber-700 mt-0.5">Specific degree or certificate offering. Programs belong to departments and contain courses and curriculum (e.g., BS in Computer Science).</p>
            </div>
        </div>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Primary vs Supporting Departments" icon="share-alt" color="purple">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Programs can have one primary department and zero or more supporting departments:</p>
        <div class="space-y-2 mt-2">
            <div class="flex items-start gap-3 p-2.5 rounded-lg bg-blue-50 border border-blue-200">
                <i class="bx bx-info-circle text-blue-600 shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-blue-800">Primary Department</p>
                    <p class="text-[12px] text-blue-700 mt-0.5">The main department responsible for the program. Administers curriculum, oversees academic outcomes, and manages program administration.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-2.5 rounded-lg bg-purple-50 border border-purple-200">
                <i class="bx bx-group text-purple-600 shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-purple-800">Supporting Departments</p>
                    <p class="text-[12px] text-purple-700 mt-0.5">Additional departments that contribute to or collaborate on the program. Provide interdisciplinary support and shared resources.</p>
                </div>
            </div>
        </div>
        <ul class="space-y-1.5 mt-2">
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>A program must have exactly one primary department</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>A program can have zero or more supporting departments</span></li>
        </ul>
    </div>
</x-layout.accordion>

<x-layout.accordion title="When to Create Each Level" icon="plus-circle" color="emerald">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Use these guidelines to determine when to create each structural level:</p>
        <ul class="space-y-1.5 mt-2">
            <li class="flex gap-2"><i class="bx bxs-school text-green-600 shrink-0 mt-0.5"></i><span><strong>Create a College</strong> when you have a distinct academic division with multiple related departments</span></li>
            <li class="flex gap-2"><i class="bx bx-building text-blue-600 shrink-0 mt-0.5"></i><span><strong>Create a Department</strong> when you have a specialized field of study with multiple programs</span></li>
            <li class="flex gap-2"><i class="bx bx-book text-amber-600 shrink-0 mt-0.5"></i><span><strong>Create a Program</strong> when you offer a specific degree or certificate</span></li>
        </ul>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Managing Structure" icon="edit" color="slate">
    <ol class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Select a college from the left panel to view its departments and programs</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Use <strong>Add College</strong> to create new top-level academic units</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>Use <strong>Add Department</strong> to create departments within the selected college</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">4</span>
            <span>Use the program card actions to add, edit, or delete programs</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">5</span>
            <span>Use the dropdown menu (three dots) to edit or delete colleges and departments</span>
        </li>
    </ol>
</x-layout.accordion>

<x-layout.accordion title="BOR Approval Information" icon="file-text" color="indigo">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Programs can include Board of Regents (BOR) approval details for official record-keeping and accreditation purposes:</p>
        <ul class="space-y-1.5 mt-2">
            <li class="flex gap-2"><i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i><span><strong>BOR Approval No:</strong> The official approval number assigned by the Board of Regents</span></li>
            <li class="flex gap-2"><i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i><span><strong>BOR Approval Date:</strong> The date when the program was approved (cannot be in the future)</span></li>
        </ul>
        <div class="flex items-start gap-3 p-2.5 rounded-lg bg-indigo-50 border border-indigo-200 mt-2">
            <i class="bx bx-info-circle text-indigo-600 shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold text-indigo-800">Validation Rules</p>
                <p class="text-[12px] text-indigo-700 mt-0.5">If you provide a BOR approval number, you must also provide the approval date. The approval date cannot be set to a future date.</p>
            </div>
        </div>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Deletion Rules" icon="lock" color="red">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>To maintain data integrity, certain deletion restrictions apply:</p>
        <ul class="space-y-1.5 mt-2">
            <li class="flex gap-2"><i class="bx bx-x-circle text-red-500 shrink-0 mt-0.5"></i><span>Colleges cannot be deleted if they have departments with courses</span></li>
            <li class="flex gap-2"><i class="bx bx-x-circle text-red-500 shrink-0 mt-0.5"></i><span>Departments cannot be deleted if they have programs with courses</span></li>
            <li class="flex gap-2"><i class="bx bx-x-circle text-red-500 shrink-0 mt-0.5"></i><span>Programs cannot be deleted if they have courses assigned</span></li>
        </ul>
        <div class="flex items-start gap-3 p-2.5 rounded-lg bg-red-50 border border-red-200 mt-2">
            <i class="bx bx-error text-red-600 shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold text-red-800">Important</p>
                <p class="text-[12px] text-red-700 mt-0.5">Delete courses first before removing the containing structure. The system will prevent deletion and show an error message if dependent data exists.</p>
            </div>
        </div>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Best Practices" icon="star" color="amber">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <ul class="space-y-1.5">
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Plan your hierarchy before creating entries to avoid reorganization</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Use clear, descriptive names for colleges, departments, and programs</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Assign supporting departments for interdisciplinary programs</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Keep BOR approval information current for accreditation requirements</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Review structure periodically to ensure it reflects current academic offerings</span></li>
        </ul>
    </div>
</x-layout.accordion>