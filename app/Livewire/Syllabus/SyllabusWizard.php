<?php

namespace App\Livewire\Syllabus;

use App\Models\CourseComponent;
use App\Models\CourseOutcome;
use App\Models\Course;
use App\Models\Syllabus;
use App\Models\SyllabusWeek;
use App\Models\WeekContent;
use App\Models\SyllabusEvaluationItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class SyllabusWizard extends Component
{
    public ?Syllabus $syllabus    = null;
    public ?Course   $course      = null;
    public string    $currentStep = 'academic_calendar';
    public array     $stepDirty   = [];

    /**
     * This runs when the component loads. It sets up the wizard for either editing an existing syllabus or creating a new one.
     * If neither a syllabus nor a course is found, it shows a 404 error.
     */
    public function mount($syllabusId = null, $courseId = null)
    {
        $syllabusId = $syllabusId ? (int) $syllabusId : null;
        $courseId   = $courseId   ? (int) $courseId   : null;

        if ($syllabusId) {
            $this->syllabus = Syllabus::with('course.program')->findOrFail($syllabusId);

            if ($this->syllabus->prepared_by !== Auth::id()) {
                abort(403, 'Unauthorized');
            }

            $this->course = $this->syllabus->course;
            $steps        = array_keys($this->syllabus->getWizardSteps());
            $persisted    = (string) ($this->syllabus->current_step ?? '');

            $this->currentStep = in_array($persisted, $steps, true) ? $persisted : 'academic_calendar';

            if ($this->syllabus->current_step !== $this->currentStep) {
                $this->syllabus->update(['current_step' => $this->currentStep]);
            }
        } elseif ($courseId) {
            $this->course   = Course::with('program')->findOrFail($courseId);
            $this->syllabus = Syllabus::create([
                'course_id'            => $this->course->id,
                'academic_calendar_id' => null,
                'status'               => 'draft',
                'current_step'         => 'academic_calendar',
                'prepared_by'          => Auth::id(),
            ]);
        } else {
            abort(404);
        }

        $this->initializeStepState();
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    #[On('syllabus-step-dirty')]
    public function onStepDirty(string $step, bool $dirty = true): void
    {
        if (! array_key_exists($step, $this->syllabus->getWizardSteps())) {
            return;
        }
        $this->stepDirty[$step] = $dirty;
    }

    /**
     * Child steps still fire this after saving so the wizard can:
     *  - clear the dirty flag
     *  - show a toast if a message was provided
     *  - refresh the syllabus model (e.g. after calendar is linked)
     *
     * Navigation is NO LONGER gated on this event — saveAndNavigate() already
     * changed $currentStep in the same request as the save dispatch, so the
     * UI has already updated by the time this event arrives.
     */
    #[On('syllabus-step-saved')]
    public function onStepSaved(string $step, ?string $message = null): void
    {
        if (! array_key_exists($step, $this->syllabus->getWizardSteps())) {
            return;
        }

        $this->stepDirty[$step] = false;

        if ($message) {
            $this->dispatch('lw-toast', type: 'success', message: $message);
        }

        $this->syllabus->refresh();
    }

    // ── Navigation — ONE round trip ───────────────────────────────────────────

    /**
     * HOW THIS IS FAST:
     *
     * Old flow (2 round trips, 3-5 s):
     *   1. clickTab() → dispatch 'syllabus-save-step'
     *      Child receives it, saves, dispatches 'syllabus-step-saved' back
     *   2. onStepSaved() receives it → calls setStep() → re-renders wizard
     *      The child component has :key="...currentStep" so it's DESTROYED and
     *      REMOUNTED — cold-boot DB queries, full render.
     *
     * New flow (1 round trip, ~200-400 ms):
     *   1. saveAndNavigate() → dispatch 'syllabus-save-step' (fire-and-forget to child)
     *      + immediately set $currentStep in the SAME request
     *   2. Wizard re-renders with new $currentStep.
     *      The blade uses block/hidden on wrapper divs — NO :key, NO remount.
     *      The child component that was already mounted just gets hidden/shown.
     *      Child's onSaveRequested() runs in the same Livewire request batch,
     *      writing to DB before the response is sent.
     */
    private function saveAndNavigate(string $toStep): void
    {
        if (! array_key_exists($toStep, $this->syllabus->getWizardSteps())) {
            return;
        }
        if ($toStep === $this->currentStep) {
            return;
        }

        // 1. Tell the current child to save — fire-and-forget within this request
        $this->dispatch('syllabus-save-step', step: $this->currentStep);

        // 2. Switch step immediately — same round trip, no waiting
        $this->currentStep = $toStep;
        $this->syllabus->update(['current_step' => $toStep]);

        // 3. Notify the new step it's now active (safety net for steps that use this)
        $this->dispatch('syllabus-step-changed', step: $toStep);
    }

    public function goNextStep(): void
    {
        $steps = array_keys($this->syllabus->getWizardSteps());
        $index = array_search($this->currentStep, $steps, true);
        if ($index === false || $index >= count($steps) - 1) {
            return;
        }
        $this->saveAndNavigate($steps[$index + 1]);
    }

    public function goPreviousStep(): void
    {
        $steps = array_keys($this->syllabus->getWizardSteps());
        $index = array_search($this->currentStep, $steps, true);
        if ($index === false || $index <= 0) {
            return;
        }
        $this->saveAndNavigate($steps[$index - 1]);
    }

    public function clickTab(string $step): void
    {
        $this->saveAndNavigate($step);
    }

    // ── Submit ────────────────────────────────────────────────────────────────

    public function submitForReview()
    {
        if ($this->stepHasMissingRequired('academic_calendar')
            || $this->stepHasMissingRequired('course_components')
            || $this->stepHasMissingRequired('course_outcomes')
            || $this->stepHasMissingRequired('weekly_coverage')
            || $this->stepHasMissingRequired('course_evaluation')) {
            $this->dispatch('lw-toast', type: 'error', message: 'Complete all required fields before submitting.');
            return null;
        }

        if (($this->stepDirty['course_outcomes'] ?? false) === true) {
            $this->dispatch('lw-toast', type: 'warning', message: 'Save Course Outcomes first before submitting.');
            return null;
        }

        $this->syllabus->update(['status' => 'under_review', 'current_step' => 'review']);

        return redirect()->route('syllabus.show', $this->syllabus->id)
            ->with('toast', ['message' => 'Syllabus submitted for review successfully.', 'type' => 'success']);
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.syllabus.syllabus-wizard');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function saveCurrentStep(): void
    {
        $this->dispatch('syllabus-save-step', step: $this->currentStep);
    }

    public function hasNextStep(): bool
    {
        $steps = array_keys($this->syllabus->getWizardSteps());
        $index = array_search($this->currentStep, $steps, true);
        return $index !== false && $index < count($steps) - 1;
    }

    public function hasPreviousStep(): bool
    {
        $steps = array_keys($this->syllabus->getWizardSteps());
        $index = array_search($this->currentStep, $steps, true);
        return $index !== false && $index > 0;
    }

    public function stepHasMissingRequired(string $step): bool
    {
        $syllabusId = (int) $this->syllabus->id;

        switch ($step) {
            case 'academic_calendar':
                return empty($this->syllabus->academic_calendar_id);

            case 'course_components':
                $lec        = CourseComponent::where('syllabus_id', $syllabusId)->where('type', 'LEC')->first();
                $missingLec = ! $this->componentComplete($lec);
                $lab        = CourseComponent::where('syllabus_id', $syllabusId)->where('type', 'LAB')->first();
                $missingLab = $this->course->has_lec_lab ? ! $this->componentComplete($lab) : false;
                return $missingLec || $missingLab;

            case 'course_outcomes':
                return ! CourseOutcome::where('syllabus_id', $syllabusId)
                    ->whereRaw("TRIM(description) <> ''")->exists();

            case 'weekly_coverage':
                return ! SyllabusWeek::where('syllabus_id', $syllabusId)->exists();

            case 'course_evaluation':
                // Require weights for all week contents that have an assessment task or belong to an exam week.
                $weekContentIds = WeekContent::query()
                    ->join('syllabus_weeks', 'syllabus_weeks.id', '=', 'week_contents.syllabus_week_id')
                    ->where('syllabus_weeks.syllabus_id', $syllabusId)
                    ->where(function ($q) {
                        $q->whereNotNull('syllabus_weeks.exam_type')
                            ->orWhereRaw("TRIM(COALESCE(week_contents.assessment_task, '')) <> ''");
                    })
                    ->pluck('week_contents.id');

                if ($weekContentIds->isEmpty()) {
                    return true;
                }

                $evaluatedCount = SyllabusEvaluationItem::query()
                    ->whereIn('week_content_id', $weekContentIds)
                    ->whereNotNull('weight')
                    ->count();

                return $evaluatedCount !== $weekContentIds->count();

            default:
                return false;
        }
    }

    private function componentComplete(?CourseComponent $component): bool
    {
        if (! $component) {
            return false;
        }

        return collect([
            $component->instructor_name,
            $component->instructor_email,
            $component->phone,
            $component->office,
            $component->class_hours,
            $component->schedule,
            $component->consultation_hours,
            $component->performance_standard,
        ])->every(fn ($v) => trim((string) $v) !== '');
    }

    private function initializeStepState(): void
    {
        foreach (array_keys($this->syllabus->getWizardSteps()) as $step) {
            $this->stepDirty[$step] = false;
        }
    }
}
