<?php

namespace App\Livewire\Syllabus;

use App\Data\ReviewCriteria;
use App\Models\AuditLog;
use App\Models\Syllabus;
use App\Models\SyllabusReviewForm;
use App\Models\SyllabusReviewer;
use App\Services\Syllabus\Review\SyllabusReviewFormService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

// SyllabusReviewPage
//
// Reviewer-facing Livewire component. Handles:
//   • Displaying the F.003 checklist for the current reviewer
//   • Saving per-criterion responses (satisfied / not_satisfied / not_applicable)
//   • Chair-only: recording a committee decision + required actions
//   • Chair-only: recommending approval (Part I)
//
// Access: assigned reviewers only (enforced in mount + every mutation).
class SyllabusReviewPage extends Component
{
    // ── Identity ──────────────────────────────────────────────────────────────
    public int $syllabusId;

    // ── Loaded state ──────────────────────────────────────────────────────────
    public ?Syllabus           $syllabus   = null;
    public ?SyllabusReviewForm $reviewForm = null;
    public ?SyllabusReviewer   $assignment = null;   // this user's assignment row

    public bool   $isChair      = false;
    public bool   $isSubmitted  = false;   // F.003 submitted_at not null
    public string $classification = 'updating';

    // Checklist: keyed by criterion_code → ['response' => string, 'comments' => string]
    public array $responses = [];

    // Decision form (chair only)
    public string  $decision         = '';
    public string  $requiredActions  = '';
    public string  $targetDate       = '';

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->syllabus = Syllabus::with([
            'course.program.departments.college',
            'academicCalendar',
            'preparer',
            'reviewForm.checklistResponses',
            'reviewForm.natureOfChange',
            'reviewForm.attachments',
            'reviewForm.recommendedByChair',
            'reviewers.user',
        ])->findOrFail($syllabusId);

        // Enforce reviewer access — admins bypass
        if (! $user->hasRole('admin')) {
            $this->assignment = $this->syllabus->reviewers
                ->firstWhere('user_id', $user->id);

            if (! $this->assignment) {
                abort(403, 'You are not assigned as a reviewer for this syllabus.');
            }
        } else {
            // Admin — use first real assignment if one exists, otherwise a read-only placeholder.
            $this->assignment = $this->syllabus->reviewers->first()
                ?? new SyllabusReviewer(['role' => 'chair', 'status' => 'pending', 'user_id' => $user->id]);
        }

        $this->isChair      = $this->assignment->role === 'chair';
        $this->reviewForm   = $this->syllabus->reviewForm;
        $this->isSubmitted  = $this->reviewForm?->submitted_at !== null;
        $this->classification = $this->reviewForm?->classification ?? 'updating';

        $this->loadResponses($user->id);

        // Pre-fill decision fields if a decision was already recorded
        if ($this->reviewForm?->decision) {
            $this->decision        = $this->reviewForm->decision;
            $this->requiredActions = $this->reviewForm->required_actions ?? '';
            $this->targetDate      = $this->reviewForm->target_compliance_date
                ? $this->reviewForm->target_compliance_date->format('Y-m-d')
                : '';
        }
    }

    public function render()
    {
        return view('livewire.syllabus.review-page.review-page', [
            'criteria'       => $this->buildCriteria(),
            'allResponded'   => $this->allResponded(),
            'progressPct'    => $this->progressPct(),
            'otherReviewers' => $this->otherReviewers(),
        ]);
    }

    // ── Checklist ─────────────────────────────────────────────────────────────

    public function saveResponse(string $code, string $response, string $comments = ''): void
    {
        $this->authorizeReviewer();

        if (! in_array($response, ['satisfied', 'not_satisfied', 'not_applicable'], true)) {
            return;
        }

        if (! ReviewCriteria::sectionForCode($code)) {
            return;
        }

        $form = $this->getOrCreateForm();

        app(SyllabusReviewFormService::class)->saveChecklistResponse(
            $form,
            Auth::id(),
            $code,
            $response,
            blank($comments) ? null : $comments
        );

        // Refresh local state
        $this->responses[$code] = ['response' => $response, 'comments' => $comments];

        // Flip assignment status if complete
        $complete = app(SyllabusReviewFormService::class)->isChecklistComplete($form, Auth::id());
        if ($complete && $this->assignment->status !== 'approved') {
            $this->assignment->update(['status' => 'approved']);
            $this->assignment->status = 'approved';
        }

        $this->dispatch('lw-toast', type: 'success', message: "{$code} saved.");
    }

    // ── Decision (chair only) ─────────────────────────────────────────────────

    public function saveDecision(): void
    {
        $this->authorizeReviewer();

        if (! $this->isChair) {
            $this->dispatch('lw-toast', type: 'error', message: 'Only the CQI Chair can record a decision.');
            return;
        }

        $allowed = [
            'approved_as_updating', 'approved_as_revision',
            'approved_with_corrections', 'returned_for_revision', 'reclassified_as_revision',
        ];

        if (! in_array($this->decision, $allowed, true)) {
            $this->dispatch('lw-toast', type: 'error', message: 'Select a decision first.');
            return;
        }

        $needsActions = $this->decision === 'returned_for_revision';
        if ($needsActions) {
            if (blank($this->requiredActions)) {
                $this->dispatch('lw-toast', type: 'error', message: 'Required actions must be filled for this decision.');
                return;
            }
            if (blank($this->targetDate)) {
                $this->dispatch('lw-toast', type: 'error', message: 'Compliance deadline must be set for this decision.');
                return;
            }
        }

        $form = $this->getOrCreateForm();

        app(SyllabusReviewFormService::class)->recordDecision(
            $form,
            $this->decision,
            blank($this->requiredActions) ? null : $this->requiredActions,
            blank($this->targetDate)      ? null : $this->targetDate,
        );

        $this->reviewForm = $form->fresh();

        AuditLog::record(
            action: 'decision_recorded',
            module: 'Syllabus Review',
            referenceId: $this->syllabusId,
            description: "Chair recorded decision '{$this->decision}' on syllabus #{$this->syllabusId}."
        );

        $this->dispatch('lw-toast', type: 'success', message: 'Decision saved.');
    }

    // ── Recommend approval (chair only) ───────────────────────────────────────

    public function recommendApproval(): void
    {
        $this->authorizeReviewer();

        if (! $this->isChair) {
            $this->dispatch('lw-toast', type: 'error', message: 'Only the CQI Chair can recommend approval.');
            return;
        }

        $form = $this->getOrCreateForm();

        if (! $form->decision) {
            $this->dispatch('lw-toast', type: 'error', message: 'Record a decision before recommending approval.');
            return;
        }

        app(SyllabusReviewFormService::class)->recommendApproval($form, Auth::id());

        $this->reviewForm = $form->fresh();

        AuditLog::record(
            action: 'recommended_approval',
            module: 'Syllabus Review',
            referenceId: $this->syllabusId,
            description: "Chair #" . Auth::id() . " recommended approval for syllabus #{$this->syllabusId}."
        );

        $this->dispatch('lw-toast', type: 'success', message: 'Approval recommended.');
    }

    public function verifyPartH(): void
    {
        $this->authorizeReviewer();

        if (! $this->isChair) {
            $this->dispatch('lw-toast', type: 'error', message: 'Only the CQI Chair can verify the faculty response.');
            return;
        }

        $form = $this->reviewForm;

        if (! $form?->part_h_faculty_response) {
            $this->dispatch('lw-toast', type: 'error', message: 'No faculty response to verify.');
            return;
        }

        if ($form->part_h_verified_at) {
            $this->dispatch('lw-toast', type: 'info', message: 'Already verified.');
            return;
        }

        app(SyllabusReviewFormService::class)->verifyPartH($form, Auth::id());

        $this->reviewForm = $form->fresh();

        AuditLog::record(
            action: 'verified_part_h',
            module: 'Syllabus Review',
            referenceId: $this->syllabusId,
            description: 'Chair verified faculty Part H response on syllabus #' . $this->syllabusId . '.'
        );

        $this->dispatch('lw-toast', type: 'success', message: 'Faculty response marked as verified.');
    }

    // ── Private helpers ─────────────────────────────────────────────────────────────────

    private function authorizeReviewer(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return;
        }

        $assigned = SyllabusReviewer::where('syllabus_id', $this->syllabusId)
            ->where('user_id', $user->id)
            ->exists();

        if (! $assigned) {
            abort(403);
        }
    }

    private function getOrCreateForm(): SyllabusReviewForm
    {
        return app(SyllabusReviewFormService::class)->findOrCreate($this->syllabus);
    }

    private function loadResponses(int $userId): void
    {
        if (! $this->reviewForm) {
            $this->responses = [];
            return;
        }

        $this->responses = $this->reviewForm->checklistResponses
            ->where('reviewer_user_id', $userId)
            ->keyBy('criterion_code')
            ->map(fn ($r) => [
                'response' => $r->response ?? '',
                'comments' => $r->comments ?? '',
            ])
            ->toArray();
    }

    private function buildCriteria(): array
    {
        $sections = [
            'A' => ['label' => 'Part A — Completeness & Format',      'criteria' => ReviewCriteria::A],
            'B' => ['label' => 'Part B — OBTL Alignment',             'criteria' => ReviewCriteria::B],
        ];

        if ($this->classification === 'revision') {
            $sections['C'] = ['label' => 'Part C — Basis for Revision', 'criteria' => ReviewCriteria::C_REVISION];
        } else {
            $sections['C'] = ['label' => 'Part C — Nature of Update',   'criteria' => ReviewCriteria::C_UPDATING];
        }

        return $sections;
    }

    private function allResponded(): bool
    {
        $required = ReviewCriteria::codesForClassification($this->classification);
        foreach ($required as $code) {
            if (blank($this->responses[$code]['response'] ?? '')) {
                return false;
            }
        }
        return true;
    }

    private function progressPct(): int
    {
        $required = ReviewCriteria::codesForClassification($this->classification);
        $answered = collect($required)
            ->filter(fn ($c) => ! blank($this->responses[$c]['response'] ?? ''))
            ->count();

        return $required ? (int) round(($answered / count($required)) * 100) : 0;
    }

    private function otherReviewers(): array
    {
        $userId = Auth::id();

        return $this->syllabus->reviewers
            ->where('user_id', '!=', $userId)
            ->map(fn ($r) => [
                'name'   => $r->user->name ?? '—',
                'role'   => $r->role,
                'status' => $r->status,
            ])
            ->values()
            ->toArray();
    }
}
