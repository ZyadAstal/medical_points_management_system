@extends('layouts.doctor')

@section('title', 'لوحة التحكم - Medicare')
@section('page-id', 'dashboard')

@section('content')
<div class="page-headings">
    <h1 class="page-title">لوحة التحكم</h1>
    <p class="page-subtitle">نظرة عامة على مرضى اليوم والوصفات</p>
</div>

<div class="dashboard-cards-row">
    <div class="dashboard-card">
        <div class="dashboard-card-strip"></div>
        <div class="dashboard-card-icon-box">
            <img src="{{ asset('assets/doctor/cards/patients.svg') }}" alt="icon" />
        </div>
        <p class="dashboard-card-title">عدد المرضى اليوم</p>
        <p class="dashboard-card-number">{{ $todayPatientsCount ?? 0 }}</p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-strip"></div>
        <div class="dashboard-card-icon-box">
            <img src="{{ asset('assets/doctor/cards/Prescriptions-dispensed.svg') }}" alt="icon" />
        </div>
        <p class="dashboard-card-title">الوصفات المصروفة اليوم</p>
        <p class="dashboard-card-number">{{ $dispensedCount ?? 0 }}</p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-strip"></div>
        <div class="dashboard-card-icon-box slash">
            <img src="{{ asset('assets/doctor/cards/Prescriptions-dispensed.svg') }}" alt="icon" />
        </div>
        <p class="dashboard-card-title">الوصفات الغير مصروفة</p>
        <p class="dashboard-card-number">{{ $undispensedCount ?? 0 }}</p>
    </div>
</div>

<div class="dashboard-white-frame" id="updatesFrame">
    <div class="dashboard-updates-header">
        <div class="dashboard-updates-icon" aria-hidden="true">
            <img src="{{ asset('assets/doctor/icons/time-doc.svg') }}" alt="">
        </div>
        <h2 class="dashboard-updates-title">آخر التحديثات</h2>
    </div>

    <div class="dashboard-updates-list">
        @forelse($recentPrescriptions as $index => $prescription)
            @php
                $dispensed = $prescription->items->every(fn($item) => $item->dispenses->count() > 0);
                $partial = !$dispensed && $prescription->items->contains(fn($item) => $item->dispenses->count() > 0);
                $statusClass = $dispensed ? 'status-done' : ($partial ? 'status-partial' : 'status-none');
                $statusText = $dispensed ? 'تم الصرف' : ($partial ? 'صرف جزئي' : 'لم يصرف');
                $rowClass = $index < 3 ? 'row-' . ($index + 1) : 'row-extra hidden-row';
            @endphp
            <div class="dashboard-update-row {{ $rowClass }}">
                <img src="{{ asset('assets/doctor/icons/profile.svg') }}" class="update-profile-icon" alt="">
                <div class="update-status {{ $statusClass }}">{{ $statusText }}</div>
                <div class="update-text">
                    <div class="update-name">{{ $prescription->patient->name ?? '—' }}</div>
                    <div class="update-time">{{ $prescription->created_at->diffForHumans() }}</div>
                </div>
            </div>
        @empty
            <div class="dashboard-update-row">
                <div class="update-text">
                    <div class="update-name">لا توجد تحديثات حالياً</div>
                </div>
            </div>
        @endforelse
    </div>

    @if(isset($recentPrescriptions) && $recentPrescriptions->count() > 3)
    <div class="show-more-container">
        <button id="showMoreBtn" class="show-more-updates" type="button">عرض باقي التحديثات</button>
        <button id="showLessBtn" class="show-more-updates show-less-updates" type="button">إخفاء التحديثات</button>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    const moreBtn = document.getElementById('showMoreBtn');
    const lessBtn = document.getElementById('showLessBtn');
    if (!moreBtn || !lessBtn) return;

    const extras = document.querySelectorAll('.row-extra');

    moreBtn.addEventListener('click', function () {
        extras.forEach(r => r.classList.remove('hidden-row'));
        moreBtn.style.display = 'none';
        lessBtn.style.display = '';
    });

    lessBtn.addEventListener('click', function () {
        extras.forEach(r => r.classList.add('hidden-row'));
        lessBtn.style.display = 'none';
        moreBtn.style.display = '';
    });
})();
</script>
@endpush
