<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\AcademicCalendar;
use App\Models\CompleteSyllabus;
use App\Models\Syllabus;
use App\Models\User;
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

    public array     $revisions = [];
    public array     $reviewers = [];
    public           $allUsers;
    public ?int      $selectedReviewerId = null;

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

    // Reload when the wizard navigates to this step
    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'review') {
            $this->loadData(force: true);
        }
    }

    // Reload when any other step saves (so summary stays fresh)
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
            $this->selectedReviewerId = null;
            $this->loadData(force: true);
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
        return view('livewire.syllabus.steps.review', [
            'course' => $this->course,
        ]);
    }

    // NOTE: There is NO saveAsDone() here.
    //
    // The "Save as Done" button in review.blade.php dispatches the browser event
    // 'wizard-save-as-done', which is caught by SyllabusWizard via #[On('wizard-save-as-done')].
    // SyllabusWizard::saveAsDone() freezes an immutable HTML snapshot, saves it to disk, creates the
    // CompleteSyllabus version record, and then dispatches 'syllabus-step-changed' so
    // this component reloads and shows the updated "Latest Saved Version" card.
    //
    // This design avoids duplicating the save logic and ensures wire:loading on the
    // button works correctly (it targets the parent wizard, not this child).

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

        $this->isLoaded = true;
    }

    private function loadRevisions(): void
    {
        $this->revisions = $this->syllabus->revisions
            ->map(fn ($rev) => [
                'id' => $rev->id,
                'revision_no' => $rev->revision_no,
                'revision_date' => $rev->revision_date->format('Y-m-d'),
                'implementation_semester' => $rev->implementation_semester,
                'highlights' => $rev->highlights,
                'contributors' => $rev->contributors,
            ])
            ->values()
            ->all();

        if (empty($this->revisions)) {
            $this->revisions = [[
                'id' => null,
                'revision_no' => $this->syllabus->getCurrentRevisionNumber() + 1,
                'revision_date' => now()->format('Y-m-d'),
                'implementation_semester' => '',
                'highlights' => '',
                'contributors' => '',
            ]];
        }
    }

    private function loadReviewers(): void
    {
        $this->reviewers = $this->syllabus->reviewers()->with('user')->get()
            ->map(fn ($reviewer) => [
                'id' => $reviewer->id,
                'user_id' => $reviewer->user_id,
                'user_name' => $reviewer->user->name,
                'user_email' => $reviewer->user->email,
                'status' => $reviewer->status,
            ])
            ->values()
            ->all();

        $this->allUsers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['dean', 'chair']);
        })->orderBy('name')->get();
    }

    public function addRevision(): void
    {
        $this->revisions[] = [
            'id' => null,
            'revision_no' => $this->syllabus->getCurrentRevisionNumber() + count($this->revisions) + 1,
            'revision_date' => now()->format('Y-m-d'),
            'implementation_semester' => '',
            'highlights' => '',
            'contributors' => '',
        ];
    }

    public function removeRevision(int $index): void
    {
        if (count($this->revisions) <= 1) {
            return;
        }

        $revision = $this->revisions[$index];
        if (isset($revision['id']) && $revision['id']) {
            $this->dispatch('wizard-delete-revision', revisionId: (int) $revision['id']);
        }

        array_splice($this->revisions, $index, 1);
    }

    public function updatedSyllabus($value, $key): void
    {
        if (in_array($key, ['concurred_by', 'approved_by'])) {
            $this->syllabus->update([$key => $value]);
            $this->dispatch('lw-toast', type: 'success', message: 'Approval signatures updated.');
        }
    }
}
