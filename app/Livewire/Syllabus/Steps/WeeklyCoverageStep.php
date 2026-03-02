<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\AcademicCalendarEvent;
use App\Models\OnlineMaterial;
use App\Models\Reference;
use App\Models\Syllabus;
use App\Models\SyllabusWeek;
use App\Models\WeekContent;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class WeeklyCoverageStep extends Component
{
    // ── Public state (Livewire snapshots these between requests) ──────────────

    public int    $syllabusId;
    public ?int   $academic_calendar_id = null;
    public bool   $weeksGenerated       = false;
    public array  $courseComponents     = [];  // ['LEC' => [...], 'LAB' => [...]]
    public string $activeComponent      = 'LEC';
    public array  $courseOutcomes       = [];  // [['id'=>1,'co_code'=>'CO1','description'=>'...']]

    /**
     * Which weeks are locked and why.
     *
     * Populated by reloadWeekMeta(). A week is locked when the academic calendar
     * has an event of type 'exam' or 'non_teaching' during that week.
     *
     * Format:  [ week_no => 'exam' | 'non_teaching' ]
     * Example: [ 4 => 'exam', 7 => 'non_teaching' ]
     *
     * No database column needed — computed fresh every time loadData() runs.
     */
    public array $lockedWeeks = [];

    /**
     * Calendar events per week, shown as badges in the accordion header.
     *
     * Format:  [ week_no => [ ['name'=>'...', 'type'=>'...', 'date_display'=>'...'], ... ] ]
     */
    public array $weekEvents = [];

    /**
     * The editable form data for each week.
     *
     * IMPORTANT — key format is 'w1', 'w2', etc. (NOT plain integers like 1, 2).
     *
     * Why the 'w' prefix?
     * PHP silently converts any numeric string array key to an integer.
     * Livewire serialises $weekInputs to JSON and back between requests.
     * Without the prefix: PHP sees key "1" → stores as int 1 → JSON encodes as
     * {"1":{...}} → PHP decodes back as int 1. Then your code looks for "w1" and
     * finds nothing. The 'w' makes the key genuinely non-numeric so it survives
     * the JSON round-trip unchanged.
     *
     * CRITICAL RULE: Only call populateWeekInputs() (which fills this array)
     * from loadData() and setComponentType(). NEVER call it from save actions
     * or blur events — that would overwrite what the user is currently typing
     * with stale database values.
     *
     * Each entry looks like:
     * [
     *   'course_outcome_id'   => int|null,
     *   'learning_outcomes'   => string,
     *   'assessment_task'     => string,
     *   'topic'               => string,
     *   'teaching_activities' => string,
     *   'references'          => [ ['text' => ''], ... ],
     *   'materials'           => [ ['name' => '', 'url' => ''], ... ],
     * ]
     *
     * CRITICAL RULE: loadData() / populateWeekInputs() must ONLY run on mount()
     * and explicit generate/regenerate/component-switch operations.
     * NEVER on blur events, save actions, or exam assignment.
     * Violation causes: user types → blur fires → Livewire request → loadData() overwrites
     * weekInputs with DB values → Save sees old values not user's edits.
     */
    public array $weekInputs = [];

    /**
     * Weeks auto-locked by calendar events.
     *
     * A week is locked when it contains an AcademicCalendarEvent with
     * type 'exam' or 'non_teaching'. The week is still shown in the list
     * but all its inputs are disabled and saves are skipped (front + back).
     *
     * Format: [ week_no => lock_type ]  e.g. [ 4 => 'exam', 7 => 'non_teaching' ]
     * Computed at runtime — no extra DB column needed.
     */
    // public array $lockedWeeks = [];

    // /** Events as plain arrays per week_no — no Eloquent models in public properties. */
    // public array $weekEvents = [];

    /** Rebuilt every request from DB. Never serialised by Livewire. */
    protected Collection $syllabusWeeks;

    // ── Boot ──────────────────────────────────────────────────────────────────

    public function boot(): void
    {
        // Initialize the protected Collection so it's never null before render()
        $this->syllabusWeeks = collect();
    }

    // ── Mount (runs once when this component first loads) ─────────────────────

    /**
     * Called ONCE on fresh page load (or when wizard :key changes).
     * This is the ONLY place loadData() should be called in normal flow.
     */
    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        // Always re-fetch weeks from DB on render so the list stays up to date
        // after generation/regeneration without needing a full loadData() call.
        $this->syllabusWeeks = SyllabusWeek::query()
            ->where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        $this->weeksGenerated = $this->syllabusWeeks->isNotEmpty();

        return view('livewire.syllabus.steps.weekly-coverage', [
            'syllabusWeeks' => $this->syllabusWeeks,
            'syllabus'      => $this->freshSyllabus(),
        ]);
    }

    // ── Livewire event listeners ───────────────────────────────────────────────

    /**
     * Wizard dispatches this when navigating TO this step.
     * Because the wizard uses :key on child components, this step is fully
     * remounted (mount() runs) on every tab activation — so this listener
     * is just a safety net for cases where :key doesn't force a remount.
     */
    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'weekly_coverage') {
            $this->loadData();
        }
    }

    /** Fired when the user saves a new academic calendar in the previous step. */
    #[On('syllabus-calendar-updated')]
    public function onCalendarUpdated(): void
    {
        $this->loadData();
    }

    /**
     * Wizard fires this just before navigating AWAY from this step.
     *
     * We save whatever is in $weekInputs right now.
     * We must NOT call loadData() here — it would overwrite the user's typed
     * values with whatever is currently in the database.
     */
    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'weekly_coverage') {
            return;
        }
        $this->saveWeeklyEntries();
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    // ── Public actions ────────────────────────────────────────────────────────

    /** Generate weeks for the first time from the academic calendar. */
    public function generateWeeklyCoverage(): void
    {
        $syllabus = $this->freshSyllabus();

        if (! $syllabus || ! $syllabus->academic_calendar_id) {
            $this->dispatch('lw-toast', type: 'error', message: 'Select an academic calendar first.');
            return;
        }

        // if academic calendar events type == exam or non-teaching, disabled the weeks in weekly coverage, 
        // it means it will create a week but the users/faculty cant input in that week

        // Reload components fresh — needed by ensureWeeksGenerated()
        $this->reloadCourseComponents();
        $this->academic_calendar_id = (int) $syllabus->academic_calendar_id;
        $this->createWeekRows($syllabus);

        // Full reload here is correct — we just created new rows and need
        // to populate $weekInputs from the fresh DB data.
        $this->loadData();

        $this->dispatch('lw-toast', type: 'success', message: 'Weekly coverage generated.');
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    /** Delete all existing weeks and recreate them from the calendar. */
    public function regenerateWeeks(): void
    {
        $syllabus = $this->freshSyllabus();
        if (! $syllabus) {
            return;
        }

        $this->reloadCourseComponents();

        // Delete existing weeks and all their related data
        $weekIds = SyllabusWeek::where('syllabus_id', $syllabus->id)->pluck('id')->all();
        if ($weekIds) {
            WeekContent::whereIn('syllabus_week_id', $weekIds)->delete();
            Reference::where('syllabus_id', $this->syllabusId)->whereIn('syllabus_week_id', $weekIds)->delete();
            OnlineMaterial::where('syllabus_id', $this->syllabusId)->whereIn('syllabus_week_id', $weekIds)->delete();
            SyllabusWeek::whereIn('id', $weekIds)->delete();
        }

        $this->weekInputs  = [];
        $this->lockedWeeks = [];
        $this->weekEvents  = [];

        $this->createWeekRows($syllabus);
        $this->loadData();

        $this->dispatch('lw-toast', type: 'success', message: 'Weeks regenerated.');
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    /**
     * Save a single week when the user clicks "Save Week N".
     *
     * wire:model.lazy has already synced the $weekInputs values by the time
     * this method is called (the input blurred before the button was clicked).
     * DO NOT call loadData() here.
     */
    public function saveWeek(int $weekNo): void
    {
        if ($weekNo <= 0 || isset($this->lockedWeeks[$weekNo])) {
            return;
        }

        $this->persistWeek($weekNo);
        $this->dispatch('lw-toast', type: 'success', message: "Week {$weekNo} saved.");
    }

    /** Save every week at once. */
    public function saveAllWeeklyEntries(): void
    {
        $this->saveWeeklyEntries();
        $this->dispatch('lw-toast', type: 'success', message: 'All weeks saved.');
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    /** Add a blank reference row to the given week. */
    public function addReference(int $weekNo): void
    {
        if (isset($this->lockedWeeks[$weekNo])) {
            return;
        }
        $this->weekInputs['w' . $weekNo]['references'][] = ['text' => ''];
    }

    /** Remove a reference row by its index. */
    public function removeReference(int $weekNo, int $index): void
    {
        if (isset($this->lockedWeeks[$weekNo])) {
            return;
        }
        $key = 'w' . $weekNo;
        if (! isset($this->weekInputs[$key]['references'][$index])) {
            return;
        }
        array_splice($this->weekInputs[$key]['references'], $index, 1);
        // Keep at least one blank row so the section doesn't disappear
        if (empty($this->weekInputs[$key]['references'])) {
            $this->weekInputs[$key]['references'] = [['text' => '']];
        }
    }

    /** Add a blank online material row to the given week. */
    public function addMaterial(int $weekNo): void
    {
        if (isset($this->lockedWeeks[$weekNo])) {
            return;
        }
        $this->weekInputs['w' . $weekNo]['materials'][] = ['name' => '', 'url' => ''];
    }

    /** Remove an online material row by its index. */
    public function removeMaterial(int $weekNo, int $index): void
    {
        if (isset($this->lockedWeeks[$weekNo])) {
            return;
        }
        $key = 'w' . $weekNo;
        if (! isset($this->weekInputs[$key]['materials'][$index])) {
            return;
        }
        array_splice($this->weekInputs[$key]['materials'], $index, 1);
        if (empty($this->weekInputs[$key]['materials'])) {
            $this->weekInputs[$key]['materials'] = [['name' => '', 'url' => '']];
        }
    }

    /**
     * Switch between LEC and LAB tabs.
     *
     * Everything happens in ONE Livewire round-trip:
     * 1. Save the current component's data to DB silently.
     * 2. Change $activeComponent.
     * 3. Re-populate $weekInputs with the other component's DB data.
     *
     * The blade then renders with the new data — no extra request needed.
     */
    public function setComponentType(string $type): void
    {
        $type = strtoupper($type);

        // Guard: only allow valid types
        if (! in_array($type, ['LEC', 'LAB'], true)) {
            return;
        }
        // Guard: don't switch to a component that doesn't exist for this syllabus
        if ($type === 'LAB' && ! isset($this->courseComponents['LAB'])) {
            return;
        }
        // Guard: no-op if already on this tab
        if ($type === $this->activeComponent) {
            return;
        }

        // Step 1 — persist current component silently before switching
        $this->saveWeeklyEntries();

        // Step 2 — switch
        $this->activeComponent = $type;

        // Step 3 — load new component's data into the form
        $this->syllabusWeeks = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();
        $this->populateWeekInputs();
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Always query the Syllabus fresh with its relations.
     *
     * Livewire's rehydrated model strips eager-loaded relations, so
     * $this->syllabus->academicCalendar would be null after a round-trip.
     * Always use this method instead.
     */
    private function freshSyllabus(): ?Syllabus
    {
        return Syllabus::with('academicCalendar', 'courseOutcomes')->find($this->syllabusId);
    }

    /** Reload course components (LEC/LAB) from the database. */
    private function reloadCourseComponents(): void
    {
        $this->courseComponents = \App\Models\CourseComponent::where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('type')
            ->toArray();
    }

    // ── Private: master data loader ───────────────────────────────────────────

    /**
     * Load everything this step needs from the database.
     *
     * Call this ONLY from:
     *  - mount()
     *  - generateWeeklyCoverage() / regenerateWeeks()
     *  - onStepChanged() / onCalendarUpdated()
     *
     * NEVER call from save actions, blur handlers, or setComponentType() directly.
     */
    private function loadData(): void
    {
        $syllabus = $this->freshSyllabus();
        if (! $syllabus) {
            return;
        }

        // Sync the calendar ID we're working with
        $this->academic_calendar_id = $syllabus->academic_calendar_id
            ? (int) $syllabus->academic_calendar_id
            : null;

        // Load course components (LEC / LAB)
        $this->reloadCourseComponents();

        // Default to LEC if available, otherwise LAB
        $this->activeComponent = isset($this->courseComponents['LEC']) ? 'LEC'
            : (isset($this->courseComponents['LAB']) ? 'LAB' : 'LEC');

        // Load course outcomes for the CO dropdown in each week form
        $this->courseOutcomes = $syllabus->courseOutcomes
            ->sortBy('co_code')
            ->map(fn ($co) => [
                'id'          => (int) $co->id,
                'co_code'     => $co->co_code,
                'description' => $co->description,
            ])
            ->values()
            ->all();

        // Load the week rows
        $this->syllabusWeeks  = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();
        $this->weeksGenerated = $this->syllabusWeeks->isNotEmpty();

        // Figure out which weeks are locked and gather their calendar events
        $this->computeLockedWeeks($syllabus);

        // Fill the editable form fields from DB (safe here because this is loadData())
        $this->populateWeekInputs();
    }

    // ── Private: locked week computation ──────────────────────────────────────

    /**
     * Determine which weeks are locked based on calendar events.
     *
     * A week is "locked" if it overlaps with an event of type 'exam'
     * or 'non_teaching' in the academic calendar.
     *
     * For exam-locked weeks we also:
     *  1. Count how many exam weeks we've seen so far (1st, 2nd, Final)
     *  2. Auto-write that label ("1st Term Exam" / "2nd Term Exam" / "Final Term Exam")
     *     into the WeekContent.assessment_task column so it flows through to
     *     Course Evaluation automatically.
     */
    private function computeLockedWeeks(Syllabus $syllabus): void
    {
        $this->weekEvents  = [];
        $this->lockedWeeks = [];

        if (! $syllabus->academic_calendar_id) {
            return;
        }

        // Load all calendar events for this syllabus's calendar, oldest first
        $allEvents = AcademicCalendarEvent::where('academic_calendar_id', $syllabus->academic_calendar_id)
            ->orderBy('date')
            ->get();

        // We'll count exam weeks in order so we can label them 1st / 2nd / Final
        $examTermLabels = ['1st Term', '2nd Term', 'Final Term'];
        $examsSeen      = 0; // how many exam-locked weeks we've encountered so far

        foreach ($this->syllabusWeeks as $week) {
            $weekStart = Carbon::parse($week->start_date);
            $weekEnd   = Carbon::parse($week->end_date);

            // Find all calendar events that fall within this week
            $eventsThisWeek = $allEvents->filter(
                fn ($event) => Carbon::parse($event->date)->between($weekStart, $weekEnd)
            );

            // Store events as plain arrays (no Eloquent models in public properties)
            $this->weekEvents[$week->week_no] = $eventsThisWeek->map(fn ($event) => [
                'name'         => $event->name,
                'type'         => $event->type,
                'date_display' => Carbon::parse($event->date)->format('M d'),
            ])->values()->all();

            // Check if any event in this week triggers a lock
            $lockingEvent = $eventsThisWeek->first(
                fn ($event) => in_array($event->type, ['exam', 'non_teaching'], true)
            );

            if ($lockingEvent) {
                $this->lockedWeeks[$week->week_no] = $lockingEvent->type;

                // For exam weeks: auto-fill the assessment_task with the term label
                if ($lockingEvent->type === 'exam') {
                    // Pick the label: 1st, 2nd, Final (cap at 3)
                    $termLabel = $examTermLabels[min($examsSeen, 2)];
                    $examsSeen++;

                    // Build component-specific labels
                    $lecLabel = $termLabel . ' Exam';        // "1st Term Exam"
                    $labLabel = $termLabel . ' Practical Exam'; // "1st Term Practical Exam"

                    // Write the label into WeekContent for both LEC and LAB rows
                    // so the Course Evaluation step can read them directly
                    WeekContent::where('syllabus_week_id', $week->id)
                        ->where('component_type', 'LEC')
                        ->update(['assessment_task' => $lecLabel]);

                    WeekContent::where('syllabus_week_id', $week->id)
                        ->where('component_type', 'LAB')
                        ->update(['assessment_task' => $labLabel]);

                    // Also update the in-memory $weekInputs so the blade shows it
                    // without needing an extra DB read (the accordion body shows
                    // the locked banner anyway, but this keeps the data consistent)
                    foreach (['LEC', 'LAB'] as $comp) {
                        $label = $comp === 'LEC' ? $lecLabel : $labLabel;
                        $this->weekInputs['w' . $week->week_no]['assessment_task'] = $label;
                    }
                }
            }
        }
    }

    // ── Private: populate form inputs from DB ─────────────────────────────────

    /**
     * Read WeekContent rows from the database and fill $weekInputs.
     *
     * Only call this from loadData() and setComponentType().
     * Calling it from save/blur would overwrite the user's in-progress edits.
     */
    private function populateWeekInputs(): void
    {
        if ($this->syllabusWeeks->isEmpty()) {
            $this->weekInputs = [];
            return;
        }

        $weekIds = $this->syllabusWeeks->pluck('id')->all();

        // Load the WeekContent row for each week (one per component type per week)
        $weekContents = WeekContent::whereIn('syllabus_week_id', $weekIds)
            ->where('component_type', $this->activeComponent)
            ->get()
            ->keyBy('syllabus_week_id'); // quick lookup by week ID

        // Load all references grouped by week ID
        $allRefs = Reference::where('syllabus_id', $this->syllabusId)
            ->whereIn('syllabus_week_id', $weekIds)
            ->orderBy('id')
            ->get()
            ->groupBy('syllabus_week_id');

        // Load all online materials grouped by week ID
        $allMats = OnlineMaterial::where('syllabus_id', $this->syllabusId)
            ->whereIn('syllabus_week_id', $weekIds)
            ->orderBy('id')
            ->get()
            ->groupBy('syllabus_week_id');

        // Helper: convert null / "N/A" sentinel to blank string for clean display
        $clean = fn (?string $value): string => ($value === null || $value === 'N/A') ? '' : $value;

        $inputs = [];

        foreach ($this->syllabusWeeks as $week) {
            $content = $weekContents->get($week->id); // may be null if not yet created

            // Map saved references to array rows; show one blank row if none exist
            $refs = $allRefs->has($week->id)
                ? $allRefs->get($week->id)->map(fn ($r) => ['text' => $clean($r->reference_text)])->values()->all()
                : [['text' => '']];

            // Map saved materials to array rows; show one blank row if none exist
            $mats = $allMats->has($week->id)
                ? $allMats->get($week->id)->map(fn ($m) => ['name' => $clean($m->material_name), 'url' => $m->url ?? ''])->values()->all()
                : [['name' => '', 'url' => '']];

            $inputs['w' . $week->week_no] = [
                'course_outcome_id'   => $content?->course_outcome_id ?? null,
                'learning_outcomes'   => $clean($content?->learning_outcomes),
                'assessment_task'     => $clean($content?->assessment_task),
                'topic'               => $clean($content?->topics),
                'teaching_activities' => $clean($content?->tla),
                'references'          => $refs,
                'materials'           => $mats,
            ];
        }

        $this->weekInputs = $inputs;
    }

    // ── Private: create SyllabusWeek + WeekContent rows ───────────────────────

    /**
     * Create one SyllabusWeek row per week in the calendar range, plus
     * a WeekContent row for each component type (LEC and/or LAB).
     *
     * Only runs if no weeks exist yet (idempotent guard at the top).
     */
    private function createWeekRows(Syllabus $syllabus): void
    {
        // Don't create if weeks already exist
        if (SyllabusWeek::where('syllabus_id', $syllabus->id)->exists()) {
            return;
        }

        $calendar = $syllabus->academicCalendar;
        if (! $calendar || ! $calendar->start_date || ! $calendar->end_date) {
            $this->dispatch('lw-toast', type: 'error', message: 'Academic calendar has no start/end date.');
            return;
        }

        $hasLEC = isset($this->courseComponents['LEC']);
        $hasLAB = isset($this->courseComponents['LAB']);

        if (! $hasLEC && ! $hasLAB) {
            $this->dispatch('lw-toast', type: 'error', message: 'Complete the Course Components step first.');
            return;
        }

        $start  = Carbon::parse($calendar->start_date)->startOfDay();
        $end    = Carbon::parse($calendar->end_date)->startOfDay();
        $weekNo = 1;
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $weekStart = $cursor->copy();
            $weekEnd   = $cursor->copy()->addDays(6);

            // Don't overshoot the end date
            if ($weekEnd->gt($end)) {
                $weekEnd = $end->copy();
            }

            // Create the week row
            $syllabusWeek = SyllabusWeek::create([
                'syllabus_id' => $syllabus->id,
                'week_no'     => $weekNo,
                'start_date'  => $weekStart->toDateString(),
                'end_date'    => $weekEnd->toDateString(),
                'is_exam_week' => false,
            ]);

            // Create a WeekContent row for each component type
            if ($hasLEC) {
                WeekContent::create([
                    'syllabus_week_id' => $syllabusWeek->id,
                    'component_type'   => 'LEC',
                    'learning_outcomes' => '',
                    'assessment_task'   => '',
                    'topics'            => '',
                    'tla'               => '',
                ]);
            }
            if ($hasLAB) {
                WeekContent::create([
                    'syllabus_week_id' => $syllabusWeek->id,
                    'component_type'   => 'LAB',
                    'learning_outcomes' => '',
                    'assessment_task'   => '',
                    'topics'            => '',
                    'tla'               => '',
                ]);
            }

            $weekNo++;
            $cursor = $weekEnd->copy()->addDay();
        }

        Log::info('[WeeklyCoverageStep] Weeks created', [
            'syllabusId' => $syllabus->id,
            'total'      => $weekNo - 1,
        ]);
    }

    // ── Private: persist week data to DB ─────────────────────────────────────

    /**
     * Save all weeks (or just one week if $onlyWeekNo is provided).
     *
     * Called by: saveWeek(), saveAllWeeklyEntries(), setComponentType(),
     *            onSaveRequested() (when wizard navigates away).
     *
     * DO NOT call loadData() after this — it would overwrite user's typed values.
     */
    private function saveWeeklyEntries(?int $onlyWeekNo = null): void
    {
        $weeks = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        if ($weeks->isEmpty()) {
            return;
        }

        foreach ($weeks as $week) {
            // Skip weeks we're not saving right now
            if ($onlyWeekNo !== null && (int) $week->week_no !== $onlyWeekNo) {
                continue;
            }

            // Server-side safety: never save to locked weeks
            if (isset($this->lockedWeeks[$week->week_no])) {
                continue;
            }

            $key     = 'w' . $week->week_no;
            $payload = $this->weekInputs[$key] ?? null;

            if ($payload === null) {
                // No form data for this week — skip it
                continue;
            }

            // Normalise course_outcome_id — treat blank string as null
            $courseOutcomeId = (isset($payload['course_outcome_id'])
                && $payload['course_outcome_id'] !== ''
                && $payload['course_outcome_id'] !== null)
                ? (int) $payload['course_outcome_id']
                : null;

            // Save the main content row
            WeekContent::updateOrCreate(
                [
                    'syllabus_week_id' => $week->id,
                    'component_type'   => $this->activeComponent,
                ],
                [
                    'course_outcome_id'  => $courseOutcomeId,
                    'learning_outcomes'  => trim((string) ($payload['learning_outcomes']   ?? '')),
                    'assessment_task'    => trim((string) ($payload['assessment_task']     ?? '')),
                    'topics'             => trim((string) ($payload['topic']               ?? '')),
                    'tla'                => trim((string) ($payload['teaching_activities'] ?? '')),
                ]
            );

            // Replace all references for this week (delete + re-insert)
            Reference::where('syllabus_id', $this->syllabusId)
                ->where('syllabus_week_id', $week->id)
                ->delete();

            foreach ((array) ($payload['references'] ?? []) as $ref) {
                $text = trim((string) ($ref['text'] ?? ''));
                if ($text !== '') {
                    Reference::create([
                        'syllabus_id'      => $this->syllabusId,
                        'syllabus_week_id' => $week->id,
                        'reference_text'   => $text,
                    ]);
                }
            }

            // Replace all online materials for this week (delete + re-insert)
            OnlineMaterial::where('syllabus_id', $this->syllabusId)
                ->where('syllabus_week_id', $week->id)
                ->delete();

            foreach ((array) ($payload['materials'] ?? []) as $mat) {
                $name = trim((string) ($mat['name'] ?? ''));
                $url  = trim((string) ($mat['url']  ?? ''));
                if ($name !== '' || $url !== '') {
                    OnlineMaterial::create([
                        'syllabus_id'      => $this->syllabusId,
                        'syllabus_week_id' => $week->id,
                        'material_name'    => $name ?: 'Online Material',
                        'url'              => $url,
                    ]);
                }
            }
        }
    }

    /** Alias used by saveWeek() for clarity — persists exactly one week. */
    private function persistWeek(int $weekNo): void
    {
        $this->saveWeeklyEntries($weekNo);
    }
}