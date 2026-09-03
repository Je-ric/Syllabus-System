<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\CourseComponent;
use App\Models\Syllabus;
use App\Models\SyllabusWeek;
use App\Services\Syllabus\Weeks\WeekContentService;
use App\Services\Syllabus\Weeks\WeekGenerationService;
use App\Services\Syllabus\Weeks\WeekLockService;
use App\Services\Syllabus\Weeks\WeekReconciliationService;
use App\Services\Syllabus\Weeks\WeekResourceService;
use App\Helpers\SecurityValidator;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

// WeeklyCoverageStep
//
// Thin Livewire component — all business logic lives in five services:
//   WeekGenerationService      generate() / hardReset() / deleteAllWeeks()
//   WeekReconciliationService  reconcile()  — preserves faculty content, updates dates only
//   WeekLockService            computeLockedWeeks()
//   WeekContentService         populateInputs() / save() / reset()
//   WeekResourceService        addReference() / removeReference() / addMaterial() / removeMaterial()
//
// Two regeneration paths are exposed to the UI:
//   refreshWeekDates()  — soft path: updates week dates/exam labels, keeps all content intact
//   hardResetWeeks()    — destructive path: wipes everything and rebuilds from scratch
//
// Optimizations applied:
//   [1] saveWeekFromModal patches $weekInputs in-place — no loadData() after a single-week save.
//   [2] WeekContentService::save() scopes SyllabusWeek query to onlyWeekNo when set.
//   [3] WeekContentService::save() batch-loads existing data upfront; all writes in DB::transaction.
//   [4] skipRender() called when nothing changed after save.
class WeeklyCoverageStep extends Component
{
    // ── Reactive state (serialised into Livewire snapshot) ────────────────────
    public int    $syllabusId;
    public int    $stepNumber           = 4;
    public ?int   $academic_calendar_id = null;
    public bool   $weeksGenerated       = false;
    public bool   $isLoaded             = false;
    public array  $courseComponents     = [];
    public string $activeComponent      = 'LEC';
    public array  $courseOutcomes       = [];
    public array  $lockedWeeks          = [];
    public array  $weekEvents           = [];
    public array  $weekInputs           = [];

    // ── Non-serialised (rebuilt on every request via boot()) ──────────────────
    protected Collection $syllabusWeeks;

    public function boot(): void
    {
        $this->syllabusWeeks = collect();
    }

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    public function render()
    {
        // syllabusWeeks is loaded by loadData() on mount/step-change.
        // On single-week saves we skip loadData() [1], so we need to ensure
        // syllabusWeeks is populated for render() on those paths too.
        if ($this->syllabusWeeks->isEmpty() && $this->weeksGenerated) {
            $this->syllabusWeeks = SyllabusWeek::where('syllabus_id', $this->syllabusId)
                ->orderBy('week_no')
                ->get();
        }

        return view('livewire.syllabus.wizard.steps.weekly-coverage', [
            'syllabusWeeks' => $this->syllabusWeeks,
            'syllabus'      => $this->freshSyllabus(),
        ]);
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'weekly_coverage') {
            // Only reload data if not already loaded (prevent unnecessary DB queries)
            if (! $this->isLoaded) {
                $this->loadData();
            }
        }
    }

    #[On('syllabus-calendar-updated')]
    public function onCalendarUpdated(): void
    {
        $this->loadData();
    }

    #[On('sidebar-save-all-weeks')]
    public function onSidebarSaveAll(): void
    {
        $this->saveAllWeeklyEntries();
    }

    // Confirmation methods for modal dialogs
    public function confirmHardReset(): void
    {
        $this->dispatch('confirm-hard-reset');
    }

    public function confirmRefreshDates(): void
    {
        $this->dispatch('confirm-refresh-dates');
    }

    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'weekly_coverage') {
            return;
        }

        try {
            app(WeekContentService::class)->save(
                $this->syllabusId,
                $this->activeComponent,
                $this->weekInputs,
                $this->lockedWeeks
            );

            $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
        } catch (\RuntimeException $e) {
            $this->dispatch('lw-toast', type: 'error', message: $e->getMessage());
            $this->dispatch('syllabus-step-save-failed', step: 'weekly_coverage', error: $e->getMessage());
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('syllabus-step-save-failed', step: 'weekly_coverage', error: $e->getMessage());
        }
    }

    // ── Week generation ───────────────────────────────────────────────────────

    public function generateWeeklyCoverage(): void
    {
        $syllabus = $this->freshSyllabus();
        $this->reloadCourseComponents();
        $this->academic_calendar_id = $syllabus?->academic_calendar_id
            ? (int) $syllabus->academic_calendar_id
            : null;

        try {
            $ok = app(WeekGenerationService::class)->generate(
                $syllabus,
                $this->courseComponents
            );
        } catch (\RuntimeException $e) {
            $this->dispatch('lw-toast', type: 'error', message: $e->getMessage());
            $this->dispatch('syllabus-save-finished');
            return;
        }

        if ($ok) {
            $this->loadData();

            // Get generation statistics for better user feedback
            $stats = app(\App\Services\Syllabus\Weeks\CalendarWeekSequenceBuilder::class)
                ->getGenerationStats($syllabus->academicCalendar, (int) $syllabus->academic_calendar_id);

            $message = "{$stats['totalWeeks']} weeks created successfully.";

            $this->dispatch('lw-toast', type: 'success', message: $message);
            $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
        }

        $this->dispatch('syllabus-save-finished');
    }

    // Soft path — update week dates/exam labels without touching faculty content.
    public function refreshWeekDates(): void
    {
        $syllabus = $this->freshSyllabus();

        if ($syllabus?->academicCalendar && ! $syllabus->academicCalendar->is_active) {
            $this->dispatch('lw-toast', type: 'error',
                message: 'Please use the current active academic calendar to refresh dates.');
            $this->dispatch('syllabus-save-finished');
            return;
        }

        $this->reloadCourseComponents();

        try {
            $result = app(WeekReconciliationService::class)->reconcile(
                $syllabus,
                $this->courseComponents
            );
        } catch (\RuntimeException $e) {
            $this->dispatch('lw-toast', type: 'error', message: $e->getMessage());
            $this->dispatch('syllabus-save-finished');
            return;
        }

        $this->loadData();

        $toastType = $result->hasNoChanges() ? 'info' : 'success';
        $this->dispatch('lw-toast', type: $toastType, message: $result->toMessage());
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
        $this->dispatch('syllabus-save-finished');
    }

    // Destructive path — wipe all weeks and rebuild from scratch.
    public function hardResetWeeks(): void
    {
        $this->reloadCourseComponents();

        try {
            app(WeekGenerationService::class)->hardReset(
                $this->freshSyllabus(),
                $this->courseComponents
            );
        } catch (\RuntimeException $e) {
            $this->dispatch('lw-toast', type: 'error', message: $e->getMessage());
            $this->dispatch('syllabus-save-finished');
            return;
        }

        $this->weekInputs  = [];
        $this->lockedWeeks = [];
        $this->weekEvents  = [];

        $this->loadData();

        $this->dispatch('lw-toast', type: 'success', message: 'Weeks reset. All content has been removed.');
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
        $this->dispatch('syllabus-save-finished');
    }

    // ── Week content ──────────────────────────────────────────────────────────

    // [1] Save from the edit modal — patches weekInputs in-place, no full loadData().
    public function saveWeekFromModal(int $weekNo, array $fields): void
    {
        if ($weekNo <= 0 || isset($this->lockedWeeks[$weekNo])) {
            return;
        }

        $isMvgo = $weekNo === 1;
        if (! $isMvgo && empty($fields['course_outcome_id'])) {
            $this->dispatch('lw-toast', type: 'error',
                message: 'Select a Course Outcome first before filling in week content.');
            return;
        }

        // Sanitize material URLs.
        $materials = array_map(function (array $mat): array {
            $url = trim((string) ($mat['url'] ?? ''));
            if ($url !== '' && ! preg_match('#^https?://#i', $url)) {
                $url = '';
            }
            if ($url !== '' && SecurityValidator::containsAnyInjection($url)) {
                $type = SecurityValidator::getInjectionType($url);
                throw new \RuntimeException("Material URL contains {$type} injection and is not allowed.");
            }
            return ['name' => $mat['name'] ?? '', 'url' => $url];
        }, (array) ($fields['materials'] ?? []));

        $payload = [
            'course_outcome_id'   => $fields['course_outcome_id'] ?? null,
            'learning_outcomes'   => $fields['learning_outcomes'] ?? '',
            'assessment_task'     => $fields['assessment_task'] ?? '',
            'topic'               => $fields['topic'] ?? '',
            'teaching_activities' => $fields['teaching_activities'] ?? '',
            'references'          => $fields['references'] ?? [['text' => '']],
            'materials'           => $materials ?: [['name' => '', 'url' => '']],
        ];

        // [1] Patch in-place — weekInputs already has the new data, no reload needed.
        $this->weekInputs['w' . $weekNo] = $payload;

        try {
            $changed = app(WeekContentService::class)->save(
                $this->syllabusId,
                $this->activeComponent,
                $this->weekInputs,
                $this->lockedWeeks,
                $weekNo
            );
        } catch (\RuntimeException $e) {
            $this->dispatch('lw-toast', type: 'error', message: $e->getMessage());
            $this->dispatch('week-modal-save-failed');
            return;
        }

        // [4] Skip re-render when nothing changed.
        if (! $changed) {
            $this->skipRender();
            $this->dispatch('week-modal-saved');
            return;
        }

        $this->dispatch('lw-toast', type: 'success', message: "Week {$weekNo} saved.");
        $this->dispatch('week-modal-saved');
    }

    // Auto-save triggered when a week accordion collapses.
    public function saveWeek(int $weekNo): void
    {
        if ($weekNo <= 0 || isset($this->lockedWeeks[$weekNo])) {
            return;
        }

        if (! isset($this->weekInputs['w' . $weekNo])) {
            return;
        }

        if ($weekNo !== 1 && empty($this->weekInputs['w' . $weekNo]['course_outcome_id'] ?? null)) {
            return;
        }

        try {
            $changed = app(WeekContentService::class)->save(
                $this->syllabusId,
                $this->activeComponent,
                $this->weekInputs,
                $this->lockedWeeks,
                $weekNo
            );
        } catch (\RuntimeException $e) {
            $this->dispatch('lw-toast', type: 'error', message: $e->getMessage());
            return;
        }

        // [4] Skip re-render when nothing changed.
        if (! $changed) {
            $this->skipRender();
            return;
        }

        $this->dispatch('lw-toast', type: 'success', message: "Week {$weekNo} saved.");
    }

    public function saveAllWeeklyEntries(): void
    {
        foreach ($this->weekInputs as $key => $input) {
            $wn = (int) str_replace('w', '', $key);
            if ($wn === 1 || isset($this->lockedWeeks[$wn])) {
                continue;
            }
            if (empty($input['course_outcome_id'] ?? null)) {
                $this->dispatch('lw-toast', type: 'error',
                    message: "Please fill in all weeks to use Save All. You can save individual weeks instead.");
                $this->dispatch('syllabus-save-finished');
                return;
            }
        }

        try {
            $changed = app(WeekContentService::class)->save(
                $this->syllabusId,
                $this->activeComponent,
                $this->weekInputs,
                $this->lockedWeeks
            );
        } catch (\RuntimeException $e) {
            $this->dispatch('lw-toast', type: 'error', message: $e->getMessage());
            $this->dispatch('syllabus-save-finished');
            return;
        }

        // Full save — reload so the accordion reflects any DB-side normalisation.
        $this->loadData();

        if ($changed) {
            $this->dispatch('lw-toast', type: 'success', message: 'All weeks saved.');
        }

        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
        $this->dispatch('syllabus-save-finished');
    }

    // Reset one editable week.
    public function resetWeek(int $weekNo): void
    {
        $blank = app(WeekContentService::class)->reset(
            $this->syllabusId,
            $this->activeComponent,
            $weekNo,
            $this->lockedWeeks
        );

        if ($blank === null) {
            return;
        }

        $this->weekInputs['w' . $weekNo] = $blank;
        $this->dispatch('lw-toast', type: 'success', message: "Week {$weekNo} reset.");
    }

    // ── Resource mutations (pure in-memory) ───────────────────────────────────

    public function addReference(int $weekNo): void
    {
        $this->weekInputs = app(WeekResourceService::class)
            ->addReference($this->weekInputs, $weekNo, $this->lockedWeeks);
    }

    public function removeReference(int $weekNo, int $index): void
    {
        $this->weekInputs = app(WeekResourceService::class)
            ->removeReference($this->weekInputs, $weekNo, $index, $this->lockedWeeks);
    }

    public function addMaterial(int $weekNo): void
    {
        $this->weekInputs = app(WeekResourceService::class)
            ->addMaterial($this->weekInputs, $weekNo, $this->lockedWeeks);
    }

    public function removeMaterial(int $weekNo, int $index): void
    {
        $this->weekInputs = app(WeekResourceService::class)
            ->removeMaterial($this->weekInputs, $weekNo, $index, $this->lockedWeeks);
    }

    // ── LEC / LAB switching ───────────────────────────────────────────────────

    public function setComponentType(string $type): void
    {
        $type = strtoupper($type);

        if (! in_array($type, ['LEC', 'LAB'], true)) {
            return;
        }
        if ($type === 'LAB' && ! isset($this->courseComponents['LAB'])) {
            return;
        }
        if ($type === $this->activeComponent) {
            return;
        }

        app(WeekContentService::class)->save(
            $this->syllabusId,
            $this->activeComponent,
            $this->weekInputs,
            $this->lockedWeeks
        );

        $this->activeComponent = $type;

        $this->syllabusWeeks = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        $this->weekInputs = app(WeekContentService::class)->populateInputs(
            $this->syllabusId,
            $this->activeComponent,
            $this->syllabusWeeks
        );
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function freshSyllabus(): ?Syllabus
    {
        return Syllabus::with('academicCalendar', 'courseOutcomes')->find($this->syllabusId);
    }

    private function reloadCourseComponents(): void
    {
        $this->courseComponents = CourseComponent::with('schedules')
            ->where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('type')
            ->map(fn ($c) => array_merge($c->toArray(), [
                'schedules' => $c->schedules->map(fn ($s) => ['day' => $s->day, 'time' => $s->time])->values()->all(),
            ]))
            ->toArray();
    }

    // Full data reload — called on mount, step-changed, calendar-updated,
    // and after bulk operations (generate, reset, save-all).
    // NOT called on single-week modal saves [1].
    private function loadData(): void
    {
        $syllabus = $this->freshSyllabus();
        if (! $syllabus) {
            return;
        }

        $this->academic_calendar_id = $syllabus->academic_calendar_id
            ? (int) $syllabus->academic_calendar_id
            : null;

        $this->reloadCourseComponents();

        $this->activeComponent = isset($this->courseComponents['LEC']) ? 'LEC'
            : (isset($this->courseComponents['LAB']) ? 'LAB' : 'LEC');

        $this->courseOutcomes = $syllabus->courseOutcomes
            ->sortBy('co_code')
            ->map(fn ($co) => [
                'id'          => (int) $co->id,
                'co_code'     => $co->co_code,
                'description' => $co->description,
            ])
            ->values()
            ->all();

        $this->syllabusWeeks  = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();
        $this->weeksGenerated = $this->syllabusWeeks->isNotEmpty();

        $lock = app(WeekLockService::class)->computeLockedWeeks($syllabus, $this->syllabusWeeks);
        $this->lockedWeeks = $lock['lockedWeeks'];
        $this->weekEvents  = $lock['weekEvents'];

        $this->weekInputs = app(WeekContentService::class)->populateInputs(
            $this->syllabusId,
            $this->activeComponent,
            $this->syllabusWeeks
        );

        $this->isLoaded = true;
    }
}
