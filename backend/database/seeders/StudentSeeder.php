<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create students for different classes and sections
        $classes = ['9', '10', '11', '12'];
        $sections = ['A', 'B', 'C', 'D'];
        
        foreach ($classes as $class) {
            foreach ($sections as $section) {
                // Create 10 students per class-section combination
                Student::factory()
                    ->count(10)
                    ->create([
                        'class' => $class,
                        'section' => $section,
                    ]);
            }
        }
        
        $this->command->info('Created ' . Student::count() . ' students.');
    }
}
