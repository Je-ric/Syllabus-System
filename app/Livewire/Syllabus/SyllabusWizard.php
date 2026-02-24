<?php

namespace App\Livewire\Syllabus;

use App\Models\CourseComponent;
use App\Models\CourseOutcome;
use App\Models\Course;
use App\Models\Syllabus;
use App\Models\SyllabusWeek;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class SyllabusWizard extends Component
{
    public ?Syllabus $syllabus = null;
    public ?Course $course = null;
    public string $currentStep = 'academic_calendar';
    public ?string $lastSavedAt = null;
    public array $stepDirty = [];

    public function mount($syllabusId = null, $courseId = null)
    {
        $syllabusId = $syllabusId ? (int) $syllabusId : null;
        $courseId = $courseId ? (int) $courseId : null;

        if ($syllabusId) {
            $this->syllabus = Syllabus::with('course.program')->findOrFail($syllabusId);
            if ($this->syllabus->prepared_by !== Auth::id()) {
                abort(403, 'Unauthorized');
            }

            $this->course = $this->syllabus->course;
            $steps = array_keys($this->syllabus->getWizardSteps());
            $persistedStep = (string) ($this->syllabus->current_step ?? '');
            $this->currentStep = in_array($persistedStep, $steps, true) ? $persistedStep : 'academic_calendar';
            if ($this->syllabus->current_step !== $this->currentStep) {
                $this->syllabus->update(['current_step' => $this->currentStep]);
            }
        } elseif ($courseId) {
            $this->course = Course::with('program')->findOrFail($courseId);
            $this->syllabus = Syllabus::create([
                'course_id' => $this->course->id,
                'academic_calendar_id' => null,
                'status' => 'draft',
                'current_step' => 'academic_calendar',
                'prepared_by' => Auth::id(),
            ]);
        } else {
            abort(404);
        }

        $this->initializeStepState();
    }

    #[On('syllabus-step-dirty')]
    public function onStepDirty(string $step, bool $dirty = true): void
    {
        if (!array_key_exists($step, $this->syllabus->getWizardSteps())) {
            return;
        }

        $this->stepDirty[$step] = $dirty;
    }

    #[On('syllabus-step-saved')]
    public function onStepSaved(string $step, ?string $message = null): void
    {
        if (!array_key_exists($step, $this->syllabus->getWizardSteps())) {
            return;
        }

        $this->stepDirty[$step] = false;
        $this->lastSavedAt = now()->format('M d, Y h:i A');
        if ($message) {
            $this->dispatch('lw-toast', type: 'success', message: $message);
        }
        $this->syllabus->refresh();
    }

    public function saveCurrentStep(): void
    {
        $this->dispatch('syllabus-save-step', step: $this->currentStep);
    }

    public function submitForReview()
    {
        if ($this->stepHasMissingRequired('academic_calendar')
            || $this->stepHasMissingRequired('course_components')
            || $this->stepHasMissingRequired('course_outcomes')
            || $this->stepHasMissingRequired('weekly_coverage')) {
            $this->dispatch('lw-toast', type: 'error', message: 'Complete all required fields before submitting.');
            return null;
        }

        if (($this->stepDirty['course_outcomes'] ?? false) === true) {
            $this->dispatch('lw-toast', type: 'warning', message: 'Save Course Outcomes first before submitting.');
            return null;
        }

        $this->syllabus->update([
            'status' => 'under_review',
            'current_step' => 'review',
        ]);

        return redirect()->route('syllabus.show', $this->syllabus->id)
            ->with('toast', [
                'message' => 'Syllabus submitted for review successfully.',
                'type' => 'success'
            ]);
    }

    public function render()
    {
        return view('livewire.syllabus.syllabus-wizard');
    }

    public function setStep(string $step): void
    {
        if (!array_key_exists($step, $this->syllabus->getWizardSteps())) {
            return;
        }

        $this->currentStep = $step;
        if ($this->syllabus) {
            $this->syllabus->update(['current_step' => $step]);
        }

        $this->dispatch('syllabus-step-changed', step: $step);
    }

    public function goNextStep(): void
    {
        $steps = array_keys($this->syllabus->getWizardSteps());
        $index = array_search($this->currentStep, $steps, true);
        if ($index === false || $index >= count($steps) - 1) {
            return;
        }

        $this->navigateToStep($this->currentStep, $steps[$index + 1]);
    }

    public function goPreviousStep(): void
    {
        $steps = array_keys($this->syllabus->getWizardSteps());
        $index = array_search($this->currentStep, $steps, true);
        if ($index === false || $index <= 0) {
            return;
        }

        $this->navigateToStep($this->currentStep, $steps[$index - 1]);
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

    public function navigateToStep(string $fromStep, string $toStep): void
    {
        if ($fromStep === $toStep) {
            return;
        }

        // Only CO step requires explicit manual save before switching.
        if ($fromStep === 'course_outcomes') {
            $hasUnsavedCo = (bool) ($this->stepDirty['course_outcomes'] ?? false);
            if ($hasUnsavedCo) {
                $this->dispatch('lw-toast', type: 'warning', message: 'Save Course Outcomes first before proceeding.');
                return;
            }
        }

        $this->setStep($toStep);
    }

    private function initializeStepState(): void
    {
        foreach (array_keys($this->syllabus->getWizardSteps()) as $step) {
            $this->stepDirty[$step] = false;
        }
    }

    public function stepHasMissingRequired(string $step): bool
    {
        $syllabusId = (int) $this->syllabus->id;

        switch ($step) {
            case 'academic_calendar':
                return empty($this->syllabus->academic_calendar_id);

            case 'course_components':
                $lec = CourseComponent::query()
                    ->where('syllabus_id', $syllabusId)
                    ->where('type', 'LEC')
                    ->first();
                $missingLec = !$this->componentComplete($lec);

                $lab = CourseComponent::query()
                    ->where('syllabus_id', $syllabusId)
                    ->where('type', 'LAB')
                    ->first();
                $missingLab = $this->course->has_lec_lab ? !$this->componentComplete($lab) : false;
                return $missingLec || $missingLab;

            case 'course_outcomes':
                return !CourseOutcome::query()
                    ->where('syllabus_id', $syllabusId)
                    ->whereRaw("TRIM(description) <> ''")
                    ->exists();

            case 'weekly_coverage':
                return !SyllabusWeek::query()
                    ->where('syllabus_id', $syllabusId)
                    ->exists();

            default:
                return false;
        }
    }

    private function componentComplete(?CourseComponent $component): bool
    {
        if (!$component) {
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
        ])->every(fn($value) => trim((string) $value) !== '');
    }
}
