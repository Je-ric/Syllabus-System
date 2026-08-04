<?php

namespace App\Http\Controllers\Syllabus;

use App\Http\Controllers\Controller;
use App\Models\CompleteSyllabus;
use App\Models\Syllabus;
use App\Services\Syllabus\SyllabusSnapshotService;
use Illuminate\Support\Facades\Auth;

class SyllabusReviewFormController extends Controller
{
    public function __construct(
        private readonly SyllabusSnapshotService $snapshotService
    ) {}

    // Live preview — renders the current state of the review form.
    // Accessible by: the syllabus author, assigned reviewers, deans, admins.
    public function preview(Syllabus $syllabus)
    {
        $this->authorizeAccess($syllabus);

        $syllabus->load([
            'course.program.departments.college',
            'academicCalendar',
            'preparer',
            'components',
            'reviewers.user',
            'reviewForm.natureOfChange',
            'reviewForm.attachments',
            'reviewForm.checklistResponses',
            'reviewForm.recommendedByChair',
            'reviewForm.approvedByDean',
            'reviewForm.partHVerifier',
        ]);

        return response(
            view('Syllabus.template.review_form', [
                'syllabus'   => $syllabus,
                'reviewForm' => $syllabus->reviewForm,
            ])->render()
        )->header('Content-Type', 'text/html');
    }

    // Saved-version preview — serves the frozen HTML snapshot from disk.
    public function previewSaved(CompleteSyllabus $completeSyllabus)
    {
        $syllabus = $completeSyllabus->syllabus;
        $this->authorizeAccess($syllabus);

        if (! $completeSyllabus->review_form_path) {
            abort(404, 'No review form snapshot for this version.');
        }

        $html = $this->snapshotService->getSavedHtml($completeSyllabus->review_form_path);

        if (! $html) {
            abort(404, 'Review form snapshot file not found.');
        }

        return response($html)->header('Content-Type', 'text/html');
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function authorizeAccess(Syllabus $syllabus): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $isAuthor   = (int) $syllabus->prepared_by === $user->id;
        $isReviewer = $syllabus->reviewers()->where('user_id', $user->id)->exists();
        $isDean     = $user->hasRole('dean');
        $isAdmin    = $user->hasRole('admin');

        if (! ($isAuthor || $isReviewer || $isDean || $isAdmin)) {
            abort(403);
        }
    }
}
