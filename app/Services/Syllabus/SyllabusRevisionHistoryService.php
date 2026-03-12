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

            if (! array_key_exists('revision_no', $row) || $row['revision_no'] === null || $row['revision_no'] === '') {
                throw new \InvalidArgumentException('Revision number is required.');
            }

            $revisionNo = (int) $row['revision_no'];
            if ($revisionNo < 0) {
                throw new \InvalidArgumentException('Revision number must be 0 or higher.');
            }

            $revisionDate = $this->normalizeDate($row['revision_date'] ?? null) ?? now()->toDateString();

            $payload = [
                'revision_no'             => $revisionNo,
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

            $syllabus->revisions()->create($payload);
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
