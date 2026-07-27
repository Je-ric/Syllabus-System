<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $academic_year
 * @property string $semester
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property int|null $cais_semester_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AcademicCalendarEvent> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Syllabus> $syllabi
 * @property-read int|null $syllabi_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar whereAcademicYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar whereCaisSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar whereSemester($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendar whereUpdatedAt($value)
 */
	class AcademicCalendar extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academic_calendar_id
 * @property string $type
 * @property string $name
 * @property string $date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademicCalendar $calendar
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendarEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendarEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendarEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendarEvent whereAcademicCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendarEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendarEvent whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendarEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendarEvent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendarEvent whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicCalendarEvent whereUpdatedAt($value)
 */
	class AcademicCalendarEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string $module
 * @property int|null $reference_id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $timestamp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 */
	class AuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $external_id
 * @property int|null $external_semester_id
 * @property int|null $external_department_id
 * @property int|null $cais_semester_id
 * @property int|null $department_id
 * @property string|null $subject_code
 * @property string|null $subject_title
 * @property numeric|null $units
 * @property string|null $section
 * @property string|null $room
 * @property string|null $time
 * @property string|null $class_type
 * @property string|null $lab_type
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CaisSemester|null $caisSemester
 * @property-read \App\Models\Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CaisTeachingLoad> $teachingLoads
 * @property-read int|null $teaching_loads_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereCaisSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereClassType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereExternalDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereExternalSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereLabType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereSection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereSubjectCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereSubjectTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereUnits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisClassSchedule whereUpdatedAt($value)
 */
	class CaisClassSchedule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $external_id
 * @property string $name
 * @property int|null $number
 * @property string|null $year
 * @property string|null $status
 * @property int|null $academic_calendar_id
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademicCalendar|null $academicCalendar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CaisClassSchedule> $classSchedules
 * @property-read int|null $class_schedules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CaisTeachingLoad> $teachingLoads
 * @property-read int|null $teaching_loads_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester whereAcademicCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester whereSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisSemester whereYear($value)
 */
	class CaisSemester extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $external_id
 * @property int|null $external_user_id
 * @property int|null $external_semester_id
 * @property int|null $external_schedule_id
 * @property int|null $user_id
 * @property int|null $cais_semester_id
 * @property int|null $cais_class_schedule_id
 * @property bool $is_deleted
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CaisSemester|null $caisSemester
 * @property-read \App\Models\CaisClassSchedule|null $classSchedule
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad whereCaisClassScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad whereCaisSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad whereExternalScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad whereExternalSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad whereExternalUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad whereSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaisTeachingLoad whereUserId($value)
 */
	class CaisTeachingLoad extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $cais_college_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Department> $departments
 * @property-read int|null $departments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CollegeGoal> $goals
 * @property-read int|null $goals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserAssignment> $userAssignments
 * @property-read int|null $user_assignments_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|College newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|College newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|College query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|College whereCaisCollegeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|College whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|College whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|College whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|College whereUpdatedAt($value)
 */
	class College extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $college_id
 * @property int|null $cais_college_id
 * @property string $college_goals_code
 * @property string $goal_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\College $college
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollegeGoal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollegeGoal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollegeGoal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollegeGoal whereCaisCollegeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollegeGoal whereCollegeGoalsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollegeGoal whereCollegeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollegeGoal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollegeGoal whereGoalText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollegeGoal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollegeGoal whereUpdatedAt($value)
 */
	class CollegeGoal extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $syllabus_id
 * @property int $course_id
 * @property string $academic_year
 * @property string $semester
 * @property string $pdf_path
 * @property string|null $evaluation_path
 * @property string|null $abridged_path
 * @property int $version
 * @property string|null $approved_at
 * @property int|null $approved_by
 * @property string|null $checksum
 * @property string|null $checksum_evaluation
 * @property string|null $checksum_abridged
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\Syllabus $syllabus
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereAbridgedPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereAcademicYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereChecksum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereChecksumAbridged($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereChecksumEvaluation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereEvaluationPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus wherePdfPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereSemester($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereSyllabusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompleteSyllabus whereVersion($value)
 */
	class CompleteSyllabus extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $cais_course_id
 * @property int $program_id
 * @property string $course_code
 * @property string $course_title
 * @property string|null $course_description
 * @property int $credit_units
 * @property int $has_lec_lab
 * @property numeric $passing_mark
 * @property string|null $lec_class_hours
 * @property string|null $lab_class_hours
 * @property int $year_level
 * @property int $semester
 * @property string|null $prerequisite
 * @property string|null $corequisite
 * @property string $status
 * @property int $version
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $creator
 * @property-read \App\Models\Program $program
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramOutcome> $programOutcomes
 * @property-read int|null $program_outcomes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Syllabus> $syllabi
 * @property-read int|null $syllabi_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCaisCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCorequisite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCourseCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCourseDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCourseTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreditUnits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereHasLecLab($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereLabClassHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereLecClassHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course wherePassingMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course wherePrerequisite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereSemester($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereYearLevel($value)
 */
	class Course extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $syllabus_id
 * @property int|null $user_id
 * @property string $type
 * @property string $class_hours
 * @property numeric $performance_standard
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseComponentSchedule> $schedules
 * @property-read int|null $schedules_count
 * @property-read \App\Models\Syllabus $syllabus
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponent whereClassHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponent wherePerformanceStandard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponent whereSyllabusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponent whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponent whereUserId($value)
 */
	class CourseComponent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $course_component_id
 * @property string $day
 * @property string $time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CourseComponent $component
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponentSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponentSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponentSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponentSchedule whereCourseComponentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponentSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponentSchedule whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponentSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponentSchedule whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseComponentSchedule whereUpdatedAt($value)
 */
	class CourseComponentSchedule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $course_id
 * @property int $program_outcome_id
 * @property string $ied
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\ProgramOutcome $programOutcome
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCurriculumMap newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCurriculumMap newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCurriculumMap query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCurriculumMap whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCurriculumMap whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCurriculumMap whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCurriculumMap whereIed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCurriculumMap whereProgramOutcomeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseCurriculumMap whereUpdatedAt($value)
 */
	class CourseCurriculumMap extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $syllabus_id
 * @property string $co_code
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramOutcome> $programOutcomes
 * @property-read int|null $program_outcomes_count
 * @property-read \App\Models\Syllabus $syllabus
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseOutcome newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseOutcome newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseOutcome query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseOutcome whereCoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseOutcome whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseOutcome whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseOutcome whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseOutcome whereSyllabusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseOutcome whereUpdatedAt($value)
 */
	class CourseOutcome extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $cais_department_id
 * @property int|null $cais_college_id
 * @property int $college_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\College $college
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DepartmentObjective> $objectives
 * @property-read int|null $objectives_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Program> $programs
 * @property-read int|null $programs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserAssignment> $userAssignments
 * @property-read int|null $user_assignments_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCaisCollegeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCaisDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCollegeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department withRelations()
 */
	class Department extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $department_id
 * @property int|null $cais_department_id
 * @property string $dept_obj_code
 * @property string $objective_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Department $department
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentObjective newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentObjective newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentObjective query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentObjective whereCaisDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentObjective whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentObjective whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentObjective whereDeptObjCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentObjective whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentObjective whereObjectiveText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DepartmentObjective whereUpdatedAt($value)
 */
	class DepartmentObjective extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $syllabus_id
 * @property int|null $syllabus_week_id
 * @property string $component_type
 * @property string $material_name
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Syllabus $syllabus
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnlineMaterial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnlineMaterial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnlineMaterial query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnlineMaterial whereComponentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnlineMaterial whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnlineMaterial whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnlineMaterial whereMaterialName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnlineMaterial whereSyllabusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnlineMaterial whereSyllabusWeekId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnlineMaterial whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnlineMaterial whereUrl($value)
 */
	class OnlineMaterial extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $bor_approval_no
 * @property string|null $bor_approval_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Department> $departments
 * @property-read int|null $departments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramOutcome> $outcomes
 * @property-read int|null $outcomes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramEducationalObjective> $peos
 * @property-read int|null $peos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereBorApprovalDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereBorApprovalNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program withOrderedOutcomes()
 */
	class Program extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $program_id
 * @property string|null $peo_code
 * @property string $peo_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramOutcome> $outcomes
 * @property-read int|null $outcomes_count
 * @property-read \App\Models\Program $program
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramEducationalObjective newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramEducationalObjective newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramEducationalObjective query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramEducationalObjective whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramEducationalObjective whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramEducationalObjective wherePeoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramEducationalObjective wherePeoText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramEducationalObjective whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramEducationalObjective whereUpdatedAt($value)
 */
	class ProgramEducationalObjective extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $program_id
 * @property string|null $po_code
 * @property string $po_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseOutcome> $courseOutcomes
 * @property-read int|null $course_outcomes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramEducationalObjective> $peos
 * @property-read int|null $peos_count
 * @property-read \App\Models\Program $program
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramOutcome newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramOutcome newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramOutcome query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramOutcome whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramOutcome whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramOutcome wherePoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramOutcome wherePoText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramOutcome whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramOutcome whereUpdatedAt($value)
 */
	class ProgramOutcome extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $syllabus_id
 * @property int|null $syllabus_week_id
 * @property string $component_type
 * @property string $reference_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Syllabus $syllabus
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reference query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reference whereComponentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reference whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reference whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reference whereReferenceText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reference whereSyllabusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reference whereSyllabusWeekId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reference whereUpdatedAt($value)
 */
	class Reference extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $course_id
 * @property int|null $academic_calendar_id
 * @property string $status
 * @property string $current_step
 * @property int $prepared_by
 * @property int|null $concurred_by
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademicCalendar|null $academicCalendar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CompleteSyllabus> $completeSyllabi
 * @property-read int|null $complete_syllabi_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseComponent> $components
 * @property-read int|null $components_count
 * @property-read \App\Models\Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseOutcome> $courseOutcomes
 * @property-read int|null $course_outcomes_count
 * @property-read \App\Models\User|null $dean
 * @property-read \App\Models\User|null $deanConcurred
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OnlineMaterial> $onlineMaterials
 * @property-read int|null $online_materials_count
 * @property-read \App\Models\User $preparer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reference> $references
 * @property-read int|null $references_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SyllabusReviewer> $reviewers
 * @property-read int|null $reviewers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SyllabusRevision> $revisions
 * @property-read int|null $revisions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SyllabusWeek> $weeks
 * @property-read int|null $weeks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus whereAcademicCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus whereConcurredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus whereCurrentStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus wherePreparedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Syllabus whereUpdatedAt($value)
 */
	class Syllabus extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $syllabus_id
 * @property int $course_id
 * @property int $week_content_id
 * @property string|null $outcome_label
 * @property string|null $kind
 * @property string|null $exam_type
 * @property int|null $weight
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\WeekContent $weekContent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem whereExamType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem whereKind($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem whereOutcomeLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem whereSyllabusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem whereWeekContentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusEvaluationItem whereWeight($value)
 */
	class SyllabusEvaluationItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $syllabus_id
 * @property int $user_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Syllabus $syllabus
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusReviewer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusReviewer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusReviewer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusReviewer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusReviewer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusReviewer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusReviewer whereSyllabusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusReviewer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusReviewer whereUserId($value)
 */
	class SyllabusReviewer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $syllabus_id
 * @property int $revision_no
 * @property \Illuminate\Support\Carbon $revision_date
 * @property string $implementation_semester
 * @property string|null $highlights
 * @property string|null $contributors
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Syllabus $syllabus
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusRevision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusRevision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusRevision query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusRevision whereContributors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusRevision whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusRevision whereHighlights($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusRevision whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusRevision whereImplementationSemester($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusRevision whereRevisionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusRevision whereRevisionNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusRevision whereSyllabusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusRevision whereUpdatedAt($value)
 */
	class SyllabusRevision extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $syllabus_id
 * @property int $week_no
 * @property string $start_date
 * @property string $end_date
 * @property int $is_exam_week
 * @property string|null $exam_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WeekContent> $contents
 * @property-read int|null $contents_count
 * @property-read \App\Models\Syllabus $syllabus
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusWeek newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusWeek newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusWeek query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusWeek whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusWeek whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusWeek whereExamType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusWeek whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusWeek whereIsExamWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusWeek whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusWeek whereSyllabusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusWeek whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyllabusWeek whereWeekNo($value)
 */
	class SyllabusWeek extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $cais_user_id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string $password
 * @property string $account_status
 * @property string|null $phone_number
 * @property string|null $office
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserAssignment> $assignments
 * @property-read int|null $assignments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserConsultationHour> $consultationHours
 * @property-read int|null $consultation_hours_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserOtp> $otps
 * @property-read int|null $otps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAccountStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCaisUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOffice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $college_id
 * @property int|null $cais_college_id
 * @property int|null $department_id
 * @property int|null $cais_department_id
 * @property string $context
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\College|null $college
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment chair()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment context(string $context)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment dean()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment faculty()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment forCaisCollege(int $caisCollegeId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment forCaisDepartment(int $caisDepartmentId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment forCollege(int $collegeId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment forDepartment(int $departmentId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment whereCaisCollegeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment whereCaisDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment whereCollegeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAssignment whereUserId($value)
 */
	class UserAssignment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $day
 * @property string $time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsultationHour newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsultationHour newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsultationHour query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsultationHour whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsultationHour whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsultationHour whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsultationHour whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsultationHour whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsultationHour whereUserId($value)
 */
	class UserConsultationHour extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $purpose
 * @property string $otp
 * @property \Illuminate\Support\Carbon|null $otp_expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOtp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOtp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOtp query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOtp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOtp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOtp whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOtp whereOtpExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOtp wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOtp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOtp whereUserId($value)
 */
	class UserOtp extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereUserId($value)
 */
	class UserRole extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $syllabus_week_id
 * @property string $component_type
 * @property int|null $course_outcome_id
 * @property string $learning_outcomes
 * @property string $topics
 * @property string|null $assessment_task
 * @property string|null $tla
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CourseOutcome|null $courseOutcome
 * @property-read \App\Models\SyllabusEvaluationItem|null $evaluation
 * @property-read \App\Models\SyllabusWeek $syllabusWeek
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent whereAssessmentTask($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent whereComponentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent whereCourseOutcomeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent whereLearningOutcomes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent whereSyllabusWeekId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent whereTla($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent whereTopics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeekContent whereUpdatedAt($value)
 */
	class WeekContent extends \Eloquent {}
}

