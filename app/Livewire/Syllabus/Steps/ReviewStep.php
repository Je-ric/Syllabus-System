<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\AcademicCalendar;
use App\Models\CompleteSyllabus;
use App\Models\Syllabus;
use App\Models\User;
use App\Models\SyllabusRevision;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ReviewStep extends Component
{
    public int       $syllabusId;
    public bool      $isLoaded             = false;
    public ?Syllabus $syllabus             = null;
    public           $academicCalendars;
    public ?int      $academic_calendar_id = null;
    public array     $courseOutcomes       = [];
    public array     $examWeeks            = [];
    public           $syllabusWeeks;
    public           $course;
    public ?CompleteSyllabus $latestComplete = null;
    public           $completeVersions;
    public array     $reviewers = [];
    public           $allUsers;

    // ── Revision history (local array — wire:model binds here) ────────────
    //
    // $revisions lives on ReviewStep so that wire:model="revisions.N.field"
    // works within this component's blade.
    //
    // addRevision() / removeRevision() only mutate this local array (no DB).
    //
    // The "Save Revisions" button calls $parent.saveRevisions($wire.revisions)
    // which passes the full array to SyllabusWizard::saveRevisions() for
    // persistence via SyllabusRevisionHistoryService.
    //
    // When a persisted row (id != null) is removed, we dispatch
    // 'wizard-delete-revision' so SyllabusWizard can hard-delete it from the DB.

    public array $revisions = [];

    // selectedReviewerId lives here so $wire.selectedReviewerId works in the
    // blade when calling $parent.addReviewer($wire.selectedReviewerId).
    public ?int $selectedReviewerId = null;

    // ── Mount ──────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId        = $syllabusId;
        $this->academicCalendars = collect();
        $this->syllabusWeeks     = collect();
        $this->completeVersions  = collect();
        $this->allUsers          = collect();
        $this->loadData();
    }

    // ── Livewire event listeners ───────────────────────────────────────────

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
            $this->loadReviewers();
        }
    }

    #[On('syllabus-revisions-updated')]
    public function onRevisionsUpdated(): void
    {
        if ($this->isLoaded) {
            $this->loadData(force: true);
        }
    }

    #[On('review-step-set-revisions')]
    public function onSetRevisions(array $revisions): void
    {
        $this->revisions = $revisions;
    }

    // ── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.syllabus.steps.review', [
            'course' => $this->course,
        ]);
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
        $this->loadReviewers();
        $this->loadAssignableUsers();

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

    private function loadReviewers(): void
    {
        $this->reviewers = $this->syllabus
            ? $this->syllabus->reviewers()->with('user')->orderBy('created_at')->get()
                ->map(fn ($r) => [
                    'id' => $r->id,
                    'user_id' => $r->user_id,
                    'user_name' => $r->user->name,
                    'user_email' => $r->user->email,
                    'status' => $r->status,
                ])->values()->all()
            : [];
    }

    private function loadAssignableUsers(): void
    {
        $this->allUsers = User::whereHas('roles', fn ($q) =>
            $q->whereIn('name', ['dean', 'chair'])
        )->orderBy('name')->get();
    }
}
