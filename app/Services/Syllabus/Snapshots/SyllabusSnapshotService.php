<?php

namespace App\Services\Syllabus\Snapshots;

use App\Models\CompleteSyllabus;
use App\Models\Syllabus;
// use App\Services\Syllabus\Review\SyllabusReviewFormService;
use App\Services\Syllabus\Snapshots\SyllabusPreviewService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Handles HTML snapshot generation, version saving, and saved-version file serving.
// Used by SyllabusWizard (saveVersion) and SyllabusController (downloads, previews).
//
// Public API:
//   saveVersion(Syllabus)            — generate HTML, write files, persist DB record; returns version int
//   generateCompleteHtml(Syllabus)   — render complete preview as self-contained HTML
//   generateAbridgedHtml(Syllabus)   — render abridged preview as self-contained HTML
//   generateAssessmentHtml(Syllabus) — render assessment preview as self-contained HTML
//   getSavedHtml(string $path)       — read stored HTML from disk
//   injectVersionsDrawer(...)        — inject versions drawer into saved HTML before </body>
class SyllabusSnapshotService
{
    public function __construct(
        private readonly SyllabusPreviewService $previewService
    ) {}

    // ── Version save ──────────────────────────────────────────────────────────

    // Generate HTML snapshots, write them to disk, and persist the CompleteSyllabus
    // record in one coordinated sequence.
    //
    // Step A — reserve version number (short transaction + lock)
    // Step B — write files to disk (outside transaction — rollback must not leave orphans)
    // Step C — persist DB record now that files exist
    //
    // Returns the new version number.
    // Throws \Throwable on any failure — caller is responsible for cleanup messaging.
    public function saveVersion(Syllabus $syllabus): int
    {
        $html           = $this->generateCompleteHtml($syllabus);
        $htmlAbridged   = $this->generateAbridgedHtml($syllabus);
        $htmlAssessment = $this->generateAssessmentHtml($syllabus);
        $htmlReviewForm = $this->generateReviewFormHtml($syllabus);

        $program    = $syllabus->course?->program;
        $department = $program?->departments?->first();
        $college    = $department?->college;
        $faculty    = $syllabus->preparer;

        $collegeName    = $college?->name    ?? 'Unknown College';
        $departmentName = $department?->name ?? 'Unknown Department';
        $programName    = $program?->program_name ?? $program?->name ?? 'Unknown Program';
        $facultyName    = $faculty?->name    ?? 'User ' . ($syllabus->prepared_by ?? 'Unknown');
        $academicYear   = $syllabus->academicCalendar?->academic_year ?? 'N-A';
        $semester       = $syllabus->academicCalendar?->semester      ?? 'N-A';
        $courseCode     = $syllabus->course?->course_code             ?? 'COURSE';

        // Step A — reserve version number
        [$version, $pathComplete, $pathAbridged, $pathAssessment, $pathReviewForm] =
            DB::transaction(function () use ($syllabus, $collegeName, $departmentName, $programName, $facultyName, $courseCode, $academicYear, $semester) {
                $version = (int) (CompleteSyllabus::where('syllabus_id', $syllabus->id)
                    ->lockForUpdate()
                    ->max('version') ?? 0) + 1;

                $baseDir = implode('/', [
                    'Syllabus Snapshots',
                    $collegeName,
                    $departmentName,
                    $programName,
                    $facultyName,
                    $courseCode,
                    "v{$version} ({$academicYear} {$semester})",
                ]);

                return [
                    $version,
                    $baseDir . '/Complete - '    . $courseCode . '.html',
                    $baseDir . '/Abridged - '    . $courseCode . '.html',
                    $baseDir . '/Assessment - '  . $courseCode . '.html',
                    $baseDir . '/ReviewForm - '  . $courseCode . '.html',
                ];
            });

        // Step B — write files (outside transaction)
        Storage::disk('syllabus_snapshots')->put($pathComplete,    $html);
        Storage::disk('syllabus_snapshots')->put($pathAbridged,    $htmlAbridged);
        Storage::disk('syllabus_snapshots')->put($pathAssessment,  $htmlAssessment);
        Storage::disk('syllabus_snapshots')->put($pathReviewForm,  $htmlReviewForm);

        // Mirror to Google Drive — secondary, silent, never blocks save
        try {
            Storage::disk('google')->put($pathComplete,   $html);
            Storage::disk('google')->put($pathAbridged,   $htmlAbridged);
            Storage::disk('google')->put($pathAssessment, $htmlAssessment);
            Storage::disk('google')->put($pathReviewForm, $htmlReviewForm);
        } catch (\Throwable) {
            // Non-fatal — local copy is the source of truth
        }

        // Step C — persist DB record now that files exist
        DB::transaction(function () use ($syllabus, $pathComplete, $pathAbridged, $pathAssessment, $pathReviewForm, $version, $academicYear, $semester, $html, $htmlAbridged, $htmlAssessment, $htmlReviewForm) {
            CompleteSyllabus::create([
                'syllabus_id'          => $syllabus->id,
                'course_id'            => $syllabus->course_id,
                'academic_year'        => $academicYear,
                'semester'             => $semester,
                'pdf_path'             => $pathComplete,
                'abridged_path'        => $pathAbridged,
                'evaluation_path'      => $pathAssessment,
                'review_form_path'     => $pathReviewForm,
                'version'              => $version,
                'approved_at'          => null,
                'approved_by'          => null,
                'checksum'             => hash('sha256', $html),
                'checksum_abridged'    => hash('sha256', $htmlAbridged),
                'checksum_evaluation'  => hash('sha256', $htmlAssessment),
                'checksum_review_form' => hash('sha256', $htmlReviewForm),
            ]);

            $syllabus->forceFill([
                'status'       => 'under_review',
                'current_step' => 'review',
            ])->save();
        });

        return $version;
    }

    // ── HTML snapshot generation ──────────────────────────────────────────────

    public function generateReviewFormHtml(Syllabus $syllabus): string
    {
        $syllabus->loadMissing([
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

        return view('Syllabus.template.review_form', [
            'syllabus'        => $syllabus,
            'reviewForm'      => $syllabus->reviewForm,
            'isSnapshot'      => true,
            'inlineReviewCss' => $this->readCss('review.css'),
        ])->render();
    }

    public function generateCompleteHtml(Syllabus $syllabus): string
    {
        $data = $this->previewService->buildCompleteData($syllabus);
        $data['isSnapshot']        = true;
        $data['inlinePreviewCss']  = $this->readCss('preview.css');
        $data['inlineLogoDataUri'] = $this->logoDataUri();

        return view('Syllabus.preview.complete', $data)->render();
    }

    public function generateAbridgedHtml(Syllabus $syllabus): string
    {
        $data = $this->previewService->buildAbridgedData($syllabus);
        $data['isSnapshot']        = true;
        $data['inlinePreviewCss']  = ($this->readCss('preview.css') ?? '') . "\n" . ($this->readCss('abridged.css') ?? '') ?: null;
        $data['inlineLogoDataUri'] = $this->logoDataUri();

        return view('Syllabus.preview.abridged', $data)->render();
    }

    public function generateAssessmentHtml(Syllabus $syllabus): string
    {
        $data = $this->previewService->buildCompleteData($syllabus);
        $data['isSnapshot']        = true;
        $data['inlinePreviewCss']  = $this->readCss('preview.css');
        $data['inlineLogoDataUri'] = $this->logoDataUri();

        return view('Syllabus.preview.assessment', $data)->render();
    }

    // ── Saved version file access ─────────────────────────────────────────────

    // Read a saved HTML snapshot — local disk first, Google Drive fallback.
    // Returns null if not found on either.
    public function getSavedHtml(string $path): ?string
    {
        $path = trim($path);

        if ($path === '' || preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
            return null;
        }

        // Primary: local disk
        if (Storage::disk('syllabus_snapshots')->exists($path)) {
            return Storage::disk('syllabus_snapshots')->get($path);
        }

        // Fallback: Google Drive
        try {
            if (Storage::disk('google')->exists($path)) {
                return Storage::disk('google')->get($path);
            }
        } catch (\Throwable) {
            // Google Drive unavailable — local-only
        }

        return null;
    }

    // Inject the versions drawer partial into a saved HTML string just before </body>.
    public function injectVersionsDrawer(
        Syllabus $syllabus,
        CompleteSyllabus $activeSavedVersion,
        string $previewVariant,
        string $html
    ): string {
        $savedVersions = CompleteSyllabus::query()
            ->where('syllabus_id', $syllabus->id)
            ->orderByDesc('version')
            ->orderByDesc('created_at')
            ->get();

        $drawer = view('Syllabus.preview._versions_drawer', [
            'syllabus'           => $syllabus,
            'savedVersions'      => $savedVersions,
            'previewMode'        => 'saved',
            'previewVariant'     => $previewVariant,
            'activeSavedVersion' => $activeSavedVersion,
            'openButton'         => 'floating',
        ])->render();

        $pos = stripos($html, '</body>');

        return $pos === false
            ? $html . $drawer
            : substr($html, 0, $pos) . $drawer . substr($html, $pos);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function readCss(string $filename): ?string
    {
        return @file_get_contents(resource_path("css/{$filename}")) ?: null;
    }

    private function logoDataUri(): ?string
    {
        $path = public_path('assets/clsu-logo-green.png');

        return is_file($path)
            ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($path))
            : null;
    }
}
