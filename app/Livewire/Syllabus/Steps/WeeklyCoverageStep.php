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

    // Keyed by component type string: ['LEC' => [...], 'LAB' => [...]]
    public array  $courseComponents = [];

    // Which component tab is currently active: 'LEC' or 'LAB'
    public string $activeComponent = 'LEC';

    // Course outcomes for the CO dropdown in each week form.
    // Shape: [['id' => 1, 'co_code' => 'CO1', 'description' => '...']]
    public array $courseOutcomes = [];

    // Which weeks are locked and why.
    // Format: [ week_no => 'exam' | 'non_teaching' ]
    // Computed fresh on every loadData() — no extra DB column needed.
    public array $lockedWeeks = [];

    // Calendar events per week, shown as info badges in the accordion header.
    // Format: [ week_no => [ ['name' => '...', 'type' => '...', 'date_display' => '...'], ... ] ]
    public array $weekEvents = [];

    // The editable form data for each week.
    //
    // KEY FORMAT: 'w1', 'w2', ... — never plain integers.
    //
    // Why the 'w' prefix:
    // PHP silently casts any numeric string key to int. Livewire serialises this
    // array to JSON between requests. Without the prefix: key "1" is stored as
    // int 1, JSON encodes as {"1":{...}}, decoded back as int 1. Your lookup for
    // "w1" then finds nothing. The 'w' prefix makes keys genuinely non-numeric so
    // they survive the JSON round-trip unchanged.
    //
    // CRITICAL RULE: Only call populateWeekInputs() from loadData() and
    // setComponentType(). Never from save actions or blur events — that would
    // overwrite what the user is currently typing with stale DB values.
    //
    // Shape per week key:
    // [
    //   'course_outcome_id'   => int|null,
    //   'learning_outcomes'   => string,
    //   'assessment_task'     => string,
    //   'topic'               => string,
    //   'teaching_activities' => string,
    //   'references'          => [ ['text' => ''], ... ],
    //   'materials'           => [ ['name' => '', 'url' => ''], ... ],
    // ]
    public array $weekInputs = [];

    // Rebuilt on every render() call.
    // Protected so Livewire does NOT serialise it between requests.
    protected Collection $syllabusWeeks;

    // ── Boot ──────────────────────────────────────────────────────────────────

    public function boot(): void
    {
        $this->syllabusWeeks = collect();
    }

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        // Re-fetch weeks on every render so the accordion reflects any changes
        // from generate/regenerate without needing a full loadData() call.
        $this->syllabusWeeks = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        $this->weeksGenerated = $this->syllabusWeeks->isNotEmpty();

        return view('livewire.syllabus.steps.weekly-coverage', [
            'syllabusWeeks' => $this->syllabusWeeks,
            'syllabus'      => $this->freshSyllabus(),
        ]);
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    // Wizard dispatches this when the user navigates TO this step.
    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'weekly_coverage') {
            $this->loadData();
        }
    }

    // Fired when the user picks a new academic calendar in the previous step.
    #[On('syllabus-calendar-updated')]
    public function onCalendarUpdated(): void
    {
        $this->loadData();
    }

    // Wizard dispatches this just before navigating AWAY from this step.
    // We persist $weekInputs now. Do NOT call loadData() here — that would
    // overwrite the user's typed values with the last-saved DB state.
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

    // Generate SyllabusWeek + WeekContent rows for the first time.
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

        // Full reload is correct here — new DB rows were just created and we
        // need to populate $weekInputs and compute locked weeks from fresh data.
        $this->loadData();

        $this->dispatch('lw-toast', type: 'success', message: 'Weekly coverage generated.');
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    // Delete all weeks and recreate them from the academic calendar.
    public function regenerateWeeks(): void
    {
        $syllabus = $this->freshSyllabus();
        if (! $syllabus) {
            return;
        }

        $this->reloadCourseComponents();

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

    // Save a single week when the user clicks "Save Week N".
    public function saveWeek(int $weekNo): void
    {
        if ($weekNo <= 0 || isset($this->lockedWeeks[$weekNo])) {
            return;
        }
        $this->saveWeeklyEntries($weekNo);
        $this->dispatch('lw-toast', type: 'success', message: "Week {$weekNo} saved.");
    }

    // Reset a single week — clears all content from DB and blanks the form inputs.
    //
    // This is the "start over" action for a week. It:
    //   1. Deletes WeekContent fields (learning outcomes, assessment task, topics, TLA, CO)
    //      but PRESERVES the WeekContent row itself (so the week still exists).
    //   2. Deletes all References and OnlineMaterials for that week.
    //   3. Resets $weekInputs[$key] to empty defaults so the form fields go blank.
    //
    // Locked weeks (exam / non-teaching) are guarded — they cannot be reset.
    // The blade only shows the Reset button on editable weeks, but we double-check here.
    public function resetWeek(int $weekNo): void
    {
        if ($weekNo <= 0 || isset($this->lockedWeeks[$weekNo])) {
            return;
        }

        $week = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->where('week_no', $weekNo)
            ->first();

        if (! $week) {
            return;
        }

        // Clear WeekContent fields — keep the row, just blank the editable columns
        WeekContent::where('syllabus_week_id', $week->id)
            ->where('component_type', $this->activeComponent)
            ->update([
                'course_outcome_id'  => null,
                'learning_outcomes'  => '',
                'assessment_task'    => '',
                'topics'             => '',
                'tla'                => '',
            ]);

        // Delete references and materials for this week
        Reference::where('syllabus_id', $this->syllabusId)
            ->where('syllabus_week_id', $week->id)
            ->delete();

        OnlineMaterial::where('syllabus_id', $this->syllabusId)
            ->where('syllabus_week_id', $week->id)
            ->delete();

        // Reset in-memory inputs so the form fields clear immediately
        $key = 'w' . $weekNo;
        $this->weekInputs[$key] = [
            'course_outcome_id'   => null,
            'learning_outcomes'   => '',
            'assessment_task'     => '',
            'topic'               => '',
            'teaching_activities' => '',
            'references'          => [['text' => '']],
            'materials'           => [['name' => '', 'url' => '']],
        ];

        $this->dispatch('lw-toast', type: 'success', message: "Week {$weekNo} reset.");
    }

    // Save every unlocked week at once.
    public function saveAllWeeklyEntries(): void
    {
        $this->saveWeeklyEntries();
        $this->dispatch('lw-toast', type: 'success', message: 'All weeks saved.');
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    // Add a blank reference row to the given week.
    public function addReference(int $weekNo): void
    {
        if (isset($this->lockedWeeks[$weekNo])) {
            return;
        }
        $this->weekInputs['w' . $weekNo]['references'][] = ['text' => ''];
    }

    // Remove a reference row by its array index.
    // Always keeps at least one blank row so the section does not disappear.
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
        if (empty($this->weekInputs[$key]['references'])) {
            $this->weekInputs[$key]['references'] = [['text' => '']];
        }
    }

    // Add a blank online material row to the given week.
    public function addMaterial(int $weekNo): void
    {
        if (isset($this->lockedWeeks[$weekNo])) {
            return;
        }
        $this->weekInputs['w' . $weekNo]['materials'][] = ['name' => '', 'url' => ''];
    }

    // Remove an online material row by its array index.
    // Always keeps at least one blank row so the section does not disappear.
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

    // Switch between LEC and LAB tabs.
    // All three steps happen in ONE Livewire round-trip:
    //   1. Persist the current component's $weekInputs to DB silently.
    //   2. Change $activeComponent.
    //   3. Re-populate $weekInputs from DB for the new component.
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

        $this->saveWeeklyEntries();
        $this->activeComponent = $type;
        $this->syllabusWeeks   = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();
        $this->populateWeekInputs();
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    // Always query the Syllabus fresh with its relations.
    // Livewire strips eager-loaded relations on rehydration, so $this->syllabus
    // (if stored as a property) would return null for ->academicCalendar etc.
    private function freshSyllabus(): ?Syllabus
    {
        return Syllabus::with('academicCalendar', 'courseOutcomes')->find($this->syllabusId);
    }

    // Reload LEC/LAB component data from the database.
    private function reloadCourseComponents(): void
    {
        $this->courseComponents = \App\Models\CourseComponent::where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('type')
            ->toArray();
    }

    // ── Private: master data loader ───────────────────────────────────────────

    // Load everything this step needs from the database.
    //
    // Call ONLY from:
    //   mount()
    //   generateWeeklyCoverage() / regenerateWeeks()
    //   onStepChanged() / onCalendarUpdated()
    //
    // NEVER from save actions, blur handlers, or setComponentType() directly.
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

        // Prefer LEC; fall back to LAB for lab-only courses
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

        $this->computeLockedWeeks($syllabus);
        $this->populateWeekInputs();
    }

    // ── Private: locked week logic ────────────────────────────────────────────

    // Determine which weeks are locked and auto-write labels into WeekContent.
    //
    // Lock rules:
    //   'exam'         → lock + write "1st Term Exam" / "1st Term Practical Exam" etc.
    //                    Labels increment per occurrence: 1st, 2nd, Final.
    //   'non_teaching' → lock + write "Non-Teaching Week" into assessment_task for all
    //                    component types so it surfaces in Course Evaluation display.
    //   'break'        → NOT locked. Week stays fully editable. Shown as an info badge only.
    //
    // Why write to WeekContent for exam and non_teaching:
    //   CourseEvaluationStep decides which rows to show by reading assessment_task.
    //   Writing the label here means no special detection code is needed there —
    //   it simply shows every row where assessment_task is non-empty (and not
    //   "Non-Teaching Week", which is filtered out in CourseEvaluationStep).
    private function computeLockedWeeks(Syllabus $syllabus): void
    {
        $this->weekEvents  = [];
        $this->lockedWeeks = [];

        if (! $syllabus->academic_calendar_id) {
            return;
        }

        $allEvents = AcademicCalendarEvent::where('academic_calendar_id', $syllabus->academic_calendar_id)
            ->orderBy('date')
            ->get();

        // Labels for exam terms, assigned in order of first occurrence
        $examTermLabels = ['1st Term', '2nd Term', 'Final Term'];
        $examsSeen      = 0;

        foreach ($this->syllabusWeeks as $week) {
            $weekStart = Carbon::parse($week->start_date);
            $weekEnd   = Carbon::parse($week->end_date);

            // Collect all events that fall within this week's date range
            $eventsThisWeek = $allEvents->filter(
                fn ($event) => Carbon::parse($event->date)->between($weekStart, $weekEnd)
            );

            // Store as plain arrays — Eloquent models must not live in public Livewire properties
            $this->weekEvents[$week->week_no] = $eventsThisWeek->map(fn ($event) => [
                'name'         => $event->name,
                'type'         => $event->type,
                'date_display' => Carbon::parse($event->date)->format('M d'),
            ])->values()->all();

            // Only exam and non_teaching lock the week; break does NOT
            $lockingEvent = $eventsThisWeek->first(
                fn ($event) => in_array($event->type, ['exam', 'non_teaching'], true)
            );

            if (! $lockingEvent) {
                continue;
            }

            $this->lockedWeeks[$week->week_no] = $lockingEvent->type;

            if ($lockingEvent->type === 'exam') {
                $termLabel = $examTermLabels[min($examsSeen, 2)];
                $examsSeen++;

                WeekContent::where('syllabus_week_id', $week->id)
                    ->where('component_type', 'LEC')
                    ->update(['assessment_task' => $termLabel . ' Exam']);

                WeekContent::where('syllabus_week_id', $week->id)
                    ->where('component_type', 'LAB')
                    ->update(['assessment_task' => $termLabel . ' Practical Exam']);
            }

            // ── Non-teaching week: write label into WeekContent ───────────────
            // Written to ALL component type rows for this week.
            // CourseEvaluationStep explicitly skips rows with this value so it
            // does not appear as an assessment task in the evaluation table.
            // It is stored so the weekly coverage locked banner can display it,
            // and so any review/print step can reference it.
            if ($lockingEvent->type === 'non_teaching') {
                WeekContent::where('syllabus_week_id', $week->id)
                    ->update(['assessment_task' => 'Non-Teaching Week']);
            }
        }
    }

    // ── Private: populate form inputs ─────────────────────────────────────────

    // Read WeekContent rows from the DB and fill $weekInputs.
    // Only call from loadData() and setComponentType().
    // Calling from save/blur would overwrite the user's in-progress edits.
    private function populateWeekInputs(): void
    {
        if ($this->syllabusWeeks->isEmpty()) {
            $this->weekInputs = [];
            return;
        }

        $weekIds = $this->syllabusWeeks->pluck('id')->all();

        // One WeekContent per (week_id, component_type) combination
        $weekContents = WeekContent::whereIn('syllabus_week_id', $weekIds)
            ->where('component_type', $this->activeComponent)
            ->get()
            ->keyBy('syllabus_week_id');

        // References — multiple rows per week
        $allRefs = Reference::where('syllabus_id', $this->syllabusId)
            ->whereIn('syllabus_week_id', $weekIds)
            ->orderBy('id')
            ->get()
            ->groupBy('syllabus_week_id');

        // Online materials — multiple rows per week
        $allMats = OnlineMaterial::where('syllabus_id', $this->syllabusId)
            ->whereIn('syllabus_week_id', $weekIds)
            ->orderBy('id')
            ->get()
            ->groupBy('syllabus_week_id');

        // Convert null / "N/A" sentinel values to blank string for clean display
        $clean = fn (?string $value): string => ($value === null || $value === 'N/A') ? '' : $value;

        $inputs = [];

        foreach ($this->syllabusWeeks as $week) {
            $content = $weekContents->get($week->id);

            $refs = $allRefs->has($week->id)
                ? $allRefs->get($week->id)->map(fn ($r) => ['text' => $clean($r->reference_text)])->values()->all()
                : [['text' => '']];

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

    // ── Private: create week rows ─────────────────────────────────────────────

    // Create one SyllabusWeek row per calendar week plus a WeekContent row for
    // each component type (LEC and/or LAB).
    // Idempotent — exits immediately if weeks already exist for this syllabus.
    private function createWeekRows(Syllabus $syllabus): void
    {
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
            if ($weekEnd->gt($end)) {
                $weekEnd = $end->copy();
            }

            $syllabusWeek = SyllabusWeek::create([
                'syllabus_id'  => $syllabus->id,
                'week_no'      => $weekNo,
                'start_date'   => $weekStart->toDateString(),
                'end_date'     => $weekEnd->toDateString(),
                'is_exam_week' => false,
            ]);

            if ($hasLEC) {
                WeekContent::create([
                    'syllabus_week_id'  => $syllabusWeek->id,
                    'component_type'    => 'LEC',
                    'learning_outcomes' => '',
                    'assessment_task'   => '',
                    'topics'            => '',
                    'tla'               => '',
                ]);
            }
            if ($hasLAB) {
                WeekContent::create([
                    'syllabus_week_id'  => $syllabusWeek->id,
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

        Log::info('[WeeklyCoverageStep] weeks created', [
            'syllabusId' => $syllabus->id,
            'total'      => $weekNo - 1,
        ]);
    }

    // ── Private: save to DB ───────────────────────────────────────────────────

    // Persist $weekInputs to the database.
    // Pass $onlyWeekNo to save a single week; omit it to save all.
    // Called by: saveWeek(), saveAllWeeklyEntries(), setComponentType(), onSaveRequested().
    // Do NOT call loadData() after — that would overwrite the user's typed values.
    private function saveWeeklyEntries(?int $onlyWeekNo = null): void
    {
        $weeks = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        if ($weeks->isEmpty()) {
            return;
        }

        foreach ($weeks as $week) {
            if ($onlyWeekNo !== null && (int) $week->week_no !== $onlyWeekNo) {
                continue;
            }

            // Server-side guard: never write to locked weeks
            if (isset($this->lockedWeeks[$week->week_no])) {
                continue;
            }

            $key     = 'w' . $week->week_no;
            $payload = $this->weekInputs[$key] ?? null;

            if ($payload === null) {
                continue;
            }

            // Treat blank string as null for the foreign key
            $courseOutcomeId = (isset($payload['course_outcome_id'])
                && $payload['course_outcome_id'] !== ''
                && $payload['course_outcome_id'] !== null)
                ? (int) $payload['course_outcome_id']
                : null;

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

            // Delete + re-insert references (simpler than row-level diffing)
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

            // Delete + re-insert materials (simpler than row-level diffing)
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
}