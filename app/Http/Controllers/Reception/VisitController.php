<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    /**
     * قائمة انتظار اليوم
     */
    public function waitingList(Request $request)
    {
        $centerId = Auth::user()->medical_center_id;
        $date = $request->input('date', Carbon::today()->toDateString());

        // حساب إحصاءات اليوم الكاملة وليس فقط الصفحة الحالية
        $baseQuery = Visit::where('medical_center_id', $centerId)
            ->whereDate('visit_date', $date);

        $stats = [
            'total'       => (clone $baseQuery)->count(),
            'registered'  => (clone $baseQuery)->where('status', Visit::STATUS_REGISTERED)->count(),
            'waiting'     => (clone $baseQuery)->where('status', Visit::STATUS_WAITING)->count(),
            'in_progress' => (clone $baseQuery)->where('status', Visit::STATUS_IN_PROGRESS)->count(),
            'completed'   => (clone $baseQuery)->where('status', Visit::STATUS_COMPLETED)->count(),
        ];

        $query = Visit::with(['patient', 'doctor'])
            ->where('medical_center_id', $centerId)
            ->whereDate('visit_date', $date)
            ->orderByRaw("CASE 
                WHEN status = '" . Visit::STATUS_REGISTERED . "' THEN 1
                WHEN status = '" . Visit::STATUS_WAITING . "' THEN 2
                WHEN status = '" . Visit::STATUS_IN_PROGRESS . "' THEN 3
                WHEN status = '" . Visit::STATUS_COMPLETED . "' THEN 4
                WHEN status = '" . Visit::STATUS_CANCELLED . "' THEN 5
                ELSE 6 END")
            ->orderBy('created_at', 'asc');

        if ($request->has('print')) {
            $visits = $query->get();
            return view('reception.print-waiting-list', compact('visits', 'date', 'stats'));
        }

        $visits = $query->paginate(4)->withQueryString();

        // جلب الأطباء للقائمة المنسدلة في صفحة التسجيل
        $doctors = User::where('medical_center_id', $centerId)
            ->whereHas('role', fn($q) => $q->where('name', 'Doctor'))
            ->get();

        return view('reception.today-waiting-list', compact('visits', 'date', 'doctors', 'stats'));
    }

    /**
     * إنشاء زيارة جديدة (من صفحة تفاصيل المريض)
     */
    public function create(Patient $patient)
    {
        $centerId = Auth::user()->medical_center_id;
        $doctors = User::where('medical_center_id', $centerId)
            ->whereHas('role', fn($q) => $q->where('name', 'Doctor'))
            ->get();

        return view('reception.visits.create', compact('patient', 'doctors'));
    }

    /**
     * حفظ زيارة جديدة
     */
    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'notes'     => 'nullable|string',
        ]);

        $centerId = Auth::user()->medical_center_id;

        Visit::create([
            'patient_id'       => $patient->id,
            'doctor_id'        => $request->doctor_id,
            'medical_center_id'=> $centerId,
            'visit_date'       => now()->toDateString(),
            'priority'         => 0,
            'status'           => Visit::STATUS_REGISTERED,
            'notes'            => $request->notes,
        ]);

        return redirect()->route('reception.visits.waiting')
            ->with('success', 'تم إضافة المريض لقائمة الانتظار بنجاح');
    }

    /**
     * إلغاء زيارة (إعادة الحالة لـ waiting)
     */
    public function cancelVisit(Request $request, Visit $visit)
    {
        $oldStatus = $visit->status;
        $newStatus = $oldStatus;

        if ($oldStatus === Visit::STATUS_REGISTERED) {
            $newStatus = Visit::STATUS_CANCELLED;
        } elseif ($oldStatus === Visit::STATUS_WAITING) {
            $newStatus = Visit::STATUS_REGISTERED;
        }

        $visit->update(['status' => $newStatus]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'new_status' => $newStatus,
                'status_label' => match($newStatus) {
                    Visit::STATUS_REGISTERED => 'مسجل',
                    Visit::STATUS_CANCELLED  => 'ملغي',
                    default => '---'
                },
                'status_class' => match($newStatus) {
                    Visit::STATUS_REGISTERED => 'waiting-status-registered',
                    Visit::STATUS_CANCELLED  => 'waiting-status-cancelled',
                    default => ''
                }
            ]);
        }

        return redirect()->back()->with('success', 'تم تحديث حالة الزيارة');
    }

    /**
     * إرسال مريض للطبيب (تغيير حالة الزيارة)
     */
    public function sendToDoctor(Request $request, Visit $visit)
    {
        $visit->update(['status' => Visit::STATUS_WAITING]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'تم إرسال المريض للطبيب');
    }
}
