<?php

namespace App\Services\Syllabus\Review;

use App\Models\Syllabus;
use App\Models\SyllabusReviewer;
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

        return $syllabus->reviewers()->create([
            'user_id' => $reviewerUserId,
            'status'  => 'pending',
            'role'    => $role,
        ]);
    }

    public function removeReviewer(Syllabus $syllabus, int $syllabusReviewerId): void
    {
        $syllabus->reviewers()->whereKey($syllabusReviewerId)->delete();
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

        $reviewer->update(['status' => $status]);
    }
}
