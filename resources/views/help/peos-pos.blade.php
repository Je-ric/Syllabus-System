{{-- Help content: peos-pos --}}

<x-layout.accordion title="About PEOs and POs" icon="info-circle" color="emerald" :open="true">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>Open <strong>Academic Setup &gt; PEOs &amp; POs</strong> to maintain a program’s approved objectives and outcomes. Administrators and department chairs can access this page; chairs work with programs in their assigned department.</p>        <ul class="list-disc pl-5 space-y-2">
            <li><strong>PEO — Program Educational Objective:</strong> What graduates are expected to achieve professionally about three to five years after graduation.</li>
            <li><strong>PO — Program Outcome:</strong> What students should know or be able to do by graduation.</li>
            <li><strong>Mapping:</strong> Linking a PO to the PEOs it supports. A PO can support more than one PEO.</li>
        </ul>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Select a program and add statements" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>Use the college, department, and program selectors to open the correct program.</li>
            <li>Open the <strong>Program Educational Objectives (PEOs)</strong> tab. Click <strong>Add PEO</strong> and type one approved statement in the new row. Fill that row before adding another.</li>
            <li>Click <strong>Save All</strong>, then confirm with <strong>Save All</strong>. Wait for the success message. Codes are assigned automatically.</li>
            <li>Open the <strong>Program Outcomes (POs)</strong> tab. Click <strong>Add PO</strong>, enter the statement, and use that tab’s <strong>Save All</strong> button and confirmation.</li>
        </ol>        <p>Save each tab’s changes separately. A row marked <strong>New</strong> or <strong>Modified — not saved yet</strong> still needs to be saved. Blank rows cannot be saved; fill them in or remove them.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Link POs to PEOs" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>Save the PEOs and the PO first. A new, unsaved PO cannot be mapped yet.</li>
            <li>Under a saved PO, find <strong>Maps to PEOs</strong>. Use <strong>View PEOs</strong> to read the full statements.</li>
            <li>Select the checkbox for each PEO that the PO supports. Clear a checkbox to remove that link.</li>
            <li>Wait for the saving indicator to finish. Mapping changes save as you make them; they do not wait for <strong>Save All</strong>.</li>
            <li>Open <strong>Matrix View</strong> to review the links across the program. This view is for checking; make mapping changes in the POs tab.</li>
        </ol>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Edit, revert, or remove statements" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>Edit text:</strong> Change the text directly in its row, then click <strong>Save All</strong> and confirm.</li>
            <li><strong>Revert:</strong> Discard unsaved text edits and remove new, unsaved rows in the current tab. It does not undo saved mapping changes or deletions.</li>
            <li><strong>Remove a new row:</strong> Click its <strong>&times;</strong> button before saving.</li>
            <li><strong>Delete a saved row:</strong> Click its trash icon and confirm <strong>Delete</strong>. This takes effect immediately and cannot be undone with Revert. Save any other text edits before deleting, because deletion reloads the page.</li>
        </ul>        <p>Deleting a PEO removes its PO links. Deleting a PO removes its PEO and course links. Remaining codes are assigned again, so review the affected mappings and code references afterward.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="If something does not work" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>No program is available:</strong> Check the college and department selectors. Ask an administrator to check your department assignment if needed.</li>
            <li><strong>No PEO checkboxes appear:</strong> Add and save PEOs in the PEOs tab first.</li>
            <li><strong>Add or Save All is unavailable:</strong> Fill in any blank row and wait for a save or deletion already in progress to finish.</li>
            <li><strong>Text is rejected:</strong> Use plain statement text without HTML tags or scripts and follow the message shown.</li>
            <li><strong>A mapping did not save:</strong> Check for an error message, retry the checkbox change, and verify the result before leaving.</li>
        </ul>
    </div>
</x-layout.accordion>
