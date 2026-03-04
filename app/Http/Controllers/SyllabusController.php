<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Syllabus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Carbon\Carbon;

class SyllabusController extends Controller
{
    public function index()
    {
        $syllabi = Syllabus::where('prepared_by', Auth::id())
            ->with(['course.program', 'academicCalendar', 'chair', 'dean'])
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
        $courseId = $request->query('courseId');

        if (!$syllabusId && !$courseId) {
            abort(404, 'No syllabus or course specified.');
        }

        if (!$syllabusId) {
            $existing = Syllabus::where('course_id', $courseId)
                ->where('prepared_by', Auth::id())
                ->first();

            if ($existing) {
                return redirect()->route('syllabus.wizard', ['syllabusId' => $existing->id])
                    ->with('toast', [
                        'message' => 'A syllabus for this course already exists. You can continue editing it.',
                        'type' => 'info',
                    ]);
            }

            $syllabus = Syllabus::create([
                'course_id' => $courseId,
                'prepared_by' => Auth::id(),
                'status' => 'draft',
                'current_step' => 'academic_calendar',
            ]);

            $syllabusId = $syllabus->id;
        }

        return view('Syllabus.wizard', compact('syllabusId', 'courseId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_calendar_id' => 'required|exists:academic_calendars,id',
        ]);

        $existing = Syllabus::where('course_id', $validated['course_id'])
            ->where('academic_calendar_id', $validated['academic_calendar_id'])
            ->first();

        if ($existing) {
            return redirect()->route('syllabus.edit', $existing->id)
                ->with('toast', [
                    'message' => 'Syllabus for this course already exists.',
                    'type' => 'info',
                ]);
        }

        $syllabus = Syllabus::create([
            'course_id' => $validated['course_id'],
            'academic_calendar_id' => $validated['academic_calendar_id'],
            'status' => 'draft',
            'prepared_by' => Auth::id(),
        ]);

        return redirect()->route('syllabus.edit', $syllabus->id)
            ->with('toast', [
                'message' => 'Syllabus created successfully.',
                'type' => 'success',
            ]);
    }

    public function edit(Syllabus $syllabus)
    {
        if ($syllabus->prepared_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!$syllabus->isEditable()) {
            return redirect()->route('syllabus.show', $syllabus->id)
                ->with('toast', [
                    'message' => 'This syllabus cannot be edited in its current status.',
                    'type' => 'error',
                ]);
        }

        return redirect()->route('syllabus.wizard', ['syllabusId' => $syllabus->id]);
    }

    public function update(Syllabus $syllabus, Request $request)
    {
        if ($syllabus->prepared_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!$syllabus->isEditable()) {
            return back()->with('toast', [
                'message' => 'This syllabus cannot be edited in its current status.',
                'type' => 'error',
            ]);
        }

        return redirect()->route('syllabus.wizard', ['syllabusId' => $syllabus->id])
            ->with('toast', [
                'message' => 'Please use the wizard to edit the syllabus.',
                'type' => 'info',
            ]);
    }

    public function show(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);

        // Dedicated show page is not used; use preview as canonical view.
        return redirect()->route('syllabus.preview', ['syllabus' => $syllabus->id]);
    }

    // public function preview(Syllabus $syllabus)
    // {
    //     $this->authorizeSyllabusAccess($syllabus);

    //     return redirect()->route('syllabus.preview.complete', ['syllabus' => $syllabus->id]);
    // }

    public function previewComplete(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);

        return view('Syllabus.preview.complete', $this->buildPreviewData($syllabus));
    }

    public function previewAbridged(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);

        return view('Syllabus.preview.abridged', $this->buildPreviewData($syllabus));
    }

    public function previewAssessment(Syllabus $syllabus)
    {
        $this->authorizeSyllabusAccess($syllabus);

        return view('Syllabus.preview.assessment', $this->buildPreviewData($syllabus));
    }

    private function buildPreviewData(Syllabus $syllabus): array
    {
        $syllabus->load([
            'course.program.peos',
            'course.program.outcomes.peos',
            'course.program.departments.objectives',
            'course.program.departments.college.goals',
            'course.programOutcomes',
            'components',
            'courseOutcomes',
            'weeks.contents.courseOutcome',
            'weeks.contents.evaluation',
        ]);

        $program = $syllabus->course->program;
        $department = $program->departments->first();
        $college = $department?->college;

        $collegeName = $college?->name ?? 'College';
        $departmentName = $department?->name ?? 'Department';
        $collegeGoals = $college?->goals?->sortBy('college_goals_code') ?? collect();
        $departmentObjectives = $department?->objectives?->sortBy('dept_obj_code') ?? collect();
        $peos = $program->peos?->sortBy('peo_code') ?? collect();
        $pos = $program->outcomes?->sortBy('po_code') ?? collect();
        $courseOutcomes = $syllabus->courseOutcomes?->sortBy('co_code') ?? collect();
        $lecComponent = $syllabus->components->firstWhere('type', 'LEC');
        $labComponent = $syllabus->components->firstWhere('type', 'LAB');

        $coursePoIedMap = $syllabus->course?->programOutcomes
            ?->pluck('pivot.ied', 'id')
            ?->filter()
            ?->toArray()
            ?? [];

        $courseYearLevel = $syllabus->course?->year_level;
        $courseSemester = $syllabus->course?->semester;

        $ordinal = static function (?int $n): ?string {
            if (!$n) {
                return null;
            }

            $suffix = 'th';
            if (!in_array($n % 100, [11, 12, 13], true)) {
                $suffix = match ($n % 10) {
                    1 => 'st',
                    2 => 'nd',
                    3 => 'rd',
                    default => 'th',
                };
            }

            return $n . $suffix;
        };

        $courseLevel = match (true) {
            !blank($courseYearLevel) && !blank($courseSemester) => ($ordinal((int) $courseYearLevel) . ' Year, ' . $ordinal((int) $courseSemester) . ' Semester'),
            !blank($courseYearLevel) => ($ordinal((int) $courseYearLevel) . ' Year'),
            !blank($courseSemester) => ($ordinal((int) $courseSemester) . ' Semester'),
            default => 'N/A',
        };

        $examLabel = static function (?string $examType): string {
            $examType = trim((string) $examType);

            return match ($examType) {
                'first_term'  => '1st Term Exam',
                'second_term' => '2nd Term Exam',
                'final_term'  => 'Final Term Exam',
                'midterm'     => 'Midterm Exam',
                'final'       => 'Final Exam',
                default       => $examType !== '' ? (ucwords(str_replace('_', ' ', $examType)) . ' Exam') : 'Exam',
            };
        };

        $weeklyCoverageRows = [
            'LEC' => [],
            'LAB' => [],
        ];

        $assessmentCounters = [
            'LEC' => ['activity' => 0, 'quiz' => 0],
            'LAB' => ['activity' => 0, 'quiz' => 0],
        ];

        $weeks = $syllabus->weeks?->sortBy('week_no') ?? collect();
        foreach ($weeks as $week) {
            $isExam = (bool) $week->is_exam_week;

            $dateRange = null;
            if ($week->start_date && $week->end_date) {
                $dateRange = Carbon::parse($week->start_date)->format('M d, Y') . ' - ' . Carbon::parse($week->end_date)->format('M d, Y');
            }

            foreach (['LEC', 'LAB'] as $componentType) {
                $content = $week->contents
                    ?->where('component_type', $componentType)
                    ?->first();

                if ($isExam) {
                    $weeklyCoverageRows[$componentType][] = [
                        'week_label'        => 'Week ' . (int) $week->week_no,
                        'exam_label'        => $examLabel($week->exam_type),
                        'week_no'           => (int) $week->week_no,
                        'is_exam'           => true,
                        'date_range'        => $dateRange,
                        'co_description'    => '',
                        'learning_outcomes' => '',
                        'topics'            => '',
                        'tla'               => '',
                        'assessment_task'   => '',
                    ];
                    continue;
                }

                $kind = $content?->evaluation?->kind;
                $task = trim((string) ($content?->assessment_task ?? ''));

                $assessmentDisplay = $task;
                if ($task !== '' && in_array($kind, ['activity', 'quiz'], true)) {
                    $assessmentCounters[$componentType][$kind]++;
                    $assessmentDisplay = ucfirst($kind) . ' ' . $assessmentCounters[$componentType][$kind] . ': ' . $task;
                }

                $weeklyCoverageRows[$componentType][] = [
                    'week_label'        => 'Week ' . (int) $week->week_no,
                    'exam_label'        => null,
                    'week_no'           => (int) $week->week_no,
                    'is_exam'           => false,
                    'date_range'        => $dateRange,
                    'co_description'    => $content?->courseOutcome?->description ?? '',
                    'learning_outcomes' => trim((string) ($content?->learning_outcomes ?? '')),
                    'topics'            => trim((string) ($content?->topics ?? '')),
                    'tla'               => trim((string) ($content?->tla ?? '')),
                    'assessment_task'   => $assessmentDisplay,
                ];
            }
        }

        $evaluationRows = [];
        $evaluationTotals = [
            'lec' => 0,
            'lab' => 0,
        ];

        $evalCounters = [
            'LEC' => ['activity' => 0, 'quiz' => 0],
            'LAB' => ['activity' => 0, 'quiz' => 0],
        ];

        foreach ($weeks as $week) {
            $lecContent = $week->contents?->where('component_type', 'LEC')?->first();
            $labContent = $week->contents?->where('component_type', 'LAB')?->first();

            $lecTaskRaw = trim((string) ($lecContent?->assessment_task ?? ''));
            $labTaskRaw = trim((string) ($labContent?->assessment_task ?? ''));

            if ($lecTaskRaw === '' && $labTaskRaw === '') {
                continue;
            }

            if ($lecTaskRaw === 'Non-Teaching Week' || $labTaskRaw === 'Non-Teaching Week') {
                continue;
            }

            $lecEval = $lecContent?->evaluation;
            $labEval = $labContent?->evaluation;

            $isExam = str_contains(strtolower($lecTaskRaw), 'exam')
                || str_contains(strtolower($labTaskRaw), 'exam');

            $lecTaskLabel = $lecTaskRaw;
            if (!$isExam && $lecTaskRaw !== '' && in_array($lecEval?->kind, ['activity', 'quiz'], true)) {
                $k = $lecEval->kind;
                $evalCounters['LEC'][$k]++;
                $lecTaskLabel = ucfirst($k) . ' ' . $evalCounters['LEC'][$k];
            }

            $labTaskLabel = $labTaskRaw;
            if (!$isExam && $labTaskRaw !== '' && in_array($labEval?->kind, ['activity', 'quiz'], true)) {
                $k = $labEval->kind;
                $evalCounters['LAB'][$k]++;
                $labTaskLabel = ucfirst($k) . ' ' . $evalCounters['LAB'][$k];
            }

            $lecWeight = $lecEval?->weight;
            $labWeight = $labEval?->weight;

            if ($lecWeight !== null) {
                $evaluationTotals['lec'] += (int) $lecWeight;
            }
            if ($syllabus->course?->has_lec_lab && $labWeight !== null) {
                $evaluationTotals['lab'] += (int) $labWeight;
            }

            $coLabel = $lecEval?->outcome_label
                ?? $labEval?->outcome_label
                ?? $lecContent?->courseOutcome?->co_code
                ?? $labContent?->courseOutcome?->co_code
                ?? '';

            $evaluationRows[] = [
                'co_label'   => $coLabel,
                'is_exam'    => $isExam,
                'lec_task'   => $lecTaskLabel,
                'lec_weight' => $lecWeight,
                'lab_task'   => $labTaskLabel,
                'lab_weight' => $labWeight,
            ];
        }

        return compact(
            'syllabus',
            'program',
            'collegeName',
            'collegeGoals',
            'departmentName',
            'departmentObjectives',
            'peos',
            'pos',
            'courseOutcomes',
            'lecComponent',
            'labComponent',
            'coursePoIedMap',
            'courseLevel',
            'weeklyCoverageRows',
            'evaluationRows',
            'evaluationTotals'
        );
    }

    private function buildProgramSelectionData($programId = null): array
    {
        $program = null;
        $groupedCourses = collect();

        if ($programId) {
            $program = Program::withOrderedOutcomes()->findOrFail($programId);
            $groupedCourses = $program->getCoursesGroupedByYearAndSemester();
        }

        return [
            'program' => $program,
            'groupedCourses' => $groupedCourses,
        ];
    }

    /**
     * @throws AuthorizationException
     */
    private function authorizeSyllabusAccess(Syllabus $syllabus): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            throw new AuthorizationException('Unauthorized');
        }

        $isAdmin = $user->roles()->where('name', 'admin')->exists();
        if ($syllabus->prepared_by !== $user->id && !$isAdmin) {
            throw new AuthorizationException('Unauthorized');
        }
    }
}
