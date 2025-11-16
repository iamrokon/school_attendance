<?php

namespace App\Services\V1;

use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Student Service for API v1.
 * Standalone copy of the previous StudentService logic.
 */
class StudentService
{
    /**
     * Get paginated list of students with optional filters.
     */
    public function getStudents(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        // Only select fields that are actually needed by the API responses
        $query = Student::query()
            ->select([
                'id',
                'name',
                'student_id',
                'class',
                'section',
            ]);

        if (isset($filters['class'])) {
            $query->where('class', $filters['class']);
        }

        if (isset($filters['section'])) {
            $query->where('section', $filters['section']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $connection = $q->getModel()->getConnection();

                if ($connection->getDriverName() === 'sqlite') {
                    // SQLite: fall back to LIKE search
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('student_id', 'like', "{$search}%");
                } else {
                    // MySQL (or drivers with FULLTEXT support)
                    $q->whereFullText('name', $search)
                      ->orWhere('student_id', 'like', "{$search}%");
                }
            });
        }

        // Build a cache key from filters + pagination query param
        $page = request()->get('page', 1);
        $cacheKey = 'students:list:' . md5(json_encode([$filters, 'per' => $perPage, 'page' => $page]));

        $cached = Cache::tags(['students'])->get($cacheKey);
        if ($cached) {
            // Rebuild LengthAwarePaginator from cached array
            return new LengthAwarePaginator(
                $cached['data'],
                $cached['total'],
                $cached['per_page'],
                $cached['current_page'],
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => 'page',
                ]
            );
        }

        $paginator = $query->orderBy('class')
                    ->orderBy('section')
                    ->orderBy('name')
                    ->paginate($perPage);

        // Cache paginated result for 60 minutes under the 'students' tag
        $paginatorArray = [
            'data' => $paginator->items(),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ];

        Cache::tags(['students'])->put($cacheKey, $paginatorArray, now()->addMinutes(60));

        return $paginator;
    }

    /**
     * Create a new student.
     */
    public function createStudent(array $data): Student
    {
        $student = Student::create($data);
        // Invalidate student caches (list and single entries)
        Cache::tags(['students'])->flush();
        return $student;
    }

    /**
     * Update a student.
     */
    public function updateStudent(Student $student, array $data): Student
    {
        $student->update($data);
        // Clear cache so next reads are fresh
        Cache::tags(['students'])->flush();
        return $student->fresh();
    }

    /**
     * Delete a student.
     */
    public function deleteStudent(Student $student): bool
    {
        $result = $student->delete();
        if ($result) {
            Cache::tags(['students'])->flush();
        }
        return $result;
    }
}
