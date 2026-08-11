{{-- Help: User Assignments Management --}}

<x-layout.accordion title="Overview" icon="info-circle" color="emerald" :open="true">
    <p class="text-[13px] text-[#3f3f46] leading-relaxed">
        User Assignments manage institutional leadership roles across your academic structure. This module allows you to assign deans to colleges, chairs to departments, and faculty members to specific departments.
    </p>
    <p class="text-[13px] text-[#3f3f46] leading-relaxed mt-2">
        Access assignment management via <strong>User Management → User Assignments</strong> in the navigation menu. The interface is organized by colleges and their departments.
    </p>
</x-layout.accordion>

<x-layout.accordion title="Role Hierarchy" icon="building" color="blue">
    <div class="space-y-3 text-[13px] text-[#3f3f46]">
        <div class="flex items-start gap-3 p-2.5 rounded-lg bg-green-50 border border-green-200">
            <i class="bx bxs-school text-green-600 shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold text-green-800">College Dean</p>
                <p class="text-[12px] text-green-700 mt-0.5">Manages all departments within a college. Each college can have one dean. Oversees academic and administrative operations at the college level.</p>
            </div>
        </div>
        <div class="flex items-start gap-3 p-2.5 rounded-lg bg-blue-50 border border-blue-200">
            <i class="bx bx-building text-blue-600 shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold text-blue-800">Department Chair</p>
                <p class="text-[12px] text-blue-700 mt-0.5">Leads a specific department within a college. Each department can have one chair. Manages faculty, curriculum, and departmental operations.</p>
            </div>
        </div>
        <div class="flex items-start gap-3 p-2.5 rounded-lg bg-amber-50 border border-amber-200">
            <i class="bx bx-user text-amber-600 shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold text-amber-800">Faculty Member</p>
                <p class="text-[12px] text-amber-700 mt-0.5">Academic staff assigned to teach and conduct research within a department. Can be assigned to multiple departments (maximum 5).</p>
            </div>
        </div>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Assignment Rules" icon="shield" color="purple">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>The system enforces these assignment rules to maintain data integrity:</p>
        <ul class="space-y-1.5 mt-2">
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>A user cannot be both a dean and a chair simultaneously</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Users must have the appropriate role assigned before being assigned to a position</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Each college can have only one dean</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Each department can have only one chair</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Faculty can be assigned to multiple departments (maximum 5 for effective management)</span></li>
        </ul>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Assigning Users" icon="user-plus" color="emerald">
    <ol class="space-y-2 text-[13.5px] text-[#3f3f46]">
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">1</span>
            <span>Navigate to the college or department you want to manage</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">2</span>
            <span>Click <strong>Assign Dean</strong>, <strong>Assign Chair</strong>, or <strong>Add Faculty</strong> button</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">3</span>
            <span>Select a user from the available users list (only users with appropriate roles are shown)</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">4</span>
            <span>Confirm the assignment in the modal</span>
        </li>
        <li class="flex gap-2.5">
            <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-bold mt-0.5">5</span>
            <span>For faculty, you can select multiple users at once using bulk assignment</span>
        </li>
    </ol>
</x-layout.accordion>

<x-layout.accordion title="Removing Assignments" icon="trash" color="red">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>To remove an assignment, click the trash icon next to the assigned user. The system will show a confirmation modal before proceeding.</p>
        <div class="flex items-start gap-3 p-2.5 rounded-lg bg-red-50 border border-red-200 mt-2">
            <i class="bx bx-error text-red-600 shrink-0 mt-0.5"></i>
            <div>
                <p class="font-semibold text-red-800">Important Notes</p>
                <p class="text-[12px] text-red-700 mt-0.5">This action is immediate and cannot be undone. Plan leadership changes in advance to avoid temporary gaps in management. Consider reassigning roles before removing current assignments.</p>
            </div>
        </div>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Search and Filter" icon="search" color="slate">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <p>Use the search bar at the top of each page to quickly find:</p>
        <ul class="space-y-1.5 mt-2">
            <li class="flex gap-2"><i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i><span><strong>Colleges page:</strong> Search by college name or dean name</span></li>
            <li class="flex gap-2"><i class="bx bx-chevron-right text-[#a1a1aa] shrink-0 mt-0.5"></i><span><strong>Departments page:</strong> Search by department name, chair name, or faculty names</span></li>
        </ul>
        <p class="mt-2 text-[12px] text-[#71717a]">The search filters in real-time as you type, making it easy to locate specific assignments in large institutions.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Best Practices" icon="star" color="amber">
    <div class="space-y-2 text-[13px] text-[#3f3f46]">
        <ul class="space-y-1.5">
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Ensure users have the correct role before assigning them to positions</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Plan leadership changes in advance to avoid temporary gaps in oversight</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Review faculty assignments regularly to keep department rosters current</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Use bulk faculty assignment for efficient onboarding of multiple users</span></li>
            <li class="flex gap-2"><i class="bx bx-check-circle text-[#16a34a] shrink-0 mt-0.5"></i><span>Monitor faculty cross-department assignments to ensure manageable workloads</span></li>
        </ul>
    </div>
</x-layout.accordion>