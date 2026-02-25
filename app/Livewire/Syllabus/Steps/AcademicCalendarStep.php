<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\AcademicCalendar;
use App\Models\Syllabus;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class AcademicCalendarStep extends Component
{
    public int $syllabusId;
    public ?int $academic_calendar_id = null;
    public $academicCalendars = [];
    public bool $isLoaded = false;

    // public function mount(int $syllabusId, bool $isActive = false): void
    // {
    //     $this->syllabusId = $syllabusId;

    //     if ($isActive) {
    //         $this->loadData();
    //     }
    // }

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }


    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step !== 'academic_calendar') {
            return;
        }

        $this->loadData();
    }

    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'academic_calendar') {
            return;
        }

        if ($this->saveAcademicCalendar()) { // if save is successful, dispatch the saved event
            // dispatch means
            $this->dispatch('syllabus-step-saved', step: 'academic_calendar');
        }
    }

    public function updatedAcademicCalendarId(): void
    {
        if (!$this->isLoaded) {
            return;
        }

        if ($this->saveAcademicCalendar()) {
            $this->dispatch('syllabus-step-saved', step: 'academic_calendar');
            $this->dispatch('syllabus-calendar-updated');
        }
    }

    public function render()
    {
        return view('livewire.syllabus.steps.academic-calendar');
    }

    private function loadData(): void
    {
        if ($this->isLoaded) {
            return;
        }

        $syllabus = Syllabus::query()->findOrFail($this->syllabusId);
        $this->academic_calendar_id = $syllabus->academic_calendar_id ? (int) $syllabus->academic_calendar_id : null;

        $this->academicCalendars = AcademicCalendar::query()
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $this->isLoaded = true;
    }

    private function saveAcademicCalendar(): bool
    {
        $syllabus = Syllabus::query()->findOrFail($this->syllabusId);

        try {
            $this->validate([
                'academic_calendar_id' => [
                    'required',
                    'exists:academic_calendars,id',
                    Rule::unique('syllabi', 'academic_calendar_id')
                        ->where(fn($query) => $query->where('course_id', $syllabus->course_id))
                        ->ignore($syllabus->id),
                ],
            ]);
        } catch (ValidationException $exception) {
            $this->dispatch('lw-toast', type: 'error', message: $exception->validator->errors()->first());
            return false;
        }

        $syllabus->update([
            'academic_calendar_id' => $this->academic_calendar_id,
        ]);

        return true;
    }
}
