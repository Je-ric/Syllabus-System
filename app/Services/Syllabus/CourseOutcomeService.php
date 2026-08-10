<?php

namespace App\Services\Syllabus;

use App\Models\CourseOutcome;
use App\Models\Syllabus;
use App\Models\WeekContent;
use Illuminate\Support\Facades\DB;

// All database operations for CourseOutcome records.
//
// co_code is always derived from the row's position (1-based) among all saved
// outcomes for the syllabus — not stored as a fixed value on create. It is
// re-synced on every create/delete so the codes stay sequential (CO1, CO2, …).
//
// The service is intentionally free of Livewire — it has no knowledge of
// component state, dispatching, or $this. The component keeps that logic.
class CourseOutcomeService
{
    // Return all outcomes for a syllabus as plain arrays, ordered by co_code.
    // @return list<array{id:int,co_code:string,description:string}>
    public function all(int $syllabusId): array
    {
        return CourseOutcome::where('syllabus_id', $syllabusId)
            ->orderBy('co_code')
            ->get()
            ->map(fn ($co) => [
                'id'          => $co->id,
                'co_code'     => $co->co_code,
                'description' => $co->description,
            ])
            ->values()
            ->all();
    }

    // Create a new outcome and re-sync all codes.
    // Returns the new row as a plain array.
    // @throws \InvalidArgumentException on blank description
    // @return array{id:int,co_code:string,description:string}
    public function create(int $syllabusId, string $description): array
    {
        $description = trim($description);

        if ($description === '') {
            throw new \InvalidArgumentException('Course Outcome description cannot be blank.');
        }

        // Temporary code — will be resynced immediately after
        $outcome = CourseOutcome::create([
            'syllabus_id' => $syllabusId,
            'co_code'     => 'CO0',
            'description' => $description,
        ]);

        $this->resyncCodes($syllabusId);

        // Return the freshly-coded row
        return $this->findAsArray($syllabusId, $outcome->id);
    }

    // Batch create multiple outcomes and re-sync codes once at the end.
    // Much more efficient than calling create() multiple times.
    // @param  array  $descriptions  List of outcome descriptions
    // @return array<int, array{id:int,co_code:string,description:string}>
    public function createBatch(int $syllabusId, array $descriptions): array
    {
        $validDescriptions = collect($descriptions)
            ->map(fn ($desc) => trim($desc))
            ->filter(fn ($desc) => $desc !== '')
            ->values()
            ->all();

        if (empty($validDescriptions)) {
            return [];
        }

        // Batch insert with unique temporary codes to avoid constraint violation
        $now = now();
        $outcomesToInsert = collect($validDescriptions)
            ->map(function ($desc, $index) use ($syllabusId, $now) {
                return [
                    'syllabus_id' => $syllabusId,
                    'co_code'     => 'CO_TEMP_' . $index . '_' . time() . '_' . rand(1000, 9999), // Unique temporary code
                    'description' => $desc,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            })
            ->all();

        DB::table('course_outcomes')->insert($outcomesToInsert);

        // Re-sync codes once for all new outcomes
        $this->resyncCodes($syllabusId);

        // Return all outcomes for the syllabus
        return $this->all($syllabusId);
    }

    // Update an existing outcome's description and re-sync codes.
    // @throws \InvalidArgumentException on blank description or not found
    // @return array{id:int,co_code:string,description:string}
    public function update(int $syllabusId, int $outcomeId, string $description): array
    {
        $description = trim($description);

        if ($description === '') {
            throw new \InvalidArgumentException('Course Outcome description cannot be blank.');
        }

        $outcome = CourseOutcome::where('syllabus_id', $syllabusId)
            ->where('id', $outcomeId)
            ->firstOrFail();

        $outcome->update(['description' => $description]);

        $this->resyncCodes($syllabusId);

        return $this->findAsArray($syllabusId, $outcome->id);
    }

    // Count how many WeekContent rows are currently linked to this outcome.
    // Used by the UI to warn the faculty before deletion.
    public function countLinkedWeeks(int $outcomeId): int
    {
        return WeekContent::where('course_outcome_id', $outcomeId)->count();
    }

    // Delete an outcome and re-sync codes so remaining rows stay sequential.
    // Also nulls out any WeekContent rows that referenced this outcome so
    // weekly coverage does not hold a dangling foreign key.
    // @throws \Illuminate\Database\Eloquent\ModelNotFoundException
    public function delete(int $syllabusId, int $outcomeId): void
    {
        CourseOutcome::where('syllabus_id', $syllabusId)
            ->where('id', $outcomeId)
            ->firstOrFail()
            ->delete();

        // Null out the reference on any week content rows that used this CO
        // so weekly coverage does not hold a dangling foreign key.
        WeekContent::where('course_outcome_id', $outcomeId)
            ->update(['course_outcome_id' => null]);

        $this->resyncCodes($syllabusId);
    }

    // Renumber all outcomes CO1, CO2, … in their current DB order.
    // Called after every create/delete to keep codes sequential.
    // Optimized with batch update using transactions.
    public function resyncCodes(int $syllabusId): void
    {
        $outcomes = CourseOutcome::where('syllabus_id', $syllabusId)
            ->orderBy('id')
            ->get();

        if ($outcomes->isEmpty()) {
            return;
        }

        // Collect updates needed (including temporary codes)
        $updates = [];
        foreach ($outcomes as $i => $outcome) {
            $code = 'CO' . ($i + 1);
            // Update if code doesn't match OR if it's a temporary code
            if ($outcome->co_code !== $code || str_starts_with($outcome->co_code, 'CO_TEMP_')) {
                $updates[$outcome->id] = $code;
            }
        }

        // Batch update within transaction for performance
        if (!empty($updates)) {
            DB::transaction(function () use ($updates) {
                foreach ($updates as $id => $code) {
                    CourseOutcome::where('id', $id)->update(['co_code' => $code]);
                }
            });
        }
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function findAsArray(int $syllabusId, int $outcomeId): array
    {
        $co = CourseOutcome::where('syllabus_id', $syllabusId)
            ->where('id', $outcomeId)
            ->firstOrFail();

        return [
            'id'          => $co->id,
            'co_code'     => $co->co_code,
            'description' => $co->description,
        ];
    }
}
