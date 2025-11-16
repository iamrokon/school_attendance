<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\AttendanceRecorded;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkAttendanceRequest;
use App\Http\Resources\V1\AttendanceResource;
use App\Models\Attendance;
use App\Services\V1\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Attendance Controller for API v1.
 * Standalone copy of the previous AttendanceController logic using V1 services/resources.
 */
class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['date', 'student_id', 'status']);
        $perPage = $request->get('per_page', 15);

        $attendances = $this->attendanceService->getAttendances($filters, $perPage);

        return AttendanceResource::collection($attendances);
    }

    /**
     * Store bulk attendance records.
     */
    public function store(BulkAttendanceRequest $request): JsonResponse
    {
        $attendances = $this->attendanceService->recordBulkAttendance(
            $request->validated(),
            auth()->id()
        );

        // Dispatch event for each attendance record
        foreach ($attendances as $attendance) {
            AttendanceRecorded::dispatch($attendance);
            // event(new AttendanceRecorded($attendance));
        }

        return response()->json([
            'message' => 'Attendance recorded successfully',
            'data' => AttendanceResource::collection($attendances),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance): JsonResponse
    {
        $attendance->load(['student', 'recorder']);

        return response()->json([
            'data' => AttendanceResource::make($attendance),
        ]);
    }

    /**
     * Get monthly attendance report.
     */
    public function monthlyReport(Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'class' => 'nullable|string',
        ]);

        $report = $this->attendanceService->getMonthlyReport(
            $request->month,
            $request->class
        );

        return response()->json([
            'month' => $request->month,
            'class' => $request->class,
            'data' => $report,
        ]);
    }

    /**
     * Get today's attendance statistics.
     */
    public function todayStatistics(): JsonResponse
    {
        $statistics = $this->attendanceService->getTodayStatistics();

        return response()->json([
            'data' => $statistics,
        ]);
    }
}

