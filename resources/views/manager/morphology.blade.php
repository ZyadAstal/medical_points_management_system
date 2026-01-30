@extends('layouts.manager')

@section('title', 'عمليات الصرف - Medicare')
@section('page-id', 'medical-centers')

@push('styles')
    <link href="{{ asset('css/manager/pages-extra.css') }}" rel="stylesheet"/>
@endpush

@section('content')
<div class="medical-centers-header">
    <h1 class="medical-centers-title">عمليات الصرف </h1>
    <p class="medical-centers-desc">متابعة و توثيق عمليات صرف الادوية داخل المركز</p>

    <div class="filters">
        <input type="date">
        <select>
            <option value="">اختر الصيدلي</option>
            <option>أحمد يوسف</option>
            <option>سارة خالد</option>
            <option>محمود ناصر</option>
        </select>
        <select>
            <option value="">اختر المريض</option>
            <option>أحمد يوسف حسن</option>
            <option>سارة خالد إبراهيم</option>
            <option>محمود عبد الله ناصر</option>
        </select>
        <input type="text" placeholder="ابحث عن اسم المريض أو الدواء">
    </div>
</div>

<div class="mc-table-wrap" style="margin-top: 2rem;">
    <div class="mc-table">
        <div class="mc-table-head">
            <div class="mc-th">اسم المريض</div>
            <div class="mc-th"> اسم الدواء</div>
            <div class="mc-th">الكمية</div>
            <div class="mc-th">النقاط المصروفة</div>
            <div class="mc-th">الصيدلي </div>
            <div class="mc-th">التاريخ</div>
        </div>
        <div class="mc-table-body" id="mcTableBody">
            @foreach($dispenses as $dispense)
            <div class="mc-row">
                <div class="mc-td mc-name">{{ $dispense->prescriptionItem->prescription->patient->full_name }}</div>
                <div class="mc-td">{{ $dispense->prescriptionItem->medicine->name }}</div>
                <div class="mc-td">{{ $dispense->quantity }}</div>
                <div class="mc-td">{{ number_format($dispense->points_used) }}</div>
                <div class="mc-td">{{ $dispense->pharmacist->name }}</div>
                <div class="mc-td">{{ $dispense->created_at->format('Y/m/d') }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="mc-pagination">
    {{ $dispenses->links('pagination::bootstrap-4') }}
</div>

@include('shared.manager-user-modals')
@endsection

@push('scripts')
    <script src="{{ asset('js/manager/medical-centers-modals.js') }}"></script>
@endpush
