{{-- Help content: academic-calendar --}}

<x-layout.accordion title="About Academic Calendars" icon="info-circle" color="emerald" :open="true">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>Use <strong>Academic Setup &gt; Academic Calendars</strong> to set the dates for each academic year and record holidays, breaks, exams, and other events. An academic year contains a first and second semester.</p>        <p>This page is available to administrators and users assigned the <strong>OVPAA</strong> role (Office of the Vice President for Academic Affairs). Have the approved calendar ready before entering dates.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Create an academic year" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>Click <strong>Add Academic Year</strong>.</li>
            <li>Enter the year in <strong>YYYY-YYYY</strong> format, such as <strong>2026-2027</strong>. Do not add an academic year that already exists.</li>
            <li>Choose the start and end dates for both semesters. Each end date must be on or after its start date. The second semester must start after the first semester ends.</li>
            <li>Click <strong>Create Calendar</strong>, review the details, and click <strong>Confirm &amp; Create</strong>.</li>
            <li>After creation, the events page opens. Add events to the appropriate semester.</li>
        </ol>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Set the active academic year" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>The <strong>Active</strong> badge identifies the selected academic year on the list. Click <strong>Set Active</strong> on another year to switch the selection; the previous year’s records remain available.</p>        <p>When no calendar is active, creating an academic year activates its first semester automatically. Creating another year does not replace an existing active selection.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Add an event or date range" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ol class="list-decimal pl-5 space-y-2">
            <li>On the academic year’s card, click <strong>Manage Events</strong>.</li>
            <li>Choose the <strong>1st Semester</strong> or <strong>2nd Semester</strong> tab, then click <strong>Add Event</strong>.</li>
            <li>Choose the event <strong>Type</strong> and enter a clear <strong>Event Name</strong>, such as “Midterm Examination”.</li>
            <li>Select the date. For a single-day event, use the same start and end date. For several days, select the first and last date; both are included. Dates must be within the selected semester.</li>
            <li>Check the dates and click <strong>Add Event</strong> or the <strong>Add … Events</strong> button for a range. Wait for the success message and review the calendar.</li>
        </ol>        <p>A date range creates a separate entry for each day, including weekends. Only one event per date is allowed in a semester. A single-day duplicate is rejected; a range skips dates that already have events and keeps those existing entries.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Choose the right event type" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>Holiday (Reference):</strong> Record a holiday or suspension as a reference.</li>
            <li><strong>Other (Reference):</strong> Record another date or activity for reference.</li>
            <li><strong>Break (Skip Week):</strong> Use for an official break that should be excluded from generated teaching weeks.</li>
            <li><strong>Exam (Lock Week):</strong> Use for an examination period reserved from regular teaching edits.</li>
            <li><strong>Non-Teaching (Lock Week):</strong> Use for another period reserved for a non-teaching activity.</li>
        </ul>        <p>“Reference” records information, “Skip” excludes a week, and “Lock” reserves a week from editing in generated schedules. These types affect scheduling, so follow the approved calendar rather than choosing a type by its display color.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Edit or delete events" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>Click an existing event on the calendar to open its details. Change the type, name, or date and click <strong>Update Event</strong>. Use <strong>Cancel</strong> to close without saving.</p>        <p>Use the trash icon on an event to remove that day’s entry. Events created from a range are separate entries, so changes or deletions apply to individual days. Check the other dates in the range if the whole period has changed.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="Change or delete an academic year" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <p>Use the pencil icon on the academic year’s card to change its year label or semester dates, then click <strong>Update Calendar</strong>. Read any warning shown before continuing.</p>        <p><strong>Edit and Delete are disabled while the year has events.</strong> The page asks you to remove all events first. Keep a copy of their names, types, and dates if they will need to be entered again after an approved date correction.</p>        <p>To delete an academic year, use its trash icon and review the confirmation. Deletion removes both semester records and cannot be undone from this page. The system also blocks deletion when linked syllabi exist; ask the responsible administrator to review those dependencies.</p>
    </div>
</x-layout.accordion>

<x-layout.accordion title="If something does not work" icon="list-ul" color="slate">
    <div class="space-y-3 text-[13px] text-[#3f3f46] leading-relaxed">
        <ul class="list-disc pl-5 space-y-2">
            <li><strong>The academic year already exists:</strong> Return to the list and use the existing year.</li>
            <li><strong>A date cannot be selected:</strong> Check the semester tab and its date limits. An event cannot be placed outside that semester.</li>
            <li><strong>Fewer events were added than expected:</strong> Dates already containing an event were skipped. Review those dates individually.</li>
            <li><strong>The form will not save:</strong> Check the highlighted fields. The event name is required and can contain up to 255 characters.</li>
            <li><strong>Cannot add by clicking an empty calendar day:</strong> Use the <strong>Add Event</strong> button above the calendar.</li>
        </ul>
    </div>
</x-layout.accordion>
