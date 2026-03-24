<?php

namespace App\Services\Syllabus;

use App\Models\CompleteSyllabus;
use App\Models\Syllabus;
use Illuminate\Support\Facades\Storage;

// Handles HTML snapshot generation and saved-version file serving.
// Used by SyllabusController for downloads and saved-version previews.
//
// Public API:
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

    // ── HTML snapshot generation ──────────────────────────────────────────────

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

    // Read a saved HTML snapshot from local disk.
    // Returns null if the path is empty, external, or the file does not exist.
    public function getSavedHtml(string $path): ?string
    {
        $path = trim($path);

        if ($path === '' || preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
            return null;
        }

        if (! Storage::disk('local')->exists($path)) {
            return null;
        }

        return Storage::disk('local')->get($path);
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
