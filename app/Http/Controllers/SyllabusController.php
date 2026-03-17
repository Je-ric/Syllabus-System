<?php

namespace App\Http\Controllers;

use App\Models\CompleteSyllabus;
use App\Models\Program;
use App\Models\Syllabus;
use App\Services\SyllabusPreviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Storage;

class SyllabusController extends Controller
{
    public function __construct(
        private readonly SyllabusPreviewService $previewService
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

        return redirect()->route('syllabus.edit', $syllabus->id)
            ->with('toast', [
                'message' => 'Syllabus created successfully.',
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
        return redirect()->route('syllabus.preview', ['syllabus' => $syllabus->id]);
    }

    // ── Previews ──────────────────────────────────────────────────────────────

    public function previewComplete(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);
        return view('Syllabus.preview.complete', $this->previewService->buildCompleteData($syllabus));
    }

    public function previewAbridged(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);
        return view('Syllabus.preview.abridged', $this->previewService->buildAbridgedData($syllabus));
    }

    public function previewAssessment(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);
        return view('Syllabus.preview.assessment', $this->previewService->buildCompleteData($syllabus));
    }

    // ── Snapshot / PDF generation ─────────────────────────────────────────────

    public function generateCompleteHtmlSnapshot(Syllabus $syllabus): string
    {
        $this->authorizeSyllabusAccess($syllabus);

        $data = $this->previewService->buildCompleteData($syllabus);
        $data['isSnapshot']       = true;
        $data['inlinePreviewCss'] = @file_get_contents(resource_path('css/preview.css')) ?: null;

        $logoPath = public_path('assets/clsu-logo-green.png');
        $data['inlineLogoDataUri'] = is_file($logoPath)
            ? ('data:image/png;base64,' . base64_encode((string) file_get_contents($logoPath)))
            : null;

        return view('Syllabus.preview.complete', $data)->render();
    }

    public function generateAbridgedHtmlSnapshot(Syllabus $syllabus): string
    {
        $this->authorizeSyllabusAccess($syllabus);

        $data = $this->previewService->buildAbridgedData($syllabus);
        $data['isSnapshot'] = true;

        // Inline both shared (preview.css) and abridged-specific (abridged.css) stylesheets.
        $sharedCss   = @file_get_contents(resource_path('css/preview.css'))  ?: '';
        $abridgedCss = @file_get_contents(resource_path('css/abridged.css')) ?: '';
        $data['inlinePreviewCss'] = $sharedCss . "
" . $abridgedCss ?: null;

        $logoPath = public_path('assets/clsu-logo-green.png');
        $data['inlineLogoDataUri'] = is_file($logoPath)
            ? ('data:image/png;base64,' . base64_encode((string) file_get_contents($logoPath)))
            : null;

        return view('Syllabus.preview.abridged', $data)->render();
    }

    // ── Saved versions ────────────────────────────────────────────────────────

    public function previewSavedComplete(CompleteSyllabus $completeSyllabus)
    {
        $syllabus = $completeSyllabus->syllabus()->firstOrFail();
        $this->authorizeSyllabusAccess($syllabus);

        $path = trim((string) $completeSyllabus->pdf_path);

        if (preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
            return redirect()->away($path);
        }

        if ($path === '' || ! Storage::disk('local')->exists($path)) {
            abort(404, 'Saved version file not found.');
        }

        return response(Storage::disk('local')->get($path), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    public function downloadSavedComplete(CompleteSyllabus $completeSyllabus)
    {
        $syllabus = $completeSyllabus->syllabus()->firstOrFail();
        $this->authorizeSyllabusAccess($syllabus);

        $path = trim((string) $completeSyllabus->pdf_path);

        if ($path === '' || preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
            abort(400, 'This saved version is not stored locally.');
        }

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'Saved version file not found.');
        }

        return Storage::disk('local')->download($path, basename($path), [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    public function previewSavedAbridged(CompleteSyllabus $completeSyllabus)
    {
        $syllabus = $completeSyllabus->syllabus()->firstOrFail();
        $this->authorizeSyllabusAccess($syllabus);

        $path = trim((string) ($completeSyllabus->abridged_path ?? ''));

        if ($path === '') {
            // No abridged snapshot saved yet — fall back to live render
            return $this->previewAbridged($syllabus);
        }

        if (preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
            return redirect()->away($path);
        }

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'Abridged saved version file not found.');
        }

        return response(Storage::disk('local')->get($path), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    public function downloadSavedAbridged(CompleteSyllabus $completeSyllabus)
    {
        $syllabus = $completeSyllabus->syllabus()->firstOrFail();
        $this->authorizeSyllabusAccess($syllabus);

        $path = trim((string) ($completeSyllabus->abridged_path ?? ''));

        if ($path === '' || preg_match('#^https?://#i', $path) || str_starts_with($path, '/')) {
            abort(400, 'This abridged version is not stored locally.');
        }

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'Abridged saved version file not found.');
        }

        return Storage::disk('local')->download($path, basename($path), [
            'Content-Type' => 'text/html; charset=UTF-8',
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