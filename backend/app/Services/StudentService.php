<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentService
{
    /**
     * Get paginated list of students with optional filters.
     */
    public function getStudents(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Student::query();

        if (isset($filters['class'])) {
            $query->where('class', $filters['class']);
        }

        if (isset($filters['section'])) {
            $query->where('section', $filters['section']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('class')
                    ->orderBy('section')
                    ->orderBy('name')
                    ->paginate($perPage);
    }

    /**
     * Create a new student.
     */
    public function createStudent(array $data): Student
    {
        return Student::create($data);
    }

    /**
     * Update a student.
     */
    public function updateStudent(Student $student, array $data): Student
    {
        $student->update($data);
        return $student->fresh();
    }

    /**
     * Delete a student.
     */
    public function deleteStudent(Student $student): bool
    {
        return $student->delete();
    }
}

