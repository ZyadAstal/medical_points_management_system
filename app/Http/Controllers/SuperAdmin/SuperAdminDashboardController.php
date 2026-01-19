<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\MedicalCenter;
use App\Models\Medicine;
use Illuminate\Http\Request;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        // إحصائيات عامة للنظام
        $stats = [
            'doctors_count' => User::whereHas('role', function($q) { $q->where('name', 'Doctor'); })->count(),
            'pharmacists_count' => User::whereHas('role', function($q) { $q->where('name', 'Pharmacist'); })->count(),
            'patients_count' => Patient::count(),
            'centers_count' => MedicalCenter::count(),
            'medicines_count' => Medicine::count(),
        ];

        return view('superadmin.dashboard', compact('stats'));
    }
}
