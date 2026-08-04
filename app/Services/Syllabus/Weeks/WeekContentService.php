<?php

namespace App\Services\Syllabus\Weeks;

use App\Models\OnlineMaterial;
use App\Models\Reference;
use App\Models\SyllabusWeek;
use App\Models\WeekContent;
use Illuminate\Support\Collection;

// All read/write operations for WeekContent, Reference, and OnlineMaterial
// rows, keyed to the Livewire component's $weekInputs array.
//
// $weekInputs shape per key "w{n}":
// [
//   'course_outcome_id'   => int|null,
//   'learning_outcomes'   => string,
//   'assessment_task'     => string,
//   'topic'               => string,
//   'teaching_activities' => string,
//   'references'          => [['text' => string], …],
//   'materials'           => [['name' => string, 'url' => string], …],
// ]
class WeekContentService
{
    // ── Read ──────────────────────────────────────────────────────────────────

    // Build the full $weekInputs array from the database for a given
    // syllabus / component-type combination.
    public function populateInputs(int $syllabusId, string $activeComponent, Collection $syllabusWeeks): array
    {
        if ($syllabusWeeks->isEmpty()) {
            return [];
        }

        $weekIds = $syllabusWeeks->pluck('id')->all();

        $weekContents = WeekContent::whereIn('syllabus_week_id', $weekIds)
            ->where('component_type', $activeComponent)
            ->get()
            ->keyBy('syllabus_week_id');

        $allRefs = Reference::where('syllabus_id', $syllabusId)
            ->whereIn('syllabus_week_id', $weekIds)
            ->where('component_type', $activeComponent)
            ->orderBy('id')
            ->get()
            ->groupBy('syllabus_week_id');

        $allMats = OnlineMaterial::where('syllabus_id', $syllabusId)
            ->whereIn('syllabus_week_id', $weekIds)
            ->where('component_type', $activeComponent)
            ->orderBy('id')
            ->get()
            ->groupBy('syllabus_week_id');

        // Normalise 'N/A' sentinel and null to empty string
        $clean = fn (?string $v): string => ($v === null || $v === 'N/A') ? '' : $v;

        $inputs = [];

        foreach ($syllabusWeeks as $week) {
            $content = $weekContents->get($week->id);

            $refs = $allRefs->has($week->id)
                ? $allRefs->get($week->id)
                    ->map(fn ($r) => ['text' => $clean($r->reference_text)])
                    ->values()->all()
                : [['text' => '']];

            $mats = $allMats->has($week->id)
                ? $allMats->get($week->id)
                    ->map(fn ($m) => ['name' => $clean($m->material_name), 'url' => $m->url ?? ''])
                    ->values()->all()
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

        return $inputs;
    }

    // ── Write ─────────────────────────────────────────────────────────────────

    // Persist one or all unlocked weeks using dirty-checking.
    // Only rows that actually changed are written.
    // Returns true when at least one row was written.
    public function save(
        int $syllabusId,
        string $activeComponent,
        array $weekInputs,
        array $lockedWeeks,
        ?int $onlyWeekNo = null
    ): bool {
        $weeks = SyllabusWeek::where('syllabus_id', $syllabusId)
            ->orderBy('week_no')
            ->get();

        if ($weeks->isEmpty()) {
            return false;
        }

        $changed = false;

        foreach ($weeks as $week) {
            if ($onlyWeekNo !== null && (int) $week->week_no !== $onlyWeekNo) {
                continue;
            }

            // Never write to locked weeks
            if (isset($lockedWeeks[$week->week_no])) {
                continue;
            }

            $key     = 'w' . $week->week_no;
            $payload = $weekInputs[$key] ?? null;

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

            // Dirty-check content fields
            $existing = WeekContent::where('syllabus_week_id', $week->id)
                ->where('component_type', $activeComponent)
                ->first();

            $contentChanged = ! $existing || array_reduce(
                array_keys($incoming),
                fn (bool $carry, string $k) => $carry
                    || ((string) ($existing->{$k} ?? '')) !== ((string) ($incoming[$k] ?? '')),
                false
            );

            // Dirty-check references (sort both sides — order is irrelevant)
            $existingRefs = Reference::where('syllabus_id', $syllabusId)
                ->where('syllabus_week_id', $week->id)
                ->where('component_type', $activeComponent)
                ->pluck('reference_text')
                ->map(fn ($t) => trim((string) $t))
                ->filter()->sort()->values()->all();

            $incomingRefs = collect((array) ($payload['references'] ?? []))
                ->map(fn ($r) => trim((string) ($r['text'] ?? '')))
                ->filter()->sort()->values()->all();

            $refsChanged = $existingRefs !== $incomingRefs;

            // ── Dirty-check materials ─────────────────────────────────────────
            $existingMats = OnlineMaterial::where('syllabus_id', $syllabusId)
                ->where('syllabus_week_id', $week->id)
                ->where('component_type', $activeComponent)
                ->get()
                ->map(fn ($m) => trim($m->material_name ?? '') . '|' . trim($m->url ?? ''))
                ->sort()->values()->all();

            $incomingMats = collect((array) ($payload['materials'] ?? []))
                ->map(fn ($m) => trim((string) ($m['name'] ?? '')) . '|' . trim((string) ($m['url'] ?? '')))
                ->filter(fn ($s) => $s !== '|')
                ->sort()->values()->all();

            $matsChanged = $existingMats !== $incomingMats;

            if (! $contentChanged && ! $refsChanged && ! $matsChanged) {
                continue;
            }

            $changed = true;

            // Write content
            WeekContent::updateOrCreate(
                ['syllabus_week_id' => $week->id, 'component_type' => $activeComponent],
                $incoming
            );

            // Delete + re-insert references on change (simple; counts are small)
            if ($refsChanged) {
                Reference::where('syllabus_id', $syllabusId)
                    ->where('syllabus_week_id', $week->id)
                    ->where('component_type', $activeComponent)
                    ->delete();

                foreach ((array) ($payload['references'] ?? []) as $ref) {
                    $text = trim((string) ($ref['text'] ?? ''));
                    if ($text !== '') {
                        Reference::create([
                            'syllabus_id'      => $syllabusId,
                            'syllabus_week_id' => $week->id,
                            'component_type'   => $activeComponent,
                            'reference_text'   => $text,
                        ]);
                    }
                }
            }

            // Delete + re-insert materials on change
            if ($matsChanged) {
                OnlineMaterial::where('syllabus_id', $syllabusId)
                    ->where('syllabus_week_id', $week->id)
                    ->where('component_type', $activeComponent)
                    ->delete();

                foreach ((array) ($payload['materials'] ?? []) as $mat) {
                    $name = trim((string) ($mat['name'] ?? ''));
                    $url  = trim((string) ($mat['url']  ?? ''));
                    if ($name !== '' || $url !== '') {
                        OnlineMaterial::create([
                            'syllabus_id'      => $syllabusId,
                            'syllabus_week_id' => $week->id,
                            'component_type'   => $activeComponent,
                            'material_name'    => $name ?: 'Online Material',
                            'url'              => $url,
                        ]);
                    }
                }
            }
        }

        return $changed;
    }

    // Reset one editable week: clear all content fields and delete refs/materials.
    // Returns the blank $weekInputs entry, or null if the week is locked or not found.
    public function reset(int $syllabusId, string $activeComponent, int $weekNo, array $lockedWeeks): ?array
    {
        if (isset($lockedWeeks[$weekNo])) {
            return null;
        }

        $week = SyllabusWeek::where('syllabus_id', $syllabusId)
            ->where('week_no', $weekNo)
            ->first();

        if (! $week) {
            return null;
        }

        // Blank the editable columns — keep the WeekContent row itself
        WeekContent::where('syllabus_week_id', $week->id)
            ->where('component_type', $activeComponent)
            ->update([
                'course_outcome_id'  => null,
                'learning_outcomes'  => '',
                'assessment_task'    => '',
                'topics'             => '',
                'tla'                => '',
            ]);

        Reference::where('syllabus_id', $syllabusId)
            ->where('syllabus_week_id', $week->id)
            ->where('component_type', $activeComponent)
            ->delete();

        OnlineMaterial::where('syllabus_id', $syllabusId)
            ->where('syllabus_week_id', $week->id)
            ->where('component_type', $activeComponent)
            ->delete();

        return [
            'course_outcome_id'   => null,
            'learning_outcomes'   => '',
            'assessment_task'     => '',
            'topic'               => '',
            'teaching_activities' => '',
            'references'          => [['text' => '']],
            'materials'           => [['name' => '', 'url' => '']],
        ];
    }
}
