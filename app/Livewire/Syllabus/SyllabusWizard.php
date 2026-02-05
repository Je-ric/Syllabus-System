<?php

namespace App\Livewire\Syllabus;

use App\Models\AcademicCalendar;
use App\Models\Course;
use App\Models\CourseComponent;
use App\Models\CourseOutcome;
use App\Models\Syllabus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class SyllabusWizard extends Component
{
    public ?Syllabus $syllabus = null;
    public ?Course $course = null;
    public $currentStep;

    // Step 1: Academic Calendar
    public $academic_calendar_id;
    public $academicCalendars = [];

    // Step 2: Course Components
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

    // Step 3: Course Outcomes
    public $courseOutcomes = [];

    // Step 4: CO-PO Mapping
    public $coPoMappings = [];

    public function mount($syllabusId = null, $courseId = null)
    {
        // Cast to integers if provided
        $syllabusId = $syllabusId ? (int) $syllabusId : null;
        $courseId = $courseId ? (int) $courseId : null;

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

    public function saveCurrentStep()
    {
        switch ($this->currentStep) {
            case 'academic_calendar':
                if ($this->academic_calendar_id) {
                    $this->validate([
                        'academic_calendar_id' => 'required|exists:academic_calendars,id',
                    ]);

                    $this->syllabus->update([
                        'academic_calendar_id' => $this->academic_calendar_id,
                    ]);
                }
                break;

            case 'course_components':
                $this->saveComponents();
                break;

            case 'course_outcomes':
                $this->saveCourseOutcomes();
                break;

            case 'co_po_mapping':
                // Ensure course outcomes are saved first before mapping
                if (empty($this->courseOutcomes)) {
                    // No outcomes to map
                    break;
                }
                $this->saveCourseOutcomes();
                $this->saveCoPoMappings();
                break;
        }
    }

    private function saveComponents()
    {
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
    }

    private function saveCourseOutcomes()
    {
        // Filter out empty outcomes (no description)
        $validOutcomes = collect($this->courseOutcomes)->filter(function ($outcome) {
            return !empty(trim($outcome['description'] ?? ''));
        })->values()->toArray();

        // Get existing outcome IDs
        $existingIds = collect($validOutcomes)->pluck('id')->filter()->toArray();

        // Delete existing outcomes not in the current list
        if (!empty($existingIds)) {
            $this->syllabus->courseOutcomes()->whereNotIn('id', $existingIds)->delete();
        } else {
            // If no valid IDs, delete all (user cleared all outcomes)
            $this->syllabus->courseOutcomes()->delete();
        }

        // Save/update each outcome
        foreach ($validOutcomes as $index => $outcome) {
            if (isset($outcome['id']) && $outcome['id']) {
                // Update existing
                $co = CourseOutcome::where('id', $outcome['id'])
                    ->where('syllabus_id', $this->syllabus->id)
                    ->first();
                if ($co) {
                    $co->update([
                        'co_code' => $outcome['co_code'],
                        'description' => $outcome['description'],
                    ]);
                }
            } else {
                // Create new
                $newCo = CourseOutcome::create([
                    'syllabus_id' => $this->syllabus->id,
                    'co_code' => $outcome['co_code'],
                    'description' => $outcome['description'],
                ]);
                // Update the array with the new ID
                $validOutcomes[$index]['id'] = $newCo->id;
            }
        }

        // Update the courseOutcomes array with valid outcomes
        $this->courseOutcomes = $validOutcomes;

        // Refresh course outcomes to get updated IDs and sync mappings
        $this->syllabus->refresh();
        $this->courseOutcomes = $this->syllabus->courseOutcomes->map(function ($co) {
            return [
                'id' => $co->id,
                'co_code' => $co->co_code,
                'description' => $co->description,
            ];
        })->toArray();

        // Update CO-PO mapping keys to use actual IDs
        $newMappings = [];
        foreach ($this->courseOutcomes as $co) {
            if (isset($co['id'])) {
                // Check if there are existing mappings for this CO
                foreach ($this->coPoMappings as $key => $mappings) {
                    if ($key === $co['id'] || (is_string($key) && str_contains($key, 'new_'))) {
                        $newMappings[$co['id']] = $mappings;
                        break;
                    }
                }
                if (!isset($newMappings[$co['id']])) {
                    $newMappings[$co['id']] = [];
                }
            }
        }
        $this->coPoMappings = $newMappings;
    }

    private function saveCoPoMappings()
    {
        if (empty($this->coPoMappings)) {
            return;
        }

        // Ensure course outcomes are saved first
        $this->saveCourseOutcomes();

        foreach ($this->coPoMappings as $coKey => $poMappings) {
            if (!is_array($poMappings)) {
                continue;
            }

            // Handle both ID keys and temporary keys (new_X)
            $coId = null;
            if (is_numeric($coKey)) {
                $coId = $coKey;
            } else {
                // Extract index from 'new_X' format
                if (str_starts_with($coKey, 'new_')) {
                    $index = (int) str_replace('new_', '', $coKey);
                    if (isset($this->courseOutcomes[$index]['id'])) {
                        $coId = $this->courseOutcomes[$index]['id'];
                    }
                }
            }

            if (!$coId) {
                continue;
            }

            $co = CourseOutcome::where('id', $coId)
                ->where('syllabus_id', $this->syllabus->id)
                ->first();

            if ($co) {
                $syncData = [];
                foreach ($poMappings as $poId => $isConnected) {
                    if ($isConnected) {
                        $syncData[$poId] = [];  // No IED needed, just connect them
                    }
                }
                $co->programOutcomes()->sync($syncData);
            }
        }
    }

    public function addCourseOutcome()
    {
        $nextNumber = count($this->courseOutcomes) + 1;
        $this->courseOutcomes[] = [
            'id' => null,
            'co_code' => 'CO' . $nextNumber,
            'description' => '',
        ];

        // Initialize CO-PO mapping for new outcome
        $index = count($this->courseOutcomes) - 1;
        $this->coPoMappings['new_' . $index] = [];
    }

    public function removeCourseOutcome($index)
    {
        // Remove CO-PO mappings for this outcome
        if (isset($this->courseOutcomes[$index]['id'])) {
            $coId = $this->courseOutcomes[$index]['id'];
            unset($this->coPoMappings[$coId]);
        }
        unset($this->coPoMappings['new_' . $index]);

        // Delete from database if it exists
        if (isset($this->courseOutcomes[$index]['id']) && $this->courseOutcomes[$index]['id']) {
            CourseOutcome::where('id', $this->courseOutcomes[$index]['id'])->delete();
        }

        unset($this->courseOutcomes[$index]);
        $this->courseOutcomes = array_values($this->courseOutcomes);

        // Resequence codes and update mapping keys
        $newMappings = [];
        foreach ($this->courseOutcomes as $i => $outcome) {
            $this->courseOutcomes[$i]['co_code'] = 'CO' . ($i + 1);

            // Update mapping keys
            if (isset($outcome['id']) && $outcome['id']) {
                if (isset($this->coPoMappings[$outcome['id']])) {
                    $newMappings[$outcome['id']] = $this->coPoMappings[$outcome['id']];
                }
            } else {
                if (isset($this->coPoMappings['new_' . $index])) {
                    $newMappings['new_' . $i] = $this->coPoMappings['new_' . $index];
                }
            }
        }
        $this->coPoMappings = $newMappings;
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

    // Auto-save hooks - debounce is handled in views via wire:model.debounce
    public function updatedAcademicCalendarId()
    {
        if ($this->currentStep === 'academic_calendar' && $this->syllabus) {
            $this->saveCurrentStep();
        }
    }

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

    public function updatedCourseOutcomes()
    {
        if ($this->currentStep === 'course_outcomes' && $this->syllabus) {
            $this->saveCurrentStep();
        }
    }

    public function updatedCoPoMappings()
    {
        if ($this->currentStep === 'co_po_mapping' && $this->syllabus) {
            $this->saveCurrentStep();
        }
    }

    private function autoSaveComponents()
    {
        if ($this->currentStep === 'course_components' && $this->syllabus) {
            $this->saveCurrentStep();
        }
    }

    public function render()
    {
        return view('livewire.syllabus.syllabus-wizard');
    }
}
