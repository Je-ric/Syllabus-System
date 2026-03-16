<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\CourseComponent;
use App\Models\Syllabus;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * ComponentsStep
 *
 * wire:model.blur is used in the blade so values sync on blur/change instead of
 * every keystroke. This reduces request queueing and keeps wizard navigation
 * responsive. The save runs on navigation (syllabus-save-step) or the manual
 * Save button. Because the wizard never remounts child components, onStepChanged
 * always reloads fresh DB data when navigating back to this step.
 *
 * DB schema (course_components):
 *   performance_standard  DECIMAL(5,2)  DEFAULT 50.00
 *   instructor_name       VARCHAR        NOT NULL
 *   instructor_email      VARCHAR        NOT NULL
 *   class_hours           VARCHAR        NOT NULL
 *   phone / office / schedule / consultation_hours  NULLABLE
 */
class ComponentsStep extends Component
{
    // ── Public state ──────────────────────────────────────────────────────────

    public int  $syllabusId;
    public bool $courseHasLab = false;
    public bool $isLoaded     = false;

    // LEC fields — defaults match the DB default and the first <option> value
    public ?string $lec_instructor_name      = null;
    public ?string $lec_instructor_email     = null;
    public ?string $lec_phone                = null;
    public ?string $lec_office               = null;
    public string  $lec_class_hours          = '1 hr';
    public ?string $lec_schedule             = null;
    public ?string $lec_consultation_hours   = null;
    // Stored as bare number string to match <option value="50.00"> or value="50"
    public string  $lec_performance_standard = '50.00';

    // LAB fields
    public ?string $lab_instructor_name      = null;
    public ?string $lab_instructor_email     = null;
    public ?string $lab_phone                = null;
    public ?string $lab_office               = null;
    public string  $lab_class_hours          = '1 hr';
    public ?string $lab_schedule             = null;
    public ?string $lab_consultation_hours   = null;
    public string  $lab_performance_standard = '33.00';

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.syllabus.steps.course-components', [
            'course' => (object) ['has_lec_lab' => $this->courseHasLab],
        ]);
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step !== 'course_components') {
            return;
        }
        // Always reload from DB on re-visit — isLoaded guard does NOT apply here.
        $this->isLoaded = false;
        $this->loadData();
    }

    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'course_components') {
            return;
        }
        $this->saveComponents();
        $this->dispatch('syllabus-step-saved', step: 'course_components');
    }

    // ── Manual save button ────────────────────────────────────────────────────

    public function save(): void
    {
        $this->saveComponents();
        $this->dispatch('lw-toast', type: 'success', message: 'Course Components saved.');
        $this->dispatch('syllabus-step-saved', step: 'course_components');
    }

    // ── Dirty tracking ────────────────────────────────────────────────────────

    public function updated(string $property): void
    {
        if (! $this->isLoaded) {
            return;
        }
        if (! str_starts_with($property, 'lec_') && ! str_starts_with($property, 'lab_')) {
            return;
        }
        $this->dispatch('syllabus-step-dirty', step: 'course_components', dirty: true);
    }

    // ── Private: load ─────────────────────────────────────────────────────────

    private function loadData(): void
    {
        if ($this->isLoaded) {
            return;
        }

        $syllabus           = Syllabus::query()->with('course')->findOrFail($this->syllabusId);
        $this->courseHasLab = (bool) $syllabus->course?->has_lec_lab;

        $lec = CourseComponent::query()
            ->where('syllabus_id', $this->syllabusId)
            ->where('type', 'LEC')
            ->first();

        if ($lec) {
            $this->lec_instructor_name      = $lec->instructor_name;
            $this->lec_instructor_email     = $lec->instructor_email;
            $this->lec_phone                = $lec->phone;
            $this->lec_office               = $lec->office;
            $this->lec_class_hours          = $lec->class_hours          ?? '1 hr';
            $this->lec_schedule             = $lec->schedule;
            $this->lec_consultation_hours   = $lec->consultation_hours;
            $this->lec_performance_standard = $this->toOptionValue($lec->performance_standard, '50.00');
        } else {
            $this->prefillLecFromUser();
        }

        $lab = CourseComponent::query()
            ->where('syllabus_id', $this->syllabusId)
            ->where('type', 'LAB')
            ->first();

        if ($lab) {
            $this->lab_instructor_name      = $lab->instructor_name;
            $this->lab_instructor_email     = $lab->instructor_email;
            $this->lab_phone                = $lab->phone;
            $this->lab_office               = $lab->office;
            $this->lab_class_hours          = $lab->class_hours          ?? '1 hr';
            $this->lab_schedule             = $lab->schedule;
            $this->lab_consultation_hours   = $lab->consultation_hours;
            $this->lab_performance_standard = $this->toOptionValue($lab->performance_standard, '33.00');
        }

        $this->isLoaded = true;
    }

    private function prefillLecFromUser(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }
        if (empty($this->lec_instructor_name)  && ! empty($user->name))         $this->lec_instructor_name  = $user->name;
        if (empty($this->lec_instructor_email) && ! empty($user->email))        $this->lec_instructor_email = $user->email;
        if (empty($this->lec_phone)            && ! empty($user->phone_number)) $this->lec_phone            = $user->phone_number;
        if (empty($this->lec_office)           && ! empty($user->office))       $this->lec_office           = $user->office;
    }

    // ── Private: save ─────────────────────────────────────────────────────────

    private function saveComponents(): void
    {
        // LEC is always saved
        CourseComponent::query()->updateOrCreate(
            ['syllabus_id' => $this->syllabusId, 'type' => 'LEC'],
            $this->buildPayload('lec')
        );

        if ($this->courseHasLab) {
            CourseComponent::query()->updateOrCreate(
                ['syllabus_id' => $this->syllabusId, 'type' => 'LAB'],
                $this->buildPayload('lab')
            );
        }
    }

    /**
     * Build the DB column array from the Livewire public properties.
     *
     * Rules:
     *   - instructor_name, instructor_email, class_hours  → NOT NULL in DB.
     *     Save whatever the user typed; if blank, save NULL and let the DB default
     *     or the completeness check catch it — do NOT substitute 'None/NA'.
     *   - phone, office, schedule, consultation_hours     → NULLABLE. Blank → null.
     *   - performance_standard                            → DECIMAL. Parse the
     *     select option value (e.g. "50.00" or "67.00") to float.
     */
    private function buildPayload(string $prefix): array
    {
        $get = fn (string $field) => $this->{$prefix . '_' . $field};

        return [
            'instructor_name'      => $this->str($get('instructor_name')),
            'instructor_email'     => $this->str($get('instructor_email')),
            'phone'                => $this->nullable($get('phone')),
            'office'               => $this->nullable($get('office')),
            'class_hours'          => $this->str($get('class_hours'), '1 hr'),
            'schedule'             => $this->nullable($get('schedule')),
            'consultation_hours'   => $this->nullable($get('consultation_hours')),
            'performance_standard' => $this->toDecimal($get('performance_standard'), 50.00),
        ];
    }

    // ── Value helpers ─────────────────────────────────────────────────────────

    /**
     * Return trimmed string or null for nullable DB columns.
     */
    private function nullable(mixed $v): ?string
    {
        $s = trim((string) ($v ?? ''));
        return $s === '' ? null : $s;
    }

    /**
     * Return trimmed string; fall back to $fallback when blank.
     * Used for NOT NULL columns so the DB never complains.
     */
    private function str(mixed $v, ?string $fallback = null): ?string
    {
        $s = trim((string) ($v ?? ''));
        return $s !== '' ? $s : $fallback;
    }

    /**
     * Convert a select option value like "50.00", "67.00", "50", "50%"
     * to a float for the DECIMAL(5,2) DB column.
     */
    private function toDecimal(mixed $v, float $fallback): float
    {
        $s = str_replace('%', '', trim((string) ($v ?? '')));
        return is_numeric($s) ? round((float) $s, 2) : $fallback;
    }

    /**
     * Convert a DB DECIMAL value (e.g. 50.00 / "67.00") to the string
     * that matches the blade <option value="..."> attribute exactly.
     * We store option values as "50.00", "67.00" etc. in the blade.
     */
    private function toOptionValue(mixed $v, string $fallback): string
    {
        $s = trim((string) ($v ?? ''));
        if ($s === '') {
            return $fallback;
        }
        // If already in "50.00" format, return as-is
        if (is_numeric($s)) {
            return number_format((float) $s, 2, '.', '');
        }
        return $fallback;
    }
}
