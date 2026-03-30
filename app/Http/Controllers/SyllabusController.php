<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CompleteSyllabus;
use App\Models\Program;
use App\Models\Syllabus;
use App\Services\Syllabus\SyllabusDeleteService;
use App\Services\Syllabus\SyllabusPreviewService;
use App\Services\Syllabus\SyllabusSnapshotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id'            => 'required|exists:courses,id',
            'academic_calendar_id' => 'required|exists:academic_calendars,id',
        ]);

        $existing = Syllabus::where('course_id', $validated['course_id'])
            ->where('academic_calendar_id', $validated['academic_calendar_id'])
            ->first();

        if ($existing) {
            return redirect()->route('syllabus.edit', $existing->id)
                ->with('toast', [
                    'message' => 'Syllabus for this course already exists.',
                    'type'    => 'info',
                ]);
        }

        $syllabus = Syllabus::create([
            'course_id'            => $validated['course_id'],
            'academic_calendar_id' => $validated['academic_calendar_id'],
            'status'               => 'draft',
            'prepared_by'          => Auth::id(),
        ]);

        AuditLog::record(
            action: 'created',
            module: 'Syllabus',
            referenceId: $syllabus->id,
            description: "Created syllabus for course #{$syllabus->course_id}."
        );

        return redirect()->route('syllabus.edit', $syllabus->id)
            ->with('toast', [
                'message' => 'Syllabus created successfully.',
                'type'    => 'success',
            ]);
    }

    public function destroy(Syllabus $syllabus)
    {
        return redirect()->route('syllabus.index')
            ->with('toast', [
                'message' => 'Syllabus deletion is not allowed.',
                'type'    => 'warning',
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
        return redirect()->route('syllabus.preview.complete', ['syllabus' => $syllabus->id]);
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

        if ($path === '' || preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
            abort(400, 'This assessment version is not stored on Drive.');
        }

        if (! Storage::disk('google')->exists($path)) {
            abort(404, 'Assessment saved version file not found.');
        }

        return response(Storage::disk('google')->get($path), 200, [
            'Content-Type'        => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"',
        ]);
    }

    public function previewSavedComplete(CompleteSyllabus $completeSyllabus)
    {
        $syllabus = $completeSyllabus->syllabus()->firstOrFail();
        $this->authorizeSyllabusAccess($syllabus);

        $path = trim((string) $completeSyllabus->pdf_path);

        if (preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
            return redirect()->away($path);
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

        if ($path === '' || preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
            abort(400, 'This saved version is not stored on Drive.');
        }

        if (! Storage::disk('google')->exists($path)) {
            abort(404, 'Saved version file not found.');
        }

        return response(Storage::disk('google')->get($path), 200, [
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

        if (preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
            return redirect()->away($path);
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

        if ($path === '' || preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
            abort(400, 'This abridged version is not stored on Drive.');
        }

        if (! Storage::disk('google')->exists($path)) {
            abort(404, 'Abridged saved version file not found.');
        }

        return response(Storage::disk('google')->get($path), 200, [
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
