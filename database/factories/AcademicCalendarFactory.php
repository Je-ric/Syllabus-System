<?php

namespace Database\Factories;

use App\Models\AcademicCalendar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicCalendar>
 */
class AcademicCalendarFactory extends Factory
{
    protected $model = AcademicCalendar::class;

    public function definition(): array
    {
        return [
            'academic_year' => '2025-2026',
            'semester'      => '1st',
            'start_date'    => '2026-08-11',
            // 18 full weeks from 2026-08-11
            'end_date'      => '2026-12-12',
            'is_active'     => false,
        ];
    }

    // Marks this calendar as the single active one.
    // Callers that need an active calendar should use AcademicCalendarFactory::new()->active()->create().
    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }

    // Produces a calendar with a known, compact date range (4 clean weeks).
    // start: Monday 2026-08-10, end: Sunday 2026-09-06 → 4 × 7-day weeks.
    public function fourWeeks(): static
    {
        return $this->state([
            'start_date' => '2026-08-10',
            'end_date'   => '2026-09-06',
        ]);
    }

    // Two-week calendar — used for short-calendar edge-case tests.
    public function twoWeeks(): static
    {
        return $this->state([
            'start_date' => '2026-08-10',
            'end_date'   => '2026-08-23',
        ]);
    }
}
