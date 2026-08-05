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

        // Block leaving the academic calendar step until a calendar is selected.
        // if ($this->currentStep === 'academic_calendar' && empty($this->syllabus->academic_calendar_id)) {
        //     $this->dispatch('lw-toast', type: 'warning', message: 'Select an academic calendar before continuing.');
        //     return;
        // }
        // Step 2: Alpine holds schedule/consultation state — must push before saving.
        if ($this->currentStep === 'course_components') {
            $this->dispatch('request-push-and-navigate', toStep: $toStep);
            return;
        }

        // Step 5: Alpine holds weight inputs — flush to Livewire before saving.
        // Dispatches browser event; Alpine calls $wire.set() for each input,
        // then syllabus-save-step fires in the same tick via onSaveRequested.
        if ($this->currentStep === 'course_evaluation') {
            $this->dispatch('request-eval-flush-and-navigate', toStep: $toStep);
            return;
        }

        // Step 3: if dirty, tell Alpine to save pending COs first, then navigate.
        if ($this->currentStep === 'course_outcomes' && ($this->stepDirty['course_outcomes'] ?? false) === true) {
            $this->dispatch('request-co-save-and-navigate', toStep: $toStep);
            return;
        }

        $this->dispatch('syllabus-save-step', step: $this->currentStep);

        $this->currentStep = $toStep;
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
            $this->dispatch('lw-toast', type: 'error', message: 'Complete all required fields before submitting.');
            return null;
        }

        if (($this->stepDirty['course_outcomes'] ?? false) === true) { // set via setStepDirty(), no re-render
            $this->dispatch('lw-toast', type: 'warning', message: 'Save Course Outcomes first before submitting.');
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
