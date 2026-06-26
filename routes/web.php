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
    Route::post('/jadwal-kelas/preferences', [ProgramController::class, 'storeSchedulePreferences'])->name('student.schedule.preferences.store');
    Route::get('/placement-test', [PlacementTestController::class, 'index'])->name('placement-test');
    Route::post('/placement-test', [PlacementTestController::class, 'store'])->name('placement-test.store');

    Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('/programs/change', [ProgramController::class, 'change'])->name('programs.change');
    Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');
    Route::post('/programs/renew', [ProgramController::class, 'renew'])->name('programs.renew');
    Route::get('/programs/payment', [ProgramController::class, 'payment'])->name('programs.payment');
    Route::post('/programs/payment', [ProgramController::class, 'uploadPayment'])->name('programs.payment.store');
    Route::get('/programs/payment/success', [ProgramController::class, 'paymentSuccess'])->name('programs.payment.success');
    Route::get('/programs/payment/invoice', [ProgramController::class, 'invoice'])->name('programs.invoice');
    Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('programs.show');
    Route::post('/programs/{program}/join', [ProgramController::class, 'join'])->name('programs.join');

    Route::middleware('admin')->group(function () {
        Route::redirect('/admin', '/admin/dashboard')->name('admin.index');
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/registrants', [AdminController::class, 'registrants'])->name('admin.registrants.index');
        Route::get('/admin/registrants/{user}/edit', [AdminController::class, 'editRegistrant'])->name('admin.registrants.edit');
        Route::put('/admin/registrants/{user}', [AdminController::class, 'updateRegistrant'])->name('admin.registrants.update');
        Route::patch('/admin/registrants/{user}/cancel', [AdminController::class, 'cancelRegistrant'])->name('admin.registrants.cancel');
        Route::get('/admin/payments', [AdminController::class, 'payments'])->name('admin.payments.index');
        Route::patch('/admin/payments/{user}', [AdminController::class, 'updatePayment'])->name('admin.payments.update');
        Route::get('/admin/placement/results', [AdminController::class, 'placementResults'])->name('admin.placement.results');
        Route::delete('/admin/placement/results/{user}/reset', [AdminController::class, 'resetPlacementTest'])->name('admin.placement.results.reset');
        Route::get('/admin/schedules', [AdminController::class, 'schedules'])->name('admin.schedules.index');
        Route::get('/admin/schedules/create', [AdminController::class, 'createSchedule'])->name('admin.schedules.create');
        Route::post('/admin/schedules', [AdminController::class, 'storeSchedule'])->name('admin.schedules.store');
        Route::get('/admin/schedules/{schedule}/edit', [AdminController::class, 'editSchedule'])->name('admin.schedules.edit');
        Route::put('/admin/schedules/{schedule}', [AdminController::class, 'updateSchedule'])->name('admin.schedules.update');
        Route::delete('/admin/schedules/{schedule}', [AdminController::class, 'destroySchedule'])->name('admin.schedules.destroy');
        Route::get('/admin/class-rooms', [AdminController::class, 'classRooms'])->name('admin.class-rooms.index');
        Route::get('/admin/class-rooms/create', [AdminController::class, 'createClassRoom'])->name('admin.class-rooms.create');
        Route::post('/admin/class-rooms', [AdminController::class, 'storeClassRoom'])->name('admin.class-rooms.store');
        Route::get('/admin/class-rooms/{room}', [AdminController::class, 'showClassRoom'])->name('admin.class-rooms.show');
        Route::get('/admin/class-rooms/{room}/edit', [AdminController::class, 'editClassRoom'])->name('admin.class-rooms.edit');
        Route::put('/admin/class-rooms/{room}', [AdminController::class, 'updateClassRoom'])->name('admin.class-rooms.update');
        Route::delete('/admin/class-rooms/{room}', [AdminController::class, 'destroyClassRoom'])->name('admin.class-rooms.destroy');
        Route::get('/admin/schedule-templates', [AdminController::class, 'scheduleTemplates'])->name('admin.schedule-templates.index');
        Route::get('/admin/schedule-templates/create', [AdminController::class, 'createScheduleTemplate'])->name('admin.schedule-templates.create');
        Route::post('/admin/schedule-templates', [AdminController::class, 'storeScheduleTemplate'])->name('admin.schedule-templates.store');
        Route::get('/admin/schedule-templates/{scheduleTemplate}/edit', [AdminController::class, 'editScheduleTemplate'])->name('admin.schedule-templates.edit');
        Route::put('/admin/schedule-templates/{scheduleTemplate}', [AdminController::class, 'updateScheduleTemplate'])->name('admin.schedule-templates.update');
        Route::delete('/admin/schedule-templates/{scheduleTemplate}', [AdminController::class, 'destroyScheduleTemplate'])->name('admin.schedule-templates.destroy');
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
});
