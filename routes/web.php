<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AuditTrailController;
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

    // --- Attendance (Clock In/Out) ---
    // Defined explicitly to ensure the POST maps to the store() method
    Route::get('/attendance', [AttendanceLogController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/store', [AttendanceLogController::class, 'store'])->name('attendance.store');

    // --- Employee Directory ---
    // Accessible by Super Admin and Admin (logic handled in Controller)
    Route::resource('employees', EmployeeController::class);

    // --- Management & Setup ---
    // Resource routes for full CRUD (index, create, store, edit, update, destroy)
    Route::resource('branches', BranchController::class);
    Route::resource('positions', PositionController::class);

    // --- System Security ---
    Route::get('/audit-trails', [AuditTrailController::class, 'index'])->name('audit.index');
});

require __DIR__.'/auth.php';