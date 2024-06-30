<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create 20 students
        Student::factory()->count(20)->create()->each(function ($student) {
            // Attach 10 subjects to each student
            $student->subjects()->saveMany(Subject::factory()->count(10)->make());
        });
    }
}
