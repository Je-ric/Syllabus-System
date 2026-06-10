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
    1. Improve Syllabus Creation 
        1.1 Real-time input validations and real-time user feedbacks.
    2. During signup, Add what office/department/college they are in.
    3. Use Faculty ID instead of clsu/clsu2 (I think we can use either clsu or ID. ) 
    4. ~~Upload CSV calendar events~~ ✅ Fixed — CSV import (type, name, date columns) added to Manage Events page.
    5. Auto requisites (additional tables)
    6. ~~Passing Mark, Class Hours included in course not in course components~~ ✅ Fixed — migration adds `passing_mark`, `lec_class_hours`, `lab_class_hours` to `courses` table; removed from course components step (now read-only display).
    7. ~~PEO & PO indicator needed to save if theres update, add state. Since were using save all Dont update the unchange fields. (use createorupdate so that it doesnt create existing/ungachanged)~~ ✅ Fixed — dirty state indicator added; save now uses explicit find-then-update instead of updateOrCreate.
    8. Schedule should also reflect the week generation, exam weeks and some events are just 3 days, it means theres a remaining days for that week that user can still fill some details.

Normal
    1. Apply real-time (livewire) for the account approval, goals and objectives.
    2. Make the topics and learning outcomes using WYSIWYG Editor 
        The current is textarea
        (I recommend tiptap/summernote - no limit)
    3. ~~Course Archive, not delete~~ ✅ Fixed — archive/restore actions added; Active/Archived toggle on course index; `status` column was already in migration.
    4. ~~Calendar view in the Academic Year Events (not table)~~ ✅ Fixed — replaced events table with monthly calendar grid view.

Less Priority
    1. Improve validations for all inputs accross the system
    2. UI improvements accross the system.
    3. Add types in academic calendar events.
    4. Course Number should be displayed in the Course Coverage (Weekly)
    5. ~~Legend in syllabus creation.~~ ✅ Fixed — legend panel added to Weekly Coverage step.

Known Bugs
    1. ~~Organizational Hierarchy not visible by the dean and chairs of their coverage.~~ ✅ Fixed — hierarchy view routes now accessible to dean and chair roles.

