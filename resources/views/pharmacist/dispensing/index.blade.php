@extends('layouts.pharmacist')

@section('title', 'سجل العمليات')
@section('page-id', 'exchange-history')

@section('styles')
<style>
.history-card {
    background: #fff;
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.table-header h2 { font-size: 20px; font-weight: 700; margin: 0; color: #0f3554; }

.history-table {
    width: 100%;
    border-collapse: collapse;
}

.history-table th {
    background: #f9fafb;
    padding: 15px;
    text-align: right;
    font-size: 14px;
    color: #4b5563;
    border-bottom: 1px solid #e5e7eb;
}

.history-table td {
    padding: 15px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
}

.status-badge {
    background: #dcfce7;
    color: #166534;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.pagination-wrapper {
    margin-top: 25px;
    display: flex;
    justify-content: center;
}
</style>
@endsection

@section('content')
<div class="history-card">
    <div class="table-header">
        <h2>سجل الصرف الطبي</h2>
    </div>

    <table class="history-table">
        <thead>
            <tr>
                <th>التاريخ والوقت</th>
                <th>اسم المريض</th>
                <th>الدواء</th>
                <th>النقاط المخصومة</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dispenses as $dispense)
            <tr>
                <td>{{ $dispense->created_at->format('Y/m/d H:i') }}</td>
                <td style="font-weight:600;">{{ $dispense->prescriptionItem->prescription->patient->name }}</td>
                <td>{{ $dispense->prescriptionItem->medicine->name }}</td>
                <td style="color: #053052; font-weight: 700;">{{ $dispense->points_used }} نقطة</td>
                <td><span class="status-badge">تم الصرف</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #888; padding: 30px;">لم يتم إجراء أي عمليات صرف حتى الآن.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="custom-pagination-nav">
        {{ $dispenses->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
