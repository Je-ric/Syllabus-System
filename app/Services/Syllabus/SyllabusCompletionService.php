<?php

namespace App\Services\Syllabus;

use App\Models\CourseComponent;
use App\Models\CourseOutcome;
use App\Models\SyllabusEvaluationItem;
use App\Models\SyllabusWeek;
use App\Models\WeekContent;

// Answers "is this wizard step still missing required data?" for a given syllabus.
//
// Extracted from SyllabusWizard so the component's render() does not own DB queries.
// Each check is a single, focused query — no Livewire dependency.
//
// Public API:
//   isMissing(int $syllabusId, string $step, bool $hasLecLab): bool
class SyllabusCompletionService
{
    // Returns true when the step is incomplete (i.e. the indicator dot should show).
    // $hasLecLab must be passed in by the caller — this service does not re-query the course.
    public function isMissing(int $syllabusId, string $step, bool $hasLecLab): bool
    {
        return match ($step) {
            'course_components' => $this->componentsMissing($syllabusId, $hasLecLab),
            'course_outcomes'   => $this->outcomesMissing($syllabusId),
            'weekly_coverage'   => $this->weeksMissing($syllabusId),
            'course_evaluation' => $this->evaluationMissing($syllabusId),
            default             => false,
        };
    }

    // ── Private: one method per step ──────────────────────────────────────────

    private function componentsMissing(int $syllabusId, bool $hasLecLab): bool
    {
        $lec = CourseComponent::where('syllabus_id', $syllabusId)->where('type', 'LEC')->first();

        if (! $this->componentComplete($lec)) {
            return true;
        }

        if ($hasLecLab) {
            $lab = CourseComponent::where('syllabus_id', $syllabusId)->where('type', 'LAB')->first();
            return ! $this->componentComplete($lab);
        }

        return false;
    }

    private function outcomesMissing(int $syllabusId): bool
    {
        return ! CourseOutcome::where('syllabus_id', $syllabusId)
            ->whereRaw("TRIM(description) <> ''")
            ->exists();
    }

    private function weeksMissing(int $syllabusId): bool
    {
        return ! SyllabusWeek::where('syllabus_id', $syllabusId)->exists();
    }

    private function evaluationMissing(int $syllabusId): bool
    {
        $weekContentIds = WeekContent::query()
            ->join('syllabus_weeks', 'syllabus_weeks.id', '=', 'week_contents.syllabus_week_id')
            ->where('syllabus_weeks.syllabus_id', $syllabusId)
            ->where('syllabus_weeks.week_no', '<>', 1)
            ->whereRaw("TRIM(COALESCE(week_contents.assessment_task, '')) <> ''", [])
            ->whereRaw("TRIM(week_contents.assessment_task) <> 'Non-Teaching Week'", [])
            ->pluck('week_contents.id');

        if ($weekContentIds->isEmpty()) {
            return true;
        }

        $evaluatedCount = SyllabusEvaluationItem::whereIn('week_content_id', $weekContentIds)
            ->whereNotNull('weight')
            ->count();

        return $evaluatedCount !== $weekContentIds->count();
    }

    // A component row is complete when all required fields are filled.
    // phone and office are optional; schedules live in their own table.
    // instructor name/email are on the related User, not on CourseComponent itself.
    private function componentComplete(?CourseComponent $component): bool
    {
        if (! $component) {
            return false;
        }

        return collect([
            $component->user_id,
            $component->class_hours,
            $component->performance_standard,
        ])->every(fn ($v) => trim((string) $v) !== '');
    }
}
