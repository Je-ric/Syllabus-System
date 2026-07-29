<?php

namespace Database\Factories;

use App\Models\AcademicCalendar;
use App\Models\Course;
use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Syllabus>
 */
class SyllabusFactory extends Factory
{
    protected $model = Syllabus::class;

    public function definition(): array
    {
        return [
            'course_id'            => Course::factory(),
            'academic_calendar_id' => AcademicCalendar::factory(),
            'status'               => 'draft',
            'current_step'         => 'academic_calendar',
            'prepared_by'          => User::factory(),
        ];
    }

    // Syllabus with no calendar assigned — for "missing calendar" guard tests.
    public function withoutCalendar(): static
    {
        return $this->state(['academic_calendar_id' => null]);
    }
}
