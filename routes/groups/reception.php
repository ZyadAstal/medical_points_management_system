<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Reception\DashboardController;
use App\Http\Controllers\Reception\VisitController;
use App\Http\Controllers\Reception\PatientController;
use App\Http\Controllers\Shared\ProfileController;

Route::middleware(['auth', RoleMiddleware::class . ':Reception'])
    ->prefix('reception')
    ->name('reception.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // قائمة الانتظار اليوم
        Route::get('/waiting-list', [VisitController::class, 'waitingList'])->name('visits.waiting');

        // إجراءات الزيارة
        Route::post('/visits/{visit}/cancel', [VisitController::class, 'cancelVisit'])->name('visits.cancel');
        Route::post('/visits/{visit}/send-to-doctor', [VisitController::class, 'sendToDoctor'])->name('visits.sendToDoctor');

        // بحث مريض سابق (AJAX)
        Route::get('/patients/search-by-id', [PatientController::class, 'searchByNationalId'])->name('patients.searchById');

        // إرسال مريض سابق للطبيب
        Route::post('/patients/{patient}/send-to-doctor', [PatientController::class, 'sendToDoctor'])->name('patients.sendToDoctor');

        Route::resource('patients', PatientController::class)->except(['show', 'edit']);

        // Visit management (from patient profile)
        Route::get('/patients/{patient}/visits/create', [VisitController::class, 'create'])->name('visits.create');
        Route::post('/patients/{patient}/visits', [VisitController::class, 'store'])->name('visits.store');

        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile/personal', [ProfileController::class, 'updatePersonal'])->name('profile.update.personal');
        Route::put('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.update.security');
    });
