<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;

// auth
Route::post('/register', App\Http\Controllers\Api\RegisterController::class)->name('register');
Route::post('/login', App\Http\Controllers\Api\LoginController::class)->name('login');
Route::post('/logout', App\Http\Controllers\Api\LogoutController::class)->name('logout');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// User
Route::middleware(['auth:api', 'ensure-token-valid'])->group(function () {
    Route::get('/getuser/{id?}', [UserController::class, 'getUser']);
    Route::put('/updateuser/{id}', [UserController::class, 'updateUser']);
    Route::delete('/deleteuser/{id}', [UserController::class, 'deleteUser']);
});

// Attendance
Route::middleware(['auth:api', 'ensure-token-valid'])->group(function () {
    Route::post('/attendance', [AttendanceController::class, 'createAttendance']);
    Route::get('/attendance/history/{user_id}', [AttendanceController::class, 'getAttendanceHistory']);
    Route::get('/attendance/summary/{user_id}', [AttendanceController::class, 'getMonthlySummary']);
    Route::post('/attendance/analysis', [AttendanceController::class, 'analysis']);
});



