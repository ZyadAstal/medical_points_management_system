@extends('layouts.pharmacist')

@section('title', 'تفاصيل الوصفة')

@section('styles')
<style>
.prescription-header {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
}
.info-item h4 { color: #777; font-size: 13px; margin-bottom: 5px; }
.info-item p { color: #053052; font-weight: 700; font-size: 16px; }

.medicine-list { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.medicine-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
}
.medicine-item:last-child { border-bottom: none; }
.status-badge { padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; }
.status-dispensed { background: #dcfce7; color: #166534; }
.status-pending { background: #fef9c3; color: #854d0e; }
</style>
@endsection

@section('content')
<div class="dash-title-block">
    <h1 style="color: #053052; font-size: 32px;">تفاصيل الوصفة الطبية</h1>
    <p style="color: #6184A0; font-size: 18px;">عرض تفاصيل وادوية الوصفة رقم #{{ $prescription->id }}</p>
</div>

<div class="prescription-header">
    <div class="info-item">
        <h4>المريض</h4>
        <p>{{ $prescription->patient->name }}</p>
    </div>
    <div class="info-item">
        <h4>الطبيب</h4>
        <p>{{ $prescription->doctor->name }}</p>
    </div>
    <div class="info-item">
        <h4>التاريخ</h4>
        <p>{{ $prescription->created_at->format('Y-m-d') }}</p>
    </div>
    <div class="info-item">
        <h4>الحالة</h4>
        <p>
            @if($prescription->status == 'new')
                قيد الانتظار
            @else
                مكتملة
            @endif
        </p>
    </div>
</div>

<div class="medicine-list">
    <h3 style="margin-bottom: 20px; color: #053052;">الأدوية المطلوبة</h3>
    @foreach($prescription->items as $item)
    <div class="medicine-item">
        <div>
            <div style="font-weight: 700; color: #333;">{{ $item->medicine->name }}</div>
            <div style="font-size: 12px; color: #777;">الكمية: {{ $item->quantity }}</div>
        </div>
        <div>
            @if($item->is_dispensed)
                <span class="status-badge status-dispensed">تم الصرف</span>
            @else
                <span class="status-badge status-pending">قيد الانتظار</span>
            @endif
        </div>
    </div>
    @endforeach
</div>

<div style="margin-top: 30px; text-align: left;">
    <a href="{{ route('pharmacist.prescriptions.index') }}" class="btn" style="background: #CACACA; color: #333; text-decoration: none; padding: 10px 30px; border-radius: 6px;">العودة للقائمة</a>
</div>
@endsection
