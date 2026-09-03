<?php

namespace App\Livewire\Syllabus;

use App\Services\Syllabus\SyllabusCompletionService;
use App\Services\Syllabus\Review\SyllabusReviewService;
use App\Services\Syllabus\Snapshots\SyllabusSnapshotService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Syllabus;
use App\Models\Course;
use App\Models\AuditLog;
use App\Models\AcademicCalendar;
use Throwable;

class SyllabusWizard extends Component
{
    public ?Syllabus $syllabus    = null;
    public ?Course   $course      = null;
    public string    $currentStep = 'academic_calendar';
    public array     $stepDirty   = [];

    // Cached step-missing status — computed once per render() to avoid
    // repeated DB queries when Blade loops call the indicator check.
    public array $stepMissing = [];

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount($syllabusId = null, $courseId = null): void
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
            // Syllabus creation is handled by SyllabusController::wizard() before
            // this component mounts. By the time mount() runs with a courseId,
            // the controller has already created the record and redirected with
            // syllabusId — so this branch should never be reached in normal flow.
            // Guard defensively: look up an existing record or abort.
            $existing = Syllabus::where('course_id', $courseId)
                ->where('prepared_by', Auth::id())
                ->first();

            if ($existing) {
                $this->syllabus = $existing->load('course.program');
                $this->course   = $this->syllabus->course;
            } else {
                abort(404, 'No syllabus found for this course.');
            }
        } else {
            abort(404);
        }

        $this->initializeStepState();
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    // stepDirty is updated from JS without a round-trip to avoid re-renders.
    // Alpine calls $wire.setStepDirty() which uses skipRender() so no DOM diff occurs.
    public function setStepDirty(string $step, bool $dirty): void
    {
        if (! array_key_exists($step, $this->syllabus->getWizardSteps())) {
            return;
        }
        $this->stepDirty[$step] = $dirty;
        $this->skipRender();
    }

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
        
        // Persist current step to database after successful save
        $this->syllabus->update(['current_step' => $this->currentStep]);
    }

    #[On('syllabus-step-save-failed')]
    public function onStepSaveFailed(string $step, string $error, ?string $previousStep = null): void
    {
        if (! array_key_exists($step, $this->syllabus->getWizardSteps())) {
            return;
        }

        // If we have a previous step and the current step differs, rollback navigation
        if ($previousStep && $previousStep !== $this->currentStep) {
            $this->currentStep = $previousStep;
            $this->syllabus->update(['current_step' => $previousStep]);
            $this->dispatch('syllabus-step-changed', step: $previousStep);

            $this->dispatch('lw-toast', type: 'error', message: "Couldn't save your changes. Please try again.");
        } else {
            $this->dispatch('lw-toast', type: 'error', message: "Couldn't save your changes. Please try again.");
        }
    }

    // ── Reviewer management ───────────────────────────────────────────────────
    //
    // Reviewer mutations live on the parent because the child Blade calls
    // $parent.addReviewer() / $parent.removeReviewer(). After each mutation
    // we dispatch 'syllabus-reviewers-updated' so ReviewStep re-renders.

    public function addReviewer(?int $reviewerUserId = null, string $role = 'member'): void
    {
        if (! $this->syllabus) {
            $this->dispatch('lw-toast', type: 'error', message: 'No syllabus loaded.');
            return;
        }

        try {
            app(SyllabusReviewService::class)->assignReviewer($this->syllabus, (int) $reviewerUserId, $role);
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first() ?? 'Unable to add reviewer.';
            $this->dispatch('lw-toast', type: 'error', message: $message);
            return;
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to add reviewer.');
            return;
        }

        $this->dispatch('lw-toast', type: 'success', message: 'Reviewer assigned.');
        $this->dispatch('syllabus-reviewers-updated');
    }

    public function removeReviewer(int $syllabusReviewerId): void
    {
        if (! $this->syllabus) {
            $this->dispatch('lw-toast', type: 'error', message: 'No syllabus loaded.');
            return;
        }

        try {
            app(SyllabusReviewService::class)->removeReviewer($this->syllabus, $syllabusReviewerId);
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to remove reviewer.');
            return;
        }

        $this->dispatch('lw-toast', type: 'success', message: 'Reviewer removed.');
        $this->dispatch('syllabus-reviewers-updated');
    }

    public function updateReviewerStatus(int $syllabusReviewerId, string $status): void
    {
        if (! $this->syllabus) {
            $this->dispatch('lw-toast', type: 'error', message: 'No syllabus loaded.');
            return;
        }

        try {
            app(SyllabusReviewService::class)->updateReviewerStatus($this->syllabus, $syllabusReviewerId, $status);
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first() ?? 'Unable to update reviewer.';
            $this->dispatch('lw-toast', type: 'error', message: $message);
            return;
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to update reviewer.');
            return;
        }

        $this->dispatch('lw-toast', type: 'success', message: 'Reviewer status updated.');
        $this->dispatch('syllabus-reviewers-updated');
    }

    // ── Navigation ────────────────────────────────────────────────────────────

    private function saveAndNavigate(string $toStep): void
    {
        if (! array_key_exists($toStep, $this->syllabus->getWizardSteps())) {
            return;
        }
        if ($toStep === $this->currentStep) {
            return;
        }

        // Store previous step for potential rollback
        $previousStep = $this->currentStep;

        // Step 2: Alpine holds schedule/consultation state — check if dirty before push
        if ($this->currentStep === 'course_components') {
            if (($this->stepDirty['course_components'] ?? false) === true) {
                $this->dispatch('request-push-and-navigate', toStep: $toStep, previousStep: $previousStep);
            } else {
                // No changes, navigate immediately but still persist step
                $this->navigateImmediately($toStep);
            }
            return;
        }

        // Step 5: Alpine holds weight inputs — check if dirty before flush
        if ($this->currentStep === 'course_evaluation') {
            if (($this->stepDirty['course_evaluation'] ?? false) === true) {
                $this->dispatch('request-eval-flush-and-navigate', toStep: $toStep, previousStep: $previousStep);
            } else {
                // No changes, navigate immediately but still persist step
                $this->navigateImmediately($toStep);
            }
            return;
        }

        // Step 3: course_outcomes uses Alpine coManager - check if dirty before save
        if ($this->currentStep === 'course_outcomes') {
            if (($this->stepDirty['course_outcomes'] ?? false) === true) {
                $this->dispatch('request-co-save-and-navigate', toStep: $toStep, previousStep: $previousStep);
            } else {
                // No changes, navigate immediately but still persist step
                $this->navigateImmediately($toStep);
            }
            return;
        }

        // Check if current step has unsaved changes
        $isDirty = $this->stepDirty[$this->currentStep] ?? false;

        // Navigate immediately and persist step in background
        $this->navigateImmediately($toStep);

        // Only save previous step data in background if it has changes
        if ($isDirty) {
            $this->dispatch('syllabus-save-step', step: $previousStep, navigateToStep: $toStep);
        }
    }

    private function navigateImmediately(string $toStep): void
    {
        $this->currentStep = $toStep;
        
        // Persist step to database to maintain single source of truth
        // This is done synchronously to ensure consistency but won't cause page reload
        $this->syllabus->update(['current_step' => $toStep]);
        
        $this->dispatch('syllabus-step-changed', step: $toStep);
    }

    #[On('navigate-after-save')]
    public function onNavigateAfterSave(string $step): void
    {
        if (! array_key_exists($step, $this->syllabus->getWizardSteps())) {
            return;
        }

        $this->currentStep = $step;
        
        // Persist step to database to maintain single source of truth
        $this->syllabus->update(['current_step' => $step]);

        $this->dispatch('syllabus-step-changed', step: $step);
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

    // ── Save as Done ──────────────────────────────────────────────────────────

    public function saveAsDone(): void
    {
        if (! $this->syllabus) {
            $this->dispatch('lw-toast', type: 'error', message: 'No syllabus loaded.');
            return;
        }

        $syllabus = Syllabus::query()
            ->with([
                'course.program.departments.college',
                'academicCalendar',
                'preparer',
            ])
            ->findOrFail($this->syllabus->id);

        if (! $syllabus->academic_calendar_id) {
            $this->dispatch('lw-toast', type: 'error', message: 'Select an academic calendar first.');
            return;
        }

        try {
            $version = app(SyllabusSnapshotService::class)->saveVersion($syllabus);
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Save-as-done failed: ' . $e->getMessage());
            return;
        }

        $this->syllabus->refresh();

        $courseCode   = $syllabus->course?->course_code             ?? 'COURSE';
        $academicYear = $syllabus->academicCalendar?->academic_year ?? 'N-A';
        $semester     = $syllabus->academicCalendar?->semester      ?? 'N-A';

        AuditLog::record(
            action: 'saved_version',
            module: 'Syllabus',
            referenceId: $syllabus->id,
            description: "Saved syllabus version v{$version} for course {$courseCode} ({$academicYear} {$semester})."
        );

        $this->dispatch('lw-toast', type: 'success', message: "Syllabus version frozen (v{$version}). Saved locally" . (config('filesystems.disks.google') ? ' + Google Drive.' : '.'));
        $this->dispatch('wizard-save-done');
        $this->dispatch('syllabus-step-changed', step: 'review');
    }

    // ── Submit for review ─────────────────────────────────────────────────────

    public function submitForReview()
    {
        $completion  = app(SyllabusCompletionService::class);
        $syllabusId  = (int) $this->syllabus->id;
        $hasLecLab   = (bool) $this->course->has_lec_lab;

        if (empty($this->syllabus->academic_calendar_id)
            || $completion->isMissing($syllabusId, 'course_components', $hasLecLab)
            || $completion->isMissing($syllabusId, 'course_outcomes',   $hasLecLab)
            || $completion->isMissing($syllabusId, 'weekly_coverage',   $hasLecLab)
            || $completion->isMissing($syllabusId, 'course_evaluation', $hasLecLab)) {
            $this->dispatch('lw-toast', type: 'error', message: 'Please fill in all required information first.');
            return null;
        }

        if (($this->stepDirty['course_outcomes'] ?? false) === true) { // set via setStepDirty(), no re-render
            $this->dispatch('lw-toast', type: 'warning', message: 'Please save your Course Outcomes first.');
            return null;
        }

        $this->syllabus->update(['status' => 'under_review', 'current_step' => 'review']);

        AuditLog::record(
            action: 'submitted',
            module: 'Syllabus',
            referenceId: $this->syllabus->id,
            description: "Submitted syllabus #{$this->syllabus->id} for review."
        );

        return redirect()->route('syllabus.show', $this->syllabus->id)
            ->with('toast', ['message' => 'Syllabus submitted for review successfully.', 'type' => 'success']);
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        // Cache step-missing indicators once per render so Blade loops
        // don't fire repeated DB queries for each step tab.
        $completion = app(SyllabusCompletionService::class);
        $syllabusId = (int) $this->syllabus->id;
        $hasLecLab  = (bool) $this->course->has_lec_lab;

        $this->stepMissing = [
            'academic_calendar' => empty($this->syllabus->academic_calendar_id),
            'course_components' => $completion->isMissing($syllabusId, 'course_components', $hasLecLab),
            'course_outcomes'   => $completion->isMissing($syllabusId, 'course_outcomes',   $hasLecLab),
            'weekly_coverage'   => $completion->isMissing($syllabusId, 'weekly_coverage',   $hasLecLab),
            'course_evaluation' => $completion->isMissing($syllabusId, 'course_evaluation', $hasLecLab),
        ];

        return view('livewire.syllabus.wizard.wizard');
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

    public function calendarIsInactive(): bool
    {
        if (! $this->syllabus || ! $this->syllabus->academic_calendar_id) {
            return false;
        }
        $activeId = AcademicCalendar::active()->value('id');
        return $activeId && (int) $this->syllabus->academic_calendar_id !== (int) $activeId;
    }

    private function initializeStepState(): void
    {
        foreach (array_keys($this->syllabus->getWizardSteps()) as $step) {
            $this->stepDirty[$step] = false;
        }
    }

}
