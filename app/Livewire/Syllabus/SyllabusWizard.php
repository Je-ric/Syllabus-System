<?php

namespace App\Livewire\Syllabus;

use App\Livewire\Syllabus\Concerns\HandlesAcademicCalendar;
use App\Livewire\Syllabus\Concerns\HandlesCoPoMapping;
use App\Livewire\Syllabus\Concerns\HandlesComponents;
use App\Livewire\Syllabus\Concerns\HandlesCourseOutcomes;
use App\Livewire\Syllabus\Concerns\HandlesWeeklyCoverage;
use App\Models\AcademicCalendar;
use App\Models\Course;
use App\Models\Syllabus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class SyllabusWizard extends Component
{
    use HandlesAcademicCalendar;
    use HandlesComponents;
    use HandlesCourseOutcomes;
    use HandlesCoPoMapping;
    use HandlesWeeklyCoverage;

    public ?Syllabus $syllabus = null;
    public ?Course $course = null;
    public $currentStep;
    public ?string $lastSavedAt = null;
    public array $stepDirty = [];

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
            $this->initializeStepState();
            $this->loadExistingData();
            $this->prefillLecFromUser($user);
            $this->refreshWeeklyCoverage(false);
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
            $this->initializeStepState();
            $this->prefillLecFromUser($user);
            $this->refreshWeeklyCoverage(false);
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
                'temp_key' => 'co_' . $co->id,
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
        if (!$this->shouldSaveCurrentStep()) {
            return false;
        }

        $saved = false;

        switch ($this->currentStep) {
            case 'academic_calendar':
                if ($this->academic_calendar_id) {
                    $this->validate([
                        'academic_calendar_id' => [
                            'required',
                            'exists:academic_calendars,id',
                            Rule::unique('syllabi', 'academic_calendar_id')
                                ->where(fn($query) => $query->where('course_id', $this->course->id))
                                ->ignore($this->syllabus->id),
                        ],
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
            case 'weekly_coverage':
                // Weekly generation is explicit to avoid hidden heavy operations.
                $this->refreshWeeklyCoverage(false);
                $saved = true;
                break;
        }

        if ($saved) {
            $this->markStepSaved($this->currentStep);
        } elseif ($this->currentStep === 'course_outcomes') {
            $this->dispatch('lw-toast', type: 'error', message: 'Please save Course Outcomes first.');
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

    public function updatedCurrentStep($value): void
    {
        if ($value === 'weekly_coverage' || $value === 'review') {
            $this->refreshWeeklyCoverage(false);
        }
    }

    public function setStep(string $step): void
    {
        $this->currentStep = $step;
        if ($this->syllabus) {
            $this->syllabus->update(['current_step' => $step]);
        }

        if ($step === 'weekly_coverage') {
            if ($this->academic_calendar_id && $this->syllabus) {
                // Ensure calendar is persisted before generating weeks.
                if ($this->syllabus->academic_calendar_id !== $this->academic_calendar_id) {
                    $this->syllabus->update([
                        'academic_calendar_id' => $this->academic_calendar_id,
                    ]);
                }
            }
            $this->refreshWeeklyCoverage(false);
        }
    }

    public function navigateToStep(string $fromStep, string $toStep): void
    {
        if ($fromStep === $toStep) {
            return;
        }

        // Only CO step requires manual save before switching.
        if ($fromStep === 'course_outcomes') {
            $hasUnsavedCo = (bool) ($this->stepDirty['course_outcomes'] ?? false);
            if ($hasUnsavedCo) {
                $hasTypedOutcome = collect($this->courseOutcomes)
                    ->contains(fn($co) => trim((string) ($co['description'] ?? '')) !== '');

                if ($hasTypedOutcome && !$this->saveStep($fromStep)) {
                    $this->dispatch('lw-toast', type: 'error', message: 'Save Course Outcomes first before proceeding.');
                    return;
                }

                if (!$hasTypedOutcome) {
                    // Do not block navigation for blank CO placeholders.
                    $this->stepDirty['course_outcomes'] = false;
                }
            }
        }

        // Other steps are auto-saved via update hooks, so navigation stays fast.
        $this->setStep($toStep);
    }

    public function generateWeeklyCoverage(): void
    {
        if (!$this->syllabus || !$this->academic_calendar_id) {
            $this->dispatch('lw-toast', type: 'error', message: 'Select an academic calendar before generating weeks.');
            return;
        }

        if ($this->syllabus->academic_calendar_id !== $this->academic_calendar_id) {
            $this->syllabus->update(['academic_calendar_id' => $this->academic_calendar_id]);
        }

        $this->refreshWeeklyCoverage(true);
        $this->markStepSaved('weekly_coverage');
        $this->dispatch('lw-toast', type: 'success', message: 'Weekly coverage generated.');
    }

    public function markStepDirty(string $step): void
    {
        if (!array_key_exists($step, $this->syllabus->getWizardSteps())) {
            return;
        }
        $this->stepDirty[$step] = true;
    }

    public function markStepSaved(string $step): void
    {
        if (!array_key_exists($step, $this->syllabus->getWizardSteps())) {
            return;
        }

        $this->stepDirty[$step] = false;
        $this->markDraftSaved();
    }

    private function initializeStepState(): void
    {
        foreach (array_keys($this->syllabus->getWizardSteps()) as $step) {
            $this->stepDirty[$step] = false;
        }
    }

    private function shouldSaveCurrentStep(): bool
    {
        if ($this->currentStep === 'review') {
            return false;
        }

        if ($this->currentStep === 'weekly_coverage') {
            return true;
        }

        return (bool) ($this->stepDirty[$this->currentStep] ?? true);
    }

    public function stepHasMissingRequired(string $step): bool
    {
        switch ($step) {
            case 'academic_calendar':
                return empty($this->academic_calendar_id);

            case 'course_components':
                $missingLec = !$this->isLecComplete();
                $missingLab = $this->course->has_lec_lab ? !$this->isLabComplete() : false;
                return $missingLec || $missingLab;

            case 'course_outcomes':
                $validCoCount = collect($this->courseOutcomes)
                    ->filter(fn($co) => trim((string) ($co['description'] ?? '')) !== '')
                    ->count();
                return $validCoCount === 0;

            case 'co_po_mapping':
                if (empty($this->courseOutcomes)) {
                    return true;
                }
                foreach ($this->coPoMappings as $poMappings) {
                    if (!is_array($poMappings)) {
                        continue;
                    }
                    if (count(array_filter($poMappings)) > 0) {
                        return false;
                    }
                }
                return true;

            case 'weekly_coverage':
                return $this->syllabusWeeks->isEmpty();

            default:
                return false;
        }
    }

    protected function onValidationError(ValidationException $exception): void
    {
        $message = $exception->validator->errors()->first() ?: 'Please check your input.';
        $this->dispatch('lw-toast', type: 'error', message: $message);
    }

    private function markDraftSaved(): void
    {
        $this->lastSavedAt = now()->format('M d, Y h:i A');
    }
}
