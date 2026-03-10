<?php

namespace App\Livewire\Syllabus;

use App\Http\Controllers\SyllabusController;
use App\Models\CompleteSyllabus;
use App\Models\CourseComponent;
use App\Models\CourseOutcome;
use App\Models\Course;
use App\Models\Syllabus;
use App\Models\SyllabusWeek;
use App\Models\WeekContent;
use App\Models\SyllabusEvaluationItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

class SyllabusWizard extends Component
{
    public ?Syllabus $syllabus    = null;
    public ?Course   $course      = null;
    public string    $currentStep = 'academic_calendar';
    public array     $stepDirty   = [];

    // ── Mount ─────────────────────────────────────────────────────────────────

    // Sets up the wizard for editing an existing syllabus or creating a new one.
    // Aborts with 404 if neither a valid syllabusId nor courseId is provided.
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

    // Child steps fire this after saving so the wizard can:
    //   - clear the dirty flag for that step
    //   - show a toast if a message was provided
    //   - refresh the syllabus model (e.g. after the calendar is linked)
    //
    // Navigation is NOT gated on this event — saveAndNavigate() already changed
    // $currentStep in the same request as the dispatch, so the UI has already
    // updated by the time this event arrives.
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

    // ── Navigation (ONE round trip) ───────────────────────────────────────────

    // How this stays fast:
    //
    // Old flow (2 round trips, 3-5 s):
    //   1. clickTab() dispatches 'syllabus-save-step'.
    //      Child saves, dispatches 'syllabus-step-saved' back.
    //   2. onStepSaved() changes $currentStep → re-renders wizard.
    //      The child had :key="currentStep" so it was DESTROYED and REMOUNTED —
    //      cold-boot DB queries, full render.
    //
    // New flow (1 round trip, ~200-400 ms):
    //   1. saveAndNavigate() dispatches 'syllabus-save-step' (fire-and-forget to child)
    //      AND immediately sets $currentStep — same request.
    //   2. Wizard re-renders with the new $currentStep.
    //      The blade uses block/hidden on wrapper divs — no :key, no remount.
    //      Child's onSaveRequested() runs in the same request batch, writing to
    //      DB before the response is sent.
    private function saveAndNavigate(string $toStep): void
    {
        if (! array_key_exists($toStep, $this->syllabus->getWizardSteps())) {
            return;
        }
        if ($toStep === $this->currentStep) {
            return;
        }

        // Tell the current child to save (fire-and-forget within this request)
        $this->dispatch('syllabus-save-step', step: $this->currentStep);

        // Switch step immediately — same round trip
        $this->currentStep = $toStep;
        $this->syllabus->update(['current_step' => $toStep]);

        // Notify the new step it is now active
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
    //
    // THE only saveAsDone() in the codebase. ReviewStep has NONE.
    //
    // Called via wire:click="saveAsDone" on the button in review.blade.php.
    // Because review.blade.php is a child Livewire component (ReviewStep),
    // the button there uses $dispatch('wizard-save-as-done') and this method
    // listens via #[On('wizard-save-as-done')].
    //
    // Versioning: every call creates a new CompleteSyllabus row (v1, v2, …).
    // Old versions are preserved — they remain editable until a chair approves.
    // Status is set to 'under_review'; only a department chair sets 'approved'.
    //
    #[On('wizard-save-as-done')]
    public function saveAsDone(): void
    {
        if (! $this->syllabus) {
            $this->dispatch('lw-toast', type: 'error', message: 'No syllabus loaded.');
            return;
        }

        // Re-load with relations needed by the PDF builder and folder hierarchy
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

        // 1. Generate PDF ──────────────────────────────────────────────────
        try {
            $html = app(SyllabusController::class)->generateCompleteHtmlSnapshot($syllabus);
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Save-as-done failed: ' . $e->getMessage());
            return;
        }

        // 2. Build hierarchical folder path ────────────────────────────────
        // Organize snapshots by: College → Department → Program → Faculty
        $program    = $syllabus->course?->program;
        $department = $program?->departments?->first(); // Primary department
        $college    = $department?->college;
        $faculty    = $syllabus->preparer;

        $collegeName    = Str::slug($college?->name ?? 'unknown-college');
        $departmentName = Str::slug($department?->name ?? 'unknown-department');
        $programName    = Str::slug($program?->program_name ?? $program?->name ?? 'unknown-program');
        $facultyName    = Str::slug($faculty?->name ?? 'user-' . ($syllabus->prepared_by ?? 'unknown'));

        $version      = (int) (CompleteSyllabus::where('syllabus_id', $syllabus->id)->max('version') ?? 0) + 1;
        $academicYear = $syllabus->academicCalendar?->academic_year ?? 'N-A';
        $semester     = $syllabus->academicCalendar?->semester      ?? 'N-A';
        $courseCode   = $syllabus->course?->course_code             ?? 'COURSE';

        // Stored in storage/app/private/syllabus-snapshots/{college}/{dept}/{program}/{faculty}/{file}.html
        // Accessible via controller route (SyllabusController::previewSavedComplete)
        $fileName    = Str::slug($courseCode . '-' . $academicYear . '-' . $semester . '-v' . $version) . '.html';
        $storagePath = implode('/', [
            'syllabus-snapshots',
            $collegeName,
            $departmentName,
            $programName,
            $facultyName,
            $fileName,
        ]);

        // 3. Write to public disk ─────────────────────────────────────────
        try {
            $ok = Storage::disk('local')->put($storagePath, $html);

            if (! $ok) {
                $this->dispatch('lw-toast', type: 'error', message: 'Snapshot write failed — storage returned false.');
                return;
            }

        } catch (Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Disk write error: ' . $e->getMessage());
            return;
        }

        // 4. Persist version record ───────────────────────────────────────
        try {
            CompleteSyllabus::create([
                'syllabus_id'   => $syllabus->id,
                'course_id'     => $syllabus->course_id,
                'academic_year' => $academicYear,
                'semester'      => $semester,
                'pdf_path'      => $storagePath,     // local path (served via controller route)
                'version'       => $version,
                'approved_at'   => null,
                'approved_by'   => null,
                'checksum'      => hash('sha256', $html),
            ]);
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'DB record error: ' . $e->getMessage());
            return;
        }

        // 5. Update syllabus status ────────────────────────────────────────
        // 'under_review' = submitted for review. 'approved' is set by the chair only.
        try {
            $syllabus->forceFill([
                'status'       => 'under_review',
                'current_step' => 'review',
            ])->save();
        } catch (Throwable $e) {
            report($e);
            // Non-fatal — version snapshot is saved; just warn
        }

        $this->syllabus->refresh();

        $this->dispatch('lw-toast', type: 'success', message: "Syllabus version frozen (v{$version}).");

        // Reset the Alpine spinner in review.blade.php
        $this->dispatch('wizard-save-done');

        // Tell ReviewStep to reload so the "Latest Saved Version" card updates
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

    // Check whether a wizard step is missing required data.
    // Used by submitForReview() to block incomplete submissions.
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
                // Find every WeekContent row that should have a weight entered.
                //
                // Qualifying rows must have a non-empty assessment_task AND must NOT be
                // "Non-Teaching Week" (those are locked placeholder rows, not assessable).
                // We do not rely on syllabus_weeks.exam_type because that column is
                // not used by the new evaluation flow — we read assessment_task directly.
                $weekContentIds = WeekContent::query()
                    ->join('syllabus_weeks', 'syllabus_weeks.id', '=', 'week_contents.syllabus_week_id')
                    ->where('syllabus_weeks.syllabus_id', $syllabusId)
                    ->whereRaw("TRIM(COALESCE(week_contents.assessment_task, '')) <> ''")
                    ->whereRaw("TRIM(week_contents.assessment_task) <> 'Non-Teaching Week'")
                    ->pluck('week_contents.id');

                if ($weekContentIds->isEmpty()) {
                    // No tasks exist yet — treat as incomplete
                    return true;
                }

                // All qualifying rows must have a non-null weight saved
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
