<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\AcademicCalendar;
use App\Models\Syllabus;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class AcademicCalendarStep extends Component
{
    public int    $syllabusId;
    public int    $stepNumber           = 1;
    public ?int   $academic_calendar_id = null;
    public array  $academicCalendars    = [];   // plain arrays, not Eloquent models
    public bool   $isLoaded             = false;

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

        // Only reload data if not already loaded (prevent unnecessary DB queries)
        if (! $this->isLoaded) {
            $this->loadData();
        }
    }

    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'academic_calendar') {
            return;
        }

        try {
            if ($this->saveAcademicCalendar()) { // if save is successful, dispatch the saved event
                // dispatch means
                $this->dispatch('syllabus-step-saved', step: 'academic_calendar');
            }
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('syllabus-step-save-failed', step: 'academic_calendar', error: $e->getMessage());
        }
    }

    public function updatedAcademicCalendarId(): void
    {
        if (! $this->isLoaded) {
            return;
        }

        if ($this->saveAcademicCalendar()) {
            $this->dispatch('syllabus-step-saved', step: 'academic_calendar');
            $this->dispatch('syllabus-calendar-updated');
        }
    }

    public function render()
    {
        return view('livewire.syllabus.wizard.steps.academic-calendar');
    }

    private function loadData(): void
    {
        if ($this->isLoaded) {
            return;
        }

        $syllabus = Syllabus::query()->findOrFail($this->syllabusId);

        $activeId = AcademicCalendar::active()->value('id');

        if ($syllabus->academic_calendar_id) {
            $selectedId = (int) $syllabus->academic_calendar_id;
            if ($activeId && $selectedId !== $activeId) {
                // Previously selected calendar is no longer active — auto-switch
                $syllabus->update(['academic_calendar_id' => $activeId]);
                $this->dispatch('syllabus-calendar-updated');
                $this->dispatch('lw-toast', type: 'warning', message: 'Switched to the currently active academic calendar.');
            }
            $this->academic_calendar_id = $activeId ?: $selectedId;
        } else {
            $this->academic_calendar_id = $activeId;
            if ($activeId) {
                $syllabus->update(['academic_calendar_id' => $activeId]);
                $this->dispatch('syllabus-calendar-updated');
            }
        }

        // Store as plain arrays — Livewire serialises these cheaply.
        $this->academicCalendars = AcademicCalendar::query()
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get()
            ->map(fn ($c) => [
                'id'               => $c->id,
                'academic_year'    => $c->academic_year,
                'formatted_semester' => $c->getFormattedSemester(),
                'is_active'        => $c->is_active,
            ])
            ->all();

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
                ],
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('lw-toast', type: 'error', message: $e->validator->errors()->first());
            return false;
        }

        $syllabus->update(['academic_calendar_id' => $this->academic_calendar_id]);

        return true;
    }
}
