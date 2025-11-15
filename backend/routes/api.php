<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication Routes
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');

// Student Management Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('students', \App\Http\Controllers\Api\StudentController::class);
    
    // Attendance Routes
    Route::prefix('attendances')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\AttendanceController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\AttendanceController::class, 'store']);
        Route::get('/{attendance}', [\App\Http\Controllers\Api\AttendanceController::class, 'show']);
        Route::get('/reports/monthly', [\App\Http\Controllers\Api\AttendanceController::class, 'monthlyReport']);
        Route::get('/statistics/today', [\App\Http\Controllers\Api\AttendanceController::class, 'todayStatistics']);
    });
});
