<?php

namespace App\Services\Academic;

use App\Models\Course;
use App\Models\Program;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\Syllabus\SyllabusDeleteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CourseService
{
    public function __construct(
        private readonly SyllabusDeleteService $syllabusDeleteService
    ) {}
    // Create a new course with PO mappings and audit logging
    public function createCourse(array $data, array $poMapping = [])
    {
        return DB::transaction(function () use ($data, $poMapping) {
            $prerequisite = $this->normalizeRequisite($data['prerequisite'] ?? null);
            $corequisite = $this->normalizeRequisite($data['corequisite'] ?? null);

            $course = Course::create([
                'program_id'       => $data['program_id'],
                'course_code'      => $data['code'],
                'course_title'     => $data['name'],
                'course_description' => $data['description'] ?? null,
                'prerequisite'     => $prerequisite,
                'corequisite'      => $corequisite,
                'credit_units'     => $data['credits'],
                'year_level'       => $data['year_level'] ?? null,
                'semester'         => $data['semester'] ?? null,
                'created_by'       => Auth::id(),
                'has_lec_lab'      => $data['has_lec_lab'] ?? false,
                'passing_mark'     => $data['passing_mark'] ?? 60.00,
                'lec_class_hours'  => $data['lec_class_hours'] ?? '3 hr',
                'lab_class_hours'  => ($data['has_lec_lab'] ?? false) ? ($data['lab_class_hours'] ?? '3 hr') : null,
            ]);

            if (!empty($poMapping)) {
                $course->syncPoMappings($poMapping);
            }

            $this->logAction('created', $course);

            return $course;
        });
    }

    // Update an existing course with PO mappings and audit logging
    public function updateCourse(Course $course, array $data, array $poMapping = [])
    {
        return DB::transaction(function () use ($course, $data, $poMapping) {
            $prerequisite = $this->normalizeRequisite($data['prerequisite'] ?? null);
            $corequisite  = $this->normalizeRequisite($data['corequisite'] ?? null);

            $newHasLecLab = filter_var($data['has_lec_lab'] ?? $course->has_lec_lab, FILTER_VALIDATE_BOOLEAN);

            // Only block if the value is actually changing
            if ((bool) $course->has_lec_lab !== $newHasLecLab && $course->syllabi()->exists()) {
                $direction = $newHasLecLab ? 'No → Yes' : 'Yes → No';
                throw new \RuntimeException(
                    "Cannot change \"Has Laboratory\" ({$direction}) because this course already has syllabi. " .
                    "Delete all syllabi for this course first, then change this setting."
                );
            }

            $course->update([
                'course_code'        => $data['code'],
                'course_title'       => $data['name'],
                'course_description' => $data['description'] ?? null,
                'prerequisite'       => $prerequisite,
                'corequisite'        => $corequisite,
                'credit_units'       => $data['credits'],
                'year_level'         => $data['year_level'] ?? null,
                'semester'           => $data['semester'] ?? null,
                'has_lec_lab'        => $newHasLecLab,
                'passing_mark'       => $data['passing_mark'] ?? $course->passing_mark,
                'lec_class_hours'    => $data['lec_class_hours'] ?? $course->lec_class_hours,
                'lab_class_hours'    => $newHasLecLab ? ($data['lab_class_hours'] ?? $course->lab_class_hours ?? '3 hr') : null,
            ]);

            // Sync PO mappings with IED levels
            $syncData = [];
            foreach ($poMapping as $outcomeId => $iedLevel) {
                if (in_array($iedLevel, ['I', 'E', 'D'], true)) {
                    $syncData[$outcomeId] = ['ied' => $iedLevel];
                }
            }
            $course->programOutcomes()->sync($syncData);

            $this->logAction('updated', $course);

            return $course;
        });
    }

    // Archive a course (soft-delete alternative)
    public function archiveCourse(Course $course): void
    {
        $course->update(['status' => 'archived']);
        $this->logAction('archived', $course);
    }

    // Restore an archived course
    public function restoreCourse(Course $course): void
    {
        $course->update(['status' => 'active']);
        $this->logAction('restored', $course);
    }

    // Returns true if the given user is allowed to delete the course.
    // Admins can always delete. Chairs can only delete courses whose program
    // belongs to a department they are assigned as chair of.
    public function canDelete(Course $course, User $user): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('chair')) {
            $chairAssignment = $user->assignments()->where('context', 'chair')->first();
            if ($chairAssignment) {
                $course->loadMissing('program.departments');
                $programDeptIds = $course->program?->departments->pluck('id')->toArray() ?? [];
                return in_array($chairAssignment->department_id, $programDeptIds);
            }
        }

        return false;
    }

    // Delete a course and all its related data
    public function deleteCourse(Course $course): void
    {
        DB::transaction(function () use ($course) {
            $course->programOutcomes()->detach();

            foreach ($course->syllabi as $syllabus) {
                $this->syllabusDeleteService->delete($syllabus);
            }

            $this->logAction('deleted', $course);

            $course->delete();
        });
    }

    protected function logAction(string $action, Course $course)
    {
        $program = Program::with('departments.college')->find($course->program_id);
        $primaryDepartment = $program?->departments->first();
        $collegeName = $primaryDepartment?->college?->name ?? 'N/A';
        $departmentName = $primaryDepartment?->name ?? 'N/A';

        $description = match ($action) {
            'updated'  => "Updated course {$course->course_code} ({$course->course_title}); program {$program?->name}; college: {$collegeName}; department: {$departmentName}.",
            'archived' => "Archived course {$course->course_code} ({$course->course_title}) for program {$program?->name}.",
            'restored' => "Restored course {$course->course_code} ({$course->course_title}) for program {$program?->name}.",
            default    => ucfirst($action) . " course {$course->course_code} ({$course->course_title}) for program {$program?->name}; college: {$collegeName}; department: {$departmentName}.",
        };

        AuditLog::record(
            action: $action,
            module: 'Course',
            referenceId: $course->id,
            description: $description
        );
    }

    protected function normalizeRequisite(?string $value): string
    {
        $trimmed = trim((string) $value);

        return $trimmed === '' ? 'None' : $trimmed;
    }
}
