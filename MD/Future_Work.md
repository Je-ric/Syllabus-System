# CSMS — Future Work & Known Issues

Missing/Lacks
    1. Add the approval process, from faculty, to submit for review to other faculties, to approved by chairs and deans.
    2. Apply review form for reviewing submitted syllabus.
    3. Dashboard (we focus in syllabus creation)
    4. Add Additional Templates (Current 3 templates)
        - hide/unhide some fields if needed
    5. Notification In-app and email for approval workflow process ?
    6. Collaboration of LEC & LAB Professor
    7. Admin view lists of created syllabus of all faculties 
        7.1 Use hierarchy From College -> Department -> Program -> Course -> Faculty to narrow. 
        7.2 Because a single course can be created a syllabus by multiple faculties.
        7.3 And each syllabus version has 3 templates with versions. So need to have like folder like structure.

Critical
    3. Use Faculty ID instead of clsu/clsu2 (I think we can use either clsu or ID. ) 
    5. Auto requisites (additional tables)
    Schedule should also reflect the week generation, exam weeks and some events are just 3 days, it means theres a remaining days for that week that user can still fill some details.

Normal

Less Priority
    3. Add types in academic calendar events.
    4. Course Number should be displayed in the Course Coverage (Weekly)

Known Bugs


updateGoalModal / updateObjectiveModal — textarea has both value in slot AND x-model — double-binding causes x-model to always win over server old() on reload
NEXT
assignRolesModal — x-on:submit returns false to block but Alpine x-on:submit ignores return value — dean+chair block silently does nothing
addDepartmentModal — college_id hidden input has empty value and no JS to populate it — will always fail server validation
deleteAYModal — modal id uses str_replace('-','_',$year) but route passes $year as-is — ID inconsistency if year format changes
approvalModal — loadingText uses ucfirst($action).'ing…' which produces wrong label for 'disable' → 'Disableing…'
addProgramModal — primaryDept comparison uses == with integer dept id but Alpine stores it as string from x-model — strict type mismatch in :class binding