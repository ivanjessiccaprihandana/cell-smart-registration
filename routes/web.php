<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProgramController;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Step 2: Register (Complete data)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Program Routes - Separate from Authentication
Route::prefix('programs')->group(function () {
    // Public routes
    Route::get('/', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('/{program}', [ProgramController::class, 'show'])->name('programs.show');
    Route::get('/{program}/participants', [ProgramController::class, 'participants'])->name('programs.participants');
    
    // Protected routes
    Route::middleware('auth')->group(function () {
        Route::post('/{program}/join', [ProgramController::class, 'join'])->name('programs.join');
        Route::post('/{program}/leave', [ProgramController::class, 'leave'])->name('programs.leave');
        Route::get('/my-programs', [ProgramController::class, 'myPrograms'])->name('programs.my');
    });
    
    // Admin routes
    Route::middleware('auth', 'admin')->group(function () {
        Route::put('/{enrollment}/approve', [ProgramController::class, 'approveEnrollment'])->name('enrollments.approve');
        Route::delete('/{enrollment}/reject', [ProgramController::class, 'rejectEnrollment'])->name('enrollments.reject');
    });
});