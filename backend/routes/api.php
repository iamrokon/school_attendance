<?php

use App\Http\Controllers\Api\V1\AttendanceController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    // Authentication Routes (v1)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Student Management Routes (v1)
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('students', StudentController::class);

        // Attendance Routes (v1)
        Route::prefix('attendances')->group(function () {
            Route::get('/', [AttendanceController::class, 'index']);
            Route::post('/', [AttendanceController::class, 'store']);
            Route::get('/{attendance}', [AttendanceController::class, 'show']);
            Route::get('/reports/monthly', [AttendanceController::class, 'monthlyReport']);
            Route::get('/statistics/today', [AttendanceController::class, 'todayStatistics']);
        });
    });
});
