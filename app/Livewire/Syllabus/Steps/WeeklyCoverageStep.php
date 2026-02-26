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
    public int $syllabusId;

    /**
     * DO NOT make $syllabus public — Livewire rehydrates Eloquent models
     * WITHOUT their eager-loaded relations. When ensureWeeksGenerated() later
     * calls $this->syllabus->academicCalendar it gets NULL and silently returns.
     *
     * Instead: always load fresh via freshSyllabus() wherever the relation is needed.
     */
    public ?int    $academic_calendar_id = null;
    public bool    $weeksGenerated       = false;
    public array   $weekEvents           = [];
    public array   $examWeeks            = [];
    public ?string $activeWeekTab        = null;
    public array   $courseComponents     = [];
    public string  $activeComponent      = 'LEC';
    public array   $courseOutcomes       = [];

    /**
     * The main form array. Keys are week_no integers stored by PHP.
     *
     * After Livewire JSON round-trip: json_decode returns STRING keys ("1","2"…).
     * PHP DOES NOT treat $arr[1] the same as $arr["1"] when the stored key is a
     * string from JSON decode — they occupy different slots.
     *
     * Proof: json_decode('{"1":"x"}', true)[1] === null
     *        json_decode('{"1":"x"}', true)["1"] === "x"
     *
     * So ALL reads from $weekInputs after mount() must use STRING keys:
     * $this->weekInputs[(string) $week->week_no]
     *
     * And populateWeekInputs() must also write with STRING keys so the
     * initial render matches what Livewire will send back.
     */
    public array $weekInputs = [];

    /** Rebuilt every request — never serialised by Livewire. */
    protected Collection $syllabusWeeks;

    // ── Boot ─────────────────────────────────────────────────────────────────

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
        $this->syllabusWeeks = SyllabusWeek::query()
            ->where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        $this->weeksGenerated = $this->syllabusWeeks->isNotEmpty();

        // Expose the syllabus object to the Blade just for the calendar dates display
        // (matches original blade: $syllabus?->academicCalendar)
        $syllabus = $this->freshSyllabus();

        return view('livewire.syllabus.steps.weekly-coverage', [
            'syllabusWeeks' => $this->syllabusWeeks,
            'syllabus'      => $syllabus,
        ]);
    }

    // ── Event Listeners ───────────────────────────────────────────────────────

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
        // Always reload with eager-loaded relation — critical for ensureWeeksGenerated()
        $syllabus = $this->freshSyllabus();

        if (! $syllabus || ! $syllabus->academic_calendar_id) {
            $this->dispatch('lw-toast', type: 'error', message: 'Select an academic calendar before generating weeks.');
            return;
        }

        // Reload course components fresh — they must exist for WeekContent creation
        $this->courseComponents = \App\Models\CourseComponent::query()
            ->where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('type')
            ->toArray();

        $this->academic_calendar_id = (int) $syllabus->academic_calendar_id;

        $this->ensureWeeksGenerated($syllabus);
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

        // Reload components before we build the new weeks
        $this->courseComponents = \App\Models\CourseComponent::query()
            ->where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('type')
            ->toArray();

        // Delete all existing data (explicit — no relying on DB cascades)
        $weekIds = SyllabusWeek::where('syllabus_id', $syllabus->id)->pluck('id')->all();
        if ($weekIds) {
            WeekContent::whereIn('syllabus_week_id', $weekIds)->delete();
            Reference::where('syllabus_id', $this->syllabusId)
                ->whereIn('syllabus_week_id', $weekIds)->delete();
            OnlineMaterial::where('syllabus_id', $this->syllabusId)
                ->whereIn('syllabus_week_id', $weekIds)->delete();
            SyllabusWeek::whereIn('id', $weekIds)->delete();
        }

        $this->weekInputs = [];
        $this->ensureWeeksGenerated($syllabus);
        $this->loadData();

        $this->dispatch('lw-toast', type: 'success', message: 'Weeks regenerated.');
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage', message: 'Weeks regenerated.');
    }

    /**
     * Save a single week. The wire:model on inputs syncs $weekInputs BEFORE
     * this action fires because we use wire:model (not .lazy) on text fields,
     * OR the Save button itself triggers a Livewire request that includes the
     * current form state.
     */
    public function saveWeek(int $weekNo): void
    {
        if ($weekNo <= 0) {
            return;
        }

        Log::debug('[WeeklyCoverageStep] saveWeek', [
            'weekNo'    => $weekNo,
            'key'       => (string) $weekNo,
            'payload'   => $this->weekInputs[(string) $weekNo] ?? 'MISSING',
            'allKeys'   => array_keys($this->weekInputs),
            'component' => $this->activeComponent,
        ]);

        $this->saveWeeklyEntries($weekNo);
        $this->dispatch('lw-toast', type: 'success', message: "Week {$weekNo} saved.");
    }

    public function saveAllWeeklyEntries(): void
    {
        $this->saveWeeklyEntries();
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage', message: 'All weekly entries saved.');
        $this->dispatch('lw-toast', type: 'success', message: 'All weeks saved.');
    }

    public function assignExamWeek(string $type, int $weekNo): void
    {
        if (! in_array($type, ['first_term', 'second_term', 'final_term'], true)) {
            return;
        }

        $syllabus = $this->freshSyllabus();
        if (! $syllabus) {
            return;
        }

        SyllabusWeek::query()
            ->where('syllabus_id', $syllabus->id)
            ->where('exam_type', $type)
            ->update(['exam_type' => null, 'is_exam_week' => false]);

        SyllabusWeek::query()
            ->where('syllabus_id', $syllabus->id)
            ->where('week_no', $weekNo)
            ->update(['exam_type' => $type, 'is_exam_week' => true]);

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

        SyllabusWeek::query()
            ->where('syllabus_id', $syllabus->id)
            ->where('exam_type', $type)
            ->update(['exam_type' => null, 'is_exam_week' => false]);

        $this->reloadWeekMeta($syllabus);
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
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

        $this->saveWeeklyEntries();
        $this->activeComponent = $type;
        $this->syllabusWeeks   = SyllabusWeek::where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')->get();
        $this->populateWeekInputs();
    }

    public function setActiveWeekTab(string $tab): void
    {
        if (! str_starts_with($tab, 'week_')) {
            return;
        }

        $weekNo = (int) str_replace('week_', '', $tab);
        if ($weekNo <= 0) {
            return;
        }

        $this->activeWeekTab = $tab;
    }

    // ── Private: Helpers ──────────────────────────────────────────────────────

    /**
     * Always loads Syllabus with academicCalendar eager-loaded.
     * Never trust Livewire's rehydrated $syllabus — relations are stripped.
     */
    private function freshSyllabus(): ?Syllabus
    {
        return Syllabus::query()
            ->with('academicCalendar', 'courseOutcomes')
            ->find($this->syllabusId);
    }

    // ── Private: Data Loading ─────────────────────────────────────────────────

    /**
     * Master loader. No isLoaded guard — always runs fresh.
     *
     * Why no guard? The original had one, and it caused silent failures when
     * actions (generate, save) ran on subsequent requests where $syllabus was
     * a stale Livewire-rehydrated model with no relations loaded.
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

        // Course components — always reload fresh
        $this->courseComponents = \App\Models\CourseComponent::query()
            ->where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('type')
            ->toArray();

        $this->activeComponent = isset($this->courseComponents['LEC']) ? 'LEC'
            : (isset($this->courseComponents['LAB']) ? 'LAB' : 'LEC');

        // Course outcomes for dropdown
        $this->courseOutcomes = $syllabus->courseOutcomes
            ->sortBy('co_code')
            ->map(fn ($co) => [
                'id'          => (int) $co->id,
                'co_code'     => $co->co_code,
                'description' => $co->description,
            ])
            ->values()
            ->all();

        // Load week list + events + exam map
        $this->reloadWeekMeta($syllabus);

        // Populate form inputs
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
     * Reload week list, exam map, and events from DB.
     * Called after generate/regenerate/exam-assign mutations.
     */
    private function reloadWeekMeta(Syllabus $syllabus): void
    {
        if (! $syllabus->academic_calendar_id) {
            $this->syllabusWeeks = collect();
            $this->weekEvents    = [];
            $this->examWeeks     = [];
            $this->activeWeekTab = null;
            $this->weeksGenerated = false;
            return;
        }

        $this->syllabusWeeks = SyllabusWeek::query()
            ->where('syllabus_id', $syllabus->id)
            ->orderBy('week_no')
            ->get();

        $this->weeksGenerated = $this->syllabusWeeks->isNotEmpty();

        // Exam week map
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

        // Events — stored as plain arrays (no Eloquent models in public properties)
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
                ->values();   // Keep as Eloquent Collection — original blade uses $event->name
        }
        $this->weekEvents = $weekEvents;
    }

    /**
     * Populate $weekInputs from DB.
     *
     * Keys MUST be written as explicit STRING keys using (string) cast, because:
     * - PHP's $arr[(string)1] stores the key as int(1) internally
     * - BUT Livewire deserialises its JSON snapshot with json_decode which produces
     *   string keys: $weekInputs["1"] after round-trip
     * - Reading: $weekInputs[(string)$week->week_no] where PHP casts "1" → int(1) lookup
     *   This MATCHES the int key stored by PHP, so reads work correctly.
     * - After Livewire round-trip the key is STRING "1" from JSON — and PHP's
     *   (string)$week->week_no gives "1" which correctly finds that string key too.
     *
     * In short: always use (string) cast on both read and write for consistency.
     */
    private function populateWeekInputs(): void
    {
        if ($this->syllabusWeeks->isEmpty()) {
            $this->weekInputs = [];
            return;
        }

        $weekIds = $this->syllabusWeeks->pluck('id')->all();

        $weekContents = WeekContent::query()
            ->whereIn('syllabus_week_id', $weekIds)
            ->where('component_type', $this->activeComponent)
            ->get()
            ->keyBy('syllabus_week_id');

        $references = Reference::query()
            ->where('syllabus_id', $this->syllabusId)
            ->whereIn('syllabus_week_id', $weekIds)
            ->get()
            ->keyBy('syllabus_week_id');

        $materials = OnlineMaterial::query()
            ->where('syllabus_id', $this->syllabusId)
            ->whereIn('syllabus_week_id', $weekIds)
            ->get()
            ->keyBy('syllabus_week_id');

        $inputs = [];
        foreach ($this->syllabusWeeks as $week) {
            $content   = $weekContents->get($week->id);
            $reference = $references->get($week->id);
            $material  = $materials->get($week->id);

            // Store with explicit string key using (string) cast
            $inputs[(string) $week->week_no] = [
                'course_outcome_id'   => $content?->course_outcome_id ?? null,
                'learning_outcomes'   => $content?->learning_outcomes ?? '',
                'assessment_task'     => $content?->assessment_task   ?? '',
                'topic'               => $content?->topics            ?? '',
                'teaching_activities' => $content?->tla               ?? '',
                'reference_text'      => $reference?->reference_text  ?? '',
                'material_name'       => $material?->material_name    ?? '',
                'material_url'        => $material?->url              ?? '',
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

        // $syllabus must be loaded with 'academicCalendar' — always use freshSyllabus()
        $calendar = $syllabus->academicCalendar;
        if (! $calendar || ! $calendar->start_date || ! $calendar->end_date) {
            $this->dispatch('lw-toast', type: 'error', message: 'Academic calendar has no start/end date.');
            return;
        }

        $hasLEC = isset($this->courseComponents['LEC']);
        $hasLAB = isset($this->courseComponents['LAB']);

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
                    'learning_outcomes' => 'N/A',
                    'assessment_task'   => 'N/A',
                    'topics'            => 'N/A',
                    'tla'               => 'N/A',
                ]);
            }

            if ($hasLAB) {
                WeekContent::create([
                    'syllabus_week_id'  => $syllabusWeek->id,
                    'component_type'    => 'LAB',
                    'learning_outcomes' => 'N/A',
                    'assessment_task'   => 'N/A',
                    'topics'            => 'N/A',
                    'tla'               => 'N/A',
                ]);
            }

            $weekNo++;
            $cursor = $weekEnd->copy()->addDay();
        }

        Log::info('[WeeklyCoverageStep] ensureWeeksGenerated done', [
            'syllabusId' => $syllabus->id,
            'count'      => $weekNo - 1,
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

            // After Livewire JSON round-trip keys are STRINGS — must use (string) cast
            $payload = $this->weekInputs[(string) $week->week_no] ?? null;

            if ($payload === null) {
                Log::debug('[WeeklyCoverageStep] saveWeeklyEntries: no payload', [
                    'week_no' => $week->week_no,
                    'allKeys' => array_keys($this->weekInputs),
                ]);
                continue;
            }

            Log::debug('[WeeklyCoverageStep] saveWeeklyEntries: saving', [
                'week_no'   => $week->week_no,
                'payload'   => $payload,
                'component' => $this->activeComponent,
            ]);

            // WeekContent
            $courseOutcomeId = (isset($payload['course_outcome_id'])
                && $payload['course_outcome_id'] !== ''
                && $payload['course_outcome_id'] !== null)
                ? (int) $payload['course_outcome_id']
                : null;

            $lo  = trim((string) ($payload['learning_outcomes']   ?? ''));
            $at  = trim((string) ($payload['assessment_task']     ?? ''));
            $tp  = trim((string) ($payload['topic']               ?? ''));
            $tla = trim((string) ($payload['teaching_activities'] ?? ''));

            WeekContent::query()->updateOrCreate(
                [
                    'syllabus_week_id' => $week->id,
                    'component_type'   => $this->activeComponent,
                ],
                [
                    'course_outcome_id' => $courseOutcomeId,
                    'learning_outcomes' => $lo  ?: 'N/A',
                    'assessment_task'   => $at  ?: 'N/A',
                    'topics'            => $tp  ?: 'N/A',
                    'tla'               => $tla ?: 'N/A',
                ]
            );

            // Reference
            $refText = trim((string) ($payload['reference_text'] ?? ''));

            Reference::query()
                ->where('syllabus_id', $this->syllabusId)
                ->where('syllabus_week_id', $week->id)
                ->delete();

            if ($refText !== '') {
                Reference::create([
                    'syllabus_id'      => $this->syllabusId,
                    'syllabus_week_id' => $week->id,
                    'reference_text'   => $refText,
                ]);
            }

            // Online Material
            $matName = trim((string) ($payload['material_name'] ?? ''));
            $matUrl  = trim((string) ($payload['material_url']  ?? ''));

            OnlineMaterial::query()
                ->where('syllabus_id', $this->syllabusId)
                ->where('syllabus_week_id', $week->id)
                ->delete();

            if ($matName !== '' || $matUrl !== '') {
                OnlineMaterial::create([
                    'syllabus_id'      => $this->syllabusId,
                    'syllabus_week_id' => $week->id,
                    'material_name'    => $matName ?: 'Online Material',
                    'url'              => $matUrl,
                ]);
            }
        }
    }
}