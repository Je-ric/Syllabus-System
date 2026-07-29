<?php

namespace Tests\Feature\Syllabus;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Services\Syllabus\CalendarWeekSequenceBuilder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

// Covers CalendarWeekSequenceBuilder::build()
//
// Groups:
//   A. Basic date arithmetic
//   B. Break-week skipping
//   C. Locking-event detection (exam / non_teaching)
//   D. Edge cases (single day, all-break, cross-calendar isolation)
class CalendarWeekSequenceBuilderTest extends TestCase
{
    use RefreshDatabase;

    private CalendarWeekSequenceBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new CalendarWeekSequenceBuilder();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeCalendar(string $start, string $end): AcademicCalendar
    {
        return AcademicCalendar::factory()->create([
            'start_date' => $start,
            'end_date'   => $end,
        ]);
    }

    // ── A. Basic date arithmetic ───────────────────────────────────────────────

    #[Test]
    public function it_produces_correct_week_count_for_exact_multiple_of_7(): void
    {
        // 4 × 7 = 28 days → 4 weeks
        $calendar = $this->makeCalendar('2026-08-10', '2026-09-06');

        $sequence = $this->builder->build($calendar, $calendar->id);

        $this->assertCount(4, $sequence);
    }

    #[Test]
    public function week_one_starts_on_the_calendar_start_date(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-09-06');

        $sequence = $this->builder->build($calendar, $calendar->id);

        $this->assertSame('2026-08-10', $sequence[0]['start']);
    }

    #[Test]
    public function final_week_end_is_capped_at_calendar_end_date_when_not_a_full_7_days(): void
    {
        // 10 days → week 1 (7 days) + week 2 (3 days, capped at end_date)
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-19');

        $sequence = $this->builder->build($calendar, $calendar->id);

        $this->assertCount(2, $sequence);
        $this->assertSame('2026-08-19', $sequence[1]['end']);
    }

    #[Test]
    public function each_week_start_is_exactly_one_day_after_the_previous_end(): void
    {
        // 35 days → 5 full weeks
        $calendar = $this->makeCalendar('2026-08-10', '2026-09-13');

        $sequence = $this->builder->build($calendar, $calendar->id);

        for ($i = 1; $i < count($sequence); $i++) {
            $prevEnd   = Carbon::parse($sequence[$i - 1]['end']);
            $currStart = Carbon::parse($sequence[$i]['start']);
            // Cast to int: Carbon 3 returns float from diffInDays()
            $this->assertSame(1, (int) $prevEnd->diffInDays($currStart),
                "Week {$i} start should be 1 day after week " . ($i - 1) . ' end'
            );
        }
    }

    #[Test]
    public function week_end_is_always_6_days_after_start_for_full_weeks(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-09-06'); // 4 full weeks

        $sequence = $this->builder->build($calendar, $calendar->id);

        foreach ($sequence as $slot) {
            $start = Carbon::parse($slot['start']);
            $end   = Carbon::parse($slot['end']);
            $this->assertSame(6, (int) $start->diffInDays($end));
        }
    }

    // ── B. Break-week skipping ─────────────────────────────────────────────────

    #[Test]
    public function break_week_is_excluded_from_the_sequence(): void
    {
        // 3 weeks normally; week 2 is a break → 2 teachable weeks
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');

        AcademicCalendarEvent::factory()->break()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-17', // falls in week 2
        ]);

        $sequence = $this->builder->build($calendar, $calendar->id);

        $this->assertCount(2, $sequence);
    }

    #[Test]
    public function week_after_a_break_starts_immediately_after_the_break_range(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');

        AcademicCalendarEvent::factory()->break()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-17', // week 2 break
        ]);

        $sequence = $this->builder->build($calendar, $calendar->id);

        // slot 0 = week 1 (Aug 10–16), slot 1 = week 3 (Aug 24–30), week 2 skipped
        $this->assertSame('2026-08-10', $sequence[0]['start']);
        $this->assertSame('2026-08-24', $sequence[1]['start']);
    }

    #[Test]
    public function multiple_consecutive_break_weeks_are_all_excluded(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-09-06'); // 4 weeks

        // Weeks 2 and 3 are breaks
        AcademicCalendarEvent::factory()->break()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-17',
        ]);
        AcademicCalendarEvent::factory()->break()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-24',
        ]);

        $sequence = $this->builder->build($calendar, $calendar->id);

        $this->assertCount(2, $sequence);
    }

    // ── C. Locking-event detection ────────────────────────────────────────────

    #[Test]
    public function exam_event_is_returned_as_locking_event_for_its_week(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');

        // Exam on Aug 20 falls in week 2 (Aug 17–23)
        AcademicCalendarEvent::factory()->exam()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-20',
        ]);

        $sequence = $this->builder->build($calendar, $calendar->id);

        $this->assertNull($sequence[0]['lockingEvent'],    'Week 1 should have no locking event');
        $this->assertNotNull($sequence[1]['lockingEvent'], 'Week 2 should have a locking event');
        $this->assertSame('exam', $sequence[1]['lockingEvent']->type);
    }

    #[Test]
    public function non_teaching_event_is_returned_as_locking_event(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');

        AcademicCalendarEvent::factory()->nonTeaching()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-18', // week 2
        ]);

        $sequence = $this->builder->build($calendar, $calendar->id);

        $this->assertSame('non_teaching', $sequence[1]['lockingEvent']->type);
    }

    #[Test]
    public function holiday_event_does_not_lock_a_week(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');

        AcademicCalendarEvent::factory()->create([
            'academic_calendar_id' => $calendar->id,
            'type'                 => 'holiday',
            'name'                 => 'National Holiday',
            'date'                 => '2026-08-18',
        ]);

        $sequence = $this->builder->build($calendar, $calendar->id);

        $this->assertNull($sequence[1]['lockingEvent'],
            'A holiday should not produce a locking event'
        );
    }

    #[Test]
    public function first_locking_event_in_date_order_wins_when_multiple_exist_in_same_week(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');

        // non_teaching on Aug 17, exam on Aug 20 — non_teaching is first
        AcademicCalendarEvent::factory()->nonTeaching()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-17',
        ]);
        AcademicCalendarEvent::factory()->exam()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-20',
        ]);

        $sequence = $this->builder->build($calendar, $calendar->id);

        $this->assertSame('non_teaching', $sequence[1]['lockingEvent']->type,
            'non_teaching (Aug 17) should win over exam (Aug 20)'
        );
    }

    #[Test]
    public function weeks_without_locking_events_have_null_locking_event(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-30');

        $sequence = $this->builder->build($calendar, $calendar->id);

        foreach ($sequence as $slot) {
            $this->assertNull($slot['lockingEvent']);
        }
    }

    // ── D. Edge cases ─────────────────────────────────────────────────────────

    #[Test]
    public function single_day_calendar_produces_one_week_slot(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-10');

        $sequence = $this->builder->build($calendar, $calendar->id);

        $this->assertCount(1, $sequence);
        $this->assertSame('2026-08-10', $sequence[0]['start']);
        $this->assertSame('2026-08-10', $sequence[0]['end']);
    }

    #[Test]
    public function all_break_calendar_produces_empty_sequence(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-16');

        AcademicCalendarEvent::factory()->break()->create([
            'academic_calendar_id' => $calendar->id,
            'date'                 => '2026-08-10',
        ]);

        $sequence = $this->builder->build($calendar, $calendar->id);

        $this->assertEmpty($sequence);
    }

    #[Test]
    public function slot_shape_contains_required_keys(): void
    {
        $calendar = $this->makeCalendar('2026-08-10', '2026-08-16');

        $sequence = $this->builder->build($calendar, $calendar->id);

        $this->assertArrayHasKey('start',        $sequence[0]);
        $this->assertArrayHasKey('end',          $sequence[0]);
        $this->assertArrayHasKey('lockingEvent', $sequence[0]);
    }

    #[Test]
    public function events_from_a_different_calendar_do_not_affect_the_sequence(): void
    {
        $calendar      = $this->makeCalendar('2026-08-10', '2026-08-30');
        $otherCalendar = $this->makeCalendar('2026-08-10', '2026-08-30');

        // Break event belongs to the OTHER calendar
        AcademicCalendarEvent::factory()->break()->create([
            'academic_calendar_id' => $otherCalendar->id,
            'date'                 => '2026-08-17',
        ]);

        $sequence = $this->builder->build($calendar, $calendar->id);

        // All 3 weeks must survive — the other calendar's break is irrelevant
        $this->assertCount(3, $sequence);
    }
}
