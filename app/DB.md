users
id
name
email
password
account_status
roles
id 
name (admin, oloi, chair, faculty, reviewer, dean)
user_roles
user_id
role_id
colleges
Id
Name
college_goals
id
college_id
college_goals_code
goal_text
departments
id 
college_id
name
department_objectives
id
department_id
dept_obj_code
objective_text
programs (can be in multiple dept)
id
name
bor_approval_no
bor_approval_date
program_departments
id (PK)
program_id 
department_id
role (primary / supporting)
program_peos or program_eos
id
program_id
peo_code  (1,2,3 / a,b,c)
peo_text
program_outcomes
id
program_id
po_code    (a, b, c - used in CO)
po_text
program_outcome_peo
id
program_outcome_id
program_peo_id
course
id
program_id
course_code
course_title
course_description
credit_units
has_lec_lab  (boolean - if true - component) 
prerequisite         -- nullable
corequisite          -- nullable
status               -- active / archived
version
created_by
course_curriculum_map
id
course_id
program_outcome_id
ied   (I / E / D)
UNIQUE (course_id, program_outcome_id)
academic_calendars
id
academic_year
semester (1st, 2nd)
start_date
end_date
academic_year
semester
start_date
end_date
2025–2026
1st
Aug 5, 2025
Dec 12, 2025
2025–2026
2nd
Jan 13, 2026
May 15, 2026

academic_calendar_events
id
academic_calendar_id
type  (holiday / exam / break / weekend)
name
date

syllabi
id
course_id
academic_year_id
semester
status   (draft / under_review / for_revision / approved)
prepared_by  (faculty (user_id))
concurred_by (single user (chair))
approved_by (single user (dean))
approved_at
syllabus_revisions
id
syllabus_id
revision_no   (0, 1, 2,...)
revision_date  (date when revision was done)
implementation_semester  (e.g."1st Sem 2019-2020")
highlights  (description of changes)
contributors   (text field with names)
course_components
Id 
course_id (chair set if meron)
type  (LEC / LAB) 
units
class_hours
schedule (auto-save - input, comma-separated)
instructor_name
instructor_email
phone (contact) (nullable)
office
consultation_hours
performance standard (50%, 60%, 75%) dropdown
course_outcomes
id
syllabus_id
co_code  (CO1, CO2)
description
course_outcome_po
id
course_outcome_id
program_outcome_id (get po_codes and text)
syllabus_weeks
id
syllabus_id
Week_no (separate per week)
Start_date (Jan 5)
End_date (Jan 9)
is_exam_week (boolean) 
Plan: By default Week 1 is MVGO
week_contents
id
syllabus_week_id
component_type (LEC / LAB) (auto when the subject is selected)
course_outcome_id (nullable) input text and dropdown
learning_outcomes
topics
tla
assessment_name (options)
assessment_desc (nullable text)
raw_score
one input saved in two different tables?
syllabus_evaluations (if multiple assessments per week)
id
syllabus_id
component_type (LEC / LAB)
assessment_name      
assessment_desc
raw_score
syllabus_references (for citations)
id
syllabus_id
reference_text
syllabus_materials (for urls)
id
syllabus_id
material_name
url
Syllabus_reviewers (input)
id
syllabus_id 
role (Member, Faculty, Reviewer, Head, Dean, Concurred) - dropdown
status(pending / approved / rejected)
signed_at (image?)
Audit_logs (never delete)
id
user_id
action
record_id
timestamp
Approved_syllabi (dean)
id 
course_id
academic_year
semester
pdf_path  (storage path or filename)
approved_at
approved_by  (dean or final approver)
checksum   (optional (file integrity))
created_at

Approval Flow
Draft
Pending (Waiting for approval - editable) (notify the references editors)
Approved (pdf - uneditable since its not in the database anymore)
If approved, saved as pdf (don't delete the record, it's overwritable).
We don't have versioning, but we have the approved as pdf, then the data in the system will be editable.
Approved syllabi are not edited, not versioned, and not affected by reference data changes because they exist only as PDFs.


