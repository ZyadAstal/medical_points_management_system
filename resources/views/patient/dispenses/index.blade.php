@extends('layouts.patient')

@section('title', 'الأدوية المصروفة - Medicare')
@section('page-id', 'dispenses')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/patient/dispenses.css') }}">
@endpush

@section('content')
<div class="container" style="padding: 20px;">
    <div class="section-header">
        <h1>سجل الأدوية المصروفة</h1>
        <p>عرض جميع الأدوية التي تم صرفها لك مع تفاصيلها</p>
    </div>

    <div class="car-table">
        <table>
            <thead>
                <tr>
                    <th>تاريخ الصرف</th>
                    <th>الدواء</th>
                    <th>الكمية</th>
                    <th>النقاط</th>
                    <th>المركز الطبي</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dispenses as $dispense)
                    <tr>
                        <td style="font-weight: 500">{{ $dispense->created_at->format('Y/m/d') }}</td>
                        <td class="med-name">
                            <div style="font-weight: 700;">{{ $dispense->prescriptionItem->medicine->name }}</div>
                            @if($dispense->prescriptionItem->medicine->name_en)
                                <div style="font-size: 13px; color: #6184A0; direction: ltr; text-align: left;">{{ $dispense->prescriptionItem->medicine->name_en }}</div>
                            @endif
                        </td>
                        <td style="color: #7F7676;">{{ $dispense->quantity }}</td>
                        <td class="points">{{ $dispense->points_used }}</td>
                        <td style="font-weight: 400;">{{ $dispense->medicalCenter->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 40px; color: #888;">لا توجد سجلات صرف أدوية حتى الآن.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mc-pagination" style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $dispenses->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
