<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_requires_authentication_for_student_routes(): void
    {
        $response = $this->getJson('/api/v1/students');

        $response->assertStatus(401);
    }

    public function test_can_list_students_with_filters(): void
    {
        Sanctum::actingAs($this->user);

        Student::factory()->create([
            'name' => 'John Doe',
            'student_id' => 'STU001',
            'class' => '10',
            'section' => 'A',
        ]);

        Student::factory()->create([
            'name' => 'Jane Smith',
            'student_id' => 'STU002',
            'class' => '10',
            'section' => 'B',
        ]);

        $response = $this->getJson('/api/v1/students?class=10&section=A&search=STU001');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'student_id',
                             'class',
                             'section',
                             'photo',
                             'created_at',
                             'updated_at',
                         ],
                     ],
                 ])
                 ->assertJsonFragment(['name' => 'John Doe'])
                 ->assertJsonMissing(['name' => 'Jane Smith']);
    }

    public function test_can_store_student(): void
    {
        Sanctum::actingAs($this->user);

        $payload = [
            'name' => 'John Doe',
            'student_id' => 'STU100',
            'class' => '10',
            'section' => 'A',
            'photo' => null,
        ];

        $response = $this->postJson('/api/v1/students', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'student_id',
                         'class',
                         'section',
                     ],
                 ]);

        $this->assertDatabaseHas('students', [
            'student_id' => 'STU100',
            'name' => 'John Doe',
        ]);
    }

    public function test_store_student_validates_input(): void
    {
        Sanctum::actingAs($this->user);

        $payload = [
            // 'name' is missing
            'student_id' => 'STU101',
            'class' => '10',
            'section' => 'A',
        ];

        $response = $this->postJson('/api/v1/students', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    public function test_can_show_student(): void
    {
        Sanctum::actingAs($this->user);

        $student = Student::factory()->create();

        $response = $this->getJson('/api/v1/students/' . $student->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'name',
                         'student_id',
                         'class',
                         'section',
                     ],
                 ])
                 ->assertJsonFragment(['id' => $student->id]);
    }

    public function test_can_update_student(): void
    {
        Sanctum::actingAs($this->user);

        $student = Student::factory()->create([
            'name' => 'Old Name',
            'student_id' => 'STU200',
        ]);

        $payload = [
            'name' => 'New Name',
        ];

        $response = $this->putJson('/api/v1/students/' . $student->id, $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'New Name',
        ]);
    }

    public function test_can_delete_student(): void
    {
        Sanctum::actingAs($this->user);

        $student = Student::factory()->create();

        $response = $this->deleteJson('/api/v1/students/' . $student->id);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
        ]);
    }
}


