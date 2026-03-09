<?php

namespace App\Livewire\Syllabus\Steps;

use App\Http\Controllers\SyllabusController;
use App\Models\AcademicCalendar;
use App\Models\CompleteSyllabus;
use App\Models\Syllabus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

class ReviewStep extends Component
{
    public int       $syllabusId;
    public bool      $isLoaded             = false;
    public ?Syllabus $syllabus             = null;
    public           $academicCalendars;
    public ?int      $academic_calendar_id = null;
    public array     $courseOutcomes       = [];
    public array     $examWeeks            = [];
    public           $syllabusWeeks;
    public           $course;
    public ?CompleteSyllabus $latestComplete = null;

    // ── Mount ──────────────────────────────────────────────────────────────

    public function mount(int $syllabusId): void
    {
        $this->syllabusId        = $syllabusId;
        $this->academicCalendars = collect();
        $this->syllabusWeeks     = collect();
        $this->loadData();
    }

    // ── Livewire event listeners ───────────────────────────────────────────

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step === 'review') {
            $this->loadData(force: true);
        }
    }

    #[On('syllabus-step-saved')]
    public function onAnyStepSaved(): void
    {
        if ($this->isLoaded) {
            $this->loadData(force: true);
        }
    }

    // ── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.syllabus.steps.review', [
            'course' => $this->course,
        ]);
    }

    // ── Save as Done ───────────────────────────────────────────────────────

    public function saveAsDone(): void
    {
        $tag = '[ReviewStep::saveAsDone]';
        Log::info("$tag ── started ──────────────────────────────────────────");
        Log::info("$tag syllabusId = {$this->syllabusId}");

        // ── 1. Load syllabus ───────────────────────────────────────────────
        try {
            $syllabus = Syllabus::query()
                ->with(['course', 'academicCalendar'])
                ->findOrFail($this->syllabusId);
            Log::info("$tag syllabus loaded: id={$syllabus->id} course={$syllabus->course?->course_code}");
        } catch (Throwable $e) {
            Log::error("$tag FAILED to load syllabus", ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->dispatch('lw-toast', type: 'error', message: 'Could not load syllabus: ' . $e->getMessage());
            return;
        }

        // ── 2. Guard: academic calendar must be set ───────────────────────
        if (! $syllabus->academic_calendar_id) {
            Log::warning("$tag aborted — no academic_calendar_id");
            $this->dispatch('lw-toast', type: 'error', message: 'Select an academic calendar first.');
            return;
        }
        Log::info("$tag academic_calendar_id = {$syllabus->academic_calendar_id}");

        // ── 3. Generate PDF bytes via DomPDF ──────────────────────────────
        Log::info("$tag generating PDF bytes via SyllabusController…");
        try {
            $pdfBytes = app(SyllabusController::class)->generateCompletePdfBytes($syllabus);
            $byteLen  = strlen($pdfBytes);
            Log::info("$tag PDF generated OK — {$byteLen} bytes");
        } catch (Throwable $e) {
            Log::error("$tag FAILED to generate PDF", [
                'error' => $e->getMessage(),
                'file'  => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('lw-toast', type: 'error', message: 'PDF generation failed: ' . $e->getMessage());
            return;
        }

        // ── 4. Build file path ─────────────────────────────────────────────
        $version      = (int) (CompleteSyllabus::where('syllabus_id', $syllabus->id)->max('version') ?? 0) + 1;
        $academicYear = $syllabus->academicCalendar?->academic_year ?? 'N-A';
        $semester     = $syllabus->academicCalendar?->semester      ?? 'N-A';
        $courseCode   = $syllabus->course?->course_code             ?? 'COURSE';

        $fileName     = Str::slug($courseCode . '-' . $academicYear . '-' . $semester . '-v' . $version) . '.pdf';
        $relativePath = 'syllabi/' . $fileName;          // storage/app/syllabi/<file>.pdf
        $absolutePath = storage_path('app/' . $relativePath);

        Log::info("$tag file plan", [
            'version'       => $version,
            'fileName'      => $fileName,
            'relativePath'  => $relativePath,
            'absolutePath'  => $absolutePath,
        ]);

        // ── 5. Save to local storage ───────────────────────────────────────
        Log::info("$tag writing PDF to local disk…");
        try {
            // Ensure the directory exists
            $dir = storage_path('app/syllabi');
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
                Log::info("$tag created directory: {$dir}");
            }

            $written = Storage::disk('local')->put($relativePath, $pdfBytes);
            Log::info("$tag Storage::disk('local')->put() returned: " . ($written ? 'true' : 'false'));

            if (! $written) {
                $this->dispatch('lw-toast', type: 'error', message: 'Failed to write PDF to local storage.');
                return;
            }

            // Double-check the file actually exists and has content
            if (! file_exists($absolutePath)) {
                Log::error("$tag file does NOT exist after put(): {$absolutePath}");
                $this->dispatch('lw-toast', type: 'error', message: 'PDF was not written to disk (file missing).');
                return;
            }

            $fileSize = filesize($absolutePath);
            Log::info("$tag file confirmed on disk: {$absolutePath} ({$fileSize} bytes)");

        } catch (Throwable $e) {
            Log::error("$tag FAILED to write PDF to disk", [
                'error' => $e->getMessage(),
                'file'  => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('lw-toast', type: 'error', message: 'Disk write error: ' . $e->getMessage());
            return;
        }

        // ── 6. Persist CompleteSyllabus record ────────────────────────────
        Log::info("$tag persisting CompleteSyllabus record…");
        try {
            $record = CompleteSyllabus::create([
                'syllabus_id'   => $syllabus->id,
                'course_id'     => $syllabus->course_id,
                'academic_year' => $academicYear,
                'semester'      => $semester,
                'pdf_path'      => $relativePath,   // local relative path
                'version'       => $version,
                'approved_at'   => null,
                'approved_by'   => null,
                'checksum'      => hash('sha256', $pdfBytes),
            ]);
            Log::info("$tag CompleteSyllabus record created: id={$record->id}");
        } catch (Throwable $e) {
            Log::error("$tag FAILED to create CompleteSyllabus record", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('lw-toast', type: 'error', message: 'DB record error: ' . $e->getMessage());
            return;
        }

        // ── 7. Update syllabus status ─────────────────────────────────────
        Log::info("$tag updating syllabus status to 'under_review'…");
        try {
            $syllabus->forceFill([
                'status'       => 'under_review',
                'current_step' => 'review',
            ])->save();
            Log::info("$tag syllabus status updated OK");
        } catch (Throwable $e) {
            Log::error("$tag FAILED to update syllabus status", ['error' => $e->getMessage()]);
            // Non-fatal — PDF and record already saved, just warn
            $this->dispatch('lw-toast', type: 'warning', message: 'PDF saved but syllabus status update failed: ' . $e->getMessage());
        }

        // ── 8. Done ───────────────────────────────────────────────────────
        Log::info("$tag ── completed successfully ─────────────────────────");
        $this->dispatch('lw-toast', type: 'success', message: "Syllabus saved (v{$version}) — PDF stored locally at syllabi/{$fileName}.");
        $this->loadData(force: true);
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function loadData(bool $force = false): void
    {
        if ($this->isLoaded && ! $force) {
            return;
        }

        $this->syllabus = Syllabus::query()->with([
            'course.program.outcomes',
            'course.programOutcomes',
            'components',
            'weeks',
            'academicCalendar',
        ])->findOrFail($this->syllabusId);

        $this->course               = $this->syllabus->course;
        $this->academic_calendar_id = $this->syllabus->academic_calendar_id
            ? (int) $this->syllabus->academic_calendar_id
            : null;

        $this->academicCalendars = AcademicCalendar::query()
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $this->courseOutcomes = $this->syllabus->courseOutcomes
            ->map(fn ($co) => [
                'id'          => $co->id,
                'co_code'     => $co->co_code,
                'description' => $co->description,
            ])
            ->values()
            ->all();

        $this->syllabusWeeks = $this->syllabus->weeks->sortBy('week_no')->values();

        $examWeeks = [];
        foreach ($this->syllabusWeeks as $week) {
            if ($week->exam_type) {
                $examWeeks[$week->exam_type] = $week->week_no;
            }
        }
        $this->examWeeks = $examWeeks;

        $this->latestComplete = CompleteSyllabus::where('syllabus_id', $this->syllabusId)
            ->orderByDesc('version')
            ->first();

        $this->isLoaded = true;
    }
}