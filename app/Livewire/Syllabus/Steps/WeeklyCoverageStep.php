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
    public int    $syllabusId;
    public ?int   $academic_calendar_id = null;
    public bool   $weeksGenerated       = false;
    public array  $courseComponents     = [];
    public string $activeComponent      = 'LEC';
    public array  $courseOutcomes       = [];
    public array  $lockedWeeks          = [];
    public array  $weekEvents           = [];
    public array  $weekInputs           = [];

    protected Collection $syllabusWeeks;

    public function boot(): void
    {
        $this->syllabusWeeks = collect();
    }

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    public function render()
    {
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
        $this->saveWeeklyEntries();
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    // ── Public actions ────────────────────────────────────────────────────────

    public function generateWeeklyCoverage(): void
    {
        $syllabus = $this->freshSyllabus();

        if (! $syllabus || ! $syllabus->academic_calendar_id) {
            $this->dispatch('lw-toast', type: 'error', message: 'Select an academic calendar first.');
            return;
        }

        $this->reloadCourseComponents();
        $this->academic_calendar_id = (int) $syllabus->academic_calendar_id;
        $this->createWeekRows($syllabus);
        $this->loadData();

        $this->dispatch('lw-toast', type: 'success', message: 'Weekly coverage generated.');
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

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
    // Only shows a success toast when something actually changed — prevents
    // constant "saved" noise when the user clicks Save on an unmodified week.
    public function saveWeek(int $weekNo): void
    {
        if ($weekNo <= 0 || isset($this->lockedWeeks[$weekNo])) {
            return;
        }
        $changed = $this->saveWeeklyEntries($weekNo);
        if ($changed) {
            $this->dispatch('lw-toast', type: 'success', message: "Week {$weekNo} saved.");
        }
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
    // Only toasts when at least one week actually had changes.
    public function saveAllWeeklyEntries(): void
    {
        $changed = $this->saveWeeklyEntries();
        if ($changed) {
            $this->dispatch('lw-toast', type: 'success', message: 'All weeks saved.');
        }
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    public function addReference(int $weekNo): void
    {
        if (isset($this->lockedWeeks[$weekNo])) {
            return;
        }
        $this->weekInputs['w' . $weekNo]['references'][] = ['text' => ''];
    }

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

    public function addMaterial(int $weekNo): void
    {
        if (isset($this->lockedWeeks[$weekNo])) {
            return;
        }
        $this->weekInputs['w' . $weekNo]['materials'][] = ['name' => '', 'url' => ''];
    }

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

    private function freshSyllabus(): ?Syllabus
    {
        return Syllabus::with('academicCalendar', 'courseOutcomes')->find($this->syllabusId);
    }

    private function reloadCourseComponents(): void
    {
        $this->courseComponents = \App\Models\CourseComponent::where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('type')
            ->toArray();
    }

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

        $this->computeLockedWeeks($syllabus);
        $this->populateWeekInputs();
    }

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

        $examTermLabels = ['1st Term', '2nd Term', 'Final Term'];
        $examsSeen      = 0;

        foreach ($this->syllabusWeeks as $week) {
            $weekStart = Carbon::parse($week->start_date);
            $weekEnd   = Carbon::parse($week->end_date);

            $eventsThisWeek = $allEvents->filter(
                fn ($event) => Carbon::parse($event->date)->between($weekStart, $weekEnd)
            );

            $this->weekEvents[$week->week_no] = $eventsThisWeek->map(fn ($event) => [
                'name'         => $event->name,
                'type'         => $event->type,
                'date_display' => Carbon::parse($event->date)->format('M d'),
            ])->values()->all();

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

            if ($lockingEvent->type === 'non_teaching') {
                WeekContent::where('syllabus_week_id', $week->id)
                    ->update(['assessment_task' => 'Non-Teaching Week']);
            }
        }
    }

    private function populateWeekInputs(): void
    {
        if ($this->syllabusWeeks->isEmpty()) {
            $this->weekInputs = [];
            return;
        }

        $weekIds = $this->syllabusWeeks->pluck('id')->all();

        $weekContents = WeekContent::whereIn('syllabus_week_id', $weekIds)
            ->where('component_type', $this->activeComponent)
            ->get()
            ->keyBy('syllabus_week_id');

        $allRefs = Reference::where('syllabus_id', $this->syllabusId)
            ->whereIn('syllabus_week_id', $weekIds)
            ->orderBy('id')
            ->get()
            ->groupBy('syllabus_week_id');

        $allMats = OnlineMaterial::where('syllabus_id', $this->syllabusId)
            ->whereIn('syllabus_week_id', $weekIds)
            ->orderBy('id')
            ->get()
            ->groupBy('syllabus_week_id');

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

    // Create one SyllabusWeek row per calendar week, plus a WeekContent row for
    // each component type (LEC and/or LAB).
    //
    // Break weeks are SKIPPED entirely — a week whose range overlaps a 'break'
    // event is not created. Week numbers stay sequential (no gaps). A 3-week
    // break reduces total week count by 3.
    //
    // Exam and non-teaching weeks are created here and then locked later by
    // computeLockedWeeks() once the rows exist.
    //
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

        // Pre-load break events so we can skip break weeks cheaply inside the loop.
        // A week is considered a break week if any break event date falls within
        // [weekStart, weekEnd]. Skipped weeks do not create any rows.
        $breakDates = AcademicCalendarEvent::where('academic_calendar_id', $syllabus->academic_calendar_id)
            ->where('type', 'break')
            ->orderBy('date')
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->startOfDay());

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

            // Skip break weeks — institution is closed, no coverage needed.
            // The cursor still advances so the rest of the calendar is unaffected.
            $isBreak = $breakDates->contains(
                fn ($d) => $d->between($weekStart, $weekEnd)
            );
            if ($isBreak) {
                $cursor = $weekEnd->copy()->addDay();
                continue;
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

    private function saveWeeklyEntries(?int $onlyWeekNo = null): bool
    {
        $weeks = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        if ($weeks->isEmpty()) {
            return false;
        }

        // Track whether anything actually differed from DB values.
        // updateOrCreate always runs even for no-op updates, so we compare
        // the incoming payload against the existing row manually.
        // Callers check this return value to decide whether to show a toast.
        $changed = false;

        foreach ($weeks as $week) {
            if ($onlyWeekNo !== null && (int) $week->week_no !== $onlyWeekNo) {
                continue;
            }

            if (isset($this->lockedWeeks[$week->week_no])) {
                continue;
            }

            $key     = 'w' . $week->week_no;
            $payload = $this->weekInputs[$key] ?? null;

            if ($payload === null) {
                continue;
            }

            $courseOutcomeId = (isset($payload['course_outcome_id'])
                && $payload['course_outcome_id'] !== ''
                && $payload['course_outcome_id'] !== null)
                ? (int) $payload['course_outcome_id']
                : null;

            $incoming = [
                'course_outcome_id'  => $courseOutcomeId,
                'learning_outcomes'  => trim((string) ($payload['learning_outcomes']   ?? '')),
                'assessment_task'    => trim((string) ($payload['assessment_task']     ?? '')),
                'topics'             => trim((string) ($payload['topic']               ?? '')),
                'tla'                => trim((string) ($payload['teaching_activities'] ?? '')),
            ];

            // ── Dirty-check content fields ────────────────────────────────────
            // Compare each incoming field against the existing DB row.
            // Casts to string for safe comparison across int/null/'' variants.
            $existing = WeekContent::where('syllabus_week_id', $week->id)
                ->where('component_type', $this->activeComponent)
                ->first();

            $contentChanged = ! $existing || array_reduce(
                array_keys($incoming),
                fn (bool $carry, string $k) => $carry
                    || ((string) ($existing->{$k} ?? '')) !== ((string) ($incoming[$k] ?? '')),
                false
            );

            // ── Dirty-check references ────────────────────────────────────────
            // Sort both sides so order changes (e.g. after delete+reinsert) don't
            // trigger false positives.
            $existingRefs = Reference::where('syllabus_id', $this->syllabusId)
                ->where('syllabus_week_id', $week->id)
                ->pluck('reference_text')
                ->map(fn ($t) => trim((string) $t))
                ->filter()
                ->sort()
                ->values()
                ->all();

            $incomingRefs = collect((array) ($payload['references'] ?? []))
                ->map(fn ($r) => trim((string) ($r['text'] ?? '')))
                ->filter()
                ->sort()
                ->values()
                ->all();

            $refsChanged = $existingRefs !== $incomingRefs;

            // ── Dirty-check materials ─────────────────────────────────────────
            $existingMats = OnlineMaterial::where('syllabus_id', $this->syllabusId)
                ->where('syllabus_week_id', $week->id)
                ->get()
                ->map(fn ($m) => trim($m->material_name ?? '') . '|' . trim($m->url ?? ''))
                ->sort()
                ->values()
                ->all();

            $incomingMats = collect((array) ($payload['materials'] ?? []))
                ->map(fn ($m) => trim((string) ($m['name'] ?? '')) . '|' . trim((string) ($m['url'] ?? '')))
                ->filter(fn ($s) => $s !== '|')
                ->sort()
                ->values()
                ->all();

            $matsChanged = $existingMats !== $incomingMats;

            // Skip this week entirely — nothing to write
            if (! $contentChanged && ! $refsChanged && ! $matsChanged) {
                continue;
            }

            $changed = true;

            WeekContent::updateOrCreate(
                [
                    'syllabus_week_id' => $week->id,
                    'component_type'   => $this->activeComponent,
                ],
                $incoming
            );

            // Delete + re-insert references only when they actually changed.
            // (Simpler than row-level diffing; acceptable for small counts.)
            if ($refsChanged) {
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
            }

            // Delete + re-insert materials only when they actually changed.
            if ($matsChanged) {
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

        return $changed;
    }
}