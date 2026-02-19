<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\Prescription;

class PatientController extends Controller
{
    /**
     * Today's patients (visits for today)
     */
    public function index(Request $request)
    {
        $doctor = Auth::user();
        $date = $request->input('date', now()->toDateString());

        $visits = Visit::where('doctor_id', $doctor->id)
            ->whereDate('visit_date', $date)
            ->with('patient')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('doctor.today-patients', compact('visits', 'date'));
    }

    /**
     * Search patients by name, national_id, or phone
     */
    public function search(Request $request)
    {
        $name = $request->input('name');
        $nationalId = $request->input('national_id');
        $phone = $request->input('phone');

        $patients = null;

        if ($name || $nationalId || $phone) {
            $query = Patient::query();

            if ($name) {
                // Split name into words and match each word individually
                $words = preg_split('/\s+/', trim($name));
                foreach ($words as $word) {
                    if ($word) {
                        // Normalize the search word (PHP side)
                        $normalizedWord = $this->normalizeArabic($word);
                        // Normalize the database column (SQL side) and compare
                        $query->whereRaw(
                            "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(full_name, 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ة', 'ه'), 'ى', 'ي') LIKE ?",
                            ["%{$normalizedWord}%"]
                        );
                    }
                }
            }
            if ($nationalId) {
                $query->where('national_id', 'LIKE', "%{$nationalId}%");
            }
            if ($phone) {
                $query->where('phone', 'LIKE', "%{$phone}%");
            }

            $patients = $query->get();
        }

        return view('doctor.search-patient', compact('patients'));
    }

    /**
     * Normalize Arabic text for search comparison
     * Converts all alef variants to plain alef, taa marbuta to haa, alef maksura to yaa
     */
    private function normalizeArabic(string $text): string
    {
        // Replace all alef variants (أ إ آ) with plain alef (ا)
        $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);
        // Replace taa marbuta with haa
        $text = str_replace('ة', 'ه', $text);
        // Replace alef maksura with yaa
        $text = str_replace('ى', 'ي', $text);
        // Remove diacritics (tashkeel)
        $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text);
        return $text;
    }

    /**
     * Show patient details (supports JSON for AJAX modal)
     */
    public function show(Request $request, Patient $patient)
    {
        if ($request->has('json')) {
            $patient->load(['prescriptions.items.dispenses.pharmacist', 'prescriptions.items.medicine', 'prescriptions.doctor', 'visits.medicalCenter', 'visits.doctor']);

            $totalPrescriptions = $patient->prescriptions->count();

            $allDispenses = $patient->prescriptions->flatMap->items->flatMap->dispenses;
            $totalMeds = $allDispenses->count();
            $totalPoints = $allDispenses->sum('points_used');

            $lastVisit = $patient->visits->sortByDesc('visit_date')->first();

            $dispenseHistory = $allDispenses->sortByDesc('created_at')->take(20)->map(function ($dispense) {
                return [
                    'medicine' => $dispense->prescriptionItem->medicine->name ?? '—',
                    'quantity' => $dispense->quantity,
                    'points' => $dispense->points_used,
                    'date' => $dispense->created_at->format('Y/m/d'),
                    'pharmacist' => $dispense->pharmacist->name ?? '—',
                ];
            })->values();

            return response()->json([
                'total_prescriptions' => $totalPrescriptions,
                'total_meds' => $totalMeds,
                'total_points' => $totalPoints,
                'last_visit_date' => $lastVisit ? $lastVisit->visit_date : '—',
                'last_visit_center' => $lastVisit?->medicalCenter?->name ?? '—',
                'last_visit_doctor' => $lastVisit?->doctor?->name ?? '—',
                'dispense_history' => $dispenseHistory,
            ]);
        }

        $patient->load(['prescriptions.items', 'prescriptions.doctor']);
        return view('doctor.patients.show', compact('patient'));
    }
}
