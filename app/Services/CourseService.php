<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Program;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CourseService
{
    /**
     * Create a new course with PO mappings and audit logging
     *
     * @param array $data Course data (program_id, code, name, description, etc.)
     * @param array $poMapping PO mapping data (outcome_id => ied_level)
     * @return Course
     */
    public function createCourse(array $data, array $poMapping = [])
    {
        return DB::transaction(function () use ($data, $poMapping) {
            $course = Course::create([
                'program_id' => $data['program_id'],
                'course_code' => $data['code'],
                'course_title' => $data['name'],
                'course_description' => $data['description'] ?? null,
                'prerequisite' => $data['prerequisite'] ?? null,
                'corequisite' => $data['corequisite'] ?? null,
                'credit_units' => $data['credits'],
                'year_level' => $data['year_level'] ?? null,
                'semester' => $data['semester'] ?? null,
                'created_by' => Auth::id(),
                'has_lec_lab' => $data['has_lec_lab'] ?? false,
            ]);

            if (!empty($poMapping)) {
                $course->syncPoMappings($poMapping);
            }

            $this->logAction('created', $course);

            return $course;
        });
    }

    /**
     * Update an existing course with PO mappings and audit logging
     *
     * @param Course $course The course to update
     * @param array $data Update data
     * @param array $poMapping PO mapping data (outcome_id => ied_level)
     * @return Course
     */
    public function updateCourse(Course $course, array $data, array $poMapping = [])
    {
        return DB::transaction(function () use ($course, $data, $poMapping) {
            $course->update([
                'course_code' => $data['code'],
                'course_title' => $data['name'],
                'course_description' => $data['description'] ?? null,
                'prerequisite' => $data['prerequisite'] ?? null,
                'corequisite' => $data['corequisite'] ?? null,
                'credit_units' => $data['credits'],
                'year_level' => $data['year_level'] ?? null,
                'semester' => $data['semester'] ?? null,
                'has_lec_lab' => $data['has_lec_lab'] ?? false,
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

    /**
     * Log course action to audit log
     *
     * @param string $action The action performed (created, updated, deleted)
     * @param Course $course The course model
     * @return void
     */
    protected function logAction(string $action, Course $course)
    {
        $program = Program::with('departments.college')->find($course->program_id);
        $primaryDepartment = $program?->departments->first();
        $collegeName = $primaryDepartment?->college?->name ?? 'N/A';
        $departmentName = $primaryDepartment?->name ?? 'N/A';

        $description = match ($action) {
            'updated' => "Updated course {$course->course_code} ({$course->course_title}); program {$program?->name}; college: {$collegeName}; department: {$departmentName}.",
            default => ucfirst($action) . " course {$course->course_code} ({$course->course_title}) for program {$program?->name}; college: {$collegeName}; department: {$departmentName}.",
        };

        AuditLog::record(
            action: $action,
            module: 'Course',
            referenceId: $course->id,
            description: $description
        );
    }
}
