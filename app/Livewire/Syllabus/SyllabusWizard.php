<?php

namespace App\Livewire\Syllabus;

use App\Livewire\Syllabus\Concerns\HandlesAcademicCalendar;
use App\Livewire\Syllabus\Concerns\HandlesCoPoMapping;
use App\Livewire\Syllabus\Concerns\HandlesComponents;
use App\Livewire\Syllabus\Concerns\HandlesCourseOutcomes;
use App\Models\AcademicCalendar;
use App\Models\Course;
use App\Models\Syllabus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SyllabusWizard extends Component
{
    use HandlesAcademicCalendar;
    use HandlesComponents;
    use HandlesCourseOutcomes;
    use HandlesCoPoMapping;

    public ?Syllabus $syllabus = null;
    public ?Course $course = null;
    public $currentStep;
    public ?string $lastSavedAt = null;

    public function mount($syllabusId = null, $courseId = null)
    {
        // Cast to integers if provided
        $syllabusId = $syllabusId ? (int) $syllabusId : null;
        $courseId = $courseId ? (int) $courseId : null;

        $user = Auth::user();

        if ($syllabusId) {
            // Editing existing syllabus
            $this->syllabus = Syllabus::with([
                'course.program.outcomes',
                'components',
                'courseOutcomes.programOutcomes'
            ])->findOrFail($syllabusId);

            // Authorization check
            if ($this->syllabus->prepared_by !== Auth::id()) {
                abort(403, 'Unauthorized');
            }

            $this->course = $this->syllabus->course;
            $this->currentStep = $this->syllabus->current_step;
            $this->loadExistingData();
            $this->prefillLecFromUser($user);
        } elseif ($courseId) {
            // Creating new syllabus
            $this->course = Course::with('program.outcomes')->findOrFail($courseId);

            // Create draft syllabus (academic_calendar_id will be set in first step)
            $this->syllabus = Syllabus::create([
                'course_id' => $this->course->id,
                'academic_calendar_id' => null, // Will be set in first step
                'status' => 'draft',
                'current_step' => 'academic_calendar',
                'prepared_by' => Auth::id(),
            ]);

            $this->currentStep = 'academic_calendar';
            $this->prefillLecFromUser($user);
        } else {
            abort(404);
        }

        $this->academicCalendars = AcademicCalendar::orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
    }

    private function loadExistingData()
    {
        // Load academic calendar
        $this->academic_calendar_id = $this->syllabus->academic_calendar_id;

        // Load components
        $lecComponent = $this->syllabus->getLecComponent();
        if ($lecComponent) {
            $this->lec_instructor_name = $lecComponent->instructor_name;
            $this->lec_instructor_email = $lecComponent->instructor_email;
            $this->lec_phone = $lecComponent->phone;
            $this->lec_office = $lecComponent->office;
            $this->lec_class_hours = $lecComponent->class_hours;
            $this->lec_schedule = $lecComponent->schedule;
            $this->lec_consultation_hours = $lecComponent->consultation_hours;
            $this->lec_performance_standard = $lecComponent->performance_standard;
        }

        $labComponent = $this->syllabus->getLabComponent();
        if ($labComponent) {
            $this->lab_instructor_name = $labComponent->instructor_name;
            $this->lab_instructor_email = $labComponent->instructor_email;
            $this->lab_phone = $labComponent->phone;
            $this->lab_office = $labComponent->office;
            $this->lab_class_hours = $labComponent->class_hours;
            $this->lab_schedule = $labComponent->schedule;
            $this->lab_consultation_hours = $labComponent->consultation_hours;
            $this->lab_performance_standard = $labComponent->performance_standard;
        }

        // Load course outcomes
        $this->courseOutcomes = $this->syllabus->courseOutcomes->map(function ($co) {
            return [
                'id' => $co->id,
                'co_code' => $co->co_code,
                'description' => $co->description,
            ];
        })->toArray();

        // Load CO-PO mappings
        $this->coPoMappings = [];
        foreach ($this->syllabus->courseOutcomes as $co) {
            $this->coPoMappings[$co->id] = [];
            foreach ($co->programOutcomes as $po) {
                $this->coPoMappings[$co->id][$po->id] = true;  // Just mark as connected
            }
        }
    }

    // Prefill instructor info from user profile if not already set
    private function prefillLecFromUser($user): void
    {
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

    public function saveAndNext()
    {
        $this->saveCurrentStep();
        $nextStep = $this->syllabus->getNextStep();
        if ($nextStep) {
            $this->currentStep = $nextStep;
            $this->syllabus->update(['current_step' => $nextStep]);
        }
    }

    public function saveAndPrevious()
    {
        $this->saveCurrentStep();
        $previousStep = $this->syllabus->getPreviousStep();
        if ($previousStep) {
            $this->currentStep = $previousStep;
            $this->syllabus->update(['current_step' => $previousStep]);
        }
    }

    public function saveStep(string $step): bool
    {
        $previous = $this->currentStep;
        $this->currentStep = $step;
        $saved = $this->saveCurrentStep();
        $this->currentStep = $previous;

        return $saved;
    }

    public function saveCurrentStep(): bool
    {
        $saved = false;

        switch ($this->currentStep) {
            case 'academic_calendar':
                if ($this->academic_calendar_id) {
                    $this->validate([
                        'academic_calendar_id' => 'required|exists:academic_calendars,id',
                    ]);

                    $this->syllabus->update([
                        'academic_calendar_id' => $this->academic_calendar_id,
                    ]);
                    $saved = true;
                }
                break;

            case 'course_components':
                $saved = $this->saveComponents();
                break;

            case 'course_outcomes':
                $saved = $this->saveCourseOutcomes();
                break;

            case 'co_po_mapping':
                // Ensure course outcomes are saved first before mapping
                if (empty($this->courseOutcomes)) {
                    // No outcomes to map
                    break;
                }
                $this->saveCourseOutcomes();
                $saved = $this->saveCoPoMappings();
                break;
        }

        if ($saved) {
            $this->markDraftSaved();
        }

        return $saved;
    }

    public function submitForReview()
    {
        $this->saveCurrentStep();

        $this->syllabus->update([
            'status' => 'under_review',
            'current_step' => 'review', // Lock at review step
        ]);

        return redirect()->route('syllabus.show', $this->syllabus->id)
            ->with('toast', [
                'message' => 'Syllabus submitted for review successfully.',
                'type' => 'success'
            ]);
    }

    public function render()
    {
        return view('livewire.syllabus.syllabus-wizard');
    }

    private function markDraftSaved(): void
    {
        $this->lastSavedAt = now()->format('M d, Y h:i A');
    }
}
