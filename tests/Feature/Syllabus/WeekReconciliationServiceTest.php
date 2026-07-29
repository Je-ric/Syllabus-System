<?php

namespace Tests\Feature\Syllabus;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Models\OnlineMaterial;
use App\Models\Reference;
use App\Models\Syllabus;
use App\Models\SyllabusWeek;
use App\Models\WeekContent;
use App\Services\Syllabus\CalendarWeekSequenceBuilder;
use App\Services\Syllabus\ReconciliationResult;
use App\Services\Syllabus\WeekGenerationService;
use App\Services\Syllabus\WeekReconciliationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

// Covers WeekReconciliationService::reconcile() and ReconciliationResult
//
// Groups:
//   A. Validation guards
//   B. In-place date updates (same week count, shifted dates)
//   C. Content preservation — faculty data must never be touched
//   D. Surplus week trimming (new calendar is shorter)
//   E. New week appending (new calendar is longer)
//   F. Exam label re-sync on locked weeks
//   G. No-op detection (already in sync)
//   H. ReconciliationResult value object
class WeekReconciliationServiceTest extends TestCase
{
    use RefreshDatabase;

    private WeekGenerationService    $generator;
    private WeekReconciliationService $reconciler;

    private array $lecOnly;
    private array $lecAndLab;

    protected function setUp(): void
    {
        parent::setUp();

        $builder          = new CalendarWeekSequenceBuilder();
        $this->generator  = new WeekGenerationService($builder);
        $this->reconciler = new WeekReconciliationService($builder);

        $this->lecOnly   = ['LEC' => ['type' => 'LEC']];
        $this->lecAndLab = [
            'LEC' => ['type' => 'LEC'],
            'LAB' => ['type' => 'LAB'],
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeCalendar(string $start, string $end, bool $active = true): AcademicCalendar
    {
        return AcademicCalendar::factory()->create([
            'start_date' => $start,
            'end_date'   => $end,
            'is_active'  => $active,
        ]);
    }

    private function makeSyllabus(AcademicCalendar $calendar): Syllabus
    {
        return Syllabus::factory()->create([
            'academic_calendar_id' => $calendar->id,
        ]);
    }

    private function seedWeekContent(int $weekId, string $component = 'LEC'): WeekContent
    {
        return WeekContent::where('syllabus_week_id', $weekId)
            ->where('component_type', $component)
            ->firstOrFail()
            ->tap(fn ($c) => $c->update([
                'learning_outcomes' => 'Faculty LO for this week',
                'topics'            => 'Faculty topic content',
                'assessment_task'   => 'Faculty quiz',
                'tla'               => 'Faculty teaching activity',
            ]));
    }

    // ── A. Validation guards ──────────────────────────────────────────────────

    #[Test]
    public function reconcile_throws_when_syllabus_has_no_calendar(): void
    {
        $syllabus = Syllabus::factory()->withoutCalendar()->create();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Select an academic calendar first.');

        $this->reconciler->reconcile($syllabus, $this->lecOnly);
    }

    #[Test]
    public function reconcile_throws_when_calendar_has_no_dates(): void
    {
        $calendar = AcademicCalendar::factory()->create(['start_date' => null, 'end_date' => null]);
        $syllabus = $this->makeSyllabus($calendar);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Academic calendar has no start/end date.');

        $this->reconciler->reconcile($syllabus, $this->lecOnly);
    }

    #[Test]
    public function reconcile_throws_when_no_course_components_provided(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        $syllabus = $this->makeSyllabus($calendar);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Complete the Course Components step first.');

        $this->reconciler->reconcile($syllabus, []);
    }

    #[Test]
    public function reconcile_throws_when_calendar_produces_no_teachable_weeks(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-16');
        AcademicCalendarEvent::factory()->break()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-10',
        ]);
        $syllabus = $this->makeSyllabus($calendar);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('produced no teachable weeks');

        $this->reconciler->reconcile($syllabus, $this->lecOnly);
    }

    // ── B. In-place date updates ──────────────────────────────────────────────

    #[Test]
    public function reconcile_updates_week_start_and_end_dates_in_place(): void
    {
        $calendarA = $this->makeCalendar('2026-08-10', '2026-08-30'); // 3 weeks
        $syllabus  = $this->makeSyllabus($calendarA);
        $this->generator->generate($syllabus, $this->lecOnly);

        $calendarB = $this->makeCalendar('2026-08-17', '2026-09-06'); // 3 weeks, shifted
        $syllabus->update(['academic_calendar_id' => $calendarB->id]);
        $syllabus->refresh();

        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $week1 = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 1)->first();
        $this->assertSame('2026-08-17', $week1->start_date);
        $this->assertSame('2026-08-23', $week1->end_date);
    }

    #[Test]
    public function reconcile_updates_all_weeks_and_reports_count(): void
    {
        $calendarA = $this->makeCalendar('2026-08-10', '2026-08-30');
        $syllabus  = $this->makeSyllabus($calendarA);
        $this->generator->generate($syllabus, $this->lecOnly);

        $calendarB = $this->makeCalendar('2026-08-17', '2026-09-06');
        $syllabus->update(['academic_calendar_id' => $calendarB->id]);
        $syllabus->refresh();

        $result = $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertSame(3, $result->datesUpdated);
    }

    #[Test]
    public function reconcile_does_not_create_duplicate_weeks_when_calendar_unchanged(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        $syllabus = $this->makeSyllabus($calendar);
        $this->generator->generate($syllabus, $this->lecOnly);

        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertDatabaseCount('syllabus_weeks', 3);
    }

    // ── C. Content preservation ───────────────────────────────────────────────

    #[Test]
    public function reconcile_preserves_faculty_learning_outcomes(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        $syllabus = $this->makeSyllabus($calendar);
        $this->generator->generate($syllabus, $this->lecOnly);

        $week1 = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 1)->first();
        $this->seedWeekContent($week1->id);

        $calendarB = $this->makeCalendar('2026-08-17', '2026-09-06');
        $syllabus->update(['academic_calendar_id' => $calendarB->id]);
        $syllabus->refresh();
        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertDatabaseHas('week_contents', [
            'syllabus_week_id'  => $week1->id,
            'learning_outcomes' => 'Faculty LO for this week',
        ]);
    }

    #[Test]
    public function reconcile_preserves_faculty_topics(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        $syllabus = $this->makeSyllabus($calendar);
        $this->generator->generate($syllabus, $this->lecOnly);

        $week1 = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 1)->first();
        $this->seedWeekContent($week1->id);

        $calendarB = $this->makeCalendar('2026-08-17', '2026-09-06');
        $syllabus->update(['academic_calendar_id' => $calendarB->id]);
        $syllabus->refresh();
        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertDatabaseHas('week_contents', [
            'syllabus_week_id' => $week1->id,
            'topics'           => 'Faculty topic content',
        ]);
    }

    #[Test]
    public function reconcile_preserves_references_on_surviving_weeks(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        $syllabus = $this->makeSyllabus($calendar);
        $this->generator->generate($syllabus, $this->lecOnly);

        $week1 = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 1)->first();
        Reference::create([
            'syllabus_id'      => $syllabus->id,
            'syllabus_week_id' => $week1->id,
            'component_type'   => 'LEC',
            'reference_text'   => 'Textbook chapter 1',
        ]);

        $calendarB = $this->makeCalendar('2026-08-17', '2026-09-06');
        $syllabus->update(['academic_calendar_id' => $calendarB->id]);
        $syllabus->refresh();
        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertDatabaseHas('references', [
            'syllabus_id'    => $syllabus->id,
            'reference_text' => 'Textbook chapter 1',
        ]);
    }

    #[Test]
    public function reconcile_preserves_online_materials_on_surviving_weeks(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        $syllabus = $this->makeSyllabus($calendar);
        $this->generator->generate($syllabus, $this->lecOnly);

        $week1 = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 1)->first();
        OnlineMaterial::create([
            'syllabus_id'      => $syllabus->id,
            'syllabus_week_id' => $week1->id,
            'component_type'   => 'LEC',
            'material_name'    => 'Recorded lecture',
            'url'              => 'https://example.com/lecture1',
        ]);

        $calendarB = $this->makeCalendar('2026-08-17', '2026-09-06');
        $syllabus->update(['academic_calendar_id' => $calendarB->id]);
        $syllabus->refresh();
        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertDatabaseHas('online_materials', ['material_name' => 'Recorded lecture']);
    }

    #[Test]
    public function reconcile_does_not_overwrite_faculty_assessment_task_on_editable_weeks(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        $syllabus = $this->makeSyllabus($calendar);
        $this->generator->generate($syllabus, $this->lecOnly);

        $week1 = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 1)->first();
        WeekContent::where('syllabus_week_id', $week1->id)->update(['assessment_task' => 'Seatwork 1']);

        // Same calendar — week 1 has no locking event so label must not be touched
        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertDatabaseHas('week_contents', [
            'syllabus_week_id' => $week1->id,
            'assessment_task'  => 'Seatwork 1',
        ]);
    }


    // ── D. Surplus week trimming ──────────────────────────────────────────────

    #[Test]
    public function reconcile_removes_tail_weeks_when_calendar_is_shorter(): void
    {
        $calendar4 = $this->makeCalendar('2026-08-10', '2026-09-06'); // 4 weeks
        $syllabus  = $this->makeSyllabus($calendar4);
        $this->generator->generate($syllabus, $this->lecOnly);

        $calendar2 = $this->makeCalendar('2026-08-10', '2026-08-23'); // 2 weeks
        $syllabus->update(['academic_calendar_id' => $calendar2->id]);
        $syllabus->refresh();
        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertDatabaseCount('syllabus_weeks', 2);
    }

    #[Test]
    public function reconcile_removes_week_content_for_dropped_weeks(): void
    {
        $calendar4 = $this->makeCalendar('2026-08-10', '2026-09-06');
        $syllabus  = $this->makeSyllabus($calendar4);
        $this->generator->generate($syllabus, $this->lecOnly);

        $calendar2 = $this->makeCalendar('2026-08-10', '2026-08-23');
        $syllabus->update(['academic_calendar_id' => $calendar2->id]);
        $syllabus->refresh();
        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        // 2 weeks × 1 component = 2 rows
        $this->assertDatabaseCount('week_contents', 2);
    }

    #[Test]
    public function reconcile_removes_references_for_dropped_weeks(): void
    {
        $calendar4 = $this->makeCalendar('2026-08-10', '2026-09-06');
        $syllabus  = $this->makeSyllabus($calendar4);
        $this->generator->generate($syllabus, $this->lecOnly);

        $week3 = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 3)->first();
        Reference::create([
            'syllabus_id'      => $syllabus->id,
            'syllabus_week_id' => $week3->id,
            'component_type'   => 'LEC',
            'reference_text'   => 'Will be deleted',
        ]);

        $calendar2 = $this->makeCalendar('2026-08-10', '2026-08-23');
        $syllabus->update(['academic_calendar_id' => $calendar2->id]);
        $syllabus->refresh();
        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertDatabaseMissing('references', ['reference_text' => 'Will be deleted']);
    }

    #[Test]
    public function reconcile_result_reports_correct_weeks_dropped_count(): void
    {
        $calendar4 = $this->makeCalendar('2026-08-10', '2026-09-06');
        $syllabus  = $this->makeSyllabus($calendar4);
        $this->generator->generate($syllabus, $this->lecOnly);

        $calendar2 = $this->makeCalendar('2026-08-10', '2026-08-23');
        $syllabus->update(['academic_calendar_id' => $calendar2->id]);
        $syllabus->refresh();
        $result = $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertSame(2, $result->weeksDropped);
    }

    // ── E. New week appending ─────────────────────────────────────────────────

    #[Test]
    public function reconcile_appends_new_empty_weeks_when_calendar_is_longer(): void
    {
        $calendar2 = $this->makeCalendar('2026-08-10', '2026-08-23');
        $syllabus  = $this->makeSyllabus($calendar2);
        $this->generator->generate($syllabus, $this->lecOnly);

        $calendar4 = $this->makeCalendar('2026-08-10', '2026-09-06');
        $syllabus->update(['academic_calendar_id' => $calendar4->id]);
        $syllabus->refresh();
        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertDatabaseCount('syllabus_weeks', 4);
    }

    #[Test]
    public function appended_weeks_have_empty_content_fields(): void
    {
        $calendar2 = $this->makeCalendar('2026-08-10', '2026-08-23');
        $syllabus  = $this->makeSyllabus($calendar2);
        $this->generator->generate($syllabus, $this->lecOnly);

        $calendar4 = $this->makeCalendar('2026-08-10', '2026-09-06');
        $syllabus->update(['academic_calendar_id' => $calendar4->id]);
        $syllabus->refresh();
        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $newWeek    = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 3)->first();
        $newContent = WeekContent::where('syllabus_week_id', $newWeek->id)->first();

        $this->assertSame('', $newContent->learning_outcomes);
        $this->assertSame('', $newContent->topics);
    }

    #[Test]
    public function reconcile_result_reports_correct_weeks_added_count(): void
    {
        $calendar2 = $this->makeCalendar('2026-08-10', '2026-08-23');
        $syllabus  = $this->makeSyllabus($calendar2);
        $this->generator->generate($syllabus, $this->lecOnly);

        $calendar4 = $this->makeCalendar('2026-08-10', '2026-09-06');
        $syllabus->update(['academic_calendar_id' => $calendar4->id]);
        $syllabus->refresh();
        $result = $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertSame(2, $result->weeksAdded);
    }

    #[Test]
    public function appended_weeks_get_lab_content_rows_for_lec_lab_course(): void
    {
        $calendar2 = $this->makeCalendar('2026-08-10', '2026-08-23');
        $syllabus  = $this->makeSyllabus($calendar2);
        $this->generator->generate($syllabus, $this->lecAndLab);

        $calendar4 = $this->makeCalendar('2026-08-10', '2026-09-06');
        $syllabus->update(['academic_calendar_id' => $calendar4->id]);
        $syllabus->refresh();
        $this->reconciler->reconcile($syllabus, $this->lecAndLab);

        $newWeek    = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 3)->first();
        $labContent = WeekContent::where('syllabus_week_id', $newWeek->id)
            ->where('component_type', 'LAB')
            ->first();

        $this->assertNotNull($labContent);
    }

    // ── F. Exam label re-sync ─────────────────────────────────────────────────

    #[Test]
    public function reconcile_rewrites_exam_label_on_locked_week_after_calendar_shift(): void
    {
        $calendarA = $this->makeCalendar('2026-08-10', '2026-08-30');
        AcademicCalendarEvent::factory()->exam()->create([
            'academic_calendar_id' => $calendarA->id,
            'date'                 => '2026-08-17',
        ]);
        $syllabus = $this->makeSyllabus($calendarA);
        $this->generator->generate($syllabus, $this->lecOnly);

        $calendarB = $this->makeCalendar('2026-08-17', '2026-09-06');
        AcademicCalendarEvent::factory()->exam()->create([
            'academic_calendar_id' => $calendarB->id,
            'date'                 => '2026-08-24',
        ]);
        $syllabus->update(['academic_calendar_id' => $calendarB->id]);
        $syllabus->refresh();
        $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $week2   = SyllabusWeek::where('syllabus_id', $syllabus->id)->where('week_no', 2)->first();
        $content = WeekContent::where('syllabus_week_id', $week2->id)->where('component_type', 'LEC')->first();

        $this->assertSame('1st Term Exam', $content->assessment_task);
    }

    #[Test]
    public function reconcile_result_reports_labels_resynced_when_locked_week_present(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        AcademicCalendarEvent::factory()->exam()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-17',
        ]);
        $syllabus = $this->makeSyllabus($calendar);
        $this->generator->generate($syllabus, $this->lecOnly);

        $result = $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertGreaterThan(0, $result->labelsResynced);
    }

    // ── G. No-op detection ────────────────────────────────────────────────────

    #[Test]
    public function reconcile_reports_no_changes_when_weeks_already_match_calendar(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');
        $syllabus = $this->makeSyllabus($calendar);
        $this->generator->generate($syllabus, $this->lecOnly);

        $result = $this->reconciler->reconcile($syllabus, $this->lecOnly);

        $this->assertSame(0, $result->datesUpdated);
        $this->assertSame(0, $result->weeksAdded);
        $this->assertSame(0, $result->weeksDropped);
    }

    // ── H. ReconciliationResult value object ─────────────────────────────────

    #[Test]
    public function reconciliation_result_has_no_changes_returns_true_when_all_zero(): void
    {
        $result = new ReconciliationResult(0, 0, 0, 0);

        $this->assertTrue($result->hasNoChanges());
    }

    #[Test]
    public function reconciliation_result_has_no_changes_returns_false_when_any_changed(): void
    {
        $result = new ReconciliationResult(datesUpdated: 1, weeksAdded: 0, weeksDropped: 0, labelsResynced: 0);

        $this->assertFalse($result->hasNoChanges());
    }

    #[Test]
    public function reconciliation_result_to_message_returns_nothing_changed_string(): void
    {
        $result = new ReconciliationResult(0, 0, 0, 0);

        $this->assertStringContainsString('nothing changed', $result->toMessage());
    }

    #[Test]
    public function reconciliation_result_to_message_lists_dates_updated(): void
    {
        $result = new ReconciliationResult(datesUpdated: 3, weeksAdded: 0, weeksDropped: 0, labelsResynced: 0);

        $this->assertStringContainsString('3 week dates updated', $result->toMessage());
    }

    #[Test]
    public function reconciliation_result_to_message_lists_weeks_added(): void
    {
        $result = new ReconciliationResult(datesUpdated: 0, weeksAdded: 2, weeksDropped: 0, labelsResynced: 0);

        $this->assertStringContainsString('2 new weeks added', $result->toMessage());
    }

    #[Test]
    public function reconciliation_result_to_message_lists_weeks_dropped(): void
    {
        $result = new ReconciliationResult(datesUpdated: 0, weeksAdded: 0, weeksDropped: 1, labelsResynced: 0);

        $this->assertStringContainsString('1 surplus week removed', $result->toMessage());
    }

    #[Test]
    public function reconciliation_result_singular_vs_plural_week_date_label(): void
    {
        $singular = new ReconciliationResult(datesUpdated: 1, weeksAdded: 0, weeksDropped: 0, labelsResynced: 0);
        $plural   = new ReconciliationResult(datesUpdated: 2, weeksAdded: 0, weeksDropped: 0, labelsResynced: 0);

        $this->assertStringContainsString('1 week date updated', $singular->toMessage());
        $this->assertStringContainsString('2 week dates updated', $plural->toMessage());
    }

    #[Test]
    public function reconciliation_result_to_array_contains_all_four_keys(): void
    {
        $result = new ReconciliationResult(1, 2, 3, 4);

        $this->assertSame([
            'datesUpdated'   => 1,
            'weeksAdded'     => 2,
            'weeksDropped'   => 3,
            'labelsResynced' => 4,
        ], $result->toArray());
    }
}
