@extends('layouts.pharmacist')

@section('title', 'تفاصيل الوصفة')

@push('styles')
<style>
/* Premium Spaced-Box Design */
:root {
    --navy: #053052;
    --accent: #076DBE;
    --bg-faded: #F1F5F9;
    --border-color: #E2E8F0;
}

.report-wrapper {
    max-width: 1100px;
    margin: 40px auto;
    padding: 0 20px;
}

.page-main-title {
    color: var(--navy);
    font-size: 30px;
    font-weight: 900;
    margin-bottom: 40px;
    text-align: right;
    display: flex;
    align-items: center;
    gap: 15px;
}

.page-main-title::after {
    content: '';
    height: 3px;
    background: var(--accent);
    flex: 1;
    border-radius: 10px;
}

/* Grid of highly distinct boxes */
.info-boxes-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px; /* المسافة الكبيرة المطلوبة */
    margin-bottom: 50px;
}

.info-slot {
    background: #FFFFFF;
    border: 1px solid var(--border-color);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
    gap: 15px;
    position: relative;
    transition: all 0.3s ease;
}

.info-slot::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 8px;
    height: 100%;
    background: var(--navy);
    border-radius: 0 20px 20px 0;
}

.info-slot:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    border-color: var(--accent);
}

.slot-header {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #64748B;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.slot-header svg {
    color: var(--accent);
    width: 20px;
    height: 20px;
}

.slot-content {
    color: var(--navy);
    font-size: 22px;
    font-weight: 800;
    line-height: 1.2;
}

/* Medicines Section */
.medicines-card {
    background: #FFFFFF;
    border: 1px solid var(--border-color);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.meds-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--bg-faded);
}

.meds-header h2 {
    margin: 0;
    color: var(--navy);
    font-size: 24px;
    font-weight: 900;
}

.medicine-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 25px;
    background: #F8FAFC;
    border-radius: 18px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    transition: all 0.2s;
}

.medicine-row:hover {
    background: #FFFFFF;
    border-color: var(--accent);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}

.med-details h4 {
    margin: 0;
    font-size: 19px;
    color: var(--navy);
    font-weight: 800;
}

.med-details p {
    margin: 6px 0 0;
    font-size: 14px;
    color: #64748B;
    font-style: italic;
}

.med-stats {
    display: flex;
    align-items: center;
    gap: 30px;
}

.qty-tag {
    background: var(--navy);
    color: #FFFFFF;
    padding: 10px 25px;
    border-radius: 12px;
    font-weight: 800;
    font-size: 18px;
}

.status-pill {
    padding: 8px 16px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 13px;
}

.status-pill.done { background: #DCFCE7; color: #166534; }
.status-pill.wait { background: #FEE2E2; color: #B91C1C; }

/* Back Action */
.actions-footer {
    margin-top: 50px;
    text-align: right;
}

.btn-back-home {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: var(--navy);
    color: #FFFFFF;
    padding: 18px 45px;
    border-radius: 16px;
    font-weight: 800;
    font-size: 18px;
    box-shadow: 0 10px 15px -3px rgba(11, 58, 90, 0.3);
    transition: all 0.2s;
}

.btn-back-home:hover {
    background: var(--accent);
    transform: translateX(-5px);
}
</style>
@endpush

@section('content')
<div class="report-wrapper">
    <h1 class="page-main-title">وصفة طبية رقم #{{ $prescription->id }}</h1>

    <div class="info-boxes-grid">
        <!-- Patient Box -->
        <div class="info-slot">
            <div class="slot-header">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                المريــــــض
            </div>
            <div class="slot-content">{{ $prescription->patient->name }}</div>
        </div>

        <!-- Doctor Box -->
        <div class="info-slot">
            <div class="slot-header">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                الطبيــــــب
            </div>
            <div class="slot-content">د. {{ $prescription->doctor->name }}</div>
        </div>

        <!-- Date Box -->
        <div class="info-slot">
            <div class="slot-header">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                تاريخ الإصدار
            </div>
            <div class="slot-content">{{ $prescription->created_at->format('Y-m-d') }}</div>
        </div>

        <!-- Status Box -->
        <div class="info-slot">
            <div class="slot-header">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                حالة الوصفة
            </div>
            <div class="slot-content">
                @php
                    $status = $prescription->status;
                    $color = '#64748B'; // Default
                    if ($status == 'تم الصرف') $color = '#166534';
                    elseif ($status == 'لم يتم الصرف') $color = '#B91C1C';
                    elseif ($status == 'صرف جزئي') $color = '#854D0E';
                @endphp
                <span style="color: {{ $color }};">
                    @if($status == 'تم الصرف') ✓ @else ● @endif
                    {{ $status }}
                </span>
            </div>
        </div>
    </div>

    <div class="medicines-card">
        <div class="meds-header">
            <h2>الأدوية الموصوفة</h2>
            <div style="font-weight: 800; color: var(--accent);">{{ $prescription->items->count() }} أصناف</div>
        </div>

        @foreach($prescription->items as $item)
        <div class="medicine-row">
            <div class="med-details">
                <h4>{{ $item->medicine->name }}</h4>
                @if($item->medicine->name_en)
                    <p>{{ $item->medicine->name_en }}</p>
                @endif
            </div>
            <div class="med-stats">
                <div class="qty-tag">{{ $item->quantity }} عبوة</div>
                @if($item->is_dispensed)
                    <span class="status-pill done">تم الصرف</span>
                @else
                    <span class="status-pill wait">لم يتم الصرف</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="actions-footer">
        <a href="{{ route('pharmacist.prescriptions.index') }}" class="btn-back-home">
            <svg style="width: 24px; height: 24px; fill: currentColor; margin-left: 10px;" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            الرجوع لقائمة البحث
        </a>
    </div>
</div>
@endsection




