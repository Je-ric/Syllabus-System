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
    // $revisions lives here so wire:model.lazy="revisions.N.field" works.
    // All mutations (add/remove/save) are local — no $parent calls needed.
    public array $revisions = [];

    // ── Approval signature slots ───────────────────────────────────────────
    // approved_by  → Dean      (exactly 1, nullable)   — only users with 'dean' role
    // concurred_by → Dean/Chair (exactly 1, nullable)   — only users with 'dean' role
    // selectedReviewerId → staging for "Add Reviewer"  — users with 'faculty' role
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
        // Only users with 'dean' role appear in approved_by / concurred_by selects
        $deanUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'dean'))
            ->orderBy('name')
            ->get();

        // All faculty appear in the "Reviewed By" additional reviewer select
        $facultyUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'faculty'))
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

        $concurredUser = $this->concurredBy ? User::find($this->concurredBy) : null;
        $approvedUser  = $this->approvedBy  ? User::find($this->approvedBy)  : null;

        return view('livewire.syllabus.steps.review', [
            'course'        => $this->course,
            'reviewers'     => $reviewers,
            'deanUsers'     => $deanUsers,
            'facultyUsers'  => $facultyUsers,
            'concurredUser' => $concurredUser,
            'approvedUser'  => $approvedUser,
        ]);
    }

    // ── Revision mutations ─────────────────────────────────────────────────

    public function addRevision(): void
    {
        $maxNo = (int) max(array_column($this->revisions, 'revision_no') ?: [0]);

        $this->revisions[] = [
            'id'                      => null,
            'revision_no'             => $maxNo + 1,
            'revision_date'           => now()->format('Y-m-d'),
            'implementation_semester' => '',
            'highlights'              => '',
            'contributors'            => '',
        ];
    }

    public function removeRevision(int $index): void
    {
        if (count($this->revisions) <= 1) {
            return;
        }

        $row = $this->revisions[$index] ?? null;

        if ($row && ! empty($row['id'])) {
            app(SyllabusRevisionHistoryService::class)->delete($this->syllabus, (int) $row['id']);
        }

        array_splice($this->revisions, $index, 1);
    }

    public function saveRevisions(): void
    {
        if (! $this->syllabus) {
            $this->dispatch('lw-toast', type: 'error', message: 'No syllabus loaded.');
            return;
        }

        try {
            app(SyllabusRevisionHistoryService::class)->upsertMany($this->syllabus, $this->revisions);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('lw-toast', type: 'error', message: 'Unable to save revisions.');
            return;
        }

        $this->dispatch('lw-toast', type: 'success', message: 'Revisions saved.');
        $this->loadData(force: true);
    }

    // ── Approval signature mutations ───────────────────────────────────────

    public function saveConcurred(): void
    {
        if (! $this->syllabus) return;
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

        $this->isLoaded = true;
    }

    private function loadRevisions(): void
    {
        $mapped = $this->syllabus->revisions
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

        if (empty($mapped)) {
            $mapped = [[
                'id'                      => null,
                'revision_no'             => $this->syllabus->getCurrentRevisionNumber() + 1,
                'revision_date'           => now()->format('Y-m-d'),
                'implementation_semester' => '',
                'highlights'              => '',
                'contributors'            => '',
            ]];
        }

        $this->revisions = $mapped;
    }
}
