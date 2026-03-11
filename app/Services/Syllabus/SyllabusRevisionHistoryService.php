<?php

namespace App\Services\Syllabus;

use App\Models\Syllabus;
use Carbon\Carbon;

class SyllabusRevisionHistoryService
{
    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function upsertMany(Syllabus $syllabus, array $rows): void
    {
        foreach ($rows as $row) {
            $implementationSemester = trim((string) ($row['implementation_semester'] ?? ''));
            if ($implementationSemester === '') {
                continue;
            }

            $revisionDate = $this->normalizeDate($row['revision_date'] ?? null) ?? now()->toDateString();

            $payload = [
                'revision_date'           => $revisionDate,
                'implementation_semester' => $implementationSemester,
                'highlights'              => $this->nullIfBlank($row['highlights'] ?? null),
                'contributors'            => $this->nullIfBlank($row['contributors'] ?? null),
            ];

            $id = (int) ($row['id'] ?? 0);
            if ($id > 0) {
                $existing = $syllabus->revisions()->whereKey($id)->first();
                if ($existing) {
                    $existing->update($payload);
                }
                continue;
            }

            // revision_no is 0-based. Use null coalescing so explicit 0 is preserved.
            // Only auto-assign when revision_no is not provided at all (null/missing).
            $revisionNo = array_key_exists('revision_no', $row)
                ? (int) $row['revision_no']
                : null;

            if ($revisionNo === null) {
                // Not provided — auto-assign as max + 1
                $revisionNo = ((int) ($syllabus->revisions()->max('revision_no') ?? -1)) + 1;
            }

            $syllabus->revisions()->create($payload + [
                'revision_no' => $revisionNo,
            ]);
        }
    }

    public function delete(Syllabus $syllabus, int $revisionId): void
    {
        $syllabus->revisions()->whereKey($revisionId)->delete();
    }

    private function nullIfBlank(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        return $value === '' ? null : $value;
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
