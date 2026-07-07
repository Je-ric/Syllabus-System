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
    public ?int  $lec_user_id = null;
    public array $lec_schedules = [];

    // LAB fields
    public ?int  $lab_user_id = null;
    public array $lab_schedules = [];

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
                ->when($this->lec_user_id, fn ($q) => $q->where('id', '!=', $this->lec_user_id))
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'phone_number', 'office'])
                ->toArray()
            : [];

        // Fallback: if lec_user_id wasn't persisted yet, use the authenticated user
        $lecUserId = $this->lec_user_id ?? Auth::id();
        $lecUser   = $lecUserId ? User::with('consultationHours')->find($lecUserId) : null;
        $labUser   = $this->lab_user_id ? User::with('consultationHours')->find($this->lab_user_id) : null;

        return view('livewire.syllabus.steps.course-components', [
            'course'               => (object) ['has_lec_lab' => $this->courseHasLab],
            'labConsultationHours' => $this->labConsultationHours,
            'labUsers'             => $labUsers,
            'lecUser'              => $lecUser,
            'labUser'              => $labUser,
        ]);
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step !== 'course_components') return;
        $this->reset(['lec_user_id', 'lab_user_id', 'lec_schedules', 'lab_schedules', 'userConsultationHours', 'labConsultationHours']);
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

    #[On('request-push-and-navigate')]
    public function onPushAndNavigate(string $toStep): void
    {
        $this->saveComponents();
        $this->dispatch('lw-toast', type: 'success', message: 'Course Components saved.');
        $this->dispatch('navigate-after-save', step: $toStep);
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
        if ($this->hasScheduleConflict($this->lec_schedules, $rows)) {
            $this->dispatch('lw-toast', type: 'error', message: 'Consultation hours conflict with LEC class schedule — not saved.');
            return;
        }
        $this->userConsultationHours = $rows;
        $this->saveConsultationHours($rows);
    }

    public function pushLecSchedules(array $rows): void
    {
        $this->lec_schedules = $rows;
    }

    public function pushLabSchedules(array $rows): void
    {
        $this->lab_schedules = $rows;
    }

    public function saveLabConsultationHours(array $rows): void
    {
        if (! $this->lab_user_id) return;

        $labInstructor = User::find($this->lab_user_id);
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
    }

    public function pushLabConsultationHours(array $rows): void
    {
        if ($this->hasScheduleConflict($this->lab_schedules, $rows)) {
            $this->dispatch('lw-toast', type: 'error', message: 'Consultation hours conflict with LAB class schedule — not saved.');
            return;
        }
        $this->labConsultationHours = $rows;
        if ($this->lab_user_id) {
            $this->saveLabConsultationHours($rows);
        }
    }

    /**
     * Called when the lab instructor user is selected from the dropdown.
     * Populates lab fields from the selected user's profile.
     */
    public function selectLabInstructor(int $userId): void
    {
        $user = User::with('consultationHours')->find($userId);
        if (! $user) return;

        $this->lab_user_id = $user->id;

        $this->labConsultationHours = $user->consultationHours
            ->map(fn ($h) => ['day' => $h->day, 'time' => $h->time])
            ->values()->all();

        $this->dispatch('lab-instructor-selected', [
            'name'              => $user->name,
            'email'             => $user->email,
            'phone'             => $user->phone_number,
            'office'            => $user->office,
            'consultationHours' => $this->labConsultationHours,
        ]);
    }

    public function clearLabInstructor(): void
    {
        $this->lab_user_id = null;
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
        if (! in_array($property, ['lec_user_id', 'lab_user_id'])) return;
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
            $passing = trim((string) ($course->passing_mark ?? ''));
            $this->lec_performance_standard = is_numeric($passing)
                ? number_format((float) $passing, 2, '.', '')
                : '60.00';
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
            $this->lec_user_id   = $lec->user_id ?? Auth::id();
            $this->lec_schedules = $lec->schedules
                ->map(fn ($s) => ['day' => $s->day, 'time' => $s->time])->values()->all();
        } else {
            $this->lec_user_id = Auth::id();
        }

        if ($this->courseHasLab) {
            $lab = CourseComponent::with('schedules')
                ->where('syllabus_id', $this->syllabusId)->where('type', 'LAB')->first();

            if ($lab) {
                $this->lab_user_id   = $lab->user_id;
                $this->lab_schedules = $lab->schedules
                    ->map(fn ($s) => ['day' => $s->day, 'time' => $s->time])->values()->all();

                if ($this->lab_user_id) {
                    $labInstructor = User::with('consultationHours')->find($this->lab_user_id);
                    $this->labConsultationHours = $labInstructor?->consultationHours
                        ->map(fn ($h) => ['day' => $h->day, 'time' => $h->time])
                        ->values()->all() ?? [];
                }
            }
        }

        $this->isLoaded = true;
    }

    // ── Private: save ─────────────────────────────────────────────────────────

    private function saveComponents(): void
    {
        // Ensure lec_user_id is always set before saving
        if (! $this->lec_user_id) {
            $this->lec_user_id = Auth::id();
        }

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
        \Illuminate\Support\Facades\DB::transaction(function () use ($component, $schedules) {
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
        });
    }

    private function buildPayload(string $prefix): array
    {
        $classHours = $prefix === 'lec' ? $this->lec_class_hours : ($this->lab_class_hours ?? '3 hr');
        $userId     = $prefix === 'lec' ? $this->lec_user_id : $this->lab_user_id;

        return [
            'user_id'              => $userId,
            'class_hours'          => $classHours,
            'performance_standard' => $this->toDecimal($this->lec_performance_standard, 60.00),
        ];
    }

    // ── Value helpers ─────────────────────────────────────────────────────────

    private function toDecimal(mixed $v, float $fallback): float
    {
        $s = str_replace('%', '', trim((string) ($v ?? '')));
        return is_numeric($s) ? round((float) $s, 2) : $fallback;
    }

    // ── Conflict helpers ─────────────────────────────────────────────────────

    private function hasScheduleConflict(array $schedules, array $hours): bool
    {
        foreach ($hours as $h) {
            $hDay = trim($h['day'] ?? '');
            $hRange = $this->parseTimeRange($h['time'] ?? '');
            if (!$hRange) continue;

            foreach ($schedules as $s) {
                if (trim($s['day'] ?? '') !== $hDay) continue;
                $sRange = $this->parseTimeRange($s['time'] ?? '');
                if (!$sRange) continue;

                if ($hRange[0] < $sRange[1] && $sRange[0] < $hRange[1]) {
                    return true;
                }
            }
        }
        return false;
    }

    private function parseTimeRange(string $time): ?array
    {
        $parts = array_map('trim', explode(' - ', $time, 2));
        if (count($parts) !== 2) return null;
        try {
            return [
                \Carbon\Carbon::parse($parts[0])->format('H:i'),
                \Carbon\Carbon::parse($parts[1])->format('H:i'),
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }
}
