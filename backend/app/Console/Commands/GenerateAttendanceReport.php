<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAttendanceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:generate-report {month : The month in Y-m format (e.g., 2024-01)} {class? : Optional class filter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly attendance report for a specific month and optionally filter by class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->argument('month');
        $class = $this->argument('class');
        
        // Validate month format
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $this->error('Invalid month format. Please use Y-m format (e.g., 2024-01)');
            return 1;
        }
        
        $this->info("Generating attendance report for {$month}" . ($class ? " (Class: {$class})" : ''));
        
        $attendanceService = app(\App\Services\V1\AttendanceService::class);
        $report = $attendanceService->getMonthlyReport($month, $class);
        
        if (empty($report)) {
            $this->warn('No attendance data found for the specified period.');
            return 0;
        }
        
        // Display report in table format
        $headers = ['Student ID', 'Name', 'Class', 'Section', 'Total Days', 'Present', 'Absent', 'Late', 'Attendance %'];
        $rows = array_map(function ($item) {
            return [
                $item['student_id'],
                $item['name'],
                $item['class'],
                $item['section'],
                $item['total_days'],
                $item['present_days'],
                $item['absent_days'],
                $item['late_days'],
                number_format($item['attendance_percentage'], 2) . '%',
            ];
        }, $report);
        
        $this->table($headers, $rows);
        
        $this->info("\nTotal students: " . count($report));
        
        return 0;
    }
}
