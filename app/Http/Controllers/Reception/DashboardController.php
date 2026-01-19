<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $total_patients = Patient::count();
        $new_patients_today = Patient::whereDate('created_at', $today)->count();
        $recent_patients = Patient::latest()->take(5)->get();

        return view('reception.dashboard', compact('total_patients', 'new_patients_today', 'recent_patients'));
    }
}
