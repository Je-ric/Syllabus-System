{{-- Help content: courses --}}

<x-layout.accordion title="About Courses" icon="info-circle" color="emerald" :open="true">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>Use <strong>Academic Setup &gt; Courses</strong> to maintain subjects in a program, including their units, class hours, and links to Program Outcomes (POs). A PO describes an ability students should have by graduation.</p>        <p>Administrators and department chairs can access this page. Chairs manage courses for programs in their assigned department.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Find and review a course" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>Select the college, department, and program using the selectors at the top.</li>
            <li>Choose <strong>Active</strong> or <strong>Archived</strong>. Courses are grouped by year level and semester.</li>
            <li>Review the course code, title, units, lecture/laboratory details, and PO columns. Use the <strong>View details</strong> icon to see more information.</li>
            <li>Open <strong>Program Outcomes</strong> to read the full PO statements for the selected program.</li>
        </ol>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Add a course" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>Select the correct program and click <strong>Add Course</strong>.</li>
            <li>Enter the required <strong>Course Code</strong> and <strong>Course Title</strong>. Use the official course code; it must be unique across the course records. The description is optional.</li>
            <li>Choose <strong>Credit Units</strong> (1 to 5), <strong>Has Laboratory</strong>, <strong>Year Level</strong>, and <strong>Semester</strong>.</li>
            <li>Review <strong>Passing Mark</strong> and <strong>LEC Class Hours</strong> (lecture hours). If the course has a laboratory, also set <strong>LAB Class Hours</strong>.</li>
            <li>Enter a <strong>Prerequisite</strong> for a subject required before this course, and a <strong>Corequisite</strong> for a subject taken alongside it. If none applies, enter “None”; blank entries are also saved as “None”.</li>
            <li>In <strong>Program Outcomes Mapping</strong>, select the appropriate I, E, or D level for each PO the course supports. Leave unrelated POs unselected.</li>
            <li>Click <strong>Create Course</strong>, review the confirmation, and confirm creation. Check for the success message.</li>
        </ol>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Understand I, E, and D" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>These letters describe how the course supports a Program Outcome. Choose the level in the approved curriculum map.</p>        <ul class="list-disc pl-5 space-y-2">
            <li><strong>I — Introductory:</strong> Students first learn the ideas or skills.</li>
            <li><strong>E — Enabling:</strong> Students build and practise the ideas or skills.</li>
            <li><strong>D — Demonstrative:</strong> Students show that they can apply the outcome.</li>
        </ul>        <p><strong>Reset IED Levels</strong> clears the selections in the open form. Review the mappings before submitting; on an update, saving cleared selections removes the corresponding course-to-PO links.</p>        <p>If no POs are listed, add them for this program under <strong>PEOs &amp; POs</strong>, then return to the course form.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Edit a course" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>On the Active list, click the course’s pencil icon. Update the details or PO selections, click <strong>Update Course</strong>, and review and confirm the changes. Use <strong>Cancel</strong> to leave without saving.</p>        <p>The <strong>Has Laboratory</strong> setting is locked once the course has linked syllabi. If it is incorrect, ask your administrator or department chair to review the affected records before deciding how to correct it.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Archive, restore, or delete" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>Archive:</strong> Use the archive icon and confirm to move the course out of the Active list while keeping its data.</li>
            <li><strong>Restore:</strong> Open <strong>Archived</strong> and click the restore icon to return a course to the Active list.</li>
            <li><strong>Delete:</strong> Use the trash icon and review the confirmation carefully. This permanently removes the course, its PO mappings, and associated syllabi and their data. There is no restore option for a deleted course.</li>
        </ul>        <p>Use Archive when a course is no longer offered but its records should be kept. Deletion is available to administrators and the chair responsible for the course’s department.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="If something does not work" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>A course is missing:</strong> Check the selected program and the Archived tab.</li>
            <li><strong>The course code is already used:</strong> Check for an existing course, including archived records, before creating another.</li>
            <li><strong>The form will not save:</strong> Read the highlighted field messages, correct the entries, and complete the confirmation step.</li>
            <li><strong>No program is available:</strong> Ask an administrator to check your department assignment and the program’s department.</li>
        </ul>
    </div>
</x-layout.accordion>
