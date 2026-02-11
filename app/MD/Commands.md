Notes Commands:


----------------------------------
MIGRATION
----------------------------------
php artisan make:migration create_roles_table
php artisan make:migration create_user_roles_table
php artisan make:migration create_colleges_table 
php artisan make:migration create_college_goals_table 
php artisan make:migration create_departments_table
php artisan make:migration create_department_objectives_table
php artisan make:migration create_programs_table
php artisan make:migration create_program_departments_table
php artisan make:migration create_program_eos_table
php artisan make:migration create_program_outcomes_table
php artisan make:migration create_program_outcome_peo_table
php artisan make:migration create_academic_calendars_table
php artisan make:migration create_academic_calendar_events_table
php artisan make:migration create_courses_table
php artisan make:migration create_course_curriculum_maps_table
php artisan make:migration create_syllabi_table
php artisan make:migration create_syllabus_revisions_table
php artisan make:migration create_course_components_table
php artisan make:migration create_course_outcomes_table
php artisan make:migration create_course_outcome_po_table
php artisan make:migration create_user_assignments_table
php artisan make:migration create_syllabus_weeks_table
php artisan make:migration create_co_assessment_plans_table
php artisan make:migration create_week_contents_table


----------------------------------
MODEL
----------------------------------
php artisan make:model User
php artisan make:model Role
php artisan make:model UserRole
php artisan make:model College
php artisan make:model CollegeGoal
php artisan make:model Department
php artisan make:model DepartmentObjective
php artisan make:model Program
php artisan make:model ProgramEducationObjective
php artisan make:model ProgramOutcome
php artisan make:model AcademicCalendar
php artisan make:model AcademicCalendarEvent
php artisan make:model Course
php artisan make:model CourseCurriculumMap 
php artisan make:model Syllabus
php artisan make:model SyllabusRevision
php artisan make:model CourseComponent 
php artisan make:model CourseOutcome 
php artisan make:model UserAssigment 
php artisan make:model SyllabusWeek
php artisan make:model COAssessmentPlan
php artisan make:model WeekContent


----------------------------------
CONTROLLER
----------------------------------
php artisan make:controller AuthController
php artisan make:controller AccountApprovalController
php artisan make:controller OTPController
php artisan make:controller AcademicStructureController
php artisan make:controller ObjectiveController
php artisan make:controller GoalController
php artisan make:controller ProgramController
php artisan make:controller AcademicCalendarController
php artisan make:controller AcademicCalendarEventController
php artisan make:controller CourseController
php artisan make:controller SyllabusController
php artisan make:controller UserController

----------------------------------
LIVEWIRE
----------------------------------
php artisan make:livewire Programs/ManagePeos
php artisan make:livewire Programs/ProgramSelector


----------------------------------
MAIL
----------------------------------
php artisan make:mail OtpMail
php artisan make:mail AccountStatusUpdated


----------------------------------
COMPONENT
----------------------------------
php artisan make:component StatusIndicator
php artisan make:component Button
php artisan make:component Toast
----------------------------------


php artisan migrate
php artisan migrate:fresh

php artisan db:seed --class=AdminSeeder
composer require blade-ui-kit/blade-heroicons
php artisan vendor:publish --tag=heroicons
npm install -D daisyui
npm i -D daisyui@latest
