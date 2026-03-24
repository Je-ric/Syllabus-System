<?php

namespace App\Livewire\Syllabus;

use App\Services\Syllabus\SyllabusReviewService;
use App\Services\Syllabus\SyllabusSnapshotService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Syllabus;
use App\Models\Course;
use App\Models\CompleteSyllabus;
use App\Models\AuditLog;
use App\Models\SyllabusWeek;
use App\Models\CourseOutcome;
use App\Models\CourseComponent;
use App\Models\WeekContent;
use App\Models\SyllabusEvaluationItem;
use Throwable;

class SyllabusWizard extends Component
{
    public ?Syllabus $syllabus    = null;
    public ?Course   $course      = null;
    public string    $currentStep = 'academic_calendar';
    public array     $stepDirty   = [];

    // Reviewer list lives here because addReviewer() / removeReviewer() are
    // parent methods called via $parent.* from the child blade. After each
    // mutation we reload and dispatch 'syllabus-reviewers-updated' so ReviewStep
    // re-renders with the fresh list passed from render() below.
    public array $reviewers = [];

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
        $this->loadReviewers();
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
    // Revision history mutations (saveRevisions, addRevision, removeRevision,
    // saveConcurred, saveApproved) all live on ReviewStep directly — no $parent
    // calls needed, removing the "public method not found on component" error.
    //
    // Called from the child blade via $parent.addReviewer($wire.selectedReviewerId)
    // and $parent.removeReviewer(id). After each mutation we reload $this->reviewers
    // and dispatch 'syllabus-reviewers-updated' so ReviewStep re-renders.

    public function addReviewer(?int $reviewerUserId = null): void
    {
        if (! $this->syllabus) {
            $this->dispatch('lw-toast', type: 'error', message: 'No syllabus loaded.');
            return;
        }

        try {
            app(SyllabusReviewService::class)->assignReviewer($this->syllabus, (int) $reviewerUserId);
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first() ?? 'Unable to add reviewer.';
            $this->dispatch('lw-toast', type: 'error', message: $message);
            return;
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to add reviewer.');
            return;
        }

        $this->dispatch('lw-toast', type: 'success', message: 'Reviewer assigned (auto-approved).');
        $this->loadReviewers();
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
        $this->loadReviewers();
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
        $this->loadReviewers();
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

        $this->dispatch('syllabus-save-step', step: $this->currentStep);

        $this->currentStep = $toStep;
        $this->syllabus->update(['current_step' => $toStep]);

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

    // ── Save as Done ──────────────────────────────────────────────────────────

    #[On('wizard-save-as-done')]
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

        // 1. Generate HTML snapshots (complete + abridged + assessment) ──────
        try {
            $snapshot       = app(SyllabusSnapshotService::class);
            $html           = $snapshot->generateCompleteHtml($syllabus);
            $htmlAbridged   = $snapshot->generateAbridgedHtml($syllabus);
            $htmlAssessment = $snapshot->generateAssessmentHtml($syllabus);
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Save-as-done failed: ' . $e->getMessage());
            return;
        }

        // 2. Build storage paths ─────────────────────────────────────────
        $program    = $syllabus->course?->program;
        $department = $program?->departments?->first();
        $college    = $department?->college;
        $faculty    = $syllabus->preparer;

        $collegeName    = $college?->name    ?? 'Unknown College';
        $departmentName = $department?->name ?? 'Unknown Department';
        $programName    = $program?->program_name ?? $program?->name ?? 'Unknown Program';
        $facultyName    = $faculty?->name    ?? 'User ' . ($syllabus->prepared_by ?? 'Unknown');

        $version      = (int) (CompleteSyllabus::where('syllabus_id', $syllabus->id)->max('version') ?? 0) + 1;
        $academicYear = $syllabus->academicCalendar?->academic_year ?? 'N-A';
        $semester     = $syllabus->academicCalendar?->semester      ?? 'N-A';
        $courseCode   = $syllabus->course?->course_code             ?? 'COURSE';
        $courseName   = $syllabus->course?->course_name             ?? $courseCode;

        // Folder: College / Department / Program / Faculty / Course Code / v{n} (AY Sem)
        $versionFolder = "v{$version} ({$academicYear} {$semester})";
        $baseDir = implode('/', [
            'Syllabus Snapshots',
            $collegeName,
            $departmentName,
            $programName,
            $facultyName,
            $courseCode,
            $versionFolder,
        ]);

        // File names: Complete - COURSE CODE.html, etc.
        $storagePath           = $baseDir . '/Complete - ' . $courseCode . '.html';
        $storagePathAbridged   = $baseDir . '/Abridged - ' . $courseCode . '.html';
        $storagePathAssessment = $baseDir . '/Assessment - ' . $courseCode . '.html';

        // Local paths use slugified names (filesystem-safe)
        $baseSlug      = Str::slug($courseCode . '-' . $academicYear . '-' . $semester . '-v' . $version);
        $baseDirLocal  = implode('/', [
            'syllabus-snapshots',
            Str::slug($collegeName),
            Str::slug($departmentName),
            Str::slug($programName),
            Str::slug($facultyName),
            Str::slug($courseCode),
            'v' . $version,
        ]);
        $localPath           = $baseDirLocal . '/' . $baseSlug . '.html';
        $localPathAbridged   = $baseDirLocal . '/' . $baseSlug . '-abridged.html';
        $localPathAssessment = $baseDirLocal . '/' . $baseSlug . '-assessment.html';

        // 3. Write to Google Drive (primary) ──────────────────────────────────
        $driveSuccess = false;
        try {
            Storage::disk('google')->put($storagePath,           $html);
            Storage::disk('google')->put($storagePathAbridged,   $htmlAbridged);
            Storage::disk('google')->put($storagePathAssessment, $htmlAssessment);
            $driveSuccess = true;
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'warning', message: 'Google Drive upload failed: ' . $e->getMessage());
        }

        // 3b. Always mirror to local disk as backup ───────────────────────────
        try {
            Storage::disk('local')->put($localPath,           $html);
            Storage::disk('local')->put($localPathAbridged,   $htmlAbridged);
            Storage::disk('local')->put($localPathAssessment, $htmlAssessment);
        } catch (Throwable $e) {
            report($e);
            if (! $driveSuccess) {
                $this->dispatch('lw-toast', type: 'error', message: 'Both Drive and local write failed: ' . $e->getMessage());
                return;
            }
        }

        // 4. Persist version record ──────────────────────────────────────
        try {
            CompleteSyllabus::create([
                'syllabus_id'          => $syllabus->id,
                'course_id'            => $syllabus->course_id,
                'academic_year'        => $academicYear,
                'semester'             => $semester,
                'pdf_path'             => $driveSuccess ? $storagePath           : $localPath,
                'abridged_path'        => $driveSuccess ? $storagePathAbridged   : $localPathAbridged,
                'evaluation_path'      => $driveSuccess ? $storagePathAssessment : $localPathAssessment,
                'version'              => $version,
                'approved_at'          => null,
                'approved_by'          => null,
                'checksum'             => hash('sha256', $html),
                'checksum_abridged'    => hash('sha256', $htmlAbridged),
                'checksum_evaluation'  => hash('sha256', $htmlAssessment),
            ]);
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'DB record error: ' . $e->getMessage());
            return;
        }

        // 5. Update syllabus status ───────────────────────────────────────
        try {
            $syllabus->forceFill([
                'status'       => 'under_review',
                'current_step' => 'review',
            ])->save();
        } catch (Throwable $e) {
            report($e);
            // Non-fatal — snapshot is saved
        }

        $this->syllabus->refresh();

        AuditLog::record(
            action: 'saved_version',
            module: 'Syllabus',
            referenceId: $syllabus->id,
            description: "Saved syllabus version v{$version} for course {$courseCode} ({$academicYear} {$semester})."
        );

        $message = "Syllabus version frozen (v{$version}).";
        if ($driveSuccess) {
            $message .= ' Backed up to Google Drive.';
        }
        $this->dispatch('lw-toast', type: 'success', message: $message);
        $this->dispatch('wizard-save-done');
        $this->dispatch('syllabus-step-changed', step: 'review');
    }

    // ── Submit for review ─────────────────────────────────────────────────────

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
                    ->whereRaw("TRIM(description) <> ''")
                    ->exists();

            case 'weekly_coverage':
                return ! SyllabusWeek::where('syllabus_id', $syllabusId)->exists();

            case 'course_evaluation':
                $weekContentIds = WeekContent::query()
                    ->join('syllabus_weeks', 'syllabus_weeks.id', '=', 'week_contents.syllabus_week_id')
                    ->where('syllabus_weeks.syllabus_id', $syllabusId)
                    ->whereRaw("TRIM(COALESCE(week_contents.assessment_task, '')) <> ''")
                    ->whereRaw("TRIM(week_contents.assessment_task) <> 'Non-Teaching Week'")
                    ->pluck('week_contents.id');

                if ($weekContentIds->isEmpty()) {
                    return true;
                }

                $evaluatedCount = SyllabusEvaluationItem::whereIn('week_content_id', $weekContentIds)
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

        // phone and office are optional — not checked here
        return collect([
            $component->instructor_name,
            $component->instructor_email,
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

    private function loadReviewers(): void
    {
        if (! $this->syllabus) {
            $this->reviewers = [];
            return;
        }

        $this->reviewers = $this->syllabus->reviewers()
            ->with('user')
            ->orderBy('created_at')
            ->get()
            ->map(fn ($reviewer) => [
                'id'         => $reviewer->id,
                'user_id'    => $reviewer->user_id,
                'user_name'  => $reviewer->user->name,
                'user_email' => $reviewer->user->email,
                'status'     => $reviewer->status,
            ])
            ->values()
            ->all();
    }
}
