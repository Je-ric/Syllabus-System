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


----------------------------------
MODEL
----------------------------------
php artisan make:model User -m
php artisan make:model Role -m
php artisan make:model UserRole -m
php artisan make:model College
php artisan make:model CollegeGoal
php artisan make:model Department
php artisan make:model DepartmentObjective
php artisan make:model Program
php artisan make:model ProgramEducationObjective
php artisan make:model ProgramOutcome
php artisan make:model AcademicCalendar
php artisan make:model AcademicCalendarEvent


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
----------------------------------
php artisan migrate
php artisan migrate:fresh

php artisan db:seed --class=AdminSeeder
composer require blade-ui-kit/blade-heroicons
php artisan vendor:publish --tag=heroicons
npm install -D daisyui
npm i -D daisyui@latest
