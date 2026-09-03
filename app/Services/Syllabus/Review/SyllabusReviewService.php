<?php

namespace App\Services\Syllabus\Review;

use App\Models\AuditLog;
use App\Models\Syllabus;
use App\Models\SyllabusReviewer;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SyllabusReviewService
{
    public function assignReviewer(Syllabus $syllabus, int $reviewerUserId, string $role = 'member'): SyllabusReviewer
    {
        if ($reviewerUserId <= 0) {
            throw ValidationException::withMessages(['reviewer' => 'Please select a reviewer.']);
        }

        if (! in_array($role, ['chair', 'member'], true)) {
            throw ValidationException::withMessages(['role' => 'Invalid reviewer role.']);
        }

        if ($syllabus->reviewers()->where('user_id', $reviewerUserId)->exists()) {
            throw ValidationException::withMessages(['reviewer' => 'This reviewer is already added.']);
        }

        if ($role === 'chair' && $syllabus->reviewers()->where('role', 'chair')->exists()) {
            throw ValidationException::withMessages(['reviewer' => 'A Chair reviewer is already assigned. Remove them first.']);
        }

        $reviewer = $syllabus->reviewers()->create([
            'user_id' => $reviewerUserId,
            'status'  => 'pending',
            'role'    => $role,
        ]);

        $reviewerName = User::find($reviewerUserId)?->name ?? "user #{$reviewerUserId}";
        AuditLog::record(
            action: 'reviewer_assigned',
            module: 'Syllabus Review',
            referenceId: $syllabus->id,
            description: "Assigned {$reviewerName} as {$role} reviewer for {$this->syllabusLabel($syllabus)}."
        );

        return $reviewer;
    }

    public function removeReviewer(Syllabus $syllabus, int $syllabusReviewerId): void
    {
        $reviewer = $syllabus->reviewers()->with('user')->whereKey($syllabusReviewerId)->first();
        if (! $reviewer) {
            return;
        }

        $reviewerName = $reviewer->user?->name ?? "user #{$reviewer->user_id}";
        $role = $reviewer->role;
        $reviewer->delete();

        AuditLog::record(
            action: 'reviewer_removed',
            module: 'Syllabus Review',
            referenceId: $syllabus->id,
            description: "Removed {$reviewerName} as {$role} reviewer from {$this->syllabusLabel($syllabus)}."
        );
    }

    public function updateReviewerStatus(Syllabus $syllabus, int $syllabusReviewerId, string $status): void
    {
        $allowed = ['pending', 'approved', 'rejected'];
        if (! in_array($status, $allowed, true)) {
            throw ValidationException::withMessages(['status' => 'Invalid reviewer status.']);
        }

        $reviewer = $syllabus->reviewers()->whereKey($syllabusReviewerId)->first();
        if (! $reviewer) {
            return;
        }

        $previousStatus = $reviewer->status;
        $reviewer->update(['status' => $status]);

        $reviewer->loadMissing('user');
        $reviewerName = $reviewer->user?->name ?? "user #{$reviewer->user_id}";
        AuditLog::record(
            action: 'reviewer_status_updated',
            module: 'Syllabus Review',
            referenceId: $syllabus->id,
            description: "Changed {$reviewerName}'s reviewer status from {$previousStatus} to {$status} for {$this->syllabusLabel($syllabus)}."
        );
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
