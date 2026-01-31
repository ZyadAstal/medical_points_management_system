<?php

namespace App\Http\Controllers\CenterManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispense;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Barryvdh\DomPDF\Facade\Pdf;
use Arphp\Glyphs;
use App\Models\Medicine;
use App\Models\MedicalCenter;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $centerId = Auth::user()->medical_center_id;
        $medicineId = $request->medicine_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $reportType = $request->report_type;

        // Auto-set dates for 'Daily' report if not manually picked
        if ($reportType === 'daily' && !$fromDate && !$toDate) {
            $fromDate = now()->toDateString();
            $toDate = now()->toDateString();
        }

        // Base Query for Stats
        $statsQuery = DB::table('dispenses')
            ->join('prescription_items', 'dispenses.prescription_item_id', '=', 'prescription_items.id')
            ->join('medicines', 'prescription_items.medicine_id', '=', 'medicines.id')
            ->where('dispenses.medical_center_id', $centerId)
            ->select('medicines.name', 'medicines.id', DB::raw('count(dispenses.id) as count'), DB::raw('sum(dispenses.points_used) as points'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderBy('count', 'desc');

        // Apply filters
        if ($medicineId) $statsQuery->where('medicines.id', $medicineId);
        if ($fromDate) $statsQuery->where('dispenses.created_at', '>=', $fromDate);
        if ($toDate) $statsQuery->where('dispenses.created_at', '<=', $toDate . ' 23:59:59');

        $medicineStats = $statsQuery->get();

        // 1. Total points used (filtered)
        $totalPointsQuery = Dispense::where('medical_center_id', $centerId);
        if ($medicineId) $totalPointsQuery->whereHas('prescriptionItem', fn($q) => $q->where('medicine_id', $medicineId));
        if ($fromDate) $totalPointsQuery->where('created_at', '>=', $fromDate);
        if ($toDate) $totalPointsQuery->where('created_at', '<=', $toDate . ' 23:59:59');
        $totalPointsUsed = $totalPointsQuery->sum('points_used');
        $totalDispenses = $totalPointsQuery->count();

        // 3. Inventory health (unaffected by filters usually, but showing current state)
        $lowStock = Inventory::where('medical_center_id', $centerId)
            ->where('quantity', '<', 10)
            ->with('medicine')
            ->get();

        $allMedicines = \App\Models\Medicine::all();

        return view('manager.reports', compact('totalPointsUsed', 'medicineStats', 'lowStock', 'totalDispenses', 'allMedicines'));
    }

    public function downloadPdf(Request $request)
    {
        $centerId = Auth::user()->medical_center_id;
        $medicineId = $request->medicine_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        // Base Query for Dispense
        $dispenseQuery = Dispense::where('medical_center_id', $centerId);
        if ($medicineId) $dispenseQuery->whereHas('prescriptionItem', fn($q) => $q->where('medicine_id', $medicineId));
        if ($fromDate) $dispenseQuery->where('created_at', '>=', $fromDate);
        if ($toDate) $dispenseQuery->where('created_at', '<=', $toDate . ' 23:59:59');

        $totalPointsUsed = (clone $dispenseQuery)->sum('points_used');
        $totalDispenses = (clone $dispenseQuery)->count();

        // Medicine Stats
        $medicineStats = DB::table('dispenses')
            ->join('prescription_items', 'dispenses.prescription_item_id', '=', 'prescription_items.id')
            ->join('medicines', 'prescription_items.medicine_id', '=', 'medicines.id')
            ->where('dispenses.medical_center_id', $centerId)
            ->select('medicines.name', DB::raw('count(dispenses.id) as count'), DB::raw('sum(dispenses.points_used) as points'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderBy('count', 'desc');

        if ($medicineId) $medicineStats->where('medicines.id', $medicineId);
        if ($fromDate) $medicineStats->where('dispenses.created_at', '>=', $fromDate);
        if ($toDate) $medicineStats->where('dispenses.created_at', '<=', $toDate . ' 23:59:59');
        $medicineStats = $medicineStats->get();

        // Low Stock
        $lowStock = Inventory::where('medical_center_id', $centerId)
            ->where('quantity', '<', 10)
            ->with('medicine')
            ->get();

        $scName = Auth::user()->medicalCenter->name;
        $smName = $medicineId ? Medicine::find($medicineId)?->name : 'كل الأدوية';

        $arabic = new Glyphs();

        // Formatting for Ar-PHP
        $totalPointsUsedFormatted = $arabic->utf8Glyphs(number_format($totalPointsUsed), 100) . ' ' . $arabic->utf8Glyphs('نقطة', 100) . ' :' . $arabic->utf8Glyphs('إجمالي النقاط المصروفة', 100);
        $totalDispensesSummary = $arabic->utf8Glyphs(number_format($totalDispenses), 100) . ' ' . $arabic->utf8Glyphs('عملية صنف', 100) . ' :' . $arabic->utf8Glyphs('إجمالي عمليات الصرف', 100);
        
        $selectedCenter = $arabic->utf8Glyphs($scName, 100);
        $selectedMedicine = $arabic->utf8Glyphs($smName, 100);
        $reportTitle = $arabic->utf8Glyphs('تقرير المركز الطبي لنقاط الأدوية', 100);
        
        $exportDateLabel = $arabic->utf8Glyphs(now()->format('Y-m-d H:i'), 100) . ' :' . $arabic->utf8Glyphs('تاريخ استخراج التقرير', 100);
        $statsTitle = $arabic->utf8Glyphs('تحليل استهلاك الأدوية', 100);
        $lowStockTitle = $arabic->utf8Glyphs('تنبيهات انخفاض المخزون', 100);
        $footerText = $arabic->utf8Glyphs('هذا التقرير تم إنشاؤه آلياً بواسطة نظام Medicare - إدارة المركز الطبي.', 100);

        // Column Headers
        $headers = [
            'medicine' => $arabic->utf8Glyphs('اسم الدواء', 100),
            'count' => $arabic->utf8Glyphs('عدد مرات الصرف', 100),
            'points' => $arabic->utf8Glyphs('النقاط المصروفة', 100),
            'qty' => $arabic->utf8Glyphs('الكمية المتبقية', 100),
            'status' => $arabic->utf8Glyphs('الحالة', 100),
        ];

        foreach($medicineStats as $stat) {
            $stat->shaped_name = $arabic->utf8Glyphs($stat->name, 100);
        }

        foreach($lowStock as $inv) {
            $inv->shaped_name = $arabic->utf8Glyphs($inv->medicine->name, 100);
            $inv->shaped_status = $arabic->utf8Glyphs($inv->quantity <= 3 ? 'حرجة' : 'منخفضة', 100);
        }

        $pdf = Pdf::loadView('manager.reports-pdf', compact(
            'totalPointsUsedFormatted', 
            'totalDispensesSummary',
            'selectedCenter', 
            'selectedMedicine', 
            'fromDate', 
            'toDate',
            'reportTitle',
            'exportDateLabel',
            'statsTitle',
            'lowStockTitle',
            'footerText',
            'headers',
            'medicineStats',
            'lowStock'
        ));

        return $pdf->download('Center_Report_' . now()->format('Y-m-d') . '.pdf');
    }
}
