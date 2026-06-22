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
    public array   $lec_schedules        = []; // [['day'=>'Monday','time'=>'...']]

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

    // User's consultation hours (read-only display, managed via profile)
    public array $userConsultationHours = [];

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

    // ── Schedule mutations ────────────────────────────────────────────────────

    public function addSchedule(string $prefix): void
    {
        $this->{$prefix . '_schedules'}[] = ['day' => 'Monday', 'time' => ''];
        $this->dispatch('syllabus-step-dirty', step: 'course_components', dirty: true);
    }

    public function removeSchedule(string $prefix, int $index): void
    {
        array_splice($this->{$prefix . '_schedules'}, $index, 1);
        $this->dispatch('syllabus-step-dirty', step: 'course_components', dirty: true);
    }

    // ── Manual save ───────────────────────────────────────────────────────────

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

        // Load user consultation hours for display
        /** @var User $user */
        $user = Auth::user();
        $this->userConsultationHours = $user?->consultationHours
            ->map(fn ($h) => ['day' => $h->day, 'time' => $h->time])
            ->values()->all() ?? [];

        $lec = CourseComponent::with('schedules')
            ->where('syllabus_id', $this->syllabusId)->where('type', 'LEC')->first();

        if ($lec) {
            $this->lec_instructor_name  = $lec->instructor_name;
            $this->lec_instructor_email = $lec->instructor_email;
            $this->lec_phone            = $lec->phone;
            $this->lec_office           = $lec->office;
            $this->lec_schedules        = $lec->schedules->map(fn ($s) => ['day' => $s->day, 'time' => $s->time])->values()->all();
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
                $this->lab_schedules        = $lab->schedules->map(fn ($s) => ['day' => $s->day, 'time' => $s->time])->values()->all();
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
        $lec = CourseComponent::updateOrCreate(
            ['syllabus_id' => $this->syllabusId, 'type' => 'LEC'],
            $this->buildPayload('lec')
        );
        $this->syncSchedules($lec, $this->lec_schedules);

        if ($this->courseHasLab) {
            $lab = CourseComponent::updateOrCreate(
                ['syllabus_id' => $this->syllabusId, 'type' => 'LAB'],
                $this->buildPayload('lab')
            );
            $this->syncSchedules($lab, $this->lab_schedules);
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
