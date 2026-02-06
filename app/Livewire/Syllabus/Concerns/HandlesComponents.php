<?php

namespace App\Livewire\Syllabus\Concerns;

use App\Models\CourseComponent;

trait HandlesComponents
{
    public $lec_instructor_name;
    public $lec_instructor_email;
    public $lec_phone;
    public $lec_office;
    public $lec_class_hours;
    public $lec_schedule;
    public $lec_consultation_hours;
    public $lec_performance_standard = '50%';

    public $lab_instructor_name;
    public $lab_instructor_email;
    public $lab_phone;
    public $lab_office;
    public $lab_class_hours;
    public $lab_schedule;
    public $lab_consultation_hours;
    public $lab_performance_standard = '50%';

    public function updatedLecInstructorName()
    {
        $this->autoSaveComponents();
    }

    public function updatedLecInstructorEmail()
    {
        $this->autoSaveComponents();
    }

    public function updatedLecPhone()
    {
        $this->autoSaveComponents();
    }

    public function updatedLecOffice()
    {
        $this->autoSaveComponents();
    }

    public function updatedLecClassHours()
    {
        $this->autoSaveComponents();
    }

    public function updatedLecSchedule()
    {
        $this->autoSaveComponents();
    }

    public function updatedLecConsultationHours()
    {
        $this->autoSaveComponents();
    }

    public function updatedLecPerformanceStandard()
    {
        $this->autoSaveComponents();
    }

    public function updatedLabInstructorName()
    {
        $this->autoSaveComponents();
    }

    public function updatedLabInstructorEmail()
    {
        $this->autoSaveComponents();
    }

    public function updatedLabPhone()
    {
        $this->autoSaveComponents();
    }

    public function updatedLabOffice()
    {
        $this->autoSaveComponents();
    }

    public function updatedLabClassHours()
    {
        $this->autoSaveComponents();
    }

    public function updatedLabSchedule()
    {
        $this->autoSaveComponents();
    }

    public function updatedLabConsultationHours()
    {
        $this->autoSaveComponents();
    }

    public function updatedLabPerformanceStandard()
    {
        $this->autoSaveComponents();
    }

    private function autoSaveComponents()
    {
        if ($this->currentStep === 'course_components' && $this->syllabus) {
            $this->saveCurrentStep();
        }
    }

    private function isLecComplete(): bool
    {
        return !empty(trim((string) $this->lec_instructor_name))
            && !empty(trim((string) $this->lec_instructor_email))
            && !empty(trim((string) $this->lec_phone))
            && !empty(trim((string) $this->lec_office))
            && !empty(trim((string) $this->lec_class_hours))
            && !empty(trim((string) $this->lec_schedule))
            && !empty(trim((string) $this->lec_consultation_hours))
            && !empty(trim((string) $this->lec_performance_standard));
    }

    private function isLabComplete(): bool
    {
        return !empty(trim((string) $this->lab_instructor_name))
            && !empty(trim((string) $this->lab_instructor_email))
            && !empty(trim((string) $this->lab_phone))
            && !empty(trim((string) $this->lab_office))
            && !empty(trim((string) $this->lab_class_hours))
            && !empty(trim((string) $this->lab_schedule))
            && !empty(trim((string) $this->lab_consultation_hours))
            && !empty(trim((string) $this->lab_performance_standard));
    }

    private function saveComponents(): bool
    {
        if (!$this->isLecComplete()) {
            return false;
        }

        // Save LEC component
        CourseComponent::updateOrCreate(
            [
                'syllabus_id' => $this->syllabus->id,
                'type' => 'LEC',
            ],
            [
                'units' => $this->course->credit_units,
                'instructor_name' => $this->lec_instructor_name,
                'instructor_email' => $this->lec_instructor_email,
                'phone' => $this->lec_phone,
                'office' => $this->lec_office,
                'class_hours' => $this->lec_class_hours,
                'schedule' => $this->lec_schedule,
                'consultation_hours' => $this->lec_consultation_hours,
                'performance_standard' => $this->lec_performance_standard,
            ]
        );

        // Save LAB component if course has lab
        if ($this->course->has_lec_lab) {
            if (!$this->isLabComplete()) {
                return false;
            }

            CourseComponent::updateOrCreate(
                [
                    'syllabus_id' => $this->syllabus->id,
                    'type' => 'LAB',
                ],
                [
                    'units' => $this->course->credit_units,
                    'instructor_name' => $this->lab_instructor_name,
                    'instructor_email' => $this->lab_instructor_email,
                    'phone' => $this->lab_phone,
                    'office' => $this->lab_office,
                    'class_hours' => $this->lab_class_hours,
                    'schedule' => $this->lab_schedule,
                    'consultation_hours' => $this->lab_consultation_hours,
                    'performance_standard' => $this->lab_performance_standard,
                ]
            );
        }

        return true;
    }
}
