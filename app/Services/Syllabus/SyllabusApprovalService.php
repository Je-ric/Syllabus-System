<?php

namespace App\Services\Syllabus;

use App\Models\Syllabus;
use Illuminate\Validation\ValidationException;

/**
 * Handles all approved_by / concurred_by mutations on a syllabus.
 *
 * Business rules:
 *  • approved_by  — any dean; nullable
 *  • concurred_by — any dean; nullable; MUST differ from approved_by when both are set
 */
class SyllabusApprovalService
{
    // ── Approved By ──────────────────────────────────────────────────────────

    public function setApprovedBy(Syllabus $syllabus, ?int $userId): void
    {
        $syllabus->update(['approved_by' => $userId ?: null]);
    }

    public function clearApprovedBy(Syllabus $syllabus): void
    {
        $syllabus->update(['approved_by' => null]);
    }

    // ── Concurred By ─────────────────────────────────────────────────────────

    /**
     * @throws ValidationException when concurred_by === approved_by
     */
    public function setConcurredBy(Syllabus $syllabus, ?int $userId): void
    {
        $approvedBy = $syllabus->fresh()->approved_by;

        if ($userId && (int) $userId === (int) $approvedBy) {
            throw ValidationException::withMessages([
                'concurred_by' => 'Concurred-by must be a different dean from Approved-by.',
            ]);
        }

        $syllabus->update(['concurred_by' => $userId ?: null]);
    }

    public function clearConcurredBy(Syllabus $syllabus): void
    {
        $syllabus->update(['concurred_by' => null]);
    }
}
