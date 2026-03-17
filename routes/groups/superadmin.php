<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UsersController;
use App\Http\Controllers\SuperAdmin\MedicalCenterController;
use App\Http\Controllers\SuperAdmin\MedicineController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\ReportController;
use App\Http\Controllers\Shared\ProfileController;

Route::middleware(['auth', RoleMiddleware::class . ':SuperAdmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('users', UsersController::class)->except(['show']);
        Route::resource('centers', MedicalCenterController::class)->except(['show']);
        Route::resource('medicines', MedicineController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->only(['index', 'update']);
        
        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile/personal', [ProfileController::class, 'updatePersonal'])->name('profile.update.personal');
        Route::put('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.update.security');
        
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');
    });
