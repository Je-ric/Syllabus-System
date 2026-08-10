<?php

namespace App\Services\Syllabus\Review;

use App\Data\ReviewCriteria;
use App\Models\Syllabus;
use App\Models\SyllabusReviewForm;
use App\Models\SyllabusReviewer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Handles all F.003 review form mutations.
//
// Public API:
//   findOrCreate(Syllabus)                                                  → SyllabusReviewForm
//   setClassification(SyllabusReviewForm, string)                           → void
//   syncNatureOfChange(SyllabusReviewForm, array)                           → void
//   syncAttachments(SyllabusReviewForm, array)                              → void
//   saveChecklistResponse(SyllabusReviewForm, int, string, string, ?string) → void
//   recordDecision(SyllabusReviewForm, string, ?string, ?string)            → void
//   savePartHResponse(SyllabusReviewForm, string)                           → void
//   verifyPartH(SyllabusReviewForm, int)                                    → void
//   recommendApproval(SyllabusReviewForm, int)                              → void
//   recordDeanApproval(SyllabusReviewForm, int)                             → void
//   isChecklistComplete(SyllabusReviewForm, int)                            → bool
//   generateReviewFormHtml(SyllabusReviewForm)                              → string
class SyllabusReviewFormService
{
    // ── Find or create ────────────────────────────────────────────────────────

    public function findOrCreate(Syllabus $syllabus): SyllabusReviewForm
    {
        return SyllabusReviewForm::firstOrCreate(
            ['syllabus_id' => $syllabus->id],
            ['course_lead_name' => $syllabus->preparer?->name]
        );
    }

    // ── Part B — Classification ───────────────────────────────────────────────

    // Changing classification resets checklist responses (track-specific criteria differ).
    public function setClassification(SyllabusReviewForm $form, string $classification): void
    {
        if (! in_array($classification, ['updating', 'revision'])) {
            throw new \InvalidArgumentException("Invalid classification: {$classification}");
        }

        if ($form->classification === $classification) {
            return;
        }

        DB::transaction(function () use ($form, $classification) {
            // Reset track-specific checklist responses when switching tracks
            $form->checklistResponses()
                ->whereIn('section', ['C_updating', 'C_revision'])
                ->delete();

            $form->update(['classification' => $classification]);
        });
    }

    // ── Part C — Nature of change ─────────────────────────────────────────────

    // $selectedTypes = array of change_type strings that are checked
    public function syncNatureOfChange(SyllabusReviewForm $form, array $selectedTypes): void
    {
        $allowedUpdating = ['schedule_calendar', 'faculty_contact', 'references_textbooks',
            'typographical_formatting', 'minor_administrative', 'other_updating'];
        $allowedRevision = ['stakeholder_feedback', 'cqi_findings', 'policy_curricular',
            'accreditation_qa', 'change_in_cos_po_mapping',
            'change_in_grading_assessments_content', 'other_revision'];
        $allowed = array_merge($allowedUpdating, $allowedRevision);

        $selectedTypes = array_filter($selectedTypes, fn ($t) => in_array($t, $allowed));

        DB::transaction(function () use ($form, $selectedTypes) {
            $form->natureOfChange()->delete();
            foreach ($selectedTypes as $type) {
                $form->natureOfChange()->create(['change_type' => $type]);
            }
        });
    }

    // ── Part D — Attachments ──────────────────────────────────────────────────

    // $submitted = ['draft_syllabus' => true, 'other' => true, 'other_label' => 'My doc', ...]
    public function syncAttachments(SyllabusReviewForm $form, array $submitted): void
    {
        $allowed = ['draft_syllabus', 'cqi_report', 'feedback_summary',
            'policy_memo', 'mapping_evidence', 'other'];

        DB::transaction(function () use ($form, $submitted, $allowed) {
            $form->attachments()->delete();
            foreach ($allowed as $type) {
                if (! empty($submitted[$type])) {
                    $form->attachments()->create([
                        'attachment_type' => $type,
                        'is_submitted'    => true,
                        'other_label'     => $type === 'other' ? ($submitted['other_label'] ?? null) : null,
                    ]);
                }
            }
        });
    }

    // ── Part E — Checklist ────────────────────────────────────────────────────

    public function saveChecklistResponse(
        SyllabusReviewForm $form,
        int $reviewerUserId,
        string $criterionCode,
        string $response,
        ?string $comments
    ): void {
        if (! in_array($response, ['satisfied', 'not_satisfied', 'not_applicable'])) {
            throw new \InvalidArgumentException("Invalid response: {$response}");
        }

        $section = ReviewCriteria::sectionForCode($criterionCode);
        if (! $section) {
            throw new \InvalidArgumentException("Unknown criterion code: {$criterionCode}");
        }

        $form->checklistResponses()->updateOrCreate(
            [
                'reviewer_user_id' => $reviewerUserId,
                'criterion_code'   => $criterionCode,
            ],
            [
                'section'  => $section,
                'response' => $response,
                'comments' => $comments,
            ]
        );

        // Flip reviewer status to approved when all criteria are answered
        if ($this->isChecklistComplete($form, $reviewerUserId)) {
            SyllabusReviewer::where('syllabus_id', $form->syllabus_id)
                ->where('user_id', $reviewerUserId)
                ->update(['status' => 'approved']);
        }
    }

    public function isChecklistComplete(SyllabusReviewForm $form, int $reviewerUserId): bool
    {
        $required = ReviewCriteria::codesForClassification($form->classification ?? 'updating');
        $answered = $form->checklistResponses()
            ->where('reviewer_user_id', $reviewerUserId)
            ->whereNotNull('response')
            ->pluck('criterion_code')
            ->all();

        return count(array_diff($required, $answered)) === 0;
    }

    // ── Part F — Decision ─────────────────────────────────────────────────────

    public function recordDecision(
        SyllabusReviewForm $form,
        string $decision,
        ?string $requiredActions,
        ?string $targetDate
    ): void {
        $allowed = ['approved_as_updating', 'approved_as_revision',
            'approved_with_corrections', 'returned_for_revision', 'reclassified_as_revision'];

        if (! in_array($decision, $allowed)) {
            throw new \InvalidArgumentException("Invalid decision: {$decision}");
        }

        DB::transaction(function () use ($form, $decision, $requiredActions, $targetDate) {
            $form->update([
                'decision'               => $decision,
                'decision_made_at'       => now(),
                'decision_made_by'       => Auth::id(),
                'required_actions'       => $requiredActions,
                'target_compliance_date' => $targetDate,
            ]);

            if ($decision === 'returned_for_revision') {
                Syllabus::where('id', $form->syllabus_id)
                    ->update(['status' => 'for_revision']);
            }

            if ($decision === 'reclassified_as_revision') {
                // Reset checklist + reviewer statuses; author must assign members
                $form->checklistResponses()->delete();
                SyllabusReviewer::where('syllabus_id', $form->syllabus_id)
                    ->update(['status' => 'pending']);
                $form->update(['classification' => 'revision']);
            }
        });
    }

    // ── Part H — Faculty compliance response ──────────────────────────────────

    public function savePartHResponse(SyllabusReviewForm $form, string $response): void
    {
        if (blank($response)) {
            throw new \InvalidArgumentException('Part H response cannot be empty.');
        }

        $form->update([
            'part_h_faculty_response' => $response,
            'part_h_faculty_response_updated_at' => now(),
        ]);
    }

    public function verifyPartH(SyllabusReviewForm $form, int $verifierUserId): void
    {
        if (blank($form->part_h_faculty_response)) {
            throw new \RuntimeException('Cannot verify Part H — faculty has not submitted a response.');
        }

        $form->update([
            'part_h_verified_by' => $verifierUserId,
            'part_h_verified_at' => now(),
        ]);
    }

    // ── Part I — Approval authority ───────────────────────────────────────────

    public function recommendApproval(SyllabusReviewForm $form, int $chairUserId): void
    {
        $form->update([
            'recommended_by_chair_id' => $chairUserId,
            'recommended_by_chair_at' => now(),
        ]);
    }

    public function recordDeanApproval(SyllabusReviewForm $form, int $deanUserId): void
    {
        DB::transaction(function () use ($form, $deanUserId) {
            $filingType = $form->classification === 'updating'
                ? 'updating_department'
                : 'revision_oloi';

            $form->update([
                'approved_by_dean_id' => $deanUserId,
                'approved_by_dean_at' => now(),
                'filed_at'            => now(),
                'filing_type'         => $filingType,
            ]);

            Syllabus::where('id', $form->syllabus_id)
                ->update(['status' => 'approved']);
        });
    }

    // ── HTML snapshot ─────────────────────────────────────────────────────────

    public function generateReviewFormHtml(SyllabusReviewForm $form): string
    {
        $form->load([
            'syllabus.course.program.departments.college',
            'syllabus.academicCalendar',
            'syllabus.preparer',
            'syllabus.components.user',
            'syllabus.reviewers.user',
            'natureOfChange',
            'attachments',
            'checklistResponses',
            'recommendedByChair',
            'approvedByDean',
            'partHVerifier',
        ]);

        $reviewCss = @file_get_contents(resource_path('css/review.css')) ?: '';

        return view('Syllabus.template.review_form', [
            'syllabus'       => $form->syllabus,
            'reviewForm'     => $form,
            'isSnapshot'     => true,
            'inlineReviewCss'=> $reviewCss,
        ])->render();
    }
}
