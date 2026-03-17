<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shared\ProfileController;

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/personal', [ProfileController::class, 'updatePersonal'])->name('profile.update.personal');
    Route::put('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.update.security');
});
