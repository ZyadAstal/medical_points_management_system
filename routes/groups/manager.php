<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\CenterManager\DashboardController;
use App\Http\Controllers\CenterManager\InventoryController;
use App\Http\Controllers\CenterManager\DispensingController;
use App\Http\Controllers\CenterManager\StaffController;
use App\Http\Controllers\CenterManager\PatientController;
use App\Http\Controllers\CenterManager\ReportController;
use App\Http\Controllers\Shared\ProfileController;

Route::middleware(['auth', RoleMiddleware::class . ':CenterManager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Inventory (Medicines)
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory', [InventoryController::class, 'update'])->name('inventory.update');

        // Dispensing History
        Route::get('/dispensing', [DispensingController::class, 'index'])->name('dispensing.index');

        // Staff Management
        Route::resource('staff', StaffController::class);

        // Patients
        Route::resource('patients', PatientController::class)->only(['index', 'show']);

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');

        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile/personal', [ProfileController::class, 'updatePersonal'])->name('profile.update.personal');
        Route::put('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.update.security');
    });
