<?php

namespace App\Data;

// Fixed F.003 criterion definitions — not stored in DB.
// Used by SyllabusReviewFormService and the review_form.blade.php template.
class ReviewCriteria
{
    public const A = [
        'A1' => 'The official syllabus template is used.',
        'A2' => 'All required course details are complete and correct.',
        'A3' => 'The syllabus shows the version number and date of preparation or revision.',
        'A4' => 'Submission is correctly identified as Updating or Revision.',
    ];

    public const B = [
        'B1' => 'Course Outcomes are SMART (specific, measurable, achievable, relevant, time-bound).',
        'B2' => 'Number of COs follows institutional policy (preferably 3; written justification attached if more).',
        'B3' => 'Course Outcomes are clearly mapped to the appropriate Program Outcomes.',
        'B4' => 'Course content is aligned with the stated Course Outcomes.',
        'B5' => 'Teaching-learning activities are aligned with the stated Course Outcomes.',
        'B6' => 'Assessment tasks are aligned with the stated Course Outcomes.',
    ];

    public const C_UPDATING = [
        'CU1' => 'Proposed changes are minor/routine and do not substantially alter the course design.',
        'CU2' => 'No change was made to the Course Outcomes.',
        'CU3' => 'No change was made to the grading system.',
        'CU4' => 'No change was made to core course content.',
    ];

    public const C_REVISION = [
        'CR1' => 'There is a clear reason for the revision (feedback, CQI findings, policy changes, etc.).',
        'CR2' => 'Supporting evidence for the revision is attached.',
        'CR3' => 'Revised content, TLAs, assessment tasks, and grading remain constructively aligned.',
        'CR4' => 'The revision clearly addresses the feedback, findings, or recommendation used as basis.',
        'CR5' => 'For multi-section courses, the syllabus shows how common COs, assessments, and grading will be followed.',
    ];

    public static function sectionForCode(string $code): ?string
    {
        if (isset(self::A[$code]))          return 'A';
        if (isset(self::B[$code]))          return 'B';
        if (isset(self::C_UPDATING[$code])) return 'C_updating';
        if (isset(self::C_REVISION[$code])) return 'C_revision';
        return null;
    }

    public static function codesForClassification(?string $classification): array
    {
        $base = array_merge(array_keys(self::A), array_keys(self::B));

        return $classification === 'revision'
            ? array_merge($base, array_keys(self::C_REVISION))
            : array_merge($base, array_keys(self::C_UPDATING));
    }
}
