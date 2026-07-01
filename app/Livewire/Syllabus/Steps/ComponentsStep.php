<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\CourseComponent;
use App\Models\CourseComponentSchedule;
use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ComponentsStep extends Component
{
    // ── Public state ──────────────────────────────────────────────────────────

    public int  $syllabusId;
    public bool $courseHasLab = false;
    public bool $isLoaded     = false;

    // LEC fields
    public ?string $lec_instructor_name  = null;
    public ?string $lec_instructor_email = null;
    public ?string $lec_phone            = null;
    public ?string $lec_office           = null;
    public array   $lec_schedules        = [];

    // LAB fields
    public ?string $lab_instructor_name  = null;
    public ?string $lab_instructor_email = null;
    public ?string $lab_phone            = null;
    public ?string $lab_office           = null;
    public array   $lab_schedules        = [];

    // Read-only from course
    public string  $lec_class_hours          = '3 hr';
    public string  $lec_performance_standard = '60.00';
    public ?string $lab_class_hours          = null;

    // Consultation hours — kept in sync by Alpine before any save
    public array $userConsultationHours = [];
    public array $labConsultationHours = [];

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $labUsers = $this->courseHasLab
            ? User::where('account_status', 'active')
                ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'admin'))
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'phone_number', 'office'])
                ->toArray()
            : [];

        return view('livewire.syllabus.steps.course-components', [
            'course'               => (object) ['has_lec_lab' => $this->courseHasLab],
            'labConsultationHours' => $this->labConsultationHours,
            'labUsers'             => $labUsers,
        ]);
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step !== 'course_components') return;
        $this->isLoaded = false;
        $this->loadData();
    }

    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'course_components') return;
        $this->saveComponents();
        $this->dispatch('syllabus-step-saved', step: 'course_components');
    }

    public function onPushAndNavigate(string $toStep): void
    {
        $this->saveComponents();
        $this->dispatch('lw-toast', type: 'success', message: 'Course Components saved.');
        $this->dispatch('navigate-after-save', step: $toStep);
    }

    // ── Schedule mutations (kept for backward compat, no longer called from UI) ──

    public function addSchedule(string $prefix): void
    {
        $this->{$prefix . '_schedules'}[] = ['day' => 'Monday', 'time' => ''];
    }

    public function removeSchedule(string $prefix, int $index): void
    {
        array_splice($this->{$prefix . '_schedules'}, $index, 1);
    }

    // ── Consultation Hours ────────────────────────────────────────────────────

    /**
     * Called by Alpine's x-on:click="save()" button (standalone amber save).
     * Also called internally by saveComponents() so Save All covers it.
     */
    public function saveConsultationHours(array $rows): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (! $user) return;

        $user->consultationHours()->delete();

        foreach ($rows as $row) {
            $day  = trim($row['day']  ?? '');
            $time = trim($row['time'] ?? '');
            if ($day !== '' && $time !== '') {
                $user->consultationHours()->create(['day' => $day, 'time' => $time]);
            }
        }

        $this->userConsultationHours = $user->fresh()->consultationHours
            ->map(fn ($h) => ['day' => $h->day, 'time' => $h->time])
            ->values()->all();

        $this->dispatch('consultation-hours-updated', hours: $this->userConsultationHours);
    }

    /**
     * Alpine pushes its local consultation rows into Livewire state
     * just before Save All fires, so saveComponents() can persist them.
     */
    public function pushConsultationHours(array $rows): void
    {
        $this->userConsultationHours = $rows;
    }

    /**
     * Alpine pushes its local LEC schedule rows into Livewire state.
     */
    public function pushLecSchedules(array $rows): void
    {
        $this->lec_schedules = $rows;
    }

    /**
     * Alpine pushes its local LAB schedule rows into Livewire state.
     */
    public function pushLabSchedules(array $rows): void
    {
        $this->lab_schedules = $rows;
    }

    /**
     * Save lab consultation hours from the lab instructor's user record.
     */
    public function saveLabConsultationHours(array $rows): void
    {
        $labInstructor = User::where('email', $this->lab_instructor_email)->first();
        if (! $labInstructor) return;

        $labInstructor->consultationHours()->delete();

        foreach ($rows as $row) {
            $day  = trim($row['day']  ?? '');
            $time = trim($row['time'] ?? '');
            if ($day !== '' && $time !== '') {
                $labInstructor->consultationHours()->create(['day' => $day, 'time' => $time]);
            }
        }

        $this->labConsultationHours = $labInstructor->fresh()->consultationHours
            ->map(fn ($h) => ['day' => $h->day, 'time' => $h->time])
            ->values()->all();

        $this->dispatch('lab-consultation-hours-updated', hours: $this->labConsultationHours);
    }

    /**
     * Alpine pushes its local lab consultation rows into Livewire state.
     */
    public function pushLabConsultationHours(array $rows): void
    {
        $this->labConsultationHours = $rows;
    }

    /**
     * Called when the lab instructor user is selected from the dropdown.
     * Populates lab fields from the selected user's profile.
     */
    public function selectLabInstructor(int $userId): void
    {
        $user = User::with('consultationHours')->find($userId);
        if (! $user) return;

        $this->lab_instructor_name  = $user->name;
        $this->lab_instructor_email = $user->email;
        $this->lab_phone            = $user->phone_number;
        $this->lab_office           = $user->office;

        $this->labConsultationHours = $user->consultationHours
            ->map(fn ($h) => ['day' => $h->day, 'time' => $h->time])
            ->values()->all();

        $this->dispatch('lab-instructor-selected', [
            'name'              => $this->lab_instructor_name,
            'email'             => $this->lab_instructor_email,
            'phone'             => $this->lab_phone,
            'office'            => $this->lab_office,
            'consultationHours' => $this->labConsultationHours,
        ]);
    }

    /**
     * Clear lab instructor selection.
     */
    public function clearLabInstructor(): void
    {
        $this->lab_instructor_name  = null;
        $this->lab_instructor_email = null;
        $this->lab_phone            = null;
        $this->lab_office           = null;
        $this->labConsultationHours = [];

        $this->dispatch('lab-instructor-selected', [
            'name'              => null,
            'email'             => null,
            'phone'             => null,
            'office'            => null,
            'consultationHours' => [],
        ]);
    }

    // ── Manual save (Save All button) ─────────────────────────────────────────

    public function save(): void
    {
        $this->saveComponents();
        $this->dispatch('lw-toast', type: 'success', message: 'Course Components saved.');
        $this->dispatch('syllabus-step-saved', step: 'course_components');
    }

    // ── Dirty tracking ────────────────────────────────────────────────────────

    public function updated(string $property): void
    {
        if (! $this->isLoaded) return;
        if (! str_starts_with($property, 'lec_') && ! str_starts_with($property, 'lab_')) return;
        $this->dispatch('syllabus-step-dirty', step: 'course_components', dirty: true);
    }

    // ── Private: load ─────────────────────────────────────────────────────────

    private function loadData(): void
    {
        if ($this->isLoaded) return;

        $syllabus           = Syllabus::with('course')->findOrFail($this->syllabusId);
        $this->courseHasLab = (bool) $syllabus->course?->has_lec_lab;

        $course = $syllabus->course;
        if ($course) {
            $this->lec_performance_standard = $this->toOptionValue($course->passing_mark, '60.00');
            $this->lec_class_hours          = $course->lec_class_hours ?? '3 hr';
            $this->lab_class_hours          = $course->lab_class_hours;
        }

        /** @var User $user */
        $user = Auth::user();
        $this->userConsultationHours = $user
            ? $user->consultationHours
                ->map(fn ($h) => ['day' => $h->day, 'time' => $h->time])
                ->values()->all()
            : [];

        $lec = CourseComponent::with('schedules')
            ->where('syllabus_id', $this->syllabusId)->where('type', 'LEC')->first();

        if ($lec) {
            $this->lec_instructor_name  = $lec->instructor_name;
            $this->lec_instructor_email = $lec->instructor_email;
            $this->lec_phone            = $lec->phone;
            $this->lec_office           = $lec->office;
            $this->lec_schedules        = $lec->schedules
                ->map(fn ($s) => ['day' => $s->day, 'time' => $s->time])->values()->all();
        } else {
            $this->prefillLecFromUser();
        }

        if ($this->courseHasLab) {
            $lab = CourseComponent::with('schedules')
                ->where('syllabus_id', $this->syllabusId)->where('type', 'LAB')->first();

            if ($lab) {
                $this->lab_instructor_name  = $lab->instructor_name;
                $this->lab_instructor_email = $lab->instructor_email;
                $this->lab_phone            = $lab->phone;
                $this->lab_office           = $lab->office;
                $this->lab_schedules        = $lab->schedules
                    ->map(fn ($s) => ['day' => $s->day, 'time' => $s->time])->values()->all();

                // Fetch consultation hours from the lab instructor's user record
                if (! empty($this->lab_instructor_email)) {
                    $labInstructor = User::where('email', $this->lab_instructor_email)->first();
                    $this->labConsultationHours = $labInstructor?->consultationHours
                        ->map(fn ($h) => ['day' => $h->day, 'time' => $h->time])
                        ->values()->all() ?? [];
                }
            }
        }

        $this->isLoaded = true;
    }

    private function prefillLecFromUser(): void
    {
        $user = Auth::user();
        if (! $user) return;
        if (empty($this->lec_instructor_name)  && ! empty($user->name))         $this->lec_instructor_name  = $user->name;
        if (empty($this->lec_instructor_email) && ! empty($user->email))        $this->lec_instructor_email = $user->email;
        if (empty($this->lec_phone)            && ! empty($user->phone_number)) $this->lec_phone            = $user->phone_number;
        if (empty($this->lec_office)           && ! empty($user->office))       $this->lec_office           = $user->office;
    }

    // ── Private: save ─────────────────────────────────────────────────────────

    private function saveComponents(): void
    {
        // 1. LEC component
        $lec = CourseComponent::updateOrCreate(
            ['syllabus_id' => $this->syllabusId, 'type' => 'LEC'],
            $this->buildPayload('lec')
        );
        $this->syncSchedules($lec, $this->lec_schedules);

        // 2. LAB component (if applicable)
        if ($this->courseHasLab) {
            $lab = CourseComponent::updateOrCreate(
                ['syllabus_id' => $this->syllabusId, 'type' => 'LAB'],
                $this->buildPayload('lab')
            );
            $this->syncSchedules($lab, $this->lab_schedules);
        }

        // 3. Consultation hours — always included in Save All
        $this->saveConsultationHours($this->userConsultationHours);

        // 4. Lab consultation hours (if applicable)
        if ($this->courseHasLab && ! empty($this->lab_instructor_email)) {
            $this->saveLabConsultationHours($this->labConsultationHours);
        }
    }

    private function syncSchedules(CourseComponent $component, array $schedules): void
    {
        $component->schedules()->delete();

        foreach ($schedules as $s) {
            $day  = trim($s['day']  ?? '');
            $time = trim($s['time'] ?? '');
            if ($day !== '' && $time !== '') {
                CourseComponentSchedule::create([
                    'course_component_id' => $component->id,
                    'day'                 => $day,
                    'time'                => $time,
                ]);
            }
        }
    }

    private function buildPayload(string $prefix): array
    {
        $get        = fn (string $f) => $this->{$prefix . '_' . $f};
        $classHours = $prefix === 'lec' ? $this->lec_class_hours : ($this->lab_class_hours ?? '3 hr');

        return [
            'instructor_name'      => $this->str($get('instructor_name'))  ?? '',
            'instructor_email'     => $this->str($get('instructor_email')) ?? '',
            'phone'                => $this->nullable($get('phone')),
            'office'               => $this->nullable($get('office')),
            'class_hours'          => $classHours,
            'performance_standard' => $this->toDecimal($this->lec_performance_standard, 60.00),
        ];
    }

    // ── Value helpers ─────────────────────────────────────────────────────────

    private function nullable(mixed $v): ?string
    {
        $s = trim((string) ($v ?? ''));
        return $s === '' ? null : $s;
    }

    private function str(mixed $v, ?string $fallback = null): ?string
    {
        $s = trim((string) ($v ?? ''));
        return $s !== '' ? $s : $fallback;
    }

    private function toDecimal(mixed $v, float $fallback): float
    {
        $s = str_replace('%', '', trim((string) ($v ?? '')));
        return is_numeric($s) ? round((float) $s, 2) : $fallback;
    }

    private function toOptionValue(mixed $v, string $fallback): string
    {
        $s = trim((string) ($v ?? ''));
        return is_numeric($s) ? number_format((float) $s, 2, '.', '') : $fallback;
    }
}
