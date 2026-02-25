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
    // The syllabus being worked on (can be null at first)
    public ?Syllabus $syllabus = null;
    public ?Course $course = null;
    // Which step of the wizard the user is currently on
    public string $currentStep = 'academic_calendar';
    // When the last save happened (for user feedback)
    public ?string $lastSavedAt = null;
    // Keeps track of which steps have unsaved changes
    public array $stepDirty = [];


    /**
     * This runs when the component loads. It sets up the wizard for either editing an existing syllabus or creating a new one.
     * If neither a syllabus nor a course is found, it shows a 404 error.
     */
    public function mount($syllabusId = null, $courseId = null)
    {
        $syllabusId = $syllabusId ? (int) $syllabusId : null;
        $courseId = $courseId ? (int) $courseId : null;

        if ($syllabusId) { 
            // Editing an existing syllabus
            $this->syllabus = Syllabus::with('course.program')->findOrFail($syllabusId);
            // Only the user who prepared the syllabus can edit it
            if ($this->syllabus->prepared_by !== Auth::id()) {
                abort(403, 'Unauthorized');
            }
            
            $this->course = $this->syllabus->course;
            $steps = array_keys($this->syllabus->getWizardSteps());
            $persistedStep = (string) ($this->syllabus->current_step ?? '');
            // Restore the last step the user was on, or default to the first step
            $this->currentStep = in_array($persistedStep, $steps, true) ? $persistedStep : 'academic_calendar';
            if ($this->syllabus->current_step !== $this->currentStep) {
                $this->syllabus->update(['current_step' => $this->currentStep]);
            }
        } elseif ($courseId) {
            // Creating a new syllabus for a course
            $this->course = Course::with('program')->findOrFail($courseId);
            $this->syllabus = Syllabus::create([
                'course_id' => $this->course->id,
                'academic_calendar_id' => null,
                'status' => 'draft',
                'current_step' => 'academic_calendar',
                'prepared_by' => Auth::id(),
            ]);
        } else {
            // If neither ID is provided, show a 404 error
            abort(404);
        }

        $this->initializeStepState(); // Set up the state for each wizard step
    }


    // This listens for a Livewire event when a step becomes dirty (has unsaved changes)
    #[On('syllabus-step-dirty')]
    public function onStepDirty(string $step, bool $dirty = true): void
    {
        // Ignore if the step is not valid
        if (!array_key_exists($step, $this->syllabus->getWizardSteps())) {
            return;
        }

        $this->stepDirty[$step] = $dirty;
    }


    // This listens for a Livewire event when a step is saved
    #[On('syllabus-step-saved')]
    public function onStepSaved(string $step, ?string $message = null): void
    {
        // Ignore if the step is not valid
        if (!array_key_exists($step, $this->syllabus->getWizardSteps())) {
            return;
        }

        $this->stepDirty[$step] = false; // Mark step as clean
        $this->lastSavedAt = now()->format('M d, Y h:i A'); // Update last saved time
        if ($message) {
            $this->dispatch('lw-toast', type: 'success', message: $message);
        }
        $this->syllabus->refresh(); // Refresh the syllabus from the database
    }


    // Triggers a save for the current step (handled by child components)
    public function saveCurrentStep(): void
    {
        $this->dispatch('syllabus-save-step', step: $this->currentStep);
    }


    /**
     * Called when the user submits the syllabus for review.
     * Checks that all required steps are complete and not dirty before allowing submission.
     */
    public function submitForReview()
    {
        if ($this->stepHasMissingRequired('academic_calendar')
            || $this->stepHasMissingRequired('course_components')
            || $this->stepHasMissingRequired('course_outcomes')
            || $this->stepHasMissingRequired('weekly_coverage')) {
            $this->dispatch('lw-toast', type: 'error', message: 'Complete all required fields before submitting.');
            return null;
        }

        // Prevent submission if course outcomes step has unsaved changes
        if (($this->stepDirty['course_outcomes'] ?? false) === true) {
            $this->dispatch('lw-toast', type: 'warning', message: 'Save Course Outcomes first before submitting.');
            return null;
        }

        $this->syllabus->update([
            'status' => 'under_review',
            'current_step' => 'review',
        ]);

        // Redirect to the syllabus review page with a success message
        return redirect()->route('syllabus.show', $this->syllabus->id)
            ->with('toast', [
                'message' => 'Syllabus submitted for review successfully.',
                'type' => 'success'
            ]);
    }


    // Renders the Livewire view for the wizard
    public function render()
    {
        return view('livewire.syllabus.syllabus-wizard');
    }


    /**
     * Changes the current step in the wizard.
     * Notifies child components about the step change.
     */
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


    // Moves to the next step in the wizard, if possible
    public function goNextStep(): void
    {
        $steps = array_keys($this->syllabus->getWizardSteps());
        $index = array_search($this->currentStep, $steps, true);
        if ($index === false || $index >= count($steps) - 1) {
            return;
        }

        $this->navigateToStep($this->currentStep, $steps[$index + 1]);
    }


    // Moves to the previous step in the wizard, if possible
    public function goPreviousStep(): void
    {
        $steps = array_keys($this->syllabus->getWizardSteps());
        $index = array_search($this->currentStep, $steps, true);
        if ($index === false || $index <= 0) {
            return;
        }

        $this->navigateToStep($this->currentStep, $steps[$index - 1]);
    }


    // Checks if there is a next step after the current one
    public function hasNextStep(): bool
    {
        $steps = array_keys($this->syllabus->getWizardSteps());
        $index = array_search($this->currentStep, $steps, true);
        return $index !== false && $index < count($steps) - 1;
    }


    // Checks if there is a previous step before the current one
    public function hasPreviousStep(): bool
    {
        $steps = array_keys($this->syllabus->getWizardSteps());
        $index = array_search($this->currentStep, $steps, true);
        return $index !== false && $index > 0;
    }


    /**
     * Handles logic for moving between steps, including any special save requirements.
     */
    public function navigateToStep(string $fromStep, string $toStep): void
    {
        if ($fromStep === $toStep) {
            return;
        }

        // Only the course outcomes step requires explicit manual save before switching.
        if ($fromStep === 'course_outcomes') {
            $hasUnsavedCo = (bool) ($this->stepDirty['course_outcomes'] ?? false);
            if ($hasUnsavedCo) {
                $this->dispatch('lw-toast', type: 'warning', message: 'Save Course Outcomes first before proceeding.');
                return;
            }
        }

        $this->setStep($toStep);
    }


    // Sets all steps to "not dirty" (no unsaved changes) at the start
    private function initializeStepState(): void
    {
        foreach (array_keys($this->syllabus->getWizardSteps()) as $step) {
            $this->stepDirty[$step] = false;
        }
    }


    /**
     * Checks if a given step is missing any required fields.
     * Returns true if incomplete, false if all required data is present.
     */
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


    /**
     * Checks if a course component is fully filled out (all required fields are non-empty).
     */
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
