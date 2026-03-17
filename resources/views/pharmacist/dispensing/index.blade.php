@extends('layouts.pharmacist')

@section('title', 'سجل العمليات')
@section('page-id', 'exchange-history')

@push('styles')
    <link href="{{ asset('css/pharmacist/views/dispensing-history.css') }}" rel="stylesheet">
@endpush

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
