<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    // عرض وصفات المريض
    public function index()
    {
        $patient = Auth::user()->patient;
        
        $prescriptions = $patient ? $patient->prescriptions()
            ->with(['items.medicine', 'doctor'])
            ->orderBy('created_at', 'desc')
            ->get() : [];

        return view('patient.prescriptions.index', compact('prescriptions'));
    }
}
