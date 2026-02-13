<?php

namespace App\Livewire\Syllabus\Concerns;

trait HandlesAcademicCalendar
{
    public $academic_calendar_id;
    public $academicCalendars = [];

    // Auto-save hooks - change is triggered on blur in views via wire:model.blur
    public function updatedAcademicCalendarId()
    {
        if ($this->currentStep === 'academic_calendar' && $this->syllabus) {
            $this->markStepDirty('academic_calendar');
            $this->saveCurrentStep();
            $this->refreshWeeklyCoverage(false);
        }
    }
}
