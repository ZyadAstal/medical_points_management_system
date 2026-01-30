<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispense;
use App\Models\Medicine;
use App\Models\MedicalCenter;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Arphp\Glyphs;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $centerId = $request->center_id;
        $medicineId = $request->medicine_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        // Base Query for Dispense
        $dispenseQuery = Dispense::query();

        if ($centerId) $dispenseQuery->where('medical_center_id', $centerId);
        if ($medicineId) $dispenseQuery->whereHas('prescriptionItem', function($q) use ($medicineId) {
            $q->where('medicine_id', $medicineId);
        });
        if ($fromDate) $dispenseQuery->where('created_at', '>=', $fromDate);
        if ($toDate) $dispenseQuery->where('created_at', '<=', $toDate . ' 23:59:59');

        // 1. Total points used (filtered)
        $totalPointsUsed = (clone $dispenseQuery)->sum('points_used');

        // 2. Activity per Center (filtered by medicine/dates)
        $centersQuery = MedicalCenter::withCount(['visits' => function($q) use ($fromDate, $toDate) {
            if ($fromDate) $q->where('created_at', '>=', $fromDate);
            if ($toDate) $q->where('created_at', '<=', $toDate . ' 23:59:59');
        }]);

        if ($centerId) {
            $centersQuery->where('id', $centerId);
        }

        $centersActivity = $centersQuery->get()->map(function($center) use ($medicineId, $fromDate, $toDate) {
            $q = Dispense::where('medical_center_id', $center->id);
            if ($medicineId) $q->whereHas('prescriptionItem', function($sq) use ($medicineId) {
                $sq->where('medicine_id', $medicineId);
            });
            if ($fromDate) $q->where('created_at', '>=', $fromDate);
            if ($toDate) $q->where('created_at', '<=', $toDate . ' 23:59:59');
            
            $center->total_dispenses = $q->count();
            $center->points_used = $q->sum('points_used');
            return $center;
        });

        // 3. Most dispensed medicines (filtered by center/dates)
        $topMedQuery = DB::table('dispenses')
            ->join('prescription_items', 'dispenses.prescription_item_id', '=', 'prescription_items.id')
            ->join('medicines', 'prescription_items.medicine_id', '=', 'medicines.id')
            ->select('medicines.name', DB::raw('count(dispenses.id) as count'), DB::raw('sum(dispenses.points_used) as total_points'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderBy('count', 'desc');

        if ($centerId) $topMedQuery->where('dispenses.medical_center_id', $centerId);
        if ($medicineId) $topMedQuery->where('medicines.id', $medicineId);
        if ($fromDate) $topMedQuery->where('dispenses.created_at', '>=', $fromDate);
        if ($toDate) $topMedQuery->where('dispenses.created_at', '<=', $toDate . ' 23:59:59');

        $topMedicines = $topMedQuery->take(10)->get();

        // 4. Detailed Dispense Records
        $detailedDispenses = (clone $dispenseQuery)
            ->with(['medicalCenter', 'prescriptionItem.medicine', 'pharmacist'])
            ->latest()
            ->take(20)
            ->get();

        $patientChartQuery = DB::table('dispenses')
            ->join('prescription_items', 'dispenses.prescription_item_id', '=', 'prescription_items.id')
            ->join('prescriptions', 'prescription_items.prescription_id', '=', 'prescriptions.id')
            ->join('patients', 'prescriptions.patient_id', '=', 'patients.id');

        if ($centerId) $patientChartQuery->where('dispenses.medical_center_id', $centerId);
        if ($medicineId) $patientChartQuery->where('prescription_items.medicine_id', $medicineId);
        if ($fromDate) $patientChartQuery->where('dispenses.created_at', '>=', $fromDate);
        if ($toDate) $patientChartQuery->where('dispenses.created_at', '<=', $toDate . ' 23:59:59');

        $patientsActivityChart = $patientChartQuery->select('patients.id', 'patients.full_name', DB::raw('count(dispenses.id) as total_ops'), DB::raw('sum(dispenses.points_used) as total_points'))
            ->groupBy('patients.id', 'patients.full_name')
            ->orderBy('total_points', 'desc')
            ->take(5)
            ->get();

        $centers = MedicalCenter::all();
        $medicines = Medicine::all();
        return view('superadmin.reports', compact('totalPointsUsed', 'centersActivity', 'topMedicines', 'centers', 'medicines', 'detailedDispenses', 'patientsActivityChart'));
    }

    public function downloadPdf(Request $request)
    {
        $centerId = $request->center_id;
        $medicineId = $request->medicine_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        // Base Query for Dispense
        $dispenseQuery = Dispense::query();
        if ($centerId) $dispenseQuery->where('medical_center_id', $centerId);
        if ($medicineId) $dispenseQuery->whereHas('prescriptionItem', function($q) use ($medicineId) {
            $q->where('medicine_id', $medicineId);
        });
        if ($fromDate) $dispenseQuery->where('created_at', '>=', $fromDate);
        if ($toDate) $dispenseQuery->where('created_at', '<=', $toDate . ' 23:59:59');

        $totalPointsUsed = (clone $dispenseQuery)->sum('points_used');

        // Centers Activity (Same logic as index)
        $centersActivity = MedicalCenter::all()->map(function($center) use ($medicineId, $fromDate, $toDate) {
            $q = DB::table('dispenses')->where('medical_center_id', $center->id);
            if ($medicineId) {
                $q->join('prescription_items', 'dispenses.prescription_item_id', '=', 'prescription_items.id')
                  ->where('prescription_items.medicine_id', $medicineId);
            }
            if ($fromDate) $q->where('dispenses.created_at', '>=', $fromDate);
            if ($toDate) $q->where('dispenses.created_at', '<=', $toDate . ' 23:59:59');
            $center->total_dispenses = $q->count();
            $center->points_used = $q->sum('points_used');
            return $center;
        });

        // Top Medicines
        $topMedQuery = DB::table('dispenses')
            ->join('prescription_items', 'dispenses.prescription_item_id', '=', 'prescription_items.id')
            ->join('medicines', 'prescription_items.medicine_id', '=', 'medicines.id')
            ->select('medicines.name', DB::raw('count(dispenses.id) as count'), DB::raw('sum(dispenses.points_used) as total_points'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderBy('count', 'desc');

        if ($centerId) $topMedQuery->where('dispenses.medical_center_id', $centerId);
        if ($medicineId) $topMedQuery->where('medicines.id', $medicineId);
        if ($fromDate) $topMedQuery->where('dispenses.created_at', '>=', $fromDate);
        if ($toDate) $topMedQuery->where('dispenses.created_at', '<=', $toDate . ' 23:59:59');
        $topMedicines = $topMedQuery->take(10)->get();

        $detailedDispenses = (clone $dispenseQuery)
            ->with(['medicalCenter', 'prescriptionItem.medicine'])
            ->latest()
            ->get();

        $scName = $centerId ? MedicalCenter::find($centerId)?->name : 'كل المراكز';
        $smName = $medicineId ? Medicine::find($medicineId)?->name : 'كل الأدوية';

        // Using Ar-PHP to shape Arabic text for DomPDF
        $arabic = new Glyphs();

        // Concatenate Value then Label so Label appears on the right in LTR container
        $totalPointsUsedFormatted = $arabic->utf8Glyphs(number_format($totalPointsUsed), 100) . ' ' . $arabic->utf8Glyphs('نقطة', 100) . ' :' . $arabic->utf8Glyphs('عدد النقاط المصروفة', 100);
        
        $totalDispensesSummary = $arabic->utf8Glyphs(number_format($centersActivity->sum('total_dispenses')), 100) . ' ' . $arabic->utf8Glyphs('عمليات', 100) . ' :' . $arabic->utf8Glyphs('إجمالي عمليات الصرف', 100);

        $selectedCenter = $arabic->utf8Glyphs($scName, 100);
        $selectedMedicine = $arabic->utf8Glyphs($smName, 100);
        $reportTitle = $arabic->utf8Glyphs('تقرير نقاط الخدمات الطبية', 100);
        
        // Date on the left, Label on the right
        $exportDateLabel = $arabic->utf8Glyphs(now()->format('Y-m-d H:i'), 100) . ' :' . $arabic->utf8Glyphs('تاريخ استخراج التقرير', 100);
        $detailedTitle = $arabic->utf8Glyphs('تفاصيل عمليات الصرف', 100);
        $activityTitle = $arabic->utf8Glyphs('تحليل نشاط المراكز الطبية', 100);
        $topMedTitle = $arabic->utf8Glyphs('الأدوية الأكثر صرفاً', 100);
        $footerText = $arabic->utf8Glyphs('هذا التقرير تم إنشاؤه آلياً بواسطة نظام Medicare لإدارة النقاط الطبية.', 100);

        // Column Headers
        $headers = [
            'center' => $arabic->utf8Glyphs('اسم المركز', 100),
            'medicine' => $arabic->utf8Glyphs('اسم الدواء', 100),
            'qty' => $arabic->utf8Glyphs('نقاط الصرف', 100),
            'points' => $arabic->utf8Glyphs('النقاط المصروفة', 100),
            'date' => $arabic->utf8Glyphs('التاريخ', 100),
            'count' => $arabic->utf8Glyphs('العدد', 100),
            'sum' => $arabic->utf8Glyphs('المجموع', 100),
        ];

        foreach($detailedDispenses as $dispense) {
            $dispense->shaped_medicine = $arabic->utf8Glyphs($dispense->prescriptionItem->medicine->name, 100);
            $dispense->shaped_center = $arabic->utf8Glyphs($dispense->medicalCenter->name, 100);
        }

        foreach($centersActivity as $center) {
            $center->shaped_name = $arabic->utf8Glyphs($center->name, 100);
        }

        foreach($topMedicines as $med) {
            $med->shaped_name = $arabic->utf8Glyphs($med->name, 100);
        }

        $pdf = Pdf::loadView('superadmin.reports-pdf', compact(
            'detailedDispenses', 
            'totalPointsUsedFormatted', 
            'totalDispensesSummary',
            'selectedCenter', 
            'selectedMedicine', 
            'fromDate', 
            'toDate',
            'reportTitle',
            'exportDateLabel',
            'detailedTitle',
            'activityTitle',
            'topMedTitle',
            'footerText',
            'headers',
            'centersActivity',
            'topMedicines'
        ));

        return $pdf->download('Medical_Points_Report_' . now()->format('Y-m-d') . '.pdf');
    }
}
