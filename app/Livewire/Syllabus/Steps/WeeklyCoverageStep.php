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
use Livewire\Component;

class WeeklyCoverageStep extends Component
{
    public int $syllabusId;
    public bool $isLoaded = false;
    public ?Syllabus $syllabus = null;
    public ?int $academic_calendar_id = null;

    /** @var Collection<int, SyllabusWeek> */
    public Collection $syllabusWeeks;
    public array $weekEvents = [];
    public array $examWeeks = [];
    public ?string $activeWeekTab = null;
    public bool $weeksGenerated = false;
    public array $courseComponents = [];
    public array $courseOutcomes = [];
    public array $weekInputs = [];

    public function boot(): void
    {
        $this->syllabusWeeks = collect();
    }

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
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

        $this->activeWeekTab = $tab;
    }

    public function render()
    {
        return view('livewire.syllabus.steps.weekly-coverage');
    }

    private function loadData(bool $force = false): void
    {
        if ($this->isLoaded && !$force) {
            return;
        }

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

        $this->courseOutcomes = $this->syllabus->courseOutcomes()
            ->orderBy('co_code')
            ->get()
            ->map(fn($co) => [
                'id' => (int) $co->id,
                'co_code' => $co->co_code,
                'description' => $co->description,
            ])->all();

        $this->refreshWeeklyCoverage(false);
        $this->populateWeekInputs();
        $this->isLoaded = true;
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

            SyllabusWeek::create([
                'syllabus_id' => $this->syllabus->id,
                'week_no' => $weekNo,
                'start_date' => $weekStart->toDateString(),
                'end_date' => $weekEnd->toDateString(),
                'is_exam_week' => false,
            ]);

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

    private function populateWeekInputs(): void
    {
        if ($this->syllabusWeeks->isEmpty()) {
            $this->weekInputs = [];
            return;
        }

        $weekIds = $this->syllabusWeeks->pluck('id')->all();

        $weekContents = WeekContent::query()
            ->whereIn('syllabus_week_id', $weekIds)
            ->where('component_type', 'LEC')
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
            $content  = $weekContents->get($week->id);
            $reference = $references->get($week->id);
            $material  = $materials->get($week->id);

            $inputs[$week->week_no] = [
                'course_outcome_id'  => $content?->course_outcome_id,
                'learning_outcomes'  => (string) ($content?->learning_outcomes ?? ''),
                'assessment_task'    => (string) ($content?->assessment_task ?? ''),
                'topic'              => (string) ($content?->topics ?? ''),
                'teaching_activities'=> (string) ($content?->tla ?? ''),
                'reference_text'     => (string) ($reference?->reference_text ?? ''),
                'material_name'      => (string) ($material?->material_name ?? ''),
                'material_url'       => (string) ($material?->url ?? ''),
            ];
        }

        $this->weekInputs = $inputs;
    }

    private function saveWeeklyEntries(): void
    {
        if ($this->syllabusWeeks->isEmpty()) {
            return;
        }

        foreach ($this->syllabusWeeks as $week) {
            $weekNo  = (int) $week->week_no;
            $payload = $this->weekInputs[$weekNo] ?? [];

            $courseOutcomeId  = !empty($payload['course_outcome_id']) ? (int) $payload['course_outcome_id'] : null;
            $learningOutcomes = trim((string) ($payload['learning_outcomes'] ?? ''));
            $assessmentTask   = trim((string) ($payload['assessment_task'] ?? ''));
            $topic            = trim((string) ($payload['topic'] ?? ''));
            $tla              = trim((string) ($payload['teaching_activities'] ?? $payload['tla'] ?? ''));

            if ($learningOutcomes !== '' || $assessmentTask !== '' || $topic !== '' || $tla !== '') {
                WeekContent::query()->updateOrCreate(
                    [
                        'syllabus_week_id' => $week->id,
                        'component_type'   => 'LEC',
                    ],
                    [
                        'course_outcome_id' => $courseOutcomeId,
                        'learning_outcomes' => $learningOutcomes !== '' ? $learningOutcomes : 'N/A',
                        'assessment_task'   => $assessmentTask   !== '' ? $assessmentTask   : 'N/A',
                        'topics'            => $topic            !== '' ? $topic            : 'N/A',
                        'tla'               => $tla              !== '' ? $tla              : 'N/A',
                    ]
                );
            }

            // Per-week reference
            $referenceText = trim((string) ($payload['reference_text'] ?? ''));
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
}
