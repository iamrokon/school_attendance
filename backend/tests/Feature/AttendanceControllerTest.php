<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /**
     * Test bulk attendance recording endpoint.
     */
    public function test_can_record_bulk_attendance(): void
    {
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();

        $response = $this->postJson('/api/attendances', [
            'date' => Carbon::today()->format('Y-m-d'),
            'attendances' => [
                [
                    'student_id' => $student1->id,
                    'status' => 'present',
                ],
                [
                    'student_id' => $student2->id,
                    'status' => 'absent',
                ],
            ],
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         '*' => ['id', 'student_id', 'date', 'status'],
                     ],
                 ]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student1->id,
            'status' => 'present',
        ]);
    }

    /**
     * Test getting today's statistics.
     */
    public function test_can_get_today_statistics(): void
    {
        $student = Student::factory()->create();
        
        Attendance::factory()->create([
            'student_id' => $student->id,
            'date' => Carbon::today(),
            'status' => 'present',
            'recorded_by' => $this->user->id,
        ]);

        $response = $this->getJson('/api/attendances/statistics/today');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'date',
                         'total_students',
                         'present',
                         'absent',
                         'late',
                         'attendance_percentage',
                     ],
                 ]);
    }

    /**
     * Test monthly report endpoint.
     */
    public function test_can_get_monthly_report(): void
    {
        $student = Student::factory()->create(['class' => '10']);
        
        Attendance::factory()->create([
            'student_id' => $student->id,
            'date' => Carbon::parse('2024-01-15'),
            'status' => 'present',
            'recorded_by' => $this->user->id,
        ]);

        $response = $this->getJson('/api/attendances/reports/monthly?month=2024-01');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'month',
                     'data' => [
                         '*' => [
                             'student_id',
                             'name',
                             'class',
                             'total_days',
                             'present_days',
                             'attendance_percentage',
                         ],
                     ],
                 ]);
    }
}
