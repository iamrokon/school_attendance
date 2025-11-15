<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a user for recording attendance
        $user = User::first();
        
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@school.com',
            ]);
        }
        
        // Get all students
        $students = Student::all();
        
        if ($students->isEmpty()) {
            $this->command->warn('No students found. Please run StudentSeeder first.');
            return;
        }
        
        // Generate attendance for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        
        $attendanceCount = 0;
        
        foreach ($students as $student) {
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                // Skip weekends (optional - you can remove this if you want weekend attendance)
                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }
                
                // 85% chance of being present, 10% absent, 5% late
                $random = rand(1, 100);
                
                if ($random <= 85) {
                    $status = 'present';
                } elseif ($random <= 95) {
                    $status = 'absent';
                } else {
                    $status = 'late';
                }
                
                // Check if attendance already exists for this student and date
                $dateString = $currentDate->format('Y-m-d');
                $existing = Attendance::where('student_id', $student->id)
                    ->where('date', $dateString)
                    ->exists();
                
                if (!$existing) {
                    Attendance::create([
                        'student_id' => $student->id,
                        'date' => $currentDate->format('Y-m-d'),
                        'status' => $status,
                        'note' => $status === 'absent' ? fake()->optional(0.3)->sentence() : null,
                        'recorded_by' => $user->id,
                    ]);
                    
                    $attendanceCount++;
                }
                
                $currentDate->addDay();
            }
        }
        
        $this->command->info("Created {$attendanceCount} attendance records for the last 30 days.");
    }
}
