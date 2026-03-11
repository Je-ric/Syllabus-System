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
    // Single form for adding new revisions + saved revisions list
    public array $revisions = [];  // saved revisions from DB

    // Form fields for new revision entry
    public string $newRevisionDate = '';
    public int    $newRevisionNo = 1;
    public string $newImplementationSemester = '';
    public string $newHighlights = '';
    public string $newContributors = '';

    // ── Approval signature slots ────────────────────────────────────────────
    // concurred_by  → Department Chair (exactly 1)
    // approved_by   → Dean (exactly 1)
    // reviewedBy    → Additional reviewers (N, stored in syllabus_reviewers table)
    public ?int  $concurredBy         = null;  // maps to syllabus.concurred_by
    public ?int  $approvedBy          = null;  // maps to syllabus.approved_by
    public ?int  $selectedReviewerId  = null;  // staging field for "Add Reviewer"

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
            $this->loadData(force: true);
        }
    }

    // ── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
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

        $reviewerIds = collect($reviewers)
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $deans = User::whereHas('roles', fn ($q) =>
            $q->where('name', 'dean')
        )->orderBy('name')->get();

        $availableReviewerUsers = User::whereHas('roles', fn ($q) =>
            $q->whereIn('name', ['dean', 'chair'])
        )
            ->whereNotIn('id', $reviewerIds)
            ->orderBy('name')
            ->get();

        $concurredUser = $this->concurredBy
            ? User::find($this->concurredBy)
            : null;

        $approvedUser = $this->approvedBy
            ? User::find($this->approvedBy)
            : null;

        return view('livewire.syllabus.steps.review', [
            'course'        => $this->course,
            'reviewers'     => $reviewers,
            'deans'         => $deans,
            'availableReviewerUsers' => $availableReviewerUsers,
            'concurredUser' => $concurredUser,
            'approvedUser'  => $approvedUser,
        ]);
    }

    // ── Revision mutations (local + DB, no $parent needed) ────────────────

    public function addRevision(): void
    {
        $this->validate([
            'newRevisionDate' => 'required|date',
            'newImplementationSemester' => 'required|string|max:255',
            'newHighlights' => 'nullable|string',
            'newContributors' => 'nullable|string',
        ]);

        if (!$this->syllabus) {
            $this->dispatch('lw-toast', type: 'error', message: 'No syllabus loaded.');
            return;
        }

        try {
            $revisionData = [
                'id' => null,
                'revision_no' => $this->newRevisionNo,
                'revision_date' => $this->newRevisionDate,
                'implementation_semester' => $this->newImplementationSemester,
                'highlights' => $this->newHighlights,
                'contributors' => $this->newContributors,
            ];

            app(SyllabusRevisionHistoryService::class)->upsertMany($this->syllabus, [$revisionData]);

            $this->clearForm();
            $this->loadRevisions();
            $this->dispatch('lw-toast', type: 'success', message: 'Revision added successfully.');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to add revision.');
        }
    }

    public function removeRevision(int $revisionId): void
    {
        if (!$this->syllabus) return;

        try {
            app(SyllabusRevisionHistoryService::class)->delete($this->syllabus, $revisionId);
            $this->loadRevisions();
            $this->dispatch('lw-toast', type: 'success', message: 'Revision removed.');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to remove revision.');
        }
    }

    private function clearForm(): void
    {
        $this->newRevisionDate = now()->format('Y-m-d');
        $this->newRevisionNo = $this->getNextRevisionNumber();
        $this->newImplementationSemester = '';
        $this->newHighlights = '';
        $this->newContributors = '';
    }

    private function getNextRevisionNumber(): int
    {
        if (empty($this->revisions)) {
            return 1;
        }

        $maxNo = (int) max(array_column($this->revisions, 'revision_no') ?: [0]);
        return $maxNo + 1;
    }

    // ── Approval signature mutations ───────────────────────────────────────

    public function saveConcurred(): void
    {
        if (! $this->syllabus) return;

        if ($this->concurredBy) {
            $isDean = User::where('id', $this->concurredBy)
                ->whereHas('roles', fn ($q) => $q->where('name', 'dean'))
                ->exists();

            if (! $isDean) {
                $this->dispatch('lw-toast', type: 'error', message: 'Concurred-by must be a dean.');
                return;
            }
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

    public function saveApproved(): void
    {
        if (! $this->syllabus) return;

        if ($this->approvedBy) {
            $isDean = User::where('id', $this->approvedBy)
                ->whereHas('roles', fn ($q) => $q->where('name', 'dean'))
                ->exists();

            if (! $isDean) {
                $this->dispatch('lw-toast', type: 'error', message: 'Approved-by must be a dean.');
                return;
            }
        }

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

    // ── Private helpers ────────────────────────────────────────────────────

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

        $this->concurredBy = $this->syllabus->concurred_by
            ? (int) $this->syllabus->concurred_by
            : null;

        $this->approvedBy = $this->syllabus->approved_by
            ? (int) $this->syllabus->approved_by
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
            ])
            ->values()
            ->all();

        $this->syllabusWeeks = $this->syllabus->weeks->sortBy('week_no')->values();

        $examWeeks = [];
        foreach ($this->syllabusWeeks as $week) {
            if ($week->exam_type) {
                $examWeeks[$week->exam_type] = $week->week_no;
            }
        }
        $this->examWeeks = $examWeeks;

        $this->completeVersions = CompleteSyllabus::query()
            ->whereHas('syllabus', function ($query) {
                $query->where('course_id', $this->course->id)
                    ->where('prepared_by', Auth::id());
            })
            ->orderByDesc('version')
            ->orderByDesc('created_at')
            ->get();

        $this->latestComplete = $this->completeVersions->first();

        $this->loadRevisions();
        $this->clearForm();

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
            ])
            ->values()
            ->all();
    }
}
