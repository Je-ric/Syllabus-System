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
    public bool $isLoaded = false;
    public ?Syllabus $syllabus = null;
    public ?int $academic_calendar_id = null;

    /** @var Collection<int, SyllabusWeek> NOT a public Livewire property — reloaded fresh each request */
    protected Collection $syllabusWeeks;
    public array $weekEvents = [];
    public array $examWeeks = [];
    public ?string $activeWeekTab = null;
    public bool $weeksGenerated = false;
    public array $courseComponents = [];
    public string $activeComponent = 'LEC'; // LEC or LAB
    public array $courseOutcomes = [];
    public array $weekInputs = [];

    public function boot(): void
    {
        $this->syllabusWeeks = collect();
    }

    /**
     * Always returns a fresh Collection from DB — never relies on Livewire rehydration.
     */
    private function getWeeks(): Collection
    {
        if ($this->syllabusWeeks->isEmpty()) {
            $this->syllabusWeeks = SyllabusWeek::query()
                ->where('syllabus_id', $this->syllabusId)
                ->orderBy('week_no')
                ->get();
        }
        return $this->syllabusWeeks;
    }


    public function mount(int $syllabusId)
    {
        $this->syllabusId = $syllabusId;

        foreach ($this->syllabusWeeks as $week) {
            // Get the existing LEC or LAB entry for the week
            $content = $week->weekContents()
                ->where('component_type', $this->activeComponent ?? 'LEC')
                ->first();

            $this->weekInputs[$week->week_no] = [
                'course_outcome_id' => $content->course_outcome_id ?? null,
                'learning_outcomes' => $content->learning_outcomes ?? '',
                'assessment_task' => $content->assessment_task ?? '',
                'topic' => $content->topics ?? '',
                'teaching_activities' => $content->tla ?? '',
                'reference_text' => $week->reference?->reference_text ?? '',
                'material_name' => $week->onlineMaterial?->material_name ?? '',
                'material_url' => $week->onlineMaterial?->material_url ?? '',
            ];
        }
    }

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step !== 'weekly_coverage') {
            return;
        }

        $this->loadData(force: true);
    }

    #[On('syllabus-calendar-updated')]
    public function onCalendarUpdated(): void
    {
        if (!$this->isLoaded) {
            return;
        }

        $this->loadData(force: true);
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

    public function generateWeeklyCoverage(): void
    {
        $this->loadData();

        if (!$this->syllabus || !$this->academic_calendar_id) {
            $this->dispatch('lw-toast', type: 'error', message: 'Select an academic calendar before generating weeks.');
            return;
        }

        if ($this->syllabus->academic_calendar_id !== $this->academic_calendar_id) {
            $this->syllabus->update(['academic_calendar_id' => $this->academic_calendar_id]);
            $this->syllabus->refresh();
        }

        $this->refreshWeeklyCoverage(true);
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage', message: 'Weekly coverage generated.');
    }

    public function assignExamWeek(string $type, int $weekNo): void
    {
        $this->loadData();
        if (!$this->syllabus) {
            return;
        }

        $validTypes = ['first_term', 'second_term', 'final_term'];
        if (!in_array($type, $validTypes, true)) {
            return;
        }

        $week = SyllabusWeek::query()
            ->where('syllabus_id', $this->syllabus->id)
            ->where('week_no', $weekNo)
            ->first();

        if (!$week) {
            return;
        }

        SyllabusWeek::query()
            ->where('syllabus_id', $this->syllabus->id)
            ->where('exam_type', $type)
            ->update([
                'exam_type' => null,
                'is_exam_week' => false,
            ]);

        $week->update([
            'exam_type' => $type,
            'is_exam_week' => true,
        ]);

        $this->refreshWeeklyCoverage(false);
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    public function clearExamWeek(string $type): void
    {
        $this->loadData();
        if (!$this->syllabus) {
            return;
        }

        $validTypes = ['first_term', 'second_term', 'final_term'];
        if (!in_array($type, $validTypes, true)) {
            return;
        }

        SyllabusWeek::query()
            ->where('syllabus_id', $this->syllabus->id)
            ->where('exam_type', $type)
            ->update([
                'exam_type' => null,
                'is_exam_week' => false,
            ]);

        $this->refreshWeeklyCoverage(false);
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage');
    }

    public function setActiveWeekTab(string $tab): void
    {
        if (!str_starts_with($tab, 'week_')) {
            return;
        }

        $weekNo = (int) str_replace('week_', '', $tab);
        if ($weekNo <= 0) {
            return;
        }

        $exists = $this->syllabusWeeks->contains(fn($week) => (int) $week->week_no === $weekNo);
        if (!$exists) {
            return;
        }

        // Save previous week before switching
        if ($this->activeWeekTab) {
            $prevWeekNo = (int) str_replace('week_', '', (string) $this->activeWeekTab);
            $this->saveWeeklyEntries($prevWeekNo);
        }

        $this->activeWeekTab = $tab;
        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage', message: 'Week saved.');
    }

    /**
     * Called from Alpine when switching accordion weeks.
     * Receives all pending wire:model.defer values and saves only the specified week.
     */
    public function saveWeek(int $weekNo): void
    {
        if ($weekNo <= 0) {
            Log::debug('[WeeklyCoverageStep] saveWeek: invalid weekNo', ['weekNo' => $weekNo]);
            return;
        }

        Log::debug('[WeeklyCoverageStep] saveWeek: called', [
            'weekNo' => $weekNo,
            'payload' => $this->weekInputs[(string)$weekNo] ?? null,
        ]);

        // saveWeeklyEntries() always reloads weeks from DB — no pre-check needed here
        $this->saveWeeklyEntries($weekNo);
    }

    /**
     * Manual save button - saves all weekly entries at once.
     */
    public function saveAllWeeklyEntries(): void
    {
        dd($this->weekInputs);
        // Ensure syllabus is loaded
        // if (!$this->syllabus) {
        //     $this->syllabus = Syllabus::with('academicCalendar')->findOrFail($this->syllabusId);
        // }

        $this->saveWeeklyEntries();
        $this->refreshWeeklyCoverage(false);
        $this->populateWeekInputs();

        $this->dispatch('syllabus-step-saved', step: 'weekly_coverage', message: 'All weekly entries saved successfully.');
    }

    public function render()
    {
        // Always fetch fresh — protected Collection is not persisted across Livewire requests
        $this->syllabusWeeks = SyllabusWeek::query()
            ->where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();
        $this->weeksGenerated = $this->syllabusWeeks->isNotEmpty();

        return view('livewire.syllabus.steps.weekly-coverage', [
            'syllabusWeeks' => $this->syllabusWeeks,
        ]);
    }

    private function loadData(bool $force = false): void
    {
        Log::info('[WeeklyCoverageStep] loadData called', [
            'syllabusId' => $this->syllabusId,
            'force' => $force,
        ]);
        // Always reload collections for stateless Livewire requests

        $this->syllabus = Syllabus::query()
            ->with('academicCalendar')
            ->findOrFail($this->syllabusId);
        $this->academic_calendar_id = $this->syllabus->academic_calendar_id ? (int) $this->syllabus->academic_calendar_id : null;

        // Load course components (lecture and lab schedules)
        $components = \App\Models\CourseComponent::query()
            ->where('syllabus_id', $this->syllabusId)
            ->get()
            ->keyBy('type')
            ->toArray();
        $this->courseComponents = $components;

        // Decide which component tab should be active
        if (isset($this->courseComponents['LEC'])) {
            $this->activeComponent = 'LEC';
        } elseif (isset($this->courseComponents['LAB'])) {
            $this->activeComponent = 'LAB';
        } else {
            $this->activeComponent = 'LEC';
        }

        $this->courseOutcomes = $this->syllabus->courseOutcomes()
            ->orderBy('co_code')
            ->get()
            ->map(fn($co) => [
                'id' => (int) $co->id,
                'co_code' => $co->co_code,
                'description' => $co->description,
            ])->all();

        $this->refreshWeeklyCoverage(false);
        if ($force || empty($this->weekInputs)) {
            $this->populateWeekInputs();
        }
        $this->isLoaded = true;
        Log::info('[WeeklyCoverageStep] loadData finished', [
            'syllabusWeeksCount' => $this->syllabusWeeks->count(),
            'weekInputsCount' => count($this->weekInputs),
        ]);
    }

    private function refreshWeeklyCoverage(bool $generate = false): void
    {
        if (!$this->syllabus || !$this->syllabus->academic_calendar_id) {
            $this->syllabusWeeks = collect();
            $this->weekEvents = [];
            $this->examWeeks = [];
            $this->activeWeekTab = null;
            return;
        }

        if ($generate) {
            $this->ensureWeeksGenerated();
        }

        $this->loadWeeks();
        $this->loadWeekEvents();
        $this->loadExamWeeks();
        $this->syncActiveWeekTab();
    }

    private function ensureWeeksGenerated(): void
    {
        $existing = SyllabusWeek::query()->where('syllabus_id', $this->syllabus->id)->exists();
        if ($existing) {
            return;
        }

        $calendar = $this->syllabus->academicCalendar;
        if (!$calendar || !$calendar->start_date || !$calendar->end_date) {
            return;
        }

        $hasLEC = isset($this->courseComponents['LEC']);
        $hasLAB = isset($this->courseComponents['LAB']);

        $start = Carbon::parse($calendar->start_date)->startOfDay();
        $end = Carbon::parse($calendar->end_date)->startOfDay();

        $weekNo = 1;
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $weekStart = $cursor->copy();
            $weekEnd = $cursor->copy()->addDays(6);
            if ($weekEnd->gt($end)) {
                $weekEnd = $end->copy();
            }

            $syllabusWeek = SyllabusWeek::create([
                'syllabus_id' => $this->syllabus->id,
                'week_no' => $weekNo,
                'start_date' => $weekStart->toDateString(),
                'end_date' => $weekEnd->toDateString(),
                'is_exam_week' => false,
            ]);

            // Create WeekContent for both LEC and LAB if present
            if ($hasLEC) {
                WeekContent::create([
                    'syllabus_week_id' => $syllabusWeek->id,
                    'component_type' => 'LEC',
                    'learning_outcomes' => 'N/A',
                    'assessment_task' => 'N/A',
                    'topics' => 'N/A',
                    'tla' => 'N/A',
                ]);
            }
            if ($hasLAB) {
                WeekContent::create([
                    'syllabus_week_id' => $syllabusWeek->id,
                    'component_type' => 'LAB',
                    'learning_outcomes' => 'N/A',
                    'assessment_task' => 'N/A',
                    'topics' => 'N/A',
                    'tla' => 'N/A',
                ]);
            }

            $weekNo++;
            $cursor = $weekEnd->copy()->addDay();
        }
    }

    private function loadWeeks(): void
    {
        $this->syllabusWeeks = SyllabusWeek::query()
            ->where('syllabus_id', $this->syllabus->id)
            ->orderBy('week_no')
            ->get();
        $this->weeksGenerated = $this->syllabusWeeks->isNotEmpty();
    }

    private function syncActiveWeekTab(): void
    {
        if ($this->activeWeekTab) {
            $activeWeekNo = (int) str_replace('week_', '', (string) $this->activeWeekTab);
            $exists = $this->syllabusWeeks->contains(fn($week) => (int) $week->week_no === $activeWeekNo);
            if ($exists) {
                return;
            }
        }

        if ($this->syllabusWeeks->isEmpty()) {
            $this->activeWeekTab = null;
            return;
        }

        $firstWeek = $this->syllabusWeeks->first();
        if ($firstWeek) {
            $this->activeWeekTab = 'week_' . $firstWeek->week_no;
        }
    }

    private function loadExamWeeks(): void
    {
        $examWeeks = [];
        foreach ($this->syllabusWeeks as $week) {
            if ($week->exam_type) {
                $examWeeks[$week->exam_type] = $week->week_no;
            }
        }
        $this->examWeeks = $examWeeks;
    }

    private function loadWeekEvents(): void
    {
        $calendarId = $this->syllabus->academic_calendar_id;
        if (!$calendarId) {
            $this->weekEvents = [];
            return;
        }

        $events = AcademicCalendarEvent::query()
            ->where('academic_calendar_id', $calendarId)
            ->orderBy('date')
            ->get();

        $weekEvents = [];
        foreach ($this->syllabusWeeks as $week) {
            $weekEvents[$week->week_no] = $events->filter(function ($event) use ($week) {
                $date = Carbon::parse($event->date);
                return $date->between(
                    Carbon::parse($week->start_date),
                    Carbon::parse($week->end_date)
                );
            })->values();
        }

        $this->weekEvents = $weekEvents;
    }

    public function setComponentType(string $type): void
    {
        $type = strtoupper($type);
        if (!in_array($type, ['LEC', 'LAB'], true)) {
            return;
        }

        // If LAB doesn't exist for this course, do nothing
        if ($type === 'LAB' && !isset($this->courseComponents['LAB'])) {
            return;
        }

        // Save current tab data before switching component type
        $this->saveWeeklyEntries();

        $this->activeComponent = $type;
        $this->populateWeekInputs();

        $this->activeComponent = $type;

        foreach ($this->syllabusWeeks as $week) {
            $content = $week->weekContents()
                ->where('component_type', $type)
                ->first();

            $this->weekInputs[$week->week_no] = [
                'course_outcome_id' => $content->course_outcome_id ?? null,
                'learning_outcomes' => $content->learning_outcomes ?? '',
                'assessment_task' => $content->assessment_task ?? '',
                'topic' => $content->topics ?? '',
                'teaching_activities' => $content->tla ?? '',
                'reference_text' => $week->reference?->reference_text ?? '',
                'material_name' => $week->onlineMaterial?->material_name ?? '',
                'material_url' => $week->onlineMaterial?->material_url ?? '',
            ];
        }
    }

    private function populateWeekInputs(): void
    {
        if ($this->syllabusWeeks->isEmpty()) {
            $this->weekInputs = [];
            return;
        }

        $weekIds = $this->syllabusWeeks->pluck('id')->all();

        // Load week contents for the currently active component (LEC/LAB)
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
            $content = $weekContents->get($week->id);

            $reference = $references->get($week->id);
            $material  = $materials->get($week->id);
            $inputs[(string) $week->week_no] = [
                'course_outcome_id' => $content?->course_outcome_id,
                'learning_outcomes' => $content?->learning_outcomes ?? '',
                'assessment_task'   => $content?->assessment_task ?? '',
                'topic'             => $content?->topics ?? '',
                'teaching_activities' => $content?->tla ?? '',
                'reference_text'    => $reference?->reference_text ?? '',
                'material_name'     => $material?->material_name ?? '',
                'material_url'      => $material?->url ?? '',
            ];
        }

        $this->weekInputs = $inputs;
    }

    private function saveWeeklyEntries(?int $onlyWeekNo = null): void
    {
        if (!$this->syllabus) {
            $this->syllabus = Syllabus::findOrFail($this->syllabusId);
        }

        // Always reload fresh from DB — Collection is NOT rehydrated by Livewire across requests
        $this->syllabusWeeks = SyllabusWeek::query()
            ->where('syllabus_id', $this->syllabusId)
            ->orderBy('week_no')
            ->get();

        Log::info('[WeeklyCoverageStep] saveWeeklyEntries called', [
            'syllabusId'        => $this->syllabusId,
            'onlyWeekNo'        => $onlyWeekNo,
            'syllabusWeeksCount' => $this->syllabusWeeks->count(),
            'weekInputs'        => $this->weekInputs,
        ]);

        if ($this->syllabusWeeks->isEmpty()) {
            Log::warning('[WeeklyCoverageStep] saveWeeklyEntries: no weeks found in DB', [
                'syllabusId' => $this->syllabusId,
            ]);
            return;
        }

        foreach ($this->syllabusWeeks as $week) {
            if ($onlyWeekNo && $week->week_no != $onlyWeekNo) continue;
            $payload = $this->weekInputs[(string) $week->week_no] ?? [];
            Log::debug('[WeeklyCoverageStep] saveWeeklyEntries: processing week', [
                'week_no' => $week->week_no,
                'payload' => $payload,
            ]);
            // Always save a WeekContent row for each week/component
            $courseOutcomeId = null;
            if (isset($payload['course_outcome_id']) && $payload['course_outcome_id'] !== '' && $payload['course_outcome_id'] !== null) {
                $courseOutcomeId = (int) $payload['course_outcome_id'];
            }
            $learningOutcomes = trim((string) ($payload['learning_outcomes'] ?? ''));
            $assessmentTask   = trim((string) ($payload['assessment_task'] ?? ''));
            $topic            = trim((string) ($payload['topic'] ?? ''));
            $tla              = trim((string) ($payload['teaching_activities'] ?? $payload['tla'] ?? ''));

            Log::debug('[WeeklyCoverageStep] saveWeeklyEntries: upserting WeekContent', [
                'syllabus_week_id' => $week->id,
                'component_type' => $this->activeComponent,
                'course_outcome_id' => $courseOutcomeId,
                'learning_outcomes' => $learningOutcomes,
                'assessment_task' => $assessmentTask,
                'topics' => $topic,
                'tla' => $tla,
            ]);

            WeekContent::query()->updateOrCreate(
                [
                    'syllabus_week_id' => $week->id,
                    'component_type'   => $this->activeComponent,
                ],
                [
                    'course_outcome_id' => $courseOutcomeId,
                    'learning_outcomes' => $learningOutcomes ?: 'N/A',
                    'assessment_task'   => $assessmentTask   ?: 'N/A',
                    'topics'            => $topic            ?: 'N/A',
                    'tla'               => $tla              ?: 'N/A',
                ]
            );

            // Per-week reference
            $referenceText = trim((string) ($payload['reference_text'] ?? ''));
            Log::debug('[WeeklyCoverageStep] saveWeeklyEntries: upserting Reference', [
                'syllabus_week_id' => $week->id,
                'reference_text' => $referenceText,
            ]);
            Reference::query()
                ->where('syllabus_id', $this->syllabusId)
                ->where('syllabus_week_id', $week->id)
                ->delete();
            if ($referenceText !== '') {
                Reference::create([
                    'syllabus_id'      => $this->syllabusId,
                    'syllabus_week_id' => $week->id,
                    'reference_text'   => $referenceText,
                ]);
            }

            // Per-week material
            $materialName = trim((string) ($payload['material_name'] ?? ''));
            $materialUrl  = trim((string) ($payload['material_url']  ?? ''));
            Log::debug('[WeeklyCoverageStep] saveWeeklyEntries: upserting OnlineMaterial', [
                'syllabus_week_id' => $week->id,
                'material_name' => $materialName,
                'material_url' => $materialUrl,
            ]);
            OnlineMaterial::query()
                ->where('syllabus_id', $this->syllabusId)
                ->where('syllabus_week_id', $week->id)
                ->delete();
            if ($materialName !== '' || $materialUrl !== '') {
                OnlineMaterial::create([
                    'syllabus_id'      => $this->syllabusId,
                    'syllabus_week_id' => $week->id,
                    'material_name'    => $materialName !== '' ? $materialName : 'Online Material',
                    'url'              => $materialUrl,
                ]);
            }
        }
    }

    // Autosave disabled - saving now happens when switching steps via onSaveRequested
    public function updatedWeekInputs($value, $key): void
    {
        if (!$this->isLoaded) return;

        $parts = explode('.', $key); // weekInputs.{weekNo}.field
        if (count($parts) < 3) return;

        $weekNo = (int) $parts[1];
        if ($weekNo <= 0) return;

        $this->saveWeeklyEntries($weekNo);
    }
}
