<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Shared\ProfileController;

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

// Password Reset Routes
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// --- Shared Authenticated Routes ---
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/personal', [ProfileController::class, 'updatePersonal'])->name('profile.update.personal');
    Route::put('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.update.security');
});

// --- Super Admin Routes ---
Route::middleware(['auth', RoleMiddleware::class . ':SuperAdmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('users', UsersController::class)->except(['show']);
    Route::resource('centers', MedicalCenterController::class)->except(['show']);
    Route::resource('medicines', MedicineController::class)->except(['show']);
    Route::resource('roles', App\Http\Controllers\SuperAdmin\RoleController::class)->only(['index', 'update']);
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile/personal', [ProfileController::class, 'updatePersonal'])->name('profile.update.personal');
    Route::put('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.update.security');
    
    Route::get('/reports', [App\Http\Controllers\SuperAdmin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [App\Http\Controllers\SuperAdmin\ReportController::class, 'downloadPdf'])->name('reports.pdf');
});

// --- Doctor Routes ---
Route::middleware(['auth', RoleMiddleware::class . ':Doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Doctor\DashboardController::class, 'index'])->name('dashboard');
    
    // Today's patients
    Route::get('/today-patients', [\App\Http\Controllers\Doctor\PatientController::class, 'index'])->name('patients.index');
    
    // Search patients
    Route::get('/patients/search', [\App\Http\Controllers\Doctor\PatientController::class, 'search'])->name('patients.search');
    Route::get('/patients/{patient}', [\App\Http\Controllers\Doctor\PatientController::class, 'show'])->name('patients.show');
    
    // Prescriptions / Recipes Record
    Route::get('/prescriptions', [\App\Http\Controllers\Doctor\PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/prescriptions/{patient}', [\App\Http\Controllers\Doctor\PrescriptionController::class, 'show'])->name('prescriptions.show');
    
    // Doctor Profile
    Route::get('/profile', [\App\Http\Controllers\Doctor\ProfileController::class, 'show'])->name('profile');
    Route::put('/profile/personal', [\App\Http\Controllers\Doctor\ProfileController::class, 'updatePersonal'])->name('profile.update.personal');
    Route::put('/profile/security', [\App\Http\Controllers\Doctor\ProfileController::class, 'updateSecurity'])->name('profile.update.security');
    
    // Visit Actions
    Route::post('/visits/{visit}/enter', [\App\Http\Controllers\Doctor\PatientController::class, 'enter'])->name('visits.enter');
    Route::post('/visits/{visit}/complete', [\App\Http\Controllers\Doctor\PatientController::class, 'complete'])->name('visits.complete');
});

// --- Pharmacist Routes ---
Route::middleware(['auth', RoleMiddleware::class . ':Pharmacist'])->prefix('pharmacist')->name('pharmacist.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Pharmacist\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/prescriptions/search', [\App\Http\Controllers\Pharmacist\PrescriptionController::class, 'search'])->name('prescriptions.search');
    Route::resource('prescriptions', \App\Http\Controllers\Pharmacist\PrescriptionController::class)->only(['index', 'create', 'store', 'show']);
    
    Route::post('/dispense', [\App\Http\Controllers\Pharmacist\DispenseController::class, 'store'])->name('dispense.store');
    Route::post('/dispense/manual', [\App\Http\Controllers\Pharmacist\DispenseController::class, 'manualStore'])->name('dispense.manual');
    
    // New Routes
    Route::get('/inventory', [\App\Http\Controllers\Pharmacist\InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/dispensing', [\App\Http\Controllers\Pharmacist\DispenseController::class, 'history'])->name('dispensing.history');
    Route::get('/profile', [\App\Http\Controllers\Shared\ProfileController::class, 'show'])->name('profile');
});

// --- Center Manager Routes ---
Route::middleware(['auth', RoleMiddleware::class . ':CenterManager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\CenterManager\DashboardController::class, 'index'])->name('dashboard');
    
    // Inventory (Medicines)
    Route::get('/inventory', [\App\Http\Controllers\CenterManager\InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [\App\Http\Controllers\CenterManager\InventoryController::class, 'update'])->name('inventory.update');

    // Dispensing History
    Route::get('/dispensing', [\App\Http\Controllers\CenterManager\DispensingController::class, 'index'])->name('dispensing.index');

    // Staff Management
    Route::resource('staff', \App\Http\Controllers\CenterManager\StaffController::class);

    // Patients
    Route::resource('patients', \App\Http\Controllers\CenterManager\PatientController::class)->only(['index', 'show']);

    // Reports
    Route::get('/reports', [\App\Http\Controllers\CenterManager\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [\App\Http\Controllers\CenterManager\ReportController::class, 'downloadPdf'])->name('reports.pdf');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile/personal', [ProfileController::class, 'updatePersonal'])->name('profile.update.personal');
    Route::put('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.update.security');
});

// --- Reception Routes ---
Route::middleware(['auth', RoleMiddleware::class . ':Reception'])->prefix('reception')->name('reception.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Reception\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('patients', \App\Http\Controllers\Reception\PatientController::class);
    
    // Visit management
    Route::get('/patients/{patient}/visits/create', [\App\Http\Controllers\Reception\VisitController::class, 'create'])->name('visits.create');
    Route::post('/patients/{patient}/visits', [\App\Http\Controllers\Reception\VisitController::class, 'store'])->name('visits.store');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile/personal', [ProfileController::class, 'updatePersonal'])->name('profile.update.personal');
    Route::put('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.update.security');
});

// --- Patient Routes ---
Route::middleware(['auth', RoleMiddleware::class . ':Patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Patient\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/medicines/search', [\App\Http\Controllers\Patient\MedicineController::class, 'search'])->name('medicines.search');
    Route::get('/prescriptions', [\App\Http\Controllers\Patient\PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/dispense', [\App\Http\Controllers\Patient\DispenseController::class, 'index'])->name('dispense.index');
    Route::get('/profile', [\App\Http\Controllers\Shared\ProfileController::class, 'show'])->name('profile');
});
