<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('full_name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        $patients = $query->latest()->paginate(10);
        return view('reception.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('reception.patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8',
            'name' => 'required|string|max:255',
            'national_id' => 'required|string|unique:patients,national_id',
            'phone' => 'nullable|string',
            'date_of_birth' => 'required|date',
            'address' => 'nullable|string',
            'points' => 'required|integer|min:0',
        ]);

        // Create User account first (for login capability)
        $user = \App\Models\User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@patient.local', // Dummy email
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role_id' => \App\Models\Role::where('name', 'Patient')->first()->id,
        ]);

        // Create Patient record linked to User
        Patient::create([
            'user_id' => $user->id,
            'full_name' => $request->name,
            'national_id' => $request->national_id,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
            'points' => $request->points, 
        ]);

        return redirect()->route('reception.patients.index')->with('success', 'تم تسجيل المريض بنجاح');
    }

    public function edit(Patient $patient)
    {
        return view('reception.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'national_id' => 'required|string|unique:patients,national_id,' . $patient->id,
            'phone' => 'nullable|string',
            'date_of_birth' => 'required|date',
            'address' => 'nullable|string',
            'points' => 'required|integer|min:0',
        ]);

        // Manually handling the name attribute since custom accessor/mutator might need explicit handling if not filling everything
        $data = $request->except(['name']);
        $data['full_name'] = $request->name;

        $patient->update($data);

        return redirect()->route('reception.patients.index')->with('success', 'تم تحديث بيانات المريض');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('reception.patients.index')->with('success', 'تم حذف سجل المريض.');
    }
}
