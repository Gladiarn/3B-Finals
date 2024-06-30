<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Subject;

class StudentsTableSeeder extends Seeder
{
    public function run()
    {
        Student::factory()
            ->count(10) // Number of students to create
            ->create()
            ->each(function ($student) {
                Subject::factory()
                    ->count(10) // Number of subjects per student
                    ->create(['student_id' => $student->id]);
            });
    }
}
