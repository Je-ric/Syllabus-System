<?php

namespace App\Services\Syllabus;

use App\Models\Syllabus;
use App\Models\SyllabusReviewer;
use Illuminate\Validation\ValidationException;

class SyllabusReviewService
{
    public function assignReviewer(Syllabus $syllabus, int $reviewerUserId): SyllabusReviewer
    {
        if ($reviewerUserId <= 0) {
            throw ValidationException::withMessages([
                'reviewer' => 'Please select a reviewer.',
            ]);
        }

        $exists = $syllabus->reviewers()
            ->where('user_id', $reviewerUserId)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'reviewer' => 'This reviewer is already added.',
            ]);
        }

        // No approval flow yet: assigning a reviewer marks them approved instantly.
        return $syllabus->reviewers()->create([
            'user_id' => $reviewerUserId,
            'status' => 'approved',
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
            throw ValidationException::withMessages([
                'status' => 'Invalid reviewer status.',
            ]);
        }

        $reviewer = $syllabus->reviewers()->whereKey($syllabusReviewerId)->first();
        if (! $reviewer) {
            return;
        }

        $reviewer->update(['status' => $status]);
    }
}

