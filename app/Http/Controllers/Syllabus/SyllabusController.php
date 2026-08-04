<?php

namespace App\Http\Controllers\Syllabus;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CompleteSyllabus;
use App\Models\Program;
use App\Models\Syllabus;
use App\Services\Syllabus\SyllabusDeleteService;
use App\Services\Syllabus\Snapshots\SyllabusPreviewService;
use App\Services\Syllabus\Snapshots\SyllabusSnapshotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Storage;

class SyllabusController extends Controller
{
    public function __construct(
        private readonly SyllabusPreviewService  $previewService,
        private readonly SyllabusSnapshotService $snapshotService,
        private readonly SyllabusDeleteService   $deleteService,
    ) {}

    // ── Syllabus CRUD / wizard ────────────────────────────────────────────────

    public function index()
    {
        $syllabi = Syllabus::where('prepared_by', Auth::id())
            ->with(['course.program', 'academicCalendar', 'deanConcurred', 'dean'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Syllabus.index', compact('syllabi'));
    }

    public function create(Request $request)
    {
        $programId = $request->query('program_id');
        return view('Syllabus.selectCourse', $this->buildProgramSelectionData($programId));
    }

    public function showCourses(int $programId)
    {
        return view('Syllabus.selectCourse', $this->buildProgramSelectionData($programId));
    }

    public function showForm($courseId)
    {
        return redirect()->route('syllabus.wizard', ['courseId' => $courseId]);
    }

    public function wizard(Request $request)
    {
        $syllabusId = $request->query('syllabusId');
        $courseId   = $request->query('courseId');

        if (! $syllabusId && ! $courseId) {
            abort(404, 'No syllabus or course specified.');
        }

        if (! $syllabusId) {
            $course = \App\Models\Course::findOrFail($courseId);

            // #10 — block if course has no PO mappings
            if (! $course->programOutcomes()->exists()) {
                return redirect()->route('syllabus.create', ['program_id' => $course->program_id])
                    ->with('toast', [
                        'message' => "Course {$course->course_code} has no Program Outcome mappings. Ask the chair to map POs to this course before creating a syllabus.",
                        'type'    => 'error',
                    ]);
            }

            $existing = Syllabus::where('course_id', $courseId)
                ->where('prepared_by', Auth::id())
                ->first();

            if ($existing) {
                return redirect()->route('syllabus.wizard', ['syllabusId' => $existing->id])
                    ->with('toast', [
                        'message' => 'A syllabus for this course already exists. You can continue editing it.',
                        'type'    => 'info',
                    ]);
            }

            $syllabus = Syllabus::create([
                'course_id'    => $courseId,
                'prepared_by'  => Auth::id(),
                'status'       => 'draft',
                'current_step' => 'academic_calendar',
            ]);

            $syllabusId = $syllabus->id;
        }

        return view('Syllabus.wizard', compact('syllabusId', 'courseId'));
    }

    public function destroy(Syllabus $syllabus)
    {
        // Only the preparer can delete their own syllabus.
        if ($syllabus->prepared_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Only draft syllabi with no saved versions may be deleted.
        // Under-review / approved syllabi are protected from accidental removal.
        if ($syllabus->status !== 'draft') {
            return redirect()->route('syllabus.index')
                ->with('toast', [
                    'message' => 'Only draft syllabi can be deleted.',
                    'type'    => 'error',
                ]);
        }

        // Block deletion if any saved version (CompleteSyllabus) exists.
        // A saved version means the faculty already froze a snapshot — treat
        // that as important data worth preserving.
        if ($syllabus->completeSyllabi()->exists()) {
            return redirect()->route('syllabus.index')
                ->with('toast', [
                    'message' => 'This syllabus has saved versions and cannot be deleted.',
                    'type'    => 'error',
                ]);
        }

        // Cascade-delete all child records and disk files.
        // SyllabusDeleteService::delete() owns its own transaction internally.
        $this->deleteService->delete($syllabus);

        AuditLog::record(
            action: 'deleted',
            module: 'Syllabus',
            referenceId: $syllabus->id,
            description: "Deleted draft syllabus for course #{$syllabus->course_id}."
        );

        return redirect()->route('syllabus.index')
            ->with('toast', [
                'message' => 'Syllabus deleted.',
                'type'    => 'success',
            ]);
    }

    public function edit(Syllabus $syllabus)
    {
        if ($syllabus->prepared_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (! $syllabus->isEditable()) {
            return redirect()->route('syllabus.show', $syllabus->id)
                ->with('toast', [
                    'message' => 'This syllabus cannot be edited in its current status.',
                    'type'    => 'error',
                ]);
        }

        return redirect()->route('syllabus.wizard', ['syllabusId' => $syllabus->id]);
    }

    public function update(Syllabus $syllabus, Request $request)
    {
        if ($syllabus->prepared_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (! $syllabus->isEditable()) {
            return back()->with('toast', [
                'message' => 'This syllabus cannot be edited in its current status.',
                'type'    => 'error',
            ]);
        }

        return redirect()->route('syllabus.wizard', ['syllabusId' => $syllabus->id])
            ->with('toast', [
                'message' => 'Please use the wizard to edit the syllabus.',
                'type'    => 'info',
            ]);
    }

    public function show(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);
        return view('Syllabus.preview.complete', array_merge(
            $this->previewService->buildCompleteData($syllabus),
            [
                'previewMode'        => 'live',
                'previewVariant'     => 'complete',
                'activeSavedVersion' => null,
            ],
        ));
    }

    // ── Previews ──────────────────────────────────────────────────────────────

    public function previewComplete(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);
        return view('Syllabus.preview.complete', array_merge(
            $this->previewService->buildCompleteData($syllabus),
            [
                'previewMode'        => 'live',
                'previewVariant'     => 'complete',
                'activeSavedVersion' => null,
            ],
        ));
    }

    public function previewAbridged(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);
        return view('Syllabus.preview.abridged', array_merge(
            $this->previewService->buildAbridgedData($syllabus),
            [
                'previewMode'        => 'live',
                'previewVariant'     => 'abridged',
                'activeSavedVersion' => null,
            ],
        ));
    }

    public function previewAssessment(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);
        return view('Syllabus.preview.assessment', array_merge(
            $this->previewService->buildCompleteData($syllabus),
            [
                'previewMode'        => 'live',
                'previewVariant'     => 'assessment',
                'activeSavedVersion' => null,
            ],
        ));
    }

    public function downloadComplete(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);
        $html     = $this->snapshotService->generateCompleteHtml($syllabus);
        $filename = 'syllabus-complete-' . $syllabus->course->course_code . '.html';

        return response($html, 200, [
            'Content-Type'        => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function downloadAbridged(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);
        $html     = $this->snapshotService->generateAbridgedHtml($syllabus);
        $filename = 'syllabus-abridged-' . $syllabus->course->course_code . '.html';

        return response($html, 200, [
            'Content-Type'        => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function downloadAssessment(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);
        $html     = $this->snapshotService->generateAssessmentHtml($syllabus);
        $filename = 'syllabus-assessment-' . $syllabus->course->course_code . '.html';

        return response($html, 200, [
            'Content-Type'        => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ── Saved versions ────────────────────────────────────────────────────────

    public function previewSavedAssessment(CompleteSyllabus $completeSyllabus)
    {
        $syllabus = $completeSyllabus->syllabus()->firstOrFail();
        $this->authorizeSyllabusAccess($syllabus);

        $path = trim((string) ($completeSyllabus->evaluation_path ?? ''));

        if ($path === '') {
            return $this->previewAssessment($syllabus);
        }

        $html = $this->snapshotService->getSavedHtml($path)
            ?? abort(404, 'Assessment saved version file not found.');

        return response(
            $this->snapshotService->injectVersionsDrawer($syllabus, $completeSyllabus, 'assessment', $html),
            200, ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    public function downloadSavedAssessment(CompleteSyllabus $completeSyllabus)
    {
        $syllabus = $completeSyllabus->syllabus()->firstOrFail();
        $this->authorizeSyllabusAccess($syllabus);

        $path = trim((string) ($completeSyllabus->evaluation_path ?? ''));

        if ($path === '') {
            abort(400, 'No assessment path stored.');
        }

        $html = $this->readSavedFile($path) ?? abort(404, 'Assessment saved version file not found.');

        return response($html, 200, [
            'Content-Type'        => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"',
        ]);
    }

    public function previewSavedComplete(CompleteSyllabus $completeSyllabus)
    {
        $syllabus = $completeSyllabus->syllabus()->firstOrFail();
        $this->authorizeSyllabusAccess($syllabus);

        $path = trim((string) $completeSyllabus->pdf_path);

        if ($path === '') {
            return $this->previewComplete($syllabus);
        }

        $html = $this->snapshotService->getSavedHtml($path)
            ?? abort(404, 'Saved version file not found.');

        return response(
            $this->snapshotService->injectVersionsDrawer($syllabus, $completeSyllabus, 'complete', $html),
            200, ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    public function downloadSavedComplete(CompleteSyllabus $completeSyllabus)
    {
        $syllabus = $completeSyllabus->syllabus()->firstOrFail();
        $this->authorizeSyllabusAccess($syllabus);

        $path = trim((string) $completeSyllabus->pdf_path);

        if ($path === '') {
            abort(400, 'No complete path stored.');
        }

        $html = $this->readSavedFile($path) ?? abort(404, 'Saved version file not found.');

        return response($html, 200, [
            'Content-Type'        => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"',
        ]);
    }

    public function previewSavedAbridged(CompleteSyllabus $completeSyllabus)
    {
        $syllabus = $completeSyllabus->syllabus()->firstOrFail();
        $this->authorizeSyllabusAccess($syllabus);

        $path = trim((string) ($completeSyllabus->abridged_path ?? ''));

        if ($path === '') {
            return $this->previewAbridged($syllabus);
        }

        $html = $this->snapshotService->getSavedHtml($path)
            ?? abort(404, 'Abridged saved version file not found.');

        return response(
            $this->snapshotService->injectVersionsDrawer($syllabus, $completeSyllabus, 'abridged', $html),
            200, ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    public function downloadSavedAbridged(CompleteSyllabus $completeSyllabus)
    {
        $syllabus = $completeSyllabus->syllabus()->firstOrFail();
        $this->authorizeSyllabusAccess($syllabus);

        $path = trim((string) ($completeSyllabus->abridged_path ?? ''));

        if ($path === '') {
            abort(400, 'No abridged path stored.');
        }

        $html = $this->readSavedFile($path) ?? abort(404, 'Abridged saved version file not found.');

        return response($html, 200, [
            'Content-Type'        => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"',
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function buildProgramSelectionData($programId = null): array
    {
        $program        = null;
        $groupedCourses = collect();

        if ($programId) {
            $program        = Program::withOrderedOutcomes()->findOrFail($programId);
            $groupedCourses = $program->getCoursesGroupedByYearAndSemester();
        }

        return compact('program', 'groupedCourses');
    }

    // Read a saved snapshot from local disk first, Google Drive fallback.
    private function readSavedFile(string $path): ?string
    {
        if (Storage::disk('syllabus_snapshots')->exists($path)) {
            return Storage::disk('syllabus_snapshots')->get($path);
        }
        try {
            if (Storage::disk('google')->exists($path)) {
                return Storage::disk('google')->get($path);
            }
        } catch (\Throwable) {
            // Google Drive unavailable
        }
        return null;
    }

    private function savedFileExists(string $path): bool
    {
        if (Storage::disk('syllabus_snapshots')->exists($path)) {
            return true;
        }
        try {
            return Storage::disk('google')->exists($path);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @throws AuthorizationException
     */
    private function authorizeSyllabusAccess(Syllabus $syllabus): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user) {
            throw new AuthorizationException('Unauthorized');
        }

        $isAdmin = $user->roles()->where('name', 'admin')->exists();
        if ($syllabus->prepared_by !== $user->id && ! $isAdmin) {
            throw new AuthorizationException('Unauthorized');
        }
    }
}
