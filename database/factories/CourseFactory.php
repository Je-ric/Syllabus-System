<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'program_id'   => null,
            'course_code'  => strtoupper($this->faker->unique()->lexify('??###')),
            'course_title' => $this->faker->words(4, true),
            'credit_units' => 3,
            'year_level'   => '1st Year',
            'semester'     => '1st',
            'has_lec_lab'  => false,
            'status'       => 'active',
        ];
    }

    // Course with both LEC and LAB components.
    public function withLab(): static
    {
        return $this->state(['has_lec_lab' => true]);
    }
}
