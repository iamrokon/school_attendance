<?php

namespace Tests\Unit;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use App\Services\V1\AttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private AttendanceService $attendanceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendanceService = new AttendanceService();
    }

    /**
     * Test recording bulk attendance.
     */
    public function test_can_record_bulk_attendance(): void
    {
        $user = User::factory()->create();
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();
        $date = Carbon::today();

        $data = [
            'date' => $date,
            'attendances' => [
                [
                    'student_id' => $student1->id,
                    'status' => 'present',
                    'note' => 'On time',
                ],
                [
                    'student_id' => $student2->id,
                    'status' => 'absent',
                    'note' => 'Sick',
                ],
            ],
        ];

        $attendances = $this->attendanceService->recordBulkAttendance($data, $user->id);

        $this->assertCount(2, $attendances);
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student1->id,
            'status' => 'present',
            'date' => $date,
        ]);
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student2->id,
            'status' => 'absent',
            'date' => $date,
        ]);
    }

    /**
     * Test getting today's statistics.
     */
    public function test_can_get_today_statistics(): void
    {
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();
        $user = User::factory()->create();

        Attendance::factory()->create([
            'student_id' => $student1->id,
            'date' => Carbon::today(),
            'status' => 'present',
            'recorded_by' => $user->id,
        ]);

        Attendance::factory()->create([
            'student_id' => $student2->id,
            'date' => Carbon::today(),
            'status' => 'absent',
            'recorded_by' => $user->id,
        ]);

        $statistics = $this->attendanceService->getTodayStatistics();

        $this->assertEquals(2, $statistics['total_students']);
        $this->assertEquals(1, $statistics['present']);
        $this->assertEquals(1, $statistics['absent']);
        $this->assertEquals(50.0, $statistics['attendance_percentage']);
    }

    /**
     * Test monthly report generation.
     */
    public function test_can_generate_monthly_report(): void
    {
        $student = Student::factory()->create(['class' => '10']);
        $user = User::factory()->create();
        $month = '2024-01';

        // Create attendance records for January 2024
        Attendance::factory()->create([
            'student_id' => $student->id,
            'date' => Carbon::parse('2024-01-15'),
            'status' => 'present',
            'recorded_by' => $user->id,
        ]);

        Attendance::factory()->create([
            'student_id' => $student->id,
            'date' => Carbon::parse('2024-01-16'),
            'status' => 'present',
            'recorded_by' => $user->id,
        ]);

        $report = $this->attendanceService->getMonthlyReport($month);

        $this->assertNotEmpty($report);
        $this->assertEquals($student->student_id, $report[0]['student_id']);
        $this->assertGreaterThan(0, $report[0]['present_days']);
    }
}
