<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
require __DIR__ . '/groups/auth.php';

// Shared Routes
require __DIR__ . '/groups/shared.php';

// Role-Based Routes
require __DIR__ . '/groups/superadmin.php';
require __DIR__ . '/groups/doctor.php';
require __DIR__ . '/groups/pharmacist.php';
require __DIR__ . '/groups/manager.php';
require __DIR__ . '/groups/reception.php';
require __DIR__ . '/groups/patient.php';
