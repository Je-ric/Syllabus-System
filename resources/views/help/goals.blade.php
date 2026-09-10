{{-- Help content: goals --}}

<x-layout.accordion title="About College Goals" icon="info-circle" color="emerald" :open="true">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>College goals describe what a college aims to achieve. Use this page to maintain the college’s approved goal statements, one goal per entry.</p>        <p>Open <strong>Academic Setup &gt; College Goals</strong>. Administrators and deans can access this page. A dean with a college assignment works with that college.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Add a goal" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>Check the selected <strong>College</strong>. If you can choose a college, select the one the goal belongs to.</li>
            <li>Read the existing goals to avoid adding the same statement twice.</li>
            <li>Click <strong>Add Goal</strong> and enter the goal description. Use the approved wording; you do not need to enter a code.</li>
            <li>Click <strong>Add Goal</strong> in the form. Wait for the success message and check that the goal appears in the list.</li>
        </ol>        <p>The description is required and can contain up to <strong>5,000 characters</strong>. The system assigns the goal code automatically.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Edit or delete a goal" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>Edit:</strong> Click the pencil icon beside the goal, update the description, and click <strong>Save Changes</strong>.</li>
            <li><strong>Cancel:</strong> Close the form with <strong>Cancel</strong> if you do not want to save your edits.</li>
            <li><strong>Delete:</strong> Click the trash icon, review the goal shown, and confirm with <strong>Delete Goal</strong>. Deletion is permanent; there is no restore option on this page.</li>
        </ul>        <p>After deletion, the remaining goal codes are assigned again to keep them in sequence. Recheck any documents that refer to a goal by its code. There is no manual reorder control on this page.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Write clear goal statements" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li>Use one main idea per goal and spell out unfamiliar abbreviations.</li>
            <li>Use your college’s approved statements. Do not add a target number of goals just to fill the list; this page does not require a particular count.</li>
            <li>Keep college-wide aims here. Maintain department-specific statements on <strong>Department Objectives</strong>.</li>
        </ul>        <p><strong>Example of plain wording:</strong> “Strengthen community partnerships through teaching, research, and extension activities.” This is an example only; follow your college’s approved wording.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="If something does not work" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>No college or no assignment:</strong> Ask an administrator to check your college assignment.</li>
            <li><strong>The form will not save:</strong> Read the message below the highlighted field. Enter a description, keep it within the character limit, and use plain text instead of HTML or scripts. Submit again after correcting it.</li>
            <li><strong>A goal seems missing:</strong> Check the selected college before adding it again.</li>
        </ul>
    </div>
</x-layout.accordion>
