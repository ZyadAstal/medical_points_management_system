<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Auth\LoginController;

// SuperAdmin Controllers
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UsersController;
use App\Http\Controllers\SuperAdmin\MedicalCenterController;
use App\Http\Controllers\SuperAdmin\MedicineController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Custom Auth Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// --- Super Admin Routes ---
Route::middleware(['auth', RoleMiddleware::class . ':SuperAdmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('users', UsersController::class)->except(['show']);
    Route::resource('centers', MedicalCenterController::class)->except(['show']);
    Route::resource('medicines', MedicineController::class)->except(['show']);
    
    Route::get('/reports', [App\Http\Controllers\SuperAdmin\ReportController::class, 'index'])->name('reports.index');
});

// --- Doctor Routes ---
Route::middleware(['auth', RoleMiddleware::class . ':Doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Doctor\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/patients/search', [\App\Http\Controllers\Doctor\PatientController::class, 'search'])->name('patients.search');
    Route::resource('patients', \App\Http\Controllers\Doctor\PatientController::class)->only(['index', 'show']);
    
    // Visit Actions
    Route::post('/visits/{visit}/complete', function(\App\Models\Visit $visit) {
        $visit->update(['status' => 'completed']);
        return redirect()->route('doctor.patients.show', $visit->patient_id);
    })->name('visits.complete');
});

// --- Pharmacist Routes ---
Route::middleware(['auth', RoleMiddleware::class . ':Pharmacist'])->prefix('pharmacist')->name('pharmacist.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Pharmacist\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/prescriptions/search', [\App\Http\Controllers\Pharmacist\PrescriptionController::class, 'search'])->name('prescriptions.search');
    Route::resource('prescriptions', \App\Http\Controllers\Pharmacist\PrescriptionController::class)->only(['create', 'store', 'show']);
    
    Route::post('/dispense', [\App\Http\Controllers\Pharmacist\DispenseController::class, 'store'])->name('dispense.store');
});

// --- Center Manager Routes ---
Route::middleware(['auth', RoleMiddleware::class . ':CenterManager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\CenterManager\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/inventory', [\App\Http\Controllers\CenterManager\InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [\App\Http\Controllers\CenterManager\InventoryController::class, 'update'])->name('inventory.update');

    // Staff Management
    Route::resource('staff', \App\Http\Controllers\CenterManager\StaffController::class);

    // Reports
    Route::get('/reports', [\App\Http\Controllers\CenterManager\ReportController::class, 'index'])->name('reports.index');
});

// --- Reception Routes ---
Route::middleware(['auth', RoleMiddleware::class . ':Reception'])->prefix('reception')->name('reception.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Reception\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('patients', \App\Http\Controllers\Reception\PatientController::class);
    
    // Visit management
    Route::get('/patients/{patient}/visits/create', [\App\Http\Controllers\Reception\VisitController::class, 'create'])->name('visits.create');
    Route::post('/patients/{patient}/visits', [\App\Http\Controllers\Reception\VisitController::class, 'store'])->name('visits.store');
});

// --- Patient Routes ---
Route::middleware(['auth', RoleMiddleware::class . ':Patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Patient\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/medicines/search', [\App\Http\Controllers\Patient\MedicineController::class, 'search'])->name('medicines.search');
    Route::get('/prescriptions', [\App\Http\Controllers\Patient\PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/dispense', [\App\Http\Controllers\Patient\DispenseController::class, 'index'])->name('dispense.index');
    Route::get('/profile', [\App\Http\Controllers\Patient\ProfileController::class, 'show'])->name('profile.show');
});
