<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\CourseComponent;
use App\Models\Syllabus;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ComponentsStep extends Component
{
    public int $syllabusId;
    public bool $courseHasLab = false;
    public bool $isLoaded = false;

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

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step !== 'course_components') {
            return;
        }

        $this->loadData();
    }

    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'course_components') {
            return;
        }

        if ($this->saveComponents()) {
            $this->dispatch('syllabus-step-saved', step: 'course_components');
        }
    }

    public function updated($property): void
    {
        if (!$this->isLoaded || (!str_starts_with($property, 'lec_') && !str_starts_with($property, 'lab_'))) {
            return;
        }
        // Only mark as dirty, do not auto-save.
        $this->dispatch('syllabus-step-dirty', step: 'course_components', dirty: true);
    }

    public function render()
    {
        return view('livewire.syllabus.steps.course-components', [
            'course' => (object) ['has_lec_lab' => $this->courseHasLab],
        ]);
    }

    private function loadData(): void
    {
        if ($this->isLoaded) {
            return;
        }

        $syllabus = Syllabus::query()->with('course')->findOrFail($this->syllabusId);
        $this->courseHasLab = (bool) $syllabus->course?->has_lec_lab;

        $lec = CourseComponent::query()
            ->where('syllabus_id', $this->syllabusId)
            ->where('type', 'LEC')
            ->first();

        if ($lec) {
            $this->lec_instructor_name = $lec->instructor_name;
            $this->lec_instructor_email = $lec->instructor_email;
            $this->lec_phone = $lec->phone;
            $this->lec_office = $lec->office;
            $this->lec_class_hours = $lec->class_hours;
            $this->lec_schedule = $lec->schedule;
            $this->lec_consultation_hours = $lec->consultation_hours;
            $this->lec_performance_standard = $lec->performance_standard;
        } else {
            $this->prefillLecFromUser();
        }

        $lab = CourseComponent::query()
            ->where('syllabus_id', $this->syllabusId)
            ->where('type', 'LAB')
            ->first();

        if ($lab) {
            $this->lab_instructor_name = $lab->instructor_name;
            $this->lab_instructor_email = $lab->instructor_email;
            $this->lab_phone = $lab->phone;
            $this->lab_office = $lab->office;
            $this->lab_class_hours = $lab->class_hours;
            $this->lab_schedule = $lab->schedule;
            $this->lab_consultation_hours = $lab->consultation_hours;
            $this->lab_performance_standard = $lab->performance_standard;
        }

        $this->isLoaded = true;
    }

    private function prefillLecFromUser(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        if (empty($this->lec_instructor_name) && !empty($user->name)) {
            $this->lec_instructor_name = $user->name;
        }
        if (empty($this->lec_instructor_email) && !empty($user->email)) {
            $this->lec_instructor_email = $user->email;
        }
        if (empty($this->lec_phone) && !empty($user->phone_number)) {
            $this->lec_phone = $user->phone_number;
        }
        if (empty($this->lec_office) && !empty($user->office)) {
            $this->lec_office = $user->office;
        }
    }

    private function saveComponents(): bool
    {
        if (!$this->isLecComplete()) {
            return false;
        }

        CourseComponent::query()->updateOrCreate(
            [
                'syllabus_id' => $this->syllabusId,
                'type' => 'LEC',
            ],
            [
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

        if ($this->courseHasLab) {
            if (!$this->isLabComplete()) {
                return false;
            }

            CourseComponent::query()->updateOrCreate(
                [
                    'syllabus_id' => $this->syllabusId,
                    'type' => 'LAB',
                ],
                [
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
}
