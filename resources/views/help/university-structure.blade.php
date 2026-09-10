{{-- Help content: university-structure --}}

<x-layout.accordion title="About University Structure" icon="info-circle" color="emerald" :open="true">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>Open <strong>Administration &gt; University Structure</strong> to maintain the colleges, departments, and programs in the university. This page is available to administrators.</p>
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>College:</strong> An academic unit, such as the College of Engineering.</li>
            <li><strong>Department:</strong> A unit within a college, such as the Department of Civil Engineering.</li>
            <li><strong>Program:</strong> A degree offering, such as BS Civil Engineering. A program has one primary department and may have supporting departments.</li>
        </ul>
        <p>Create these records in order: college first, then department, then program. Use the approved university names and check for existing entries before adding new ones.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Find a college, department, or program" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>Select a college from the college list to view its departments and programs. On smaller screens, scroll down to see the selected college’s details.</p>
        <p>Use the search field to narrow the visible records by name. Clear the search to show the full list again. If a program is not visible, clear the search and select its college before checking the department.</p>
        <p>A program may appear under both its primary and supporting departments. These are links to the same program, so do not create another copy just to show it under a second department.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Add a college or department" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>Click <strong>Add College</strong>, enter the <strong>College Name</strong>, and click <strong>Create College</strong>.</li>
            <li>Select the college that should contain the new department.</li>
            <li>Click <strong>Add Department</strong> for that college, enter the department name, and click <strong>Create Department</strong>.</li>
            <li>Wait for the success message and check the new entry under the correct college.</li>
        </ol>
        <p>College and department names must each be unique within their record type and contain 2 to 255 characters. Use letters, spaces, hyphens, periods, or commas. Use <strong>Cancel</strong> to close a form without creating the entry.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Add a program" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>Click <strong>Add Program</strong> in the department area.</li>
            <li>Enter the official <strong>Program Name</strong>. It must be unique and contain 2 to 255 characters. Program names may also contain numbers.</li>
            <li>Choose the <strong>Primary Department</strong> in the form. Check this selection even if you opened the form from a department card.</li>
            <li>If other departments support the program, select them under <strong>Supporting Departments</strong>. Leave these unselected if none applies.</li>
            <li>Enter the Board of Regents approval details if available, then click <strong>Create Program</strong>.</li>
            <li>Wait for the success message and check the program’s department links.</li>
        </ol>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Primary and supporting departments" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>The <strong>Primary</strong> department is the main department responsible for a program. Select exactly one. <strong>Supporting</strong> departments are additional departments associated with the same program; they are optional.</p>
        <p>The primary department cannot also be selected as a supporting department. Department choices are grouped by college, so check both names when selecting a department.</p>
        <p>These links affect which programs department chairs can manage. Review them carefully when responsibility for a program changes.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Enter Board of Regents approval details" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p><strong>BOR</strong> means <strong>Board of Regents</strong>. Copy the details from the approved resolution into <strong>BOR Approval Resolution No.</strong> and <strong>BOR Approval Date</strong>.</p>
        <ul class="list-disc pl-5 space-y-2">
            <li>These fields are optional when creating a program.</li>
            <li>If you enter a resolution number, you must also enter its approval date.</li>
            <li>The approval date cannot be in the future.</li>
            <li>The resolution number may contain letters, numbers, spaces, hyphens, slashes, and periods.</li>
        </ul>
        <p>Leave unavailable details blank rather than entering a guessed number or date. You can add them later by editing the program.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Edit an existing record" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>Open the three-dot action menu for the college, department, or program and choose its edit action. Update the name or available program details, then click <strong>Save Changes</strong>. Use <strong>Cancel</strong> to leave without saving.</p>
        <p>The department edit form changes its name. The program edit form also lets you update supporting departments and BOR details. A program’s <strong>primary department cannot be changed while it has courses</strong>, including archived courses.</p>
        <p>If a department change is blocked, review the existing courses and the intended reorganization before deciding how to proceed. Renaming a record does not require deleting and recreating it.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Before deleting a record" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>Choose the delete action from the record’s menu and read the confirmation before proceeding. These deletions are permanent; there is no restore option on this page.</p>
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>College:</strong> Removes the college, its departments, college goals, department objectives, and related college and department user assignments. Its departments are unlinked from programs.</li>
            <li><strong>Department:</strong> Removes the department, its objectives, and its user assignments. It also removes the department’s links to programs.</li>
            <li><strong>Program:</strong> Removes the program, its department links, Program Educational Objectives (PEOs), Program Outcomes (POs), and their mappings.</li>
        </ul>
        <p>Deleting a college or department does not delete the program records it was linked to. Review those programs afterward, especially any that lose their primary department.</p>
        <p>Deletion is blocked if courses exist in the program or under the selected college or department. Archived courses also count. Review the records that need to be retained before removing any courses.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="If something does not work" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>A name is already used:</strong> Find the existing record and edit it if needed. Department names must be unique even across different colleges.</li>
            <li><strong>No primary department is available:</strong> Create the college and department first, then reopen the program form.</li>
            <li><strong>The form will not save:</strong> Read the highlighted field messages. Check the name, required department, and BOR date if a resolution number was entered.</li>
            <li><strong>An entry seems missing:</strong> Clear the search, select the correct college, and review its departments.</li>
            <li><strong>You need to assign people:</strong> Open <strong>Administration &gt; University Faculties</strong> to assign deans, chairs, or faculty members.</li>
        </ul>
    </div>
</x-layout.accordion>
