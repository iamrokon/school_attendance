<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentServiceTest extends TestCase
{
    use RefreshDatabase;

    private StudentService $studentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->studentService = new StudentService();
    }

    /**
     * Test creating a student.
     */
    public function test_can_create_student(): void
    {
        $data = [
            'name' => 'John Doe',
            'student_id' => 'STU001',
            'class' => '10',
            'section' => 'A',
        ];

        $student = $this->studentService->createStudent($data);

        $this->assertInstanceOf(Student::class, $student);
        $this->assertEquals('John Doe', $student->name);
        $this->assertEquals('STU001', $student->student_id);
        $this->assertDatabaseHas('students', [
            'student_id' => 'STU001',
            'name' => 'John Doe',
        ]);
    }

    /**
     * Test updating a student.
     */
    public function test_can_update_student(): void
    {
        $student = Student::factory()->create([
            'name' => 'John Doe',
            'student_id' => 'STU001',
        ]);

        $updatedData = [
            'name' => 'Jane Doe',
        ];

        $updatedStudent = $this->studentService->updateStudent($student, $updatedData);

        $this->assertEquals('Jane Doe', $updatedStudent->name);
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Jane Doe',
        ]);
    }

    /**
     * Test filtering students by class.
     */
    public function test_can_filter_students_by_class(): void
    {
        Student::factory()->create(['class' => '10', 'section' => 'A']);
        Student::factory()->create(['class' => '10', 'section' => 'B']);
        Student::factory()->create(['class' => '11', 'section' => 'A']);

        $students = $this->studentService->getStudents(['class' => '10'], 15);

        $this->assertEquals(2, $students->total());
        $students->each(function ($student) {
            $this->assertEquals('10', $student->class);
        });
    }
}
