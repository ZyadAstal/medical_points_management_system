@extends('layouts.patient')

@section('title', 'لوحة التحكم - Medicare')
@section('page-id', 'dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/patient/dashboard.css') }}">
@endpush

@section('content')
<div class="dash-title-block" style="padding: 10px 0 30px 0;">
  <h2 class="page-title" style="font-family: 'Tajawal', sans-serif; font-size: 2.2rem; font-weight: 700; color: #012e52; margin-bottom: 5px;">نظرة على حالتك الصحية</h2>
  <p class="page-subtitle" style="font-family: 'Tajawal', sans-serif; font-size: 1.1rem; color: #718da6;">إدارة ومتابعة تدفّق المرضى داخل المركز الطبي</p>
</div>

<div class="stats">
    <div class="stat-carrd">
        <div class="stat-info"> 
            <h4>رصيد النقاط الحالي</h4>
            <div class="number">{{ number_format($points_balance) }}</div>
        </div>
        <div class="stat-icon">
           <img src="{{ asset('assets/patient/cards/points.svg') }}" alt="" style="width: 34px;">
        </div>    
    </div>
    <div class="stat-carrd">
        <div class="stat-info"> 
            <h4>إجمالي الوصفات</h4>
            <div class="number">{{ $prescripts_count }}</div>
        </div>
        <div class="stat-icon">
           <img src="{{ asset('assets/patient/cards/patients.svg') }}" alt="" style="width: 40px;">
        </div>
    </div>
    <div class="stat-carrd">
        <div class="stat-info"> 
            <h4>الأدوية المصروفة</h4>
            <div class="number">{{ $dispensed_medicines_count }}</div>
        </div>
        <div class="stat-icon">
            <img src="{{ asset('assets/patient/cards/medicines.svg') }}" alt="" style="width: 40px;">
        </div> 
    </div>
    <div class="stat-carrd">
        <div class="stat-info">
            <h4 style="font-size: 16px;">آخر مركز تعاملت معه</h4>
            <div class="number" style="font-size: 18px;">{{ $last_center }}</div>
        </div>
        <div class="stat-icon">
           <img src="{{ asset('assets/patient/cards/medical-centers.svg') }}" alt="" style="width: 40px;">
        </div>
    </div>
</div>

<div class="main-container">
    <div class="right-column white-card">
        <h2 class="card-header">آخر الوصفات الطبية</h2> 
        <div class="prescription-list">
            @forelse($recent_prescriptions as $prescription)
                @php
                    $allItems = $prescription->items;
                    $dispensedCount = $allItems->where('is_dispensed', true)->count();
                    $statusClass = 's-not-dispensed';
                    $statusText = 'لم تُصرف';
                    
                    if ($dispensedCount > 0) {
                        if ($dispensedCount == $allItems->count()) {
                            $statusClass = 's-complete';
                            $statusText = 'صُرفت بالكامل';
                        } else {
                            $statusClass = 's-partial';
                            $statusText = 'صُرفت جزئياً';
                        }
                    }
                @endphp
                <div class="prescription-row">
                    <div class="icon-graph-container">
                        <i class="far fa-file-alt"></i>
                    </div>
                    <div class="doctor-details">
                        <h4>د. {{ $prescription->doctor->name }}</h4>
                        <span>{{ $prescription->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="status-badge {{ $statusClass }}">{{ $statusText }}</div>
                </div>
            @empty
                <p style="text-align: center; color: #888;">لا توجد وصفات طبية حتى الآن.</p>
            @endforelse
        </div>
        <a href="{{ route('patient.prescriptions.index') }}" class="view-all-link">عرض كافة الوصفات</a>
    </div>

    <div class="left-column">  
        <div class="white-card" style="padding-bottom: 50px;">
            <h2 class="card-header">استعلام سريع عن دواء</h2>               
            <div class="blue-box">
                <div class="pills-bg-icon">&#128138;</div>                    
                <div class="search-content">
                    <div class="search-title">هل تبحث عن دواء محدد؟</div>
                    <p class="search-text">يمكنك الاستعلام عن توفر الدواء في المراكز الطبية القريبة منك دون الحاجة لزيارة المركز.</p>
                    <a href="{{ route('patient.medicines.search') }}" class="start-search-btn" style="text-decoration: none; text-align: center;">ابدأ البحث الآن</a>
                </div>
            </div>
        </div>
        <div class="health-alert-box">
            <i class="fa-solid fa-circle-exclamation alert-icon"></i>
            <div class="alert-text">
                <strong>تنبيه صحي :</strong><br>
                يرجى الالتزام بالجرعات المحددة من قبل الطبيب. في حال شعرت بأعراض جانبية، تواصل مع المركز فوراً.
            </div>
        </div>
    </div>
</div>
@endsection
