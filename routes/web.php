<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\OrganizationalUnitController;
use App\Http\Controllers\OrganizationalGroupController;
use App\Http\Controllers\ContractTypeController;
use App\Http\Controllers\WorkEntryController;
use App\Http\Controllers\WorkEntryTypeController;



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
    Route::post('/work-entries', [WorkEntryController::class, 'store'])->name('work-entries.store');
    Route::get('/work-entry-types', fn() => \App\Models\WorkEntryType::all());
    Route::get('/employees/{id}/work-entries', function ($id) {

        $query = \App\Models\WorkEntry::with('type')
            ->where('employee_id', $id);

        if (request('from') && request('to')) {
            $query->whereBetween('date', [request('from'), request('to')]);
        }
    Route::resource('work-entry-types', WorkEntryTypeController::class)
    ->middleware('auth');
        return $query->orderByDesc('date')->get();
    });

    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])
        ->name('attendance.checkout');
    Route::middleware(['auth'])->group(function () {
        Route::resource('work-entry-types', WorkEntryTypeController::class);
    });

    // ✅ EMPLOYEES
    Route::get('/employees/search', [EmployeeController::class, 'search']);
    Route::resource('employees', EmployeeController::class);
    Route::get('/employees/by-unit/{id}', [EmployeeController::class, 'byUnit']);
    Route::get('/employees/{id}/json', [EmployeeController::class, 'showJson']);
    Route::get('/employees/{id}', [EmployeeController::class, 'show']);

    // ✅ ORGANIZATIONAL UNITS (OVO JE DOVOLJNO!)
    Route::resource('organizational-units', OrganizationalUnitController::class);
    Route::resource('organizational-groups', OrganizationalGroupController::class);
    Route::get('/organizacija', [OrganizationalUnitController::class, 'overview'])
        ->name('organizacija.overview');
    Route::resource('contract-types', ContractTypeController::class);


    // ✅ PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
