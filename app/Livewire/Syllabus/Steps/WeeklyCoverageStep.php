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
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class WeeklyCoverageStep extends Component
{
    // ── Persisted public state ────────────────────────────────────────────────
    public int     $syllabusId;
    public ?int    $academic_calendar_id = null;
    public bool    $weeksGenerated       = false;
    public array   $examWeeks            = [];
    public ?string $activeWeekTab        = null;
    public array   $courseComponents     = [];
    public string  $activeComponent      = 'LEC';
    public array   $courseOutcomes       = [];

    /**
     * FORM DATA — Livewire snapshots and restores this between requests.
     *
     * KEY FORMAT: 'w{week_no}' e.g. 'w1', 'w2', 'w8'
     *
     *
     * WHY NOT plain integers or (string) cast?
     * PHP silently casts ANY numeric string key to int: $arr[(string)'1'] === $arr[1]
     * Livewire's JSON round-trip does json_decode which produces STRING keys: {"w1":{...}}
     * With 'w' prefix, keys are never numeric → PHP never coerces → consistent across
     * mount (PHP array), snapshot (JSON), and re-hydration (PHP array from json_decode).
     * The 'w' prefix prevents PHP's silent numeric-string → int key coercion
     * which breaks the Livewire JSON snapshot round-trip.
     *
     * Per-week structure:
     * [
     *   'course_outcome_id'   => int|null,
     *   'learning_outcomes'   => string,
     *   'assessment_task'     => string,
     *   'topic'               => string,
     *   'teaching_activities' => string,
     *   'references'          => [['text' => string], ...],              // MULTIPLE
     *   'materials'           => [['name' => string, 'url' => string], ...], // MULTIPLE
     * ]
     *
     * CRITICAL RULE: loadData() / populateWeekInputs() must ONLY run on mount()
     * and explicit generate/regenerate/component-switch operations.
     * NEVER on blur events, save actions, or exam assignment.
     * Violation causes: user types → blur fires → Livewire request → loadData() overwrites
     * weekInputs with DB values → Save sees old values not user's edits.
     */
    public array $weekInputs = [];

    /** Events stored as plain arrays — no Eloquent models in public properties. */
    public array $weekEvents = [];

    /** Rebuilt every request from DB. Never serialised by Livewire. */
    protected Collection $syllabusWeeks;

    // ── Boot ─────────────────────────────────────────────────────────────────

    public function boot(): void
    {
        $this->syllabusWeeks = collect();
    }

    // ── Mount ─────────────────────────────────────────────────────────────────

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
        // Rebuild weeks every render (protected Collection not serialised)
        $this->syllabusWeeks = SyllabusWeek::query()
            ->where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        $this->weeksGenerated = $this->syllabusWeeks->isNotEmpty();

        // freshSyllabus() only needed for calendar dates in the header card
        $syllabus = $this->freshSyllabus();

        return view('livewire.syllabus.steps.weekly-coverage', [
            'syllabusWeeks' => $this->syllabusWeeks,
            'syllabus'      => $syllabus,
        ]);
    }

    // ── Event Listeners ───────────────────────────────────────────────────────

    /**
     * Wizard dispatches this when navigating TO this step.
     * Because the wizard uses :key on child components, this step is fully
     * remounted (mount() runs) on every tab activation — so this listener
     * is just a safety net for cases where :key doesn't force a remount.
     */
    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step !== 'weekly_coverage') {
            return;
        }
        $this->loadData();
    }

    #[On('syllabus-calendar-updated')]
    public function onCalendarUpdated(): void
    {
        $this->loadData();
    }

    /**
     * Wizard dispatches this before navigating AWAY.
     * At this point Livewire has already synced $weekInputs from the snapshot
     * (wire:model.lazy blurred all fields before the nav button was clicked).
     * We MUST NOT call loadData() here — doing so would overwrite $weekInputs
     * with DB values, discarding any unsaved edits.
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

    // ── Public Actions ────────────────────────────────────────────────────────

    public function generateWeeklyCoverage(): void
    {
        $syllabus = $this->freshSyllabus();

        if (! $syllabus || ! $syllabus->academic_calendar_id) {
            $this->dispatch('lw-toast', type: 'error', message: 'Select an academic calendar before generating weeks.');
            return;
        }

        // if academic calendar events type == exam or non-teaching, disabled the weeks in weekly coverage, 
        // it means it will create a week but the users/faculty cant input in that week

        // Reload components fresh — needed by ensureWeeksGenerated()
        $this->reloadCourseComponents();
        $this->academic_calendar_id = (int) $syllabus->academic_calendar_id;

        $this->ensureWeeksGenerated($syllabus);

        // Full reload is correct here: new rows were just created, DB is source of truth
        $this->loadData();

        $this->dispatch('lw-toast', type: 'success', message: 'Weekly coverage generated.');
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage', message: 'Weekly coverage generated.');
    }

    public function regenerateWeeks(): void
    {
        $syllabus = $this->freshSyllabus();
        if (! $syllabus) {
            return;
        }

        $this->reloadCourseComponents();

        // Explicit deletes — don't rely on DB cascade
        $weekIds = SyllabusWeek::where('syllabus_id', $syllabus->id)->pluck('id')->all();
        if ($weekIds) {
            WeekContent::whereIn('syllabus_week_id', $weekIds)->delete();
            Reference::where('syllabus_id', $this->syllabusId)->whereIn('syllabus_week_id', $weekIds)->delete();
            OnlineMaterial::where('syllabus_id', $this->syllabusId)->whereIn('syllabus_week_id', $weekIds)->delete();
            SyllabusWeek::whereIn('id', $weekIds)->delete();
        }

        $this->weekInputs = [];
        $this->ensureWeeksGenerated($syllabus);

        // Full reload: fresh rows from DB
        $this->loadData();

        $this->dispatch('lw-toast', type: 'success', message: 'Weeks regenerated.');
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage', message: 'Weeks regenerated.');
    }

    /**
     * Save a single week.
     * wire:model.lazy has synced $weekInputs before this button click fires.
     * DO NOT call loadData() — would overwrite user's typed values.
     */
    public function saveWeek(int $weekNo): void
    {
        if ($weekNo <= 0) {
            return;
        }

        Log::debug('[WeeklyCoverageStep] saveWeek', [
            'weekNo'    => $weekNo,
            'wKey'      => 'w' . $weekNo,
            'payload'   => $this->weekInputs['w' . $weekNo] ?? 'MISSING',
            'allKeys'   => array_keys($this->weekInputs),
            'component' => $this->activeComponent,
        ]);

        $this->saveWeeklyEntries($weekNo);
        $this->dispatch('lw-toast', type: 'success', message: "Week {$weekNo} saved.");
    }

    /**
     * Save all weeks.
     * DO NOT call loadData() after — would overwrite user's typed values.
     */
    public function saveAllWeeklyEntries(): void
    {
        $this->saveWeeklyEntries();
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage', message: 'All weekly entries saved.');
        $this->dispatch('lw-toast', type: 'success', message: 'All weeks saved.');
    }

    // ── Reference row management ──────────────────────────────────────────────

    public function addReference(int $weekNo): void
    {
        $wKey = 'w' . $weekNo;
        $this->weekInputs[$wKey]['references'][] = ['text' => ''];
    }

    public function removeReference(int $weekNo, int $index): void
    {
        $wKey = 'w' . $weekNo;
        if (! isset($this->weekInputs[$wKey]['references'][$index])) {
            return;
        }
        array_splice($this->weekInputs[$wKey]['references'], $index, 1);
        // Always keep at least one blank row so the section never disappears
        if (empty($this->weekInputs[$wKey]['references'])) {
            $this->weekInputs[$wKey]['references'] = [['text' => '']];
        }
    }

    // ── Material row management ───────────────────────────────────────────────

    public function addMaterial(int $weekNo): void
    {
        $wKey = 'w' . $weekNo;
        $this->weekInputs[$wKey]['materials'][] = ['name' => '', 'url' => ''];
    }

    public function removeMaterial(int $weekNo, int $index): void
    {
        $wKey = 'w' . $weekNo;
        if (! isset($this->weekInputs[$wKey]['materials'][$index])) {
            return;
        }
        array_splice($this->weekInputs[$wKey]['materials'], $index, 1);
        if (empty($this->weekInputs[$wKey]['materials'])) {
            $this->weekInputs[$wKey]['materials'] = [['name' => '', 'url' => '']];
        }
    }

    // ── Exam assignment ───────────────────────────────────────────────────────

    public function assignExamWeek(string $type, int $weekNo): void
    {
        if (! in_array($type, ['first_term', 'second_term', 'final_term'], true)) {
            return;
        }

        $syllabus = $this->freshSyllabus();
        if (! $syllabus) {
            return;
        }

        SyllabusWeek::where('syllabus_id', $syllabus->id)->where('exam_type', $type)
            ->update(['exam_type' => null, 'is_exam_week' => false]);

        SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', $weekNo)
            ->update(['exam_type' => $type, 'is_exam_week' => true]);

        // Only reload metadata — DO NOT touch $weekInputs
        $this->reloadWeekMeta($syllabus);
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    public function clearExamWeek(string $type): void
    {
        if (! in_array($type, ['first_term', 'second_term', 'final_term'], true)) {
            return;
        }

        $syllabus = $this->freshSyllabus();
        if (! $syllabus) {
            return;
        }

        SyllabusWeek::where('syllabus_id', $syllabus->id)->where('exam_type', $type)
            ->update(['exam_type' => null, 'is_exam_week' => false]);

        // Only reload metadata — DO NOT touch $weekInputs
        $this->reloadWeekMeta($syllabus);
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    /**
     * Switch LEC ↔ LAB.
     *
     * Everything happens in ONE Livewire request:
     *  1. Persist current component's $weekInputs to DB (silent, no toast)
     *  2. Switch $activeComponent
     *  3. Repopulate $weekInputs from DB for the new component
     *
     * The blade re-renders with the new data immediately — no extra round-trip,
     * no loading state, no perceptible delay beyond a normal Livewire request.
     */
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
            return;   // already on this tab — no work needed
        }

        // 1. Save current unsaved edits silently
        $this->saveWeeklyEntries();

        // 2. Switch
        $this->activeComponent = $type;

        // 3. Reload form inputs for the new component from DB
        $this->syllabusWeeks = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')->get();
        $this->populateWeekInputs();
    }

    public function setActiveWeekTab(string $tab): void
    {
        if (str_starts_with($tab, 'week_') && (int) str_replace('week_', '', $tab) > 0) {
            $this->activeWeekTab = $tab;
        }
    }

    // ── Private: Helpers ──────────────────────────────────────────────────────

    /**
     * Always eager-loads relations — never trust Livewire's rehydrated model,
     * which strips eager-loaded relations (academicCalendar would be null).
     */
    private function freshSyllabus(): ?Syllabus
    {
        return Syllabus::query()
            ->with('academicCalendar', 'courseOutcomes')
            ->find($this->syllabusId);
    }

    private function reloadCourseComponents(): void
    {
        $this->courseComponents = \App\Models\CourseComponent::query()
            ->where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('type')
            ->toArray();
    }

    // ── Private: Data Loading ─────────────────────────────────────────────────

    /**
     * Master loader — runs on mount() and generate/regenerate only.
     *
     * !! NEVER call this from save actions, blur handlers, or exam assignment !!
     * Calling it there overwrites $weekInputs with DB values, destroying user edits.
     */
    private function loadData(): void
    {
        Log::info('[WeeklyCoverageStep] loadData', ['syllabusId' => $this->syllabusId]);

        $syllabus = $this->freshSyllabus();
        if (! $syllabus) {
            return;
        }

        $this->academic_calendar_id = $syllabus->academic_calendar_id
            ? (int) $syllabus->academic_calendar_id
            : null;

        $this->reloadCourseComponents();

        // Reset activeComponent to a valid choice (always LEC if available)
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

        $this->syllabusWeeks  = SyllabusWeek::query()
            ->where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        $this->weeksGenerated = $this->syllabusWeeks->isNotEmpty();

        $this->reloadWeekMeta($syllabus);

        // Safe to populate here: this only runs on mount() or explicit generation
        $this->populateWeekInputs();

        Log::info('[WeeklyCoverageStep] loadData done', [
            'calendarId' => $this->academic_calendar_id,
            'weeks'      => $this->syllabusWeeks->count(),
            'inputs'     => count($this->weekInputs),
            'keys'       => array_keys($this->weekInputs),
            'component'  => $this->activeComponent,
        ]);
    }

    /**
     * Reload exam map + events only.
     * Called after exam-assignment mutations — intentionally does NOT
     * touch $weekInputs so user's in-progress edits are preserved.
     */
    private function reloadWeekMeta(Syllabus $syllabus): void
    {
        if (! $syllabus->academic_calendar_id) {
            $this->examWeeks     = [];
            $this->weekEvents    = [];
            $this->activeWeekTab = null;
            return;
        }

        // Rebuild exam map
        $examWeeks = [];
        foreach ($this->syllabusWeeks as $week) {
            if ($week->exam_type) {
                $examWeeks[$week->exam_type] = $week->week_no;
            }
        }
        $this->examWeeks = $examWeeks;

        // Sync active tab
        if ($this->activeWeekTab) {
            $no = (int) str_replace('week_', '', (string) $this->activeWeekTab);
            if (! $this->syllabusWeeks->contains(fn ($w) => (int) $w->week_no === $no)) {
                $this->activeWeekTab = null;
            }
        }
        if (! $this->activeWeekTab && $this->syllabusWeeks->isNotEmpty()) {
            $this->activeWeekTab = 'week_' . $this->syllabusWeeks->first()->week_no;
        }

        // Events per week — plain arrays, no Eloquent models
        $allEvents = AcademicCalendarEvent::query()
            ->where('academic_calendar_id', $syllabus->academic_calendar_id)
            ->orderBy('date')
            ->get();

        $weekEvents = [];
        foreach ($this->syllabusWeeks as $week) {
            $weekEvents[$week->week_no] = $allEvents
                ->filter(fn ($e) => Carbon::parse($e->date)->between(
                    Carbon::parse($week->start_date),
                    Carbon::parse($week->end_date)
                ))
                ->map(fn ($e) => [
                    'name'         => $e->name,
                    'date_display' => Carbon::parse($e->date)->format('M d'),
                ])
                ->values()
                ->all();
        }
        $this->weekEvents = $weekEvents;
    }

    /**
     * Populate $weekInputs from DB.
     *
     * Only called from loadData() and setComponentType().
     * Never called from save/blur actions.
     *
     * 'N/A' sentinels are converted to '' so inputs appear blank rather than
     * showing the placeholder string. The DB gets '' back on save (stored as
     * empty string in WeekContent columns).
     */
    private function populateWeekInputs(): void
    {
        if ($this->syllabusWeeks->isEmpty()) {
            $this->weekInputs = [];
            return;
        }

        $weekIds = $this->syllabusWeeks->pluck('id')->all();

        // WeekContent: one row per (week_id, component_type) — keyBy is fine
        $weekContents = WeekContent::query()
            ->whereIn('syllabus_week_id', $weekIds)
            ->where('component_type', $this->activeComponent)
            ->get()
            ->keyBy('syllabus_week_id');

        // References: MULTIPLE per week — groupBy so we don't lose rows
        $allRefs = Reference::query()
            ->where('syllabus_id', $this->syllabusId)
            ->whereIn('syllabus_week_id', $weekIds)
            ->orderBy('id')
            ->get()
            ->groupBy('syllabus_week_id');

        // Materials: MULTIPLE per week — groupBy so we don't lose rows
        $allMats = OnlineMaterial::query()
            ->where('syllabus_id', $this->syllabusId)
            ->whereIn('syllabus_week_id', $weekIds)
            ->orderBy('id')
            ->get()
            ->groupBy('syllabus_week_id');

        $clean = static fn (?string $v): string =>
            ($v === null || $v === 'N/A') ? '' : $v;

        $inputs = [];
        foreach ($this->syllabusWeeks as $week) {
            $content = $weekContents->get($week->id);

            // Map saved refs to array rows; fall back to one blank row
            $refs = $allRefs->has($week->id)
                ? $allRefs->get($week->id)
                    ->map(fn ($r) => ['text' => $clean($r->reference_text)])
                    ->values()
                    ->all()
                : [['text' => '']];

            // Map saved materials to array rows; fall back to one blank row
            $mats = $allMats->has($week->id)
                ? $allMats->get($week->id)
                    ->map(fn ($m) => ['name' => $clean($m->material_name), 'url' => $m->url ?? ''])
                    ->values()
                    ->all()
                : [['name' => '', 'url' => '']];

            // 'w' prefix prevents PHP's numeric-string → int key coercion
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

        Log::debug('[WeeklyCoverageStep] populateWeekInputs', [
            'component' => $this->activeComponent,
            'keys'      => array_keys($inputs),
            'sample'    => count($inputs) ? $inputs[array_key_first($inputs)] : null,
        ]);
    }

    // ── Private: Week Generation ──────────────────────────────────────────────

    private function ensureWeeksGenerated(Syllabus $syllabus): void
    {
        if (SyllabusWeek::query()->where('syllabus_id', $syllabus->id)->exists()) {
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
            $this->dispatch('lw-toast', type: 'error', message: 'No LEC/LAB components found. Complete Course Components step first.');
            return;
        }

        $start  = Carbon::parse($calendar->start_date)->startOfDay();
        $end    = Carbon::parse($calendar->end_date)->startOfDay();
        $weekNo = 1;
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $weekStart = $cursor->copy();
            $weekEnd   = $cursor->copy()->addDays(6);
            if ($weekEnd->gt($end)) {
                $weekEnd = $end->copy();
            }

            $sw = SyllabusWeek::create([
                'syllabus_id'  => $syllabus->id,
                'week_no'      => $weekNo,
                'start_date'   => $weekStart->toDateString(),
                'end_date'     => $weekEnd->toDateString(),
                'is_exam_week' => false,
            ]);

            if ($hasLEC) {
                WeekContent::create([
                    'syllabus_week_id'  => $sw->id,
                    'component_type'    => 'LEC',
                    'learning_outcomes' => '',
                    'assessment_task'   => '',
                    'topics'            => '',
                    'tla'               => '',
                ]);
            }

            if ($hasLAB) {
                WeekContent::create([
                    'syllabus_week_id'  => $sw->id,
                    'component_type'    => 'LAB',
                    'learning_outcomes' => '',
                    'assessment_task'   => '',
                    'topics'            => '',
                    'tla'               => '',
                ]);
            }

            $weekNo++;
            $cursor = $weekEnd->copy()->addDay();
        }

        Log::info('[WeeklyCoverageStep] ensureWeeksGenerated', [
            'syllabusId' => $syllabus->id,
            'weeks'      => $weekNo - 1,
        ]);
    }

    // ── Private: Persistence ──────────────────────────────────────────────────

    private function saveWeeklyEntries(?int $onlyWeekNo = null): void
    {
        $weeks = SyllabusWeek::query()
            ->where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        if ($weeks->isEmpty()) {
            Log::warning('[WeeklyCoverageStep] saveWeeklyEntries: no weeks in DB');
            return;
        }

        Log::info('[WeeklyCoverageStep] saveWeeklyEntries', [
            'onlyWeekNo' => $onlyWeekNo,
            'weekCount'  => $weeks->count(),
            'inputKeys'  => array_keys($this->weekInputs),
            'component'  => $this->activeComponent,
        ]);

        foreach ($weeks as $week) {
            if ($onlyWeekNo !== null && (int) $week->week_no !== $onlyWeekNo) {
                continue;
            }

            $wKey    = 'w' . $week->week_no;
            $payload = $this->weekInputs[$wKey] ?? null;

            if ($payload === null) {
                Log::debug('[WeeklyCoverageStep] saveWeeklyEntries: no payload', [
                    'week_no' => $week->week_no,
                    'wKey'    => $wKey,
                    'allKeys' => array_keys($this->weekInputs),
                ]);
                continue;
            }

            // ── WeekContent ───────────────────────────────────────────────────
            $courseOutcomeId = (isset($payload['course_outcome_id'])
                && $payload['course_outcome_id'] !== ''
                && $payload['course_outcome_id'] !== null)
                ? (int) $payload['course_outcome_id']
                : null;

            WeekContent::query()->updateOrCreate(
                [
                    'syllabus_week_id' => $week->id,
                    'component_type'   => $this->activeComponent,
                ],
                [
                    'course_outcome_id' => $courseOutcomeId,
                    'learning_outcomes' => trim((string) ($payload['learning_outcomes']   ?? '')),
                    'assessment_task'   => trim((string) ($payload['assessment_task']     ?? '')),
                    'topics'            => trim((string) ($payload['topic']               ?? '')),
                    'tla'               => trim((string) ($payload['teaching_activities'] ?? '')),
                ]
            );

            // ── References (multiple rows) ────────────────────────────────────
            // Delete everything for this week, re-insert non-empty rows only.
            Reference::query()
                ->where('syllabus_id', $this->syllabusId)
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

            // ── Online Materials (multiple rows) ──────────────────────────────
            OnlineMaterial::query()
                ->where('syllabus_id', $this->syllabusId)
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
}
