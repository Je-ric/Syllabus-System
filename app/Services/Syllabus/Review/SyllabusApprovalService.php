<?php

namespace App\Services\Syllabus\Review;

use App\Models\AuditLog;
use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Validation\ValidationException;

// Handles all approved_by / concurred_by mutations on a syllabus.
//
// Business rules:
//   approved_by  — any dean; nullable
//   concurred_by — any dean; nullable; must differ from approved_by when both are set
class SyllabusApprovalService
{
    // ── Approved By ───────────────────────────────────────────────────────────

    public function setApprovedBy(Syllabus $syllabus, ?int $userId): void
    {
        $syllabus->update(['approved_by' => $userId ?: null]);

        AuditLog::record(
            action: $userId ? 'approved_by_set' : 'approved_by_cleared',
            module: 'Syllabus',
            referenceId: $syllabus->id,
            description: $userId
                ? 'Set approved-by to ' . $this->userName($userId) . ' on ' . $this->syllabusLabel($syllabus) . '.'
                : 'Cleared approved-by on ' . $this->syllabusLabel($syllabus) . '.'
        );
    }

    public function clearApprovedBy(Syllabus $syllabus): void
    {
        $syllabus->update(['approved_by' => null]);

        AuditLog::record(
            action: 'approved_by_cleared',
            module: 'Syllabus',
            referenceId: $syllabus->id,
            description: 'Cleared approved-by on ' . $this->syllabusLabel($syllabus) . '.'
        );
    }

    // ── Concurred By ──────────────────────────────────────────────────────────

    // @throws ValidationException when concurred_by === approved_by
    public function setConcurredBy(Syllabus $syllabus, ?int $userId): void
    {
        $approvedBy = $syllabus->fresh()->approved_by;

        if ($userId && (int) $userId === (int) $approvedBy) {
            throw ValidationException::withMessages([
                'concurred_by' => 'Concurred-by must be a different dean from Approved-by.',
            ]);
        }

        $syllabus->update(['concurred_by' => $userId ?: null]);

        AuditLog::record(
            action: $userId ? 'concurred_by_set' : 'concurred_by_cleared',
            module: 'Syllabus',
            referenceId: $syllabus->id,
            description: $userId
                ? 'Set concurred-by to ' . $this->userName($userId) . ' on ' . $this->syllabusLabel($syllabus) . '.'
                : 'Cleared concurred-by on ' . $this->syllabusLabel($syllabus) . '.'
        );
    }

    public function clearConcurredBy(Syllabus $syllabus): void
    {
        $syllabus->update(['concurred_by' => null]);

        AuditLog::record(
            action: 'concurred_by_cleared',
            module: 'Syllabus',
            referenceId: $syllabus->id,
            description: 'Cleared concurred-by on ' . $this->syllabusLabel($syllabus) . '.'
        );
    }

    private function userName(int $userId): string
    {
        return User::find($userId)?->name ?? "user #{$userId}";
    }

    private function syllabusLabel(Syllabus $syllabus): string
    {
        $syllabus->loadMissing('course');
        $courseCode = $syllabus->course?->course_code;

        return $courseCode
            ? "syllabus #{$syllabus->id} ({$courseCode})"
            : "syllabus #{$syllabus->id}";
    }
}
