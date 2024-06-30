<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition()
    {
        $grades = [
            'prelims' => round($this->faker->randomFloat(2, 1, 5), 2),
            'midterms' => round($this->faker->randomFloat(2, 1, 5), 2),
            'pre_finals' => round($this->faker->randomFloat(2, 1, 5), 2),
            'finals' => round($this->faker->randomFloat(2, 1, 5), 2),
        ];

        $averageGrade = round(($grades['prelims'] + $grades['midterms'] + $grades['pre_finals'] + $grades['finals']) / 4, 2);
        $remarks = $averageGrade >= 3.0 ? 'FAILED' : 'PASSED';

        return [
            'subject_code' => $this->faker->unique()->regexify('[A-Z]{2,3}-\d{3}'),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'instructor' => $this->faker->name,
            'schedule' => $this->faker->randomElement(['MW 7AM-12PM', 'TTh 1PM-5PM', 'MWF 8AM-10AM']),
            'grades' => $grades,
            'average_grade' => $averageGrade,
            'remarks' => $remarks,
            'date_taken' => $this->faker->date('Y-m-d', '-1 year'),
        ];
    }
}
