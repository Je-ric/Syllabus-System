<?php

namespace Database\Factories;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicCalendarEvent>
 */
class AcademicCalendarEventFactory extends Factory
{
    protected $model = AcademicCalendarEvent::class;

    public function definition(): array
    {
        return [
            'academic_calendar_id' => AcademicCalendar::factory(),
            'type'                 => 'holiday',
            'name'                 => 'Holiday',
            'date'                 => '2026-08-14',
        ];
    }

    public function exam(): static
    {
        return $this->state(['type' => 'exam', 'name' => 'Midterm Exam']);
    }

    public function break(): static
    {
        return $this->state(['type' => 'break', 'name' => 'Break Week']);
    }

    public function nonTeaching(): static
    {
        return $this->state(['type' => 'non_teaching', 'name' => 'Non-Teaching Week']);
    }

    public function onDate(string $date): static
    {
        return $this->state(['date' => $date]);
    }
}
