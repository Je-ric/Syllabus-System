{{-- Help content: objectives --}}

<x-layout.accordion title="About Department Objectives" icon="info-circle" color="emerald" :open="true">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>Department objectives describe what a department aims to accomplish. This page keeps the approved statements for each department together, with one objective per entry.</p>        <p>Open <strong>Academic Setup &gt; Department Objectives</strong>. Administrators and department chairs can access this page. A chair with a department assignment works with that department.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Add an objective" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>Check the selected <strong>College</strong> and <strong>Department</strong>. If the selectors are available, choose the college first, then its department.</li>
            <li>Review the existing objectives to avoid duplicates.</li>
            <li>Click <strong>Add Objective</strong> and enter the approved objective description. The system supplies the code.</li>
            <li>Click <strong>Add Objective</strong> in the form. Wait for the success message and check the new entry.</li>
        </ol>        <p>The description is required and can contain up to <strong>5,000 characters</strong>. Each objective belongs to the selected department.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Edit or delete an objective" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>Edit:</strong> Click the pencil icon beside the objective, change the description, and click <strong>Save Changes</strong>.</li>
            <li><strong>Cancel:</strong> Use <strong>Cancel</strong> to close the form without saving the edits.</li>
            <li><strong>Delete:</strong> Click the trash icon, check the statement in the confirmation window, then click <strong>Delete Objective</strong>. There is no restore option on this page.</li>
        </ul>        <p>Deleting an objective also updates the remaining codes so they stay in sequence. Check any references to the old codes. There is no manual reorder control on this page.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Choose the right kind of statement" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p><strong>College goals</strong> describe the college’s broad aims. <strong>Department objectives</strong> describe the department’s aims. <strong>Program Educational Objectives (PEOs)</strong> describe graduates’ expected achievements after graduation and are maintained under <strong>PEOs &amp; POs</strong>.</p>        <p>Use your department’s approved wording. Keep each objective focused on one idea and avoid repeating an existing statement. You do not need to select a college goal or create a link to a PEO in this form.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="If something does not work" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>No department is available:</strong> Check the college selection. If your account shows a missing assignment message, ask an administrator to check your department assignment.</li>
            <li><strong>The form will not save:</strong> Correct the highlighted description field. It must contain plain text and stay within 5,000 characters.</li>
            <li><strong>The list is empty:</strong> Confirm the selected department before creating its first objective.</li>
        </ul>
    </div>
</x-layout.accordion>
