<?php

namespace App\Livewire\Syllabus;

use App\Services\Syllabus\SyllabusReviewService;
use App\Services\Syllabus\SyllabusSnapshotService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
    // Reviewer mutations live on the parent because the child Blade calls
    // $parent.addReviewer() / $parent.removeReviewer(). After each mutation
    // we dispatch 'syllabus-reviewers-updated' so ReviewStep re-renders.

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
        if ($this->currentStep === 'course_outcomes' && ($this->stepDirty['course_outcomes'] ?? false)) {
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

        $academicYear = $syllabus->academicCalendar?->academic_year ?? 'N-A';
        $semester     = $syllabus->academicCalendar?->semester      ?? 'N-A';
        $courseCode   = $syllabus->course?->course_code             ?? 'COURSE';

        // 3. Reserve a version number, build paths, write files, then commit DB.
        //    File I/O is intentionally kept outside the transaction so a DB
        //    rollback never leaves orphaned files, and a file-write failure
        //    never leaves a DB record pointing at a missing file.
        try {
            // Step A — reserve version number inside a short transaction.
            [$version, $storagePath, $storagePathAbridged, $storagePathAssessment] =
                DB::transaction(function () use ($syllabus, $collegeName, $departmentName, $programName, $facultyName, $courseCode, $academicYear, $semester) {
                    $version = (int) (CompleteSyllabus::where('syllabus_id', $syllabus->id)
                        ->lockForUpdate()
                        ->max('version') ?? 0) + 1;

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

                    return [
                        $version,
                        $baseDir . '/Complete - '   . $courseCode . '.html',
                        $baseDir . '/Abridged - '   . $courseCode . '.html',
                        $baseDir . '/Assessment - ' . $courseCode . '.html',
                    ];
                });

            // Step B — write files to disk (outside any transaction).
            Storage::disk('syllabus_snapshots')->put($storagePath,           $html);
            Storage::disk('syllabus_snapshots')->put($storagePathAbridged,   $htmlAbridged);
            Storage::disk('syllabus_snapshots')->put($storagePathAssessment, $htmlAssessment);

            // Mirror to Google Drive (secondary, silent — never blocks save).
            try {
                Storage::disk('google')->put($storagePath,           $html);
                Storage::disk('google')->put($storagePathAbridged,   $htmlAbridged);
                Storage::disk('google')->put($storagePathAssessment, $htmlAssessment);
            } catch (Throwable) {
                // Non-fatal — local copy is the source of truth.
            }

            // Step C — persist DB record now that files exist.
            DB::transaction(function () use ($syllabus, $storagePath, $storagePathAbridged, $storagePathAssessment, $version, $academicYear, $semester, $html, $htmlAbridged, $htmlAssessment) {
                CompleteSyllabus::create([
                    'syllabus_id'         => $syllabus->id,
                    'course_id'           => $syllabus->course_id,
                    'academic_year'       => $academicYear,
                    'semester'            => $semester,
                    'pdf_path'            => $storagePath,
                    'abridged_path'       => $storagePathAbridged,
                    'evaluation_path'     => $storagePathAssessment,
                    'version'             => $version,
                    'approved_at'         => null,
                    'approved_by'         => null,
                    'checksum'            => hash('sha256', $html),
                    'checksum_abridged'   => hash('sha256', $htmlAbridged),
                    'checksum_evaluation' => hash('sha256', $htmlAssessment),
                ]);

                $syllabus->forceFill([
                    'status'       => 'under_review',
                    'current_step' => 'review',
                ])->save();
            });
        } catch (Throwable $e) {
            // If files were written but the DB commit failed, clean them up.
            foreach ([$storagePath ?? null, $storagePathAbridged ?? null, $storagePathAssessment ?? null] as $path) {
                if ($path) {
                    try { Storage::disk('syllabus_snapshots')->delete($path); } catch (Throwable) {}
                }
            }
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Save-as-done failed: ' . $e->getMessage());
            return;
        }

        $this->syllabus->refresh();

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
        // Cache step-missing indicators once per render so Blade loops
        // don't fire repeated DB queries for each step tab.
        $checkedSteps = ['academic_calendar', 'course_components', 'course_outcomes', 'weekly_coverage', 'course_evaluation'];
        $this->stepMissing = [];
        foreach ($checkedSteps as $step) {
            $this->stepMissing[$step] = $this->stepHasMissingRequired($step);
        }

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
                    ->whereRaw("syllabus_weeks.week_no <> 1")  // exclude MVGO week
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

        // phone and office are optional; schedule/consultation_hours were moved
        // to their own tables — not checked here
        return collect([
            $component->instructor_name,
            $component->instructor_email,
            $component->class_hours,
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
