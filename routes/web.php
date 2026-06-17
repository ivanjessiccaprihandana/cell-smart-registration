<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PlacementTestController;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Step 2: Register (Complete data)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/cek-kuota', [ProgramController::class, 'quota'])->name('programs.quota');

// Program Routes - Separate from Authentication
Route::middleware('auth')->group(function () {
    Route::get('/status-saya', [ProgramController::class, 'studentStatus'])->name('student.status');
    Route::get('/jadwal-kelas', [ProgramController::class, 'studentSchedule'])->name('student.schedule');
    Route::get('/placement-test', [PlacementTestController::class, 'index'])->name('placement-test');
    Route::post('/placement-test', [PlacementTestController::class, 'store'])->name('placement-test.store');

    Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');
    Route::post('/programs/renew', [ProgramController::class, 'renew'])->name('programs.renew');
    Route::get('/programs/payment', [ProgramController::class, 'payment'])->name('programs.payment');
    Route::post('/programs/payment', [ProgramController::class, 'uploadPayment'])->name('programs.payment.store');
    Route::get('/programs/payment/success', [ProgramController::class, 'paymentSuccess'])->name('programs.payment.success');
    Route::get('/programs/payment/invoice', [ProgramController::class, 'invoice'])->name('programs.invoice');
    Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('programs.show');
    Route::post('/programs/{program}/join', [ProgramController::class, 'join'])->name('programs.join');

    Route::redirect('/admin', '/admin/dashboard')->name('admin.index');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/registrants', [AdminController::class, 'registrants'])->name('admin.registrants.index');
    Route::get('/admin/payments', [AdminController::class, 'payments'])->name('admin.payments.index');
    Route::patch('/admin/payments/{user}', [AdminController::class, 'updatePayment'])->name('admin.payments.update');
    Route::get('/admin/placement/results', [AdminController::class, 'placementResults'])->name('admin.placement.results');
    Route::get('/admin/schedules', [AdminController::class, 'schedules'])->name('admin.schedules.index');
    Route::get('/admin/schedules/create', [AdminController::class, 'createSchedule'])->name('admin.schedules.create');
    Route::post('/admin/schedules', [AdminController::class, 'storeSchedule'])->name('admin.schedules.store');
    Route::get('/admin/schedules/{schedule}/edit', [AdminController::class, 'editSchedule'])->name('admin.schedules.edit');
    Route::put('/admin/schedules/{schedule}', [AdminController::class, 'updateSchedule'])->name('admin.schedules.update');
    Route::delete('/admin/schedules/{schedule}', [AdminController::class, 'destroySchedule'])->name('admin.schedules.destroy');
    Route::get('/admin/placement/questions', [AdminController::class, 'placementQuestions'])->name('admin.placement.questions.index');
    Route::get('/admin/placement/questions/create', [AdminController::class, 'createPlacementQuestion'])->name('admin.placement.questions.create');
    Route::post('/admin/placement/questions', [AdminController::class, 'storePlacementQuestion'])->name('admin.placement.questions.store');
    Route::get('/admin/placement/questions/{question}/edit', [AdminController::class, 'editPlacementQuestion'])->name('admin.placement.questions.edit');
    Route::put('/admin/placement/questions/{question}', [AdminController::class, 'updatePlacementQuestion'])->name('admin.placement.questions.update');
    Route::delete('/admin/placement/questions/{question}', [AdminController::class, 'destroyPlacementQuestion'])->name('admin.placement.questions.destroy');
    Route::get('/admin/programs', [AdminController::class, 'programs'])->name('admin.programs.index');
    Route::get('/admin/programs/create', [AdminController::class, 'createProgram'])->name('admin.programs.create');
    Route::post('/admin/programs', [AdminController::class, 'storeProgram'])->name('admin.programs.store');
    Route::get('/admin/programs/{program}/edit', [AdminController::class, 'editProgram'])->name('admin.programs.edit');
    Route::put('/admin/programs/{program}', [AdminController::class, 'updateProgram'])->name('admin.programs.update');
    Route::delete('/admin/programs/{program}', [AdminController::class, 'destroyProgram'])->name('admin.programs.destroy');
    Route::get('/admin/tutors', [AdminController::class, 'tutors'])->name('admin.tutors.index');
    Route::get('/admin/tutors/create', [AdminController::class, 'createTutor'])->name('admin.tutors.create');
    Route::post('/admin/tutors', [AdminController::class, 'storeTutor'])->name('admin.tutors.store');
    Route::get('/admin/tutors/{tutor}/edit', [AdminController::class, 'editTutor'])->name('admin.tutors.edit');
    Route::put('/admin/tutors/{tutor}', [AdminController::class, 'updateTutor'])->name('admin.tutors.update');
    Route::delete('/admin/tutors/{tutor}', [AdminController::class, 'destroyTutor'])->name('admin.tutors.destroy');
    Route::get('/admin/program-categories', [AdminController::class, 'programCategoriesIndex'])->name('admin.program-categories.index');
    Route::get('/admin/program-categories/create', [AdminController::class, 'createProgramCategory'])->name('admin.program-categories.create');
    Route::post('/admin/program-categories', [AdminController::class, 'storeProgramCategory'])->name('admin.program-categories.store');
    Route::get('/admin/program-categories/{category}/edit', [AdminController::class, 'editProgramCategory'])->name('admin.program-categories.edit');
    Route::put('/admin/program-categories/{category}', [AdminController::class, 'updateProgramCategory'])->name('admin.program-categories.update');
    Route::delete('/admin/program-categories/{category}', [AdminController::class, 'destroyProgramCategory'])->name('admin.program-categories.destroy');
});
