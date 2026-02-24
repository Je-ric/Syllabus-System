<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\AcademicCalendar;
use App\Models\Syllabus;
use Livewire\Attributes\On;
use Livewire\Component;

class ReviewStep extends Component
{
    public int $syllabusId;
    public bool $isLoaded = false;
    public ?Syllabus $syllabus = null;
    public $academicCalendars;
    public ?int $academic_calendar_id = null;
    public array $courseOutcomes = [];
    public array $examWeeks = [];
    public $syllabusWeeks;
    public $course;

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->academicCalendars = collect();
        $this->syllabusWeeks = collect();
        $this->loadData();
    }

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step !== 'review') {
            return;
        }

        $this->loadData(force: true);
    }

    #[On('syllabus-step-saved')]
    public function onAnyStepSaved(): void
    {
        if (!$this->isLoaded) {
            return;
        }

        $this->loadData(force: true);
    }

    public function render()
    {
        return view('livewire.syllabus.steps.review', [
            'course' => $this->course,
        ]);
    }

    private function loadData(bool $force = false): void
    {
        if ($this->isLoaded && !$force) {
            return;
        }

        $this->syllabus = Syllabus::query()->with([
            'course.program.outcomes',
            'course.programOutcomes',
            'components',
            'weeks',
        ])->findOrFail($this->syllabusId);

        $this->course = $this->syllabus->course;
        $this->academic_calendar_id = $this->syllabus->academic_calendar_id ? (int) $this->syllabus->academic_calendar_id : null;
        $this->academicCalendars = AcademicCalendar::query()
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $this->courseOutcomes = $this->syllabus->courseOutcomes->map(fn($co) => [
            'id' => $co->id,
            'co_code' => $co->co_code,
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

        $this->isLoaded = true;
    }
}
