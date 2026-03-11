<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\AcademicCalendar;
use App\Models\CompleteSyllabus;
use App\Models\Syllabus;
use App\Models\SyllabusRevision;
use App\Models\User;
use App\Services\Syllabus\SyllabusRevisionHistoryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ReviewStep extends Component
{
    public int               $syllabusId;
    public bool              $isLoaded             = false;
    public ?Syllabus         $syllabus             = null;
    public                   $academicCalendars;
    public ?int              $academic_calendar_id = null;
    public array             $courseOutcomes       = [];
    public array             $examWeeks            = [];
    public                   $syllabusWeeks;
    public                   $course;
    public ?CompleteSyllabus $latestComplete       = null;
    public                   $completeVersions;

    // ── Revision history ───────────────────────────────────────────────────
    // $revisions = saved rows (shown in the right list, read-only).
    // The left side is a single add/edit form using these draft fields.
    // "Add" saves immediately to DB and refreshes the right list.
    // "Edit" loads a row into the draft form; "Update" saves in-place.
    public array  $revisions          = [];
    public ?int   $editingRevisionId  = null;   // null = new, int = editing
    public string $draftDate          = '';
    public int    $draftRevisionNo    = 0;       // starts from 0
    public string $draftSemester      = '';
    public string $draftHighlights    = '';
    public string $draftContributors  = '';

    // ── Approval slots ────────────────────────────────────────────────────
    // approved_by  → Dean (required when set, must differ from concurred_by)
    // concurred_by → Dean (nullable, must differ from approved_by)
    // selectedReviewerId → staging for "Add Reviewer" (faculty only)
    public ?int $concurredBy        = null;
    public ?int $approvedBy         = null;
    public ?int $selectedReviewerId = null;

    // ── Mount ──────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId        = $syllabusId;
        $this->academicCalendars = collect();
        $this->syllabusWeeks     = collect();
        $this->completeVersions  = collect();
        $this->loadData();
    }

    // ── Event listeners ───────────────────────────────────────────────────

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'review') $this->loadData(force: true);
    }

    #[On('syllabus-step-saved')]
    public function onAnyStepSaved(): void
    {
        if ($this->isLoaded) $this->loadData(force: true);
    }

    #[On('syllabus-reviewers-updated')]
    public function onReviewersUpdated(): void
    {
        // Just refresh the syllabus so render() picks up latest reviewers.
        // Accordion stays open — no full reload.
        if ($this->isLoaded) $this->syllabus?->refresh();
    }

    #[On('syllabus-revisions-updated')]
    public function onRevisionsUpdated(): void
    {
        if ($this->isLoaded) {
            $this->syllabus->load('revisions');
            $this->loadRevisions();
        }
    }

    // ── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
        // Approved/concurred: deans only
        $deanUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'dean'))
            ->orderBy('name')->get();

        // Additional reviewers: faculty only, exclude already-added ones
        $alreadyAdded = $this->syllabus
            ? $this->syllabus->reviewers()->pluck('user_id')->map(fn ($id) => (int) $id)->all()
            : [];

        $facultyUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'faculty'))
            ->whereNotIn('id', $alreadyAdded)
            ->orderBy('name')->get();

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

    // ── Revision mutations ─────────────────────────────────────────────────

    /**
     * Save draft form → DB, refresh right-side list.  No page reload.
     */
    public function saveRevision(): void
    {
        $this->validate([
            'draftDate'     => 'required|date',
            'draftSemester' => 'required|string|max:255',
            'draftHighlights'   => 'nullable|string',
            'draftContributors' => 'nullable|string',
        ]);

        if (! $this->syllabus) {
            $this->dispatch('lw-toast', type: 'error', message: 'No syllabus loaded.');
            return;
        }

        try {
            app(SyllabusRevisionHistoryService::class)->upsertMany($this->syllabus, [[
                'id'                      => $this->editingRevisionId,
                'revision_no'             => $this->draftRevisionNo,
                'revision_date'           => $this->draftDate,
                'implementation_semester' => $this->draftSemester,
                'highlights'              => $this->draftHighlights,
                'contributors'            => $this->draftContributors,
            ]]);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to save revision.');
            return;
        }

        $isEdit = $this->editingRevisionId !== null;
        $this->syllabus->load('revisions');
        $this->loadRevisions();
        $this->resetDraftForm();

        $this->dispatch('lw-toast', type: 'success',
            message: $isEdit ? 'Revision updated.' : 'Revision added.');
    }

    /**
     * Load a saved revision into the draft form for editing.
     */
    public function editRevision(int $revisionId): void
    {
        $rev = collect($this->revisions)->firstWhere('id', $revisionId);
        if (! $rev) return;

        $this->editingRevisionId = $revisionId;
        $this->draftRevisionNo   = (int) $rev['revision_no'];
        $this->draftDate         = $rev['revision_date'];
        $this->draftSemester     = $rev['implementation_semester'];
        $this->draftHighlights   = $rev['highlights'] ?? '';
        $this->draftContributors = $rev['contributors'] ?? '';
    }

    public function cancelEdit(): void
    {
        $this->resetDraftForm();
    }

    /**
     * Delete by DB id (not array index).
     */
    public function removeRevision(int $revisionId): void
    {
        if (! $this->syllabus) return;

        try {
            app(SyllabusRevisionHistoryService::class)->delete($this->syllabus, $revisionId);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to remove revision.');
            return;
        }

        $this->syllabus->load('revisions');
        $this->loadRevisions();
        $this->dispatch('lw-toast', type: 'success', message: 'Revision removed.');
    }

    // ── Approval mutations ────────────────────────────────────────────────

    public function saveApproved(): void
    {
        if (! $this->syllabus) return;
        $this->syllabus->update(['approved_by' => $this->approvedBy ?: null]);
        $this->dispatch('lw-toast', type: 'success', message: 'Approved-by saved.');
    }

    public function clearApproved(): void
    {
        if (! $this->syllabus) return;
        $this->approvedBy = null;
        $this->syllabus->update(['approved_by' => null]);
        $this->dispatch('lw-toast', type: 'success', message: 'Approved-by cleared.');
    }

    public function saveConcurred(): void
    {
        if (! $this->syllabus) return;

        if ($this->concurredBy && $this->concurredBy === $this->approvedBy) {
            $this->dispatch('lw-toast', type: 'error',
                message: 'Concurred-by must be a different dean from Approved-by.');
            return;
        }

        $this->syllabus->update(['concurred_by' => $this->concurredBy ?: null]);
        $this->dispatch('lw-toast', type: 'success', message: 'Concurred-by saved.');
    }

    public function clearConcurred(): void
    {
        if (! $this->syllabus) return;
        $this->concurredBy = null;
        $this->syllabus->update(['concurred_by' => null]);
        $this->dispatch('lw-toast', type: 'success', message: 'Concurred-by cleared.');
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function loadData(bool $force = false): void
    {
        if ($this->isLoaded && ! $force) return;

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
            ? (int) $this->syllabus->academic_calendar_id : null;

        $this->concurredBy = $this->syllabus->concurred_by
            ? (int) $this->syllabus->concurred_by : null;
        $this->approvedBy  = $this->syllabus->approved_by
            ? (int) $this->syllabus->approved_by  : null;

        $this->academicCalendars = AcademicCalendar::query()
            ->orderBy('academic_year', 'desc')->orderBy('semester', 'desc')->get();

        $this->courseOutcomes = $this->syllabus->courseOutcomes
            ->map(fn ($co) => [
                'id'          => $co->id,
                'co_code'     => $co->co_code,
                'description' => $co->description,
            ])->values()->all();

        $this->syllabusWeeks = $this->syllabus->weeks->sortBy('week_no')->values();

        $examWeeks = [];
        foreach ($this->syllabusWeeks as $week) {
            if ($week->exam_type) $examWeeks[$week->exam_type] = $week->week_no;
        }
        $this->examWeeks = $examWeeks;

        $this->completeVersions = CompleteSyllabus::query()
            ->whereHas('syllabus', fn ($q) =>
                $q->where('course_id', $this->course->id)->where('prepared_by', Auth::id())
            )
            ->orderByDesc('version')->orderByDesc('created_at')->get();

        $this->latestComplete = $this->completeVersions->first();

        $this->loadRevisions();
        $this->resetDraftForm();
        $this->isLoaded = true;
    }

    private function loadRevisions(): void
    {
        $this->revisions = $this->syllabus->revisions
            ->sortBy('revision_no')
            ->map(fn (SyllabusRevision $rev) => [
                'id'                      => $rev->id,
                'revision_no'             => $rev->revision_no,
                'revision_date'           => $rev->revision_date->format('Y-m-d'),
                'implementation_semester' => $rev->implementation_semester,
                'highlights'              => $rev->highlights ?? '',
                'contributors'            => $rev->contributors ?? '',
            ])->values()->all();
    }

    private function resetDraftForm(): void
    {
        $this->editingRevisionId = null;
        $this->draftDate         = now()->format('Y-m-d');
        // Next revision_no = count of existing rows (0-based)
        $this->draftRevisionNo   = count($this->revisions);
        $this->draftSemester     = '';
        $this->draftHighlights   = '';
        $this->draftContributors = '';
    }
}
