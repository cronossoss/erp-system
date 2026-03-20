<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\OrganizationalUnitController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    // ✅ DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ✅ ATTENDANCE
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])
        ->name('attendance.checkin');

    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])
        ->name('attendance.checkout');

    // ✅ EMPLOYEES
    Route::get('/employees/search', [EmployeeController::class, 'search']);
    Route::resource('employees', EmployeeController::class)->except(['show']);

    // ✅ ORGANIZATIONAL UNITS (OVO JE DOVOLJNO!)
    Route::resource('organizational-units', OrganizationalUnitController::class)
        ->except(['create', 'edit', 'show']);

    // ✅ PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
