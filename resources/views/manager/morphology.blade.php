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

    <form action="{{ route('manager.dispensing.index') }}" method="GET" class="filters">
        <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()">
        
        <select name="pharmacist_id" onchange="this.form.submit()">
            <option value="">اختر الصيدلي</option>
            @foreach($pharmacists as $p)
                <option value="{{ $p->id }}" {{ request('pharmacist_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>

        <select name="patient_id" onchange="this.form.submit()">
            <option value="">اختر المريض</option>
            @foreach($patients as $patient)
                <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>{{ $patient->full_name }}</option>
            @endforeach
        </select>

        <input type="text" name="search" placeholder="ابحث عن اسم المريض أو الدواء" value="{{ request('search') }}" onkeyup="if(event.keyCode == 13) this.form.submit()">
    </form>
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
