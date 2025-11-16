<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\V1\StudentResource;
use App\Models\Student;
use App\Services\V1\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Student Controller for API v1.
 * Standalone copy of the previous StudentController logic using V1 services/resources.
 */
class StudentController extends Controller
{
    public function __construct(
        private StudentService $studentService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['class', 'section', 'search']);
        $perPage = $request->get('per_page', 15);

        $students = $this->studentService->getStudents($filters, $perPage);

        return StudentResource::collection($students);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = $this->studentService->createStudent($request->validated());

        return response()->json([
            'message' => 'Student created successfully',
            'data' => StudentResource::make($student),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): JsonResponse
    {
        return response()->json([
            'data' => StudentResource::make($student),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        $student = $this->studentService->updateStudent($student, $request->validated());

        return response()->json([
            'message' => 'Student updated successfully',
            'data' => StudentResource::make($student),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): JsonResponse
    {
        $this->studentService->deleteStudent($student);

        return response()->json([
            'message' => 'Student deleted successfully',
        ]);
    }
}

