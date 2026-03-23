<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\AcademicCalendar;
use App\Models\AuditLog;
use App\Models\CompleteSyllabus;
use App\Models\Syllabus;
use App\Models\SyllabusRevision;
use App\Models\User;
use App\Services\Syllabus\SyllabusApprovalService;
use App\Services\Syllabus\SyllabusRevisionHistoryService;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class ReviewStep extends Component
{
    // ── Identity ───────────────────────────────────────────────────────────
    public int $syllabusId;

    // ── Loaded syllabus state ──────────────────────────────────────────────
    public bool              $isLoaded             = false;
    public ?Syllabus         $syllabus             = null;
    public                   $course;
    public ?int              $academic_calendar_id = null;
    public                   $academicCalendars;
    public array             $courseOutcomes       = [];
    public array             $examWeeks            = [];
    public                   $syllabusWeeks;
    public ?CompleteSyllabus $latestComplete       = null;
    public                   $completeVersions;

    // ── Revision history ───────────────────────────────────────────────────
    // Pure-Alpine form — no wire:model on any draft field.
    // saveRevision() receives all values as typed method arguments.
    // removeRevision() dispatches 'revision-deleted' so Alpine clears its flag.
    // resequenceRevisions() renumbers all rows 0,1,2,… by current order.
    public array $revisions = [];

    // ── Approval ───────────────────────────────────────────────────────────
    public ?int $approvedBy         = null;
    public ?int $concurredBy        = null;
    public ?int $selectedReviewerId = null;

    // ══════════════════════════════════════════════════════════════════════
    // LIFECYCLE
    // ══════════════════════════════════════════════════════════════════════

    public function mount(int $syllabusId): void
    {
        $this->syllabusId        = $syllabusId;
        $this->academicCalendars = collect();
        $this->syllabusWeeks     = collect();
        $this->completeVersions  = collect();
        $this->loadData();
    }

    // ══════════════════════════════════════════════════════════════════════
    // EVENT LISTENERS
    // ══════════════════════════════════════════════════════════════════════

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'review') {
            $this->loadData(force: true);
        }
    }

    #[On('syllabus-step-saved')]
    public function onAnyStepSaved(): void
    {
        if ($this->isLoaded) {
            $this->loadData(force: true);
        }
    }

    /**
     * Fired by SyllabusWizard after addReviewer/removeReviewer mutations.
     * Refreshes the Eloquent relation — accordion stays open.
     */
    #[On('syllabus-reviewers-updated')]
    public function onReviewersUpdated(): void
    {
        if ($this->isLoaded) {
            $this->syllabus?->refresh();
        }
    }

    #[On('syllabus-revisions-updated')]
    public function onRevisionsUpdated(): void
    {
        if ($this->isLoaded) {
            $this->syllabus->load('revisions');
            $this->loadRevisions();
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // RENDER
    // ══════════════════════════════════════════════════════════════════════

    public function render()
    {
        $deanUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'dean'))
            ->orderBy('name')
            ->get();

        $alreadyAdded = $this->syllabus
            ? $this->syllabus->reviewers()->pluck('user_id')->map(fn ($id) => (int) $id)->all()
            : [];

        $facultyUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'faculty'))
            ->whereNotIn('id', $alreadyAdded)
            ->orderBy('name')
            ->get();

        $reviewers = $this->syllabus
            ? $this->syllabus->reviewers()->with('user')->orderBy('created_at')->get()
                ->map(fn ($r) => [
                    'id'         => $r->id,
                    'user_id'    => $r->user_id,
                    'user_name'  => $r->user->name,
                    'user_email' => $r->user->email,
                    'status'     => $r->status,
                ])->values()->all()
            : [];

        return view('livewire.syllabus.steps.review', [
            'course'       => $this->course,
            'reviewers'    => $reviewers,
            'deanUsers'    => $deanUsers,
            'facultyUsers' => $facultyUsers,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════
    // REVISION HISTORY
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Called from Alpine with all form values as typed arguments.
     * Zero wire:model round-trips while typing — single network call on submit.
     * Dispatches 'revision-saved' so Alpine can reset the form.
     */
    public function saveRevision(
        ?int   $editingId,
        int    $revisionNo,
        string $date,
        string $semester,
        string $highlights   = '',
        string $contributors = ''
    ): void {
        if (! $this->syllabus) {
            $this->dispatch('lw-toast', type: 'error', message: 'No syllabus loaded.');
            return;
        }

        $semester = trim($semester);
        if (! $semester) {
            $this->dispatch('lw-toast', type: 'error', message: 'Implementation Semester is required.');
            return;
        }
        if (! $date) {
            $this->dispatch('lw-toast', type: 'error', message: 'Date is required.');
            return;
        }
        if ($revisionNo < 0) {
            $this->dispatch('lw-toast', type: 'error', message: 'Revision No. must be 0 or higher.');
            return;
        }

        try {
            app(SyllabusRevisionHistoryService::class)->upsertMany($this->syllabus, [[
                'id'                      => $editingId,
                'revision_no'             => $revisionNo,
                'revision_date'           => $date,
                'implementation_semester' => $semester,
                'highlights'              => $highlights,
                'contributors'            => $contributors,
            ]]);
        } catch (\InvalidArgumentException $e) {
            $this->dispatch('lw-toast', type: 'error', message: $e->getMessage());
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to save revision.');
            return;
        }

        $isEdit = $editingId !== null;
        $this->syllabus->load('revisions');
        $this->loadRevisions();

        AuditLog::record(
            action: $isEdit ? 'updated' : 'created',
            module: 'Syllabus Revision',
            referenceId: $this->syllabus->id,
            description: ($isEdit ? 'Updated' : 'Added') . " revision #{$revisionNo} on syllabus #{$this->syllabus->id}."
        );

        // 'revision-saved' tells Alpine to reset the form.
        $this->dispatch('revision-saved');
        $this->dispatch('lw-toast', type: 'success',
            message: $isEdit ? 'Revision updated.' : 'Revision added.');
    }

    /**
     * Renumber all revisions 0, 1, 2, … by their current revision_no order.
     * Dispatches 'revisions-resequenced' so Alpine knows to reload display.
     */
    public function resequenceRevisions(): void
    {
        if (! $this->syllabus) {
            return;
        }

        try {
            app(SyllabusRevisionHistoryService::class)->resequence($this->syllabus);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to resequence revisions.');
            return;
        }

        $this->syllabus->load('revisions');
        $this->loadRevisions();
        $this->dispatch('lw-toast', type: 'success', message: 'Revisions renumbered 0, 1, 2, …');
    }

    /**
     * Delete revision by DB id.
     * Dispatches 'revision-deleted' so Alpine clears its deletingId flag.
     */
    public function removeRevision(int $revisionId): void
    {
        if (! $this->syllabus) {
            return;
        }

        try {
            app(SyllabusRevisionHistoryService::class)->delete($this->syllabus, $revisionId);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to remove revision.');
            $this->dispatch('revision-delete-failed', id: $revisionId);
            return;
        }

        $this->syllabus->load('revisions');
        $this->loadRevisions();

        AuditLog::record(
            action: 'deleted',
            module: 'Syllabus Revision',
            referenceId: $this->syllabus->id,
            description: "Removed revision #{$revisionId} from syllabus #{$this->syllabus->id}."
        );

        $this->dispatch('revision-deleted', id: $revisionId);
        $this->dispatch('lw-toast', type: 'success', message: 'Revision removed.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // APPROVAL SIGNATURES
    // ══════════════════════════════════════════════════════════════════════

    public function saveApproved(): void
    {
        if (! $this->syllabus) {
            return;
        }

        try {
            app(SyllabusApprovalService::class)->setApprovedBy($this->syllabus, $this->approvedBy);
            $this->dispatch('lw-toast', type: 'success', message: 'Approved-by saved.');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to save Approved-by.');
        }
    }

    public function clearApproved(): void
    {
        if (! $this->syllabus) {
            return;
        }

        app(SyllabusApprovalService::class)->clearApprovedBy($this->syllabus);
        $this->approvedBy = null;
        $this->dispatch('lw-toast', type: 'success', message: 'Approved-by cleared.');
    }

    public function saveConcurred(): void
    {
        if (! $this->syllabus) {
            return;
        }

        try {
            app(SyllabusApprovalService::class)->setConcurredBy($this->syllabus, $this->concurredBy);
            $this->dispatch('lw-toast', type: 'success', message: 'Concurred-by saved.');
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first()
                ?? 'Concurred-by must differ from Approved-by.';
            $this->dispatch('lw-toast', type: 'error', message: $message);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to save Concurred-by.');
        }
    }

    public function clearConcurred(): void
    {
        if (! $this->syllabus) {
            return;
        }

        app(SyllabusApprovalService::class)->clearConcurredBy($this->syllabus);
        $this->concurredBy = null;
        $this->dispatch('lw-toast', type: 'success', message: 'Concurred-by cleared.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════════

    private function loadData(bool $force = false): void
    {
        if ($this->isLoaded && ! $force) {
            return;
        }

        $this->syllabus = Syllabus::query()->with([
            'course.program.outcomes',
            'course.programOutcomes',
            'components',
            'weeks',
            'academicCalendar',
            'revisions',
        ])->findOrFail($this->syllabusId);

        $this->course               = $this->syllabus->course;
        $this->academic_calendar_id = $this->syllabus->academic_calendar_id
            ? (int) $this->syllabus->academic_calendar_id
            : null;

        $this->approvedBy  = $this->syllabus->approved_by
            ? (int) $this->syllabus->approved_by
            : null;
        $this->concurredBy = $this->syllabus->concurred_by
            ? (int) $this->syllabus->concurred_by
            : null;

        $this->academicCalendars = AcademicCalendar::query()
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $this->courseOutcomes = $this->syllabus->courseOutcomes
            ->map(fn ($co) => [
                'id'          => $co->id,
                'co_code'     => $co->co_code,
                'description' => $co->description,
            ])->values()->all();

        $this->syllabusWeeks = $this->syllabus->weeks->sortBy('week_no')->values();

        $examWeeks = [];
        foreach ($this->syllabusWeeks as $week) {
            if ($week->exam_type) {
                $examWeeks[$week->exam_type] = $week->week_no;
            }
        }
        $this->examWeeks = $examWeeks;

        $this->completeVersions = CompleteSyllabus::query()
            ->whereHas('syllabus', fn ($q) =>
                $q->where('course_id', $this->course->id)
                  ->where('prepared_by', Auth::id())
            )
            ->orderByDesc('version')
            ->orderByDesc('created_at')
            ->get();

        $this->latestComplete = $this->completeVersions->first();

        $this->loadRevisions();
        $this->isLoaded = true;
    }

    private function loadRevisions(): void
    {
        $this->revisions = $this->syllabus->revisions
            ->sortBy('revision_no')
            ->map(function (SyllabusRevision $rev): array {
                $revisionDate = $rev->revision_date;

                return [
                    'id'                      => $rev->id,
                    'revision_no'             => $rev->revision_no,
                    'revision_date'           => $revisionDate instanceof CarbonInterface
                        ? $revisionDate->toDateString()
                        : (string) ($revisionDate ?? ''),
                    'implementation_semester' => $rev->implementation_semester,
                    'highlights'              => $rev->highlights ?? '',
                    'contributors'            => $rev->contributors ?? '',
                ];
            })->values()->all();
    }
}
