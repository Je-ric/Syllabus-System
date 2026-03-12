<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\CourseComponent;
use App\Models\Syllabus;
use App\Models\SyllabusWeek;
use App\Services\Syllabus\WeekContentService;
use App\Services\Syllabus\WeekGenerationService;
use App\Services\Syllabus\WeekLockService;
use App\Services\Syllabus\WeekResourceService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * WeeklyCoverageStep
 *
 * Thin Livewire component — all business logic lives in four services:
 *
 *   WeekGenerationService  generate() / regenerate() / deleteAllWeeks()
 *   WeekLockService        computeLockedWeeks()
 *   WeekContentService     populateInputs() / save() / reset()
 *   WeekResourceService    addReference() / removeReference() / addMaterial() / removeMaterial()
 *
 * This class is responsible only for:
 *   • Holding reactive Livewire state ($weekInputs, $activeComponent, …)
 *   • Bridging wire:click calls to service methods
 *   • Dispatching toast / event notifications
 */
class WeeklyCoverageStep extends Component
{
    // ── Reactive state (serialised into Livewire snapshot) ────────────────────
    public int    $syllabusId;
    public ?int   $academic_calendar_id = null;
    public bool   $weeksGenerated       = false;
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
        $this->syllabusWeeks  = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();
        $this->weeksGenerated = $this->syllabusWeeks->isNotEmpty();

        return view('livewire.syllabus.steps.weekly-coverage', [
            'syllabusWeeks' => $this->syllabusWeeks,
            'syllabus'      => $this->freshSyllabus(),
        ]);
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'weekly_coverage') {
            $this->loadData();
        }
    }

    #[On('syllabus-calendar-updated')]
    public function onCalendarUpdated(): void
    {
        $this->loadData();
    }

    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'weekly_coverage') {
            return;
        }

        app(WeekContentService::class)->save(
            $this->syllabusId,
            $this->activeComponent,
            $this->weekInputs,
            $this->lockedWeeks
        );

        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    // ── Week generation ───────────────────────────────────────────────────────

    public function generateWeeklyCoverage(): void
    {
        $syllabus = $this->freshSyllabus();
        $this->reloadCourseComponents();
        $this->academic_calendar_id = $syllabus?->academic_calendar_id
            ? (int) $syllabus->academic_calendar_id
            : null;

        $ok = app(WeekGenerationService::class)->generate(
            $syllabus,
            $this->courseComponents,
            $this
        );

        if ($ok) {
            $this->loadData();
            $this->dispatch('lw-toast', type: 'success', message: 'Weekly coverage generated.');
            $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
        }
    }

    public function regenerateWeeks(): void
    {
        $this->reloadCourseComponents();

        app(WeekGenerationService::class)->regenerate(
            $this->freshSyllabus(),
            $this->courseComponents,
            $this
        );

        $this->weekInputs  = [];
        $this->lockedWeeks = [];
        $this->weekEvents  = [];

        $this->loadData();

        $this->dispatch('lw-toast', type: 'success', message: 'Weeks regenerated.');
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    // ── Week content ──────────────────────────────────────────────────────────

    /**
     * Auto-save triggered by the Alpine $watch when a week is collapsed.
     * Only shows a toast when something actually changed.
     */
    public function saveWeek(int $weekNo): void
    {
        if ($weekNo <= 0 || isset($this->lockedWeeks[$weekNo])) {
            return;
        }

        $changed = app(WeekContentService::class)->save(
            $this->syllabusId,
            $this->activeComponent,
            $this->weekInputs,
            $this->lockedWeeks,
            $weekNo
        );

        if ($changed) {
            $this->dispatch('lw-toast', type: 'success', message: "Week {$weekNo} saved.");
        }
    }

    public function saveAllWeeklyEntries(): void
    {
        $changed = app(WeekContentService::class)->save(
            $this->syllabusId,
            $this->activeComponent,
            $this->weekInputs,
            $this->lockedWeeks
        );

        if ($changed) {
            $this->dispatch('lw-toast', type: 'success', message: 'All weeks saved.');
        }

        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    /**
     * Reset one editable week — clears all content from DB and blanks the
     * form inputs in memory. Locked weeks are silently rejected.
     */
    public function resetWeek(int $weekNo): void
    {
        $blank = app(WeekContentService::class)->reset(
            $this->syllabusId,
            $this->activeComponent,
            $weekNo,
            $this->lockedWeeks
        );

        if ($blank === null) {
            return; // locked or week not found
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

        // Save current component before switching
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
        $this->courseComponents = CourseComponent::where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('type')
            ->toArray();
    }

    /**
     * Full data reload — called on mount, step-changed, and calendar-updated.
     * Never called from save paths to avoid overwriting in-flight edits.
     */
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

        // Compute locked weeks + write exam labels to DB
        $lock = app(WeekLockService::class)->computeLockedWeeks($syllabus, $this->syllabusWeeks);
        $this->lockedWeeks = $lock['lockedWeeks'];
        $this->weekEvents  = $lock['weekEvents'];

        // Populate form inputs from DB
        $this->weekInputs = app(WeekContentService::class)->populateInputs(
            $this->syllabusId,
            $this->activeComponent,
            $this->syllabusWeeks
        );
    }
}
