<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Pharmacist\DashboardController;
use App\Http\Controllers\Pharmacist\PrescriptionController;
use App\Http\Controllers\Pharmacist\DispenseController;
use App\Http\Controllers\Pharmacist\InventoryController;
use App\Http\Controllers\Shared\ProfileController;

Route::middleware(['auth', RoleMiddleware::class . ':Pharmacist'])
    ->prefix('pharmacist')
    ->name('pharmacist.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/prescriptions/search-patient', [PrescriptionController::class, 'search'])->name('prescriptions.search');
        Route::resource('prescriptions', PrescriptionController::class)->only(['index', 'create', 'store', 'show']);
        
        Route::post('/dispense', [DispenseController::class, 'store'])->name('dispense.store');
        Route::post('/dispense/manual', [DispenseController::class, 'manualStore'])->name('dispense.manual');
        
        // New Routes
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/dispensing', [DispenseController::class, 'history'])->name('dispensing.history');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    });
