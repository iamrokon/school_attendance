<?php

namespace App\Services\V1;

use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Attendance Service for API v1.
 * Standalone copy of the previous AttendanceService logic.
 */
class AttendanceService
{
    /**
     * Get paginated list of attendances with optional filters.
     */
    public function getAttendances(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Attendance::with([
            'student:id,name,student_id,class,section,photo',
            'recorder:id,name',
        ]);

        if (!empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Record bulk attendance for multiple students.
     */
    public function recordBulkAttendance(array $data, int $recordedBy): Collection
    {
        $date = isset($data['date']) ? Carbon::parse($data['date']) : Carbon::today();
        $attendances = collect();

        DB::transaction(function () use ($data, $date, $recordedBy, &$attendances) {
            foreach ($data['attendances'] as $attendanceData) {
                $attendance = Attendance::updateOrCreate(
                    [
                        'student_id' => $attendanceData['student_id'],
                        'date' => $date,
                    ],
                    [
                        'status' => $attendanceData['status'],
                        'note' => $attendanceData['note'] ?? null,
                        'recorded_by' => $recordedBy,
                    ]
                );

                $attendances->push($attendance);
            }

            // Clear cache for this date
            $this->clearAttendanceCache($date);
        });

        return $attendances;
    }

    /**
     * Get monthly attendance report with eager loading.
     */
    public function getMonthlyReport(string $month, ?string $class = null): array
    {
        $cacheKey = "attendance_report_{$month}_" . ($class ?? 'all');

        return Cache::remember($cacheKey, 3600, function () use ($month, $class) {
            $startDate = Carbon::parse($month)->startOfMonth();
            $endDate = Carbon::parse($month)->endOfMonth();

            $query = Student::select([
                    'id',
                    'student_id',
                    'name',
                    'class',
                    'section',
                ])
                ->with(['attendances' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
                }]);

            if ($class) {
                $query->where('class', $class);
            }

            $students = $query->get();

            $report = [];
            foreach ($students as $student) {
                $totalDays = $startDate->diffInDays($endDate) + 1;
                $presentDays = $student->attendances->where('status', 'present')->count();
                $absentDays = $student->attendances->where('status', 'absent')->count();
                $lateDays = $student->attendances->where('status', 'late')->count();
                $attendancePercentage = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;

                $report[] = [
                    'student_id' => $student->student_id,
                    'name' => $student->name,
                    'class' => $student->class,
                    'section' => $student->section,
                    'total_days' => $totalDays,
                    'present_days' => $presentDays,
                    'absent_days' => $absentDays,
                    'late_days' => $lateDays,
                    'attendance_percentage' => round($attendancePercentage, 2),
                ];
            }

            return $report;
        });
    }

    /**
     * Get today's attendance statistics.
     */
    public function getTodayStatistics(): array
    {
        $today = Carbon::today();
        $cacheKey = "attendance_stats_{$today->format('Y-m-d')}";

        return Cache::remember($cacheKey, 300, function () use ($today) {
            $totalStudents = Student::count();
            $presentCount = Attendance::where('date', $today)
                ->where('status', 'present')
                ->count();
            $absentCount = Attendance::where('date', $today)
                ->where('status', 'absent')
                ->count();
            $lateCount = Attendance::where('date', $today)
                ->where('status', 'late')
                ->count();
            $recordedCount = Attendance::where('date', $today)->count();

            return [
                'date' => $today->format('Y-m-d'),
                'total_students' => $totalStudents,
                'present' => $presentCount,
                'absent' => $absentCount,
                'late' => $lateCount,
                'recorded' => $recordedCount,
                'not_recorded' => $totalStudents - $recordedCount,
                'attendance_percentage' => $totalStudents > 0
                    ? round(($presentCount / $totalStudents) * 100, 2)
                    : 0,
            ];
        });
    }

    /**
     * Get attendance statistics for a specific date range.
     */
    public function getStatisticsByDateRange(Carbon $startDate, Carbon $endDate): array
    {
        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'present' => $attendances['present'] ?? 0,
            'absent' => $attendances['absent'] ?? 0,
            'late' => $attendances['late'] ?? 0,
            'total' => array_sum($attendances),
        ];
    }

    /**
     * Clear attendance cache for a specific date.
     */
    private function clearAttendanceCache(Carbon $date): void
    {
        $dateString = $date->format('Y-m-d');
        Cache::forget("attendance_stats_{$dateString}");

        // Clear monthly report caches
        $monthKey = $date->format('Y-m');
        Cache::forget("attendance_report_{$monthKey}_all");
        Cache::forget("attendance_report_{$monthKey}_" . $date->format('Y-m'));
    }
}
