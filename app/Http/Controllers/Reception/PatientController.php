<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use App\Models\Role;
use App\Models\Visit;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    /**
     * صفحة البحث عن مريض
     */
    public function index(Request $request)
    {
        $patients = collect();
        $searched = false;

        if ($request->hasAny(['name', 'national_id', 'phone'])) {
            $searched = true;
            $query = Patient::query();

            if ($request->filled('name')) {
                $query->searchArabic('full_name', $request->name);
            }
            if ($request->filled('national_id')) {
                $query->where('national_id', 'like', '%' . $request->national_id . '%');
            }
            if ($request->filled('phone')) {
                $query->where('phone', 'like', '%' . $request->phone . '%');
            }

            $patients = $query->withCount('visits')
                ->with(['visits' => function ($q) {
                    $q->latest()->limit(1);
                }])
                ->latest()
                ->paginate(10)
                ->withQueryString();
        }

        return view('reception.search-patient', compact('patients', 'searched'));
    }

    /**
     * صفحة تسجيل مريض (جديد أو سابق)
     */
    public function create()
    {
        $centerId = Auth::user()->medical_center_id;
        $doctors = User::where('medical_center_id', $centerId)
            ->whereHas('role', fn($q) => $q->where('name', 'Doctor'))
            ->get();

        return view('reception.patient-registration', compact('doctors'));
    }

    /**
     * تسجيل مريض جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name'   => 'required|string|max:255',
            'national_id' => 'required|string|unique:patients,national_id',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:500',
            'doctor_id'   => 'required|exists:users,id',
            'username'    => 'required|string|max:255|unique:users,username',
            'email'       => 'required|string|email|max:255|unique:users,email',
            'password'    => 'required|string|min:6',
            'priority'    => 'required|in:0,1',
            'date_of_birth' => 'required|date|before:today',
        ]);

        // إنشاء مستخدم
        $user = User::create([
            'name'              => $request->full_name,
            'username'          => $request->username,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role_id'           => Role::where('name', 'Patient')->first()->id,
            'medical_center_id' => Auth::user()->medical_center_id,
        ]);

        // إنشاء سجل المريض
        $patient = Patient::create([
            'user_id'    => $user->id,
            'full_name'  => $request->full_name,
            'national_id'=> $request->national_id,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'points'     => 100,
            'date_of_birth' => $request->date_of_birth,
        ]);

        // إضافة زيارة مباشرة
        Visit::create([
            'patient_id'       => $patient->id,
            'doctor_id'        => $request->doctor_id,
            'medical_center_id'=> Auth::user()->medical_center_id,
            'visit_date'       => now()->toDateString(),
            'status'           => Visit::STATUS_REGISTERED,
            'priority'         => $request->priority,
        ]);

        return redirect()->route('reception.visits.waiting')
            ->with('success', 'تم تسجيل المريض وإضافته لقائمة الانتظار بنجاح');
    }

    /**
     * تحديث بيانات مريض سابق
     */
    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $patient->update([
            'phone'   => $data['phone'] ?? $patient->phone,
            'address' => $data['address'] ?? $patient->address,
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم تحديث البيانات بنجاح']);
        }

        return redirect()->back()->with('success', 'تم تحديث بيانات المريض بنجاح');
    }

    /**
     * حذف مريض
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('reception.patients.index')
            ->with('success', 'تم حذف سجل المريض.');
    }

    /**
     * البحث بمريض سابق برقم الهوية (AJAX)
     */
    public function searchByNationalId(Request $request)
    {
        $nid = $request->input('national_id');

        $patient = Patient::where('national_id', $nid)
            ->orWhere(function($q) use ($nid) {
                $q->searchArabic(['full_name'], $nid);
            })
            ->first();

        if (!$patient) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'      => true,
            'id'         => $patient->id,
            'name'       => $patient->full_name,
            'national_id'=> $patient->national_id,
            'phone'      => $patient->phone,
            'address'    => $patient->address,
            'points'     => $patient->points,
            'visits_count' => $patient->visits()->count(),
        ]);
    }

    /**
     * إرسال مريض سابق للطبيب (إضافة لقائمة الانتظار)
     */
    public function sendToDoctor(Request $request, Patient $patient)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'priority'  => 'required|in:0,1',
        ]);

        // التحقق من عدم وجود زيارة اليوم مسبقاً
        $existingVisit = Visit::where('patient_id', $patient->id)
            ->whereDate('visit_date', Carbon::today())
            ->whereIn('status', ['waiting', 'in_progress'])
            ->first();

        if ($existingVisit) {
            return redirect()->back()->with('warning', 'المريض موجود بالفعل في قائمة الانتظار اليوم');
        }

        Visit::create([
            'patient_id'       => $patient->id,
            'doctor_id'        => $request->doctor_id,
            'medical_center_id'=> Auth::user()->medical_center_id,
            'visit_date'       => now()->toDateString(),
            'status'           => Visit::STATUS_REGISTERED,
            'priority'         => $request->priority,
        ]);

        return response()->json(['success' => true, 'message' => 'تم إضافة المريض لقائمة الانتظار']);
    }
}
