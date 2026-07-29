<?php

namespace Tests\Feature\Syllabus;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Models\Syllabus;
use App\Models\SyllabusWeek;
use App\Models\WeekContent;
use App\Services\Syllabus\CalendarWeekSequenceBuilder;
use App\Services\Syllabus\WeekGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

// Covers WeekGenerationService::generate(), hardReset(), and deleteAllWeeks()
//
// Groups:
//   A. generate() — validation guards
//   B. generate() — week row creation
//   C. generate() — WeekContent rows and exam labels
//   D. generate() — idempotency
//   E. hardReset() — destructive wipe + recreate
//   F. deleteAllWeeks() — cascade deletes
class WeekGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    private WeekGenerationService $service;
    private array $lecOnly;
    private array $lecAndLab;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new WeekGenerationService(new CalendarWeekSequenceBuilder());

        $this->lecOnly   = ['LEC' => ['type' => 'LEC', 'class_hours' => 3]];
        $this->lecAndLab = [
            'LEC' => ['type' => 'LEC', 'class_hours' => 3],
            'LAB' => ['type' => 'LAB', 'class_hours' => 3],
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeCalendar(string $start, string $end): AcademicCalendar
    {
        return AcademicCalendar::factory()->create([
            'start_date' => $start,
            'end_date'   => $end,
        ]);
    }

    private function makeSyllabus(?AcademicCalendar $calendar = null): Syllabus
    {
        $cal = $calendar ?? $this->makeCalendar('2026-08-10', '2026-08-30'); // 3 clean weeks

        return Syllabus::factory()->create([
            'academic_calendar_id' => $cal->id,
        ]);
    }

    // ── A. generate() — validation guards ────────────────────────────────────

    #[Test]
    public function generate_throws_when_syllabus_has_no_calendar(): void
    {
        $syllabus = Syllabus::factory()->withoutCalendar()->create();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Select an academic calendar first.');

        $this->service->generate($syllabus, $this->lecOnly);
    }

    #[Test]
    public function generate_throws_when_calendar_has_no_dates(): void
    {
        $calendar = AcademicCalendar::factory()->create([
            'start_date' => null,
            'end_date'   => null,
        ]);
        $syllabus = $this->makeSyllabus($calendar);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Academic calendar has no start/end date.');

        $this->service->generate($syllabus, $this->lecOnly);
    }

    #[Test]
    public function generate_throws_when_no_course_components_provided(): void
    {
        $syllabus = $this->makeSyllabus();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Complete the Course Components step first.');

        $this->service->generate($syllabus, []);
    }

    #[Test]
    public function generate_throws_when_all_weeks_are_breaks(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-16');
        AcademicCalendarEvent::factory()->break()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-10',
        ]);
        $syllabus = $this->makeSyllabus($calendar);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('produced no teachable weeks');

        $this->service->generate($syllabus, $this->lecOnly);
    }

    // ── B. generate() — week row creation ────────────────────────────────────

    #[Test]
    public function generate_creates_correct_number_of_syllabus_week_rows(): void
    {
        $syllabus = $this->makeSyllabus(); // 3-week calendar

        $this->service->generate($syllabus, $this->lecOnly);

        $this->assertDatabaseCount('syllabus_weeks', 3);
    }

    #[Test]
    public function generated_weeks_are_numbered_sequentially_from_one(): void
    {
        $syllabus = $this->makeSyllabus();

        $this->service->generate($syllabus, $this->lecOnly);

        $weekNos = SyllabusWeek::where('syllabus_id', $syllabus->id)
            ->orderBy('week_no')
            ->pluck('week_no')
            ->map(fn ($v) => (int) $v)
            ->all();

        $this->assertSame([1, 2, 3], $weekNos);
    }

    #[Test]
    public function generate_assigns_correct_start_and_end_dates(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        $syllabus = $this->makeSyllabus($calendar);

        $this->service->generate($syllabus, $this->lecOnly);

        $week1 = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 1)->first();
        $week2 = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 2)->first();

        $this->assertSame('2026-08-10', $week1->start_date);
        $this->assertSame('2026-08-16', $week1->end_date);
        $this->assertSame('2026-08-17', $week2->start_date);
    }

    #[Test]
    public function break_week_is_skipped_and_does_not_produce_a_week_row(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30'); // 3 weeks
        AcademicCalendarEvent::factory()->break()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-17', // week 2
        ]);
        $syllabus = $this->makeSyllabus($calendar);

        $this->service->generate($syllabus, $this->lecOnly);

        // Week 2 was a break — only 2 rows created
        $this->assertDatabaseCount('syllabus_weeks', 2);
    }

    // ── C. generate() — WeekContent rows and exam labels ─────────────────────

    #[Test]
    public function generate_creates_one_lec_week_content_row_per_week_for_lec_only_course(): void
    {
        $syllabus = $this->makeSyllabus(); // 3-week calendar

        $this->service->generate($syllabus, $this->lecOnly);

        $this->assertDatabaseCount('week_contents', 3);
        $this->assertDatabaseMissing('week_contents', ['component_type' => 'LAB']);
    }

    #[Test]
    public function generate_creates_both_lec_and_lab_rows_for_lec_lab_course(): void
    {
        $syllabus = $this->makeSyllabus();

        $this->service->generate($syllabus, $this->lecAndLab);

        // 3 weeks × 2 components = 6 rows
        $this->assertDatabaseCount('week_contents', 6);
        $this->assertDatabaseHas('week_contents', ['component_type' => 'LEC']);
        $this->assertDatabaseHas('week_contents', ['component_type' => 'LAB']);
    }

    #[Test]
    public function exam_week_gets_first_term_exam_label_on_first_encounter(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        AcademicCalendarEvent::factory()->exam()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-17', // week 2
        ]);
        $syllabus = $this->makeSyllabus($calendar);

        $this->service->generate($syllabus, $this->lecOnly);

        $examWeek    = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 2)->first();
        $examContent = WeekContent::where('syllabus_week_id', $examWeek->id)
            ->where('component_type', 'LEC')
            ->first();

        $this->assertSame('1st Term Exam', $examContent->assessment_task);
    }

    #[Test]
    public function exam_term_labels_increment_in_encounter_order(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-09-20'); // 6 weeks
        AcademicCalendarEvent::factory()->exam()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-17', // week 2
        ]);
        AcademicCalendarEvent::factory()->exam()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-09-07', // week 5
        ]);
        $syllabus = $this->makeSyllabus($calendar);

        $this->service->generate($syllabus, $this->lecOnly);

        $weeks        = SyllabusWeek::where('syllabus_id', $syllabus->id)->orderBy('week_no')->get();
        $week2Content = WeekContent::where('syllabus_week_id', $weeks[1]->id)->where('component_type', 'LEC')->first();
        $week5Content = WeekContent::where('syllabus_week_id', $weeks[4]->id)->where('component_type', 'LEC')->first();

        $this->assertSame('1st Term Exam', $week2Content->assessment_task);
        $this->assertSame('2nd Term Exam', $week5Content->assessment_task);
    }

    #[Test]
    public function exam_lab_content_gets_practical_exam_label(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        AcademicCalendarEvent::factory()->exam()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-17',
        ]);
        $syllabus = $this->makeSyllabus($calendar);

        $this->service->generate($syllabus, $this->lecAndLab);

        $examWeek   = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 2)->first();
        $labContent = WeekContent::where('syllabus_week_id', $examWeek->id)->where('component_type', 'LAB')->first();

        $this->assertSame('1st Term Practical Exam', $labContent->assessment_task);
    }

    #[Test]
    public function non_teaching_week_gets_non_teaching_label(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        AcademicCalendarEvent::factory()->nonTeaching()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-17',
        ]);
        $syllabus = $this->makeSyllabus($calendar);

        $this->service->generate($syllabus, $this->lecOnly);

        $ntWeek    = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 2)->first();
        $ntContent = WeekContent::where('syllabus_week_id', $ntWeek->id)->where('component_type', 'LEC')->first();

        $this->assertSame('Non-Teaching Week', $ntContent->assessment_task);
    }

    #[Test]
    public function regular_week_has_empty_assessment_task_on_generation(): void
    {
        $syllabus = $this->makeSyllabus();

        $this->service->generate($syllabus, $this->lecOnly);

        $week1   = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 1)->first();
        $content = WeekContent::where('syllabus_week_id', $week1->id)->first();

        $this->assertSame('', $content->assessment_task);
    }

    #[Test]
    public function regular_week_has_empty_learning_outcomes_on_generation(): void
    {
        $syllabus = $this->makeSyllabus();

        $this->service->generate($syllabus, $this->lecOnly);

        $week1   = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 1)->first();
        $content = WeekContent::where('syllabus_week_id', $week1->id)->first();

        $this->assertSame('', $content->learning_outcomes);
    }

    // ── D. generate() — idempotency ───────────────────────────────────────────

    #[Test]
    public function calling_generate_twice_does_not_duplicate_weeks(): void
    {
        $syllabus = $this->makeSyllabus();

        $this->service->generate($syllabus, $this->lecOnly);
        $this->service->generate($syllabus, $this->lecOnly);

        $this->assertDatabaseCount('syllabus_weeks', 3);
    }

    #[Test]
    public function generate_returns_true_on_first_successful_creation(): void
    {
        $syllabus = $this->makeSyllabus();

        $result = $this->service->generate($syllabus, $this->lecOnly);

        $this->assertTrue($result);
    }

    #[Test]
    public function generate_returns_true_on_second_call_idempotent_exit(): void
    {
        $syllabus = $this->makeSyllabus();
        $this->service->generate($syllabus, $this->lecOnly);

        $result = $this->service->generate($syllabus, $this->lecOnly);

        $this->assertTrue($result);
    }

    // ── E. hardReset() — destructive wipe + recreate ─────────────────────────

    #[Test]
    public function hard_reset_removes_all_previous_content(): void
    {
        $syllabus = $this->makeSyllabus();
        $this->service->generate($syllabus, $this->lecOnly);

        $week = SyllabusWeek::where('syllabus_id', $syllabus->id)->first();
        WeekContent::where('syllabus_week_id', $week->id)
            ->update(['learning_outcomes' => 'Should be gone after reset']);

        $this->service->hardReset($syllabus, $this->lecOnly);

        $this->assertDatabaseMissing('week_contents', [
            'learning_outcomes' => 'Should be gone after reset',
        ]);
    }

    #[Test]
    public function hard_reset_creates_fresh_weeks_after_clearing(): void
    {
        $syllabus = $this->makeSyllabus();
        $this->service->generate($syllabus, $this->lecOnly);
        $this->service->hardReset($syllabus, $this->lecOnly);

        $this->assertDatabaseCount('syllabus_weeks', 3);
    }

    #[Test]
    public function hard_reset_rebuilds_correct_week_count_when_calendar_is_longer(): void
    {
        $calendar3 = $this->makeCalendar('2026-08-10', '2026-08-30'); // 3 weeks
        $syllabus  = $this->makeSyllabus($calendar3);
        $this->service->generate($syllabus, $this->lecOnly);

        $calendar4 = $this->makeCalendar('2026-08-10', '2026-09-06'); // 4 weeks
        $syllabus->update(['academic_calendar_id' => $calendar4->id]);
        $syllabus->refresh();

        $this->service->hardReset($syllabus, $this->lecOnly);

        $this->assertDatabaseCount('syllabus_weeks', 4);
    }

    // ── F. deleteAllWeeks() — cascade deletes ─────────────────────────────────

    #[Test]
    public function delete_all_weeks_removes_week_content_rows(): void
    {
        $syllabus = $this->makeSyllabus();
        $this->service->generate($syllabus, $this->lecOnly);

        $this->service->deleteAllWeeks($syllabus);

        $this->assertDatabaseCount('week_contents', 0);
    }

    #[Test]
    public function delete_all_weeks_is_a_no_op_when_no_weeks_exist(): void
    {
        $syllabus = Syllabus::factory()->create();

        $this->service->deleteAllWeeks($syllabus);

        $this->assertDatabaseCount('syllabus_weeks', 0);
    }

    #[Test]
    public function delete_all_weeks_only_removes_weeks_belonging_to_the_given_syllabus(): void
    {
        $syllabusA = $this->makeSyllabus();
        $syllabusB = $this->makeSyllabus();

        $this->service->generate($syllabusA, $this->lecOnly);
        $this->service->generate($syllabusB, $this->lecOnly);

        $this->service->deleteAllWeeks($syllabusA);

        $this->assertDatabaseCount('syllabus_weeks', 3);

        $remainingOwners = SyllabusWeek::all()
            ->pluck('syllabus_id')
            ->unique()
            ->values()
            ->map(fn ($id) => (int) $id)
            ->all();

        $this->assertSame([(int) $syllabusB->id], $remainingOwners);
    }
}
