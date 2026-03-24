<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Program;
use App\Models\AuditLog;
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
                'program_id' => $data['program_id'],
                'course_code' => $data['code'],
                'course_title' => $data['name'],
                'course_description' => $data['description'] ?? null,
                'prerequisite' => $prerequisite,
                'corequisite' => $corequisite,
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

    // Update an existing course with PO mappings and audit logging
    public function updateCourse(Course $course, array $data, array $poMapping = [])
    {
        return DB::transaction(function () use ($course, $data, $poMapping) {
            $prerequisite = $this->normalizeRequisite($data['prerequisite'] ?? null);
            $corequisite  = $this->normalizeRequisite($data['corequisite'] ?? null);

            $newHasLecLab = (bool) ($data['has_lec_lab'] ?? false);

            // Block has_lec_lab change if syllabi already exist — LAB components
            // would become orphaned (Yes→No) or missing (No→Yes).
            if ($course->has_lec_lab !== $newHasLecLab && $course->syllabi()->exists()) {
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

    protected function normalizeRequisite(?string $value): string
    {
        $trimmed = trim((string) $value);

        return $trimmed === '' ? 'None' : $trimmed;
    }
}
