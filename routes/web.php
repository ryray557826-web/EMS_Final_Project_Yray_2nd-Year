<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\KpiScorecardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalaryProfileController;
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

    // --- Attendance (Clock In/Out) ---
    // Defined explicitly to ensure the POST maps to the store() method
    Route::get('/attendance', [AttendanceLogController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/store', [AttendanceLogController::class, 'store'])->name('attendance.store');

    // --- Employee Directory ---
    // Accessible by Super Admin and Admin (logic handled in Controller)
    Route::resource('employees', EmployeeController::class);

    // --- Payroll Operations ---
    // Handles salaries, transaction entries, and calculations
    Route::resource('payroll', PayrollController::class);
Route::post('/profile/cashout', [App\Http\Controllers\ProfileController::class, 'cashout'])->name('profile.cashout');
    // --- Performance Tracking ---
    // Handles KPI evaluations and metrics monitoring
    Route::resource('kpi', KpiScorecardController::class);

    // --- Management & Setup ---
    // Resource routes for full CRUD (index, create, store, edit, update, destroy)
    Route::resource('branches', BranchController::class);
    Route::resource('positions', PositionController::class);

    // --- System Security ---
    Route::get('/audit-trails', [AuditTrailController::class, 'index'])->name('audit.index');
    Route::get('/payroll/{id}/manage', [PayrollController::class, 'manage'])->name('payroll.manage');
Route::patch('/payroll/{id}/update', [PayrollController::class, 'update'])->name('payroll.update');

Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
    Route::post('/payroll', [PayrollController::class, 'store'])->name('payroll.store');
    Route::get('/salary-profiles', [SalaryProfileController::class, 'index'])->name('salary-profiles.index');
    // Core descriptive management pages and finalization routes
    Route::get('/payroll/{id}/manage', [PayrollController::class, 'manage'])->name('payroll.manage');
    Route::patch('/payroll/{id}/update', [PayrollController::class, 'update'])->name('payroll.update');
    Route::get('/payroll/{id}/edit', [PayrollController::class, 'edit'])->name('payroll.edit');
});

require __DIR__.'/auth.php';