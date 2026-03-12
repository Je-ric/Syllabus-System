<?php

namespace App\Services\Syllabus;

use App\Models\Syllabus;
use Carbon\Carbon;

class SyllabusRevisionHistoryService
{

    // Upsert one or more revision rows.

    // revision_no is now ALWAYS taken from the payload — no auto-assignment.
    // The caller (ReviewStep) is responsible for supplying the number.
    // Validation (non-negative int) is enforced here so the service stays
    // self-contained regardless of where it is called from.

    // On UPDATE: revision_no is also updated so the user can renumber inline.
    // On INSERT: revision_no is required in the payload and used as-is.

    public function upsertMany(Syllabus $syllabus, array $rows): void
    {
        foreach ($rows as $row) {
            $implementationSemester = trim((string) ($row['implementation_semester'] ?? ''));
            if ($implementationSemester === '') {
                continue;
            }

            // revision_no must be explicitly supplied and non-negative.
            if (! array_key_exists('revision_no', $row)
                || $row['revision_no'] === null
                || $row['revision_no'] === '') {
                throw new \InvalidArgumentException('Revision number is required.');
            }

            $revisionNo = (int) $row['revision_no'];
            if ($revisionNo < 0) {
                throw new \InvalidArgumentException('Revision number must be 0 or higher.');
            }

            $revisionDate = $this->normalizeDate($row['revision_date'] ?? null)
                ?? now()->toDateString();

            // Full payload — revision_no included for both insert AND update.
            $payload = [
                'revision_no'             => $revisionNo,
                'revision_date'           => $revisionDate,
                'implementation_semester' => $implementationSemester,
                'highlights'              => $this->nullIfBlank($row['highlights'] ?? null),
                'contributors'            => $this->nullIfBlank($row['contributors'] ?? null),
            ];

            $id = (int) ($row['id'] ?? 0);
            if ($id > 0) {
                // UPDATE existing row — all fields including revision_no.
                $existing = $syllabus->revisions()->whereKey($id)->first();
                if ($existing) {
                    $existing->update($payload);
                }
                continue;
            }

            // INSERT new row.
            $syllabus->revisions()->create($payload);
        }
    }


    // Resequence all revisions for a syllabus.
    // Sorts the current rows by their existing revision_no (stable sort),
    // then reassigns 0, 1, 2, … in that order.
    // Returns the resequenced rows as an array for the caller to re-render.

    public function resequence(Syllabus $syllabus): void
    {
        $revisions = $syllabus->revisions()
            ->orderBy('revision_no')
            ->orderBy('id')   // tie-break: earlier insert first
            ->get();

        foreach ($revisions as $index => $revision) {
            $revision->update(['revision_no' => $index]);
        }
    }

    public function delete(Syllabus $syllabus, int $revisionId): void
    {
        $syllabus->revisions()->whereKey($revisionId)->delete();
    }

    // ── Private helpers ───────────────────────────────────────────────────

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
