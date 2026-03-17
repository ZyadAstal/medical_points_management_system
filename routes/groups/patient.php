<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Patient\DashboardController;
use App\Http\Controllers\Patient\MedicineController;
use App\Http\Controllers\Patient\PrescriptionController;
use App\Http\Controllers\Patient\DispenseController;
use App\Http\Controllers\Shared\ProfileController;

Route::middleware(['auth', RoleMiddleware::class . ':Patient'])
    ->prefix('patient')
    ->name('patient.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/medicines/search', [MedicineController::class, 'search'])->name('medicines.search');
        Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::get('/dispense', [DispenseController::class, 'index'])->name('dispense.index');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    });
