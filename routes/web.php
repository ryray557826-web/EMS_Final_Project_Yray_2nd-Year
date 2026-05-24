<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\KpiScorecardController;
use App\Http\Controllers\SalaryProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('welcome');
});

// Unified Dashboard
Route::get('/dashboard', [AttendanceLogController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    
    // --- Profile Management ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/cashout', [ProfileController::class, 'cashout'])->name('profile.cashout');

    // --- Attendance (Clock In/Out) ---
    Route::get('/attendance', [AttendanceLogController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/store', [AttendanceLogController::class, 'store'])->name('attendance.store');

    // --- Employee Directory & Verification ---
    Route::patch('/employees/{employee}/verify-email', [EmployeeController::class, 'verifyEmail'])->name('employees.verify-email');
    Route::resource('employees', EmployeeController::class);

    // --- Payroll Operations ---
    Route::resource('payroll', PayrollController::class);
    Route::get('/payroll/{id}/manage', [PayrollController::class, 'manage'])->name('payroll.manage');
    Route::patch('/payroll/{id}/update', [PayrollController::class, 'update'])->name('payroll.update');
    Route::get('/salary-profiles', [SalaryProfileController::class, 'index'])->name('salary-profiles.index');

    // --- Performance Tracking ---
    Route::resource('kpi', KpiScorecardController::class);

    // --- Management & Setup ---
    Route::resource('branches', BranchController::class);
    Route::post('/branches/{id}/toggle', [BranchController::class, 'toggle'])->name('branches.toggle');
    
    Route::resource('positions', PositionController::class);
    Route::post('/positions/{id}/toggle', [PositionController::class, 'toggle'])->name('positions.toggle');
    
    // --- System Security ---
    Route::get('/audit-trails', [AuditTrailController::class, 'index'])->name('audit.index');
});

require __DIR__.'/auth.php';