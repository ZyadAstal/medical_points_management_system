<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Doctor\DashboardController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\PrescriptionController;
use App\Http\Controllers\Doctor\ProfileController;

Route::middleware(['auth', RoleMiddleware::class . ':Doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Today's patients
        Route::get('/today-patients', [PatientController::class, 'index'])->name('patients.index');
        
        // Search patients
        Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
        
        // Prescriptions / Recipes Record
        Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::get('/prescriptions/{patient}', [PrescriptionController::class, 'show'])->name('prescriptions.show');
        
        // Doctor Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile/personal', [ProfileController::class, 'updatePersonal'])->name('profile.update.personal');
        Route::put('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.update.security');
        
        // Visit Actions
        Route::post('/visits/{visit}/enter', [PatientController::class, 'enter'])->name('visits.enter');
        Route::post('/visits/{visit}/complete', [PatientController::class, 'complete'])->name('visits.complete');
    });
