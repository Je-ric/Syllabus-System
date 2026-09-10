{{-- Help content: user-assignments --}}

<x-layout.accordion title="About University Faculties" icon="info-circle" color="emerald" :open="true">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>Open <strong>Administration &gt; University Faculties</strong> to assign people to colleges and departments. Administrators can add and remove assignments. Deans and chairs who access their department view can review the records available to them.</p>
        <p>A <strong>role</strong> identifies what a person can do, such as dean, chair, or faculty. An <strong>assignment</strong> identifies the college or department they work with. For example, a user needs both the Chair role and a department assignment to manage that department’s academic setup.</p>
        <p>Manage account status and roles under <strong>Administration &gt; User Management</strong>. Create colleges and departments under <strong>University Structure</strong>.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Find the college or department" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>On the college list, use the search field to find a college by its name or its dean’s name.</li>
            <li>Check the <strong>College Dean</strong> section on the college card.</li>
            <li>Click <strong>Manage Departments</strong> to view that college’s department chairs and faculty lists.</li>
            <li>On the department page, search by department, chair, or faculty name. Clear the search to show all available cards again.</li>
        </ol>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Check who can be assigned" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li>The selection lists show <strong>active accounts</strong> with the matching Dean, Chair, or Faculty role. Administrator accounts are excluded from these lists.</li>
            <li>A person can be assigned as dean of only one college, or chair of only one department. They cannot hold both a dean and a chair assignment at the same time.</li>
            <li>Each college card provides one dean position, and each department card provides one chair position. Remove the current assignment before assigning a replacement.</li>
            <li>Faculty can be assigned to more than one department. The system blocks another faculty assignment once the user has five faculty assignment records.</li>
            <li>The same faculty member cannot be added twice to the same department.</li>
        </ul>
        <p>Assigning a dean or chair also ensures the user has the Faculty role and creates a related faculty assignment. A chair may therefore appear in the department’s faculty list as well.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Assign a dean or chair" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>For a dean, find the college card and click <strong>Assign Dean</strong>. For a chair, open <strong>Manage Departments</strong> and click <strong>Assign Chair</strong> on the correct department.</li>
            <li>Check the college or department name in the form.</li>
            <li>Select the person from the available users. Check their name and email to avoid choosing someone with a similar name.</li>
            <li>Click <strong>Assign Dean</strong> or <strong>Assign Chair</strong> in the form.</li>
            <li>Wait for the success message and check that the person appears in the position.</li>
        </ol>
        <p>If the person already leads another college or department, remove that earlier assignment before moving them. Use <strong>Cancel</strong> if you need to review the selection first.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Add one or more faculty members" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>Open the correct college’s <strong>Manage Departments</strong> page.</li>
            <li>Click <strong>Add Faculty</strong> on the department card.</li>
            <li>Use <strong>Search Faculty</strong> to find people by name or email. Select the people to add; you may choose one or several.</li>
            <li>Use <strong>Select All</strong> only when you want all available people in the current search results. Check the selected count before submitting.</li>
            <li>Click <strong>Assign Selected</strong> and wait for the result message.</li>
            <li>Check the department’s faculty list. The message may report that some users were skipped or could not be assigned.</li>
        </ol>
        <p>Users already in the department are not available for selection. A bulk request can also skip users who no longer meet the role or assignment-limit rules. Check individual users if the assigned count is lower than expected.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Remove or replace an assignment" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>Click the trash icon beside the dean, chair, or faculty member.</li>
            <li>Check the person and college or department named in the confirmation.</li>
            <li>Click <strong>Remove Dean</strong>, <strong>Remove Chair</strong>, or <strong>Remove Faculty</strong>. Use <strong>Cancel</strong> to keep the assignment.</li>
            <li>For a replacement, use the appropriate Assign button after removal and check the new assignment.</li>
        </ol>
        <p>Removal takes effect after confirmation. It removes that assignment, not the user’s account or account roles. Other assignments remain, including a faculty assignment created when the person became a dean or chair. Review those separately if their responsibilities have changed.</p>
        <p>There is no Undo button, but an administrator can assign the person again if they still meet the rules. Assignment changes also create a notification for the affected user.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="If something does not work" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>A person is missing from the selection list:</strong> Check that their account is active and has the required role in User Management. Administrator accounts are excluded. A dean or chair already assigned elsewhere is also excluded from the corresponding list.</li>
            <li><strong>No available users to assign:</strong> Review account roles and existing assignments before trying again.</li>
            <li><strong>A dean/chair conflict is reported:</strong> Remove the person’s conflicting leadership assignment first. Having a role alone is different from holding an assignment.</li>
            <li><strong>The faculty limit is reached:</strong> Review the person’s existing faculty assignments and remove any that no longer apply before adding another.</li>
            <li><strong>Assign or Remove buttons are missing:</strong> These actions are available to administrators. Ask an administrator to make the change.</li>
            <li><strong>No Assignment Found:</strong> Ask an administrator to check your dean or chair assignment.</li>
            <li><strong>A college or department is missing:</strong> Check the search and selected college. An administrator can add missing structure records under University Structure.</li>
        </ul>
    </div>
</x-layout.accordion>
