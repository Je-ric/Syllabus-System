<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\AcademicCalendar;
use App\Models\CompleteSyllabus;
use App\Models\Syllabus;
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

    // ── Mount ──────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId        = $syllabusId;
        $this->academicCalendars = collect();
        $this->syllabusWeeks     = collect();
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

        $this->latestComplete = CompleteSyllabus::where('syllabus_id', $this->syllabusId)
            ->orderByDesc('version')
            ->first();

        $this->isLoaded = true;
    }
}
