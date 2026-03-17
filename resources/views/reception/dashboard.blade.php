@extends('layouts.reception')

@section('title', 'لوحة التحكم - Medicare')
@section('page-id', 'dashboard')
@section('content-class', 'dashboard-content')

@section('content')
<div class="dash-title-block">
    <h1 class="page-title">لوحة التحكم</h1>
    <p class="page-subtitle">إدارة ومتابعة تدفق المرضى داخل المركز الطبي</p>
</div>

<div class="reception-dashboard-cards">
    <div class="reception-dashboard-card is-operations">
        <div class="reception-dashboard-card__top"></div>
        <div class="reception-dashboard-card__body">
            <div class="reception-dashboard-card__icon-box">
                <img src="{{ asset('assets/reception/cards/number-of-exchange-operations.svg') }}" alt="">
            </div>
            <div class="reception-dashboard-card__text">
                <div class="reception-dashboard-card__label">عمليات الصرف اليوم</div>
                <div class="reception-dashboard-card__value">{{ $dispensesCountToday }}</div>
            </div>
        </div>
    </div>

    <div class="reception-dashboard-card is-points">
        <div class="reception-dashboard-card__top"></div>
        <div class="reception-dashboard-card__body">
            <div class="reception-dashboard-card__icon-box">
                <img src="{{ asset('assets/reception/cards/points.svg') }}" alt="">
            </div>
            <div class="reception-dashboard-card__text">
                <div class="reception-dashboard-card__label">النقاط المصروفة</div>
                <div class="reception-dashboard-card__value">{{ $pointsDispensedToday }}</div>
            </div>
        </div>
    </div>

    <div class="reception-dashboard-card is-partial">
        <div class="reception-dashboard-card__top"></div>
        <div class="reception-dashboard-card__body">
            <div class="reception-dashboard-card__icon-box">
                <img src="{{ asset('assets/reception/icons/partialRecipes.svg') }}" alt="">
            </div>
            <div class="reception-dashboard-card__text">
                <div class="reception-dashboard-card__label">الوصفات الجزئية</div>
                <div class="reception-dashboard-card__value">{{ $partialPrescriptions }}</div>
            </div>
        </div>
    </div>

    <div class="reception-dashboard-card is-stock">
        <div class="reception-dashboard-card__top"></div>
        <div class="reception-dashboard-card__body">
            <div class="reception-dashboard-card__icon-box">
                <img src="{{ asset('assets/reception/icons/stock.svg') }}" alt="">
            </div>
            <div class="reception-dashboard-card__text">
                <div class="reception-dashboard-card__label">أدوية قاربت على النفاذ</div>
                <div class="reception-dashboard-card__value">{{ $lowStockCount }}</div>
            </div>
        </div>
    </div>
</div>

<div class="reception-dashboard-panels">
    <div class="reception-dashboard-panel reception-dashboard-panel--large">
        <div class="dashboard-movements">
            <h2 class="dashboard-movements__title">أول 3 مرضى في الدور</h2>

            <div class="dashboard-movements__list">
                @forelse($recentVisits as $visit)
                    @php
                        $statusClass = match($visit->status) {
                            'in_progress' => 'dashboard-movement-card__status--doctor',
                            'waiting'     => 'dashboard-movement-card__status--waiting',
                            'completed'   => 'dashboard-movement-card__status--exit',
                            default       => 'dashboard-movement-card__status--waiting',
                        };
                        $statusLabel = match($visit->status) {
                            'in_progress' => 'دخل للطبيب',
                            'waiting'     => 'بانتظار',
                            'completed'   => 'خرج',
                            'cancelled'   => 'ملغي',
                            default       => 'بانتظار',
                        };
                    @endphp
                    <div class="dashboard-movement-card">
                        <div class="dashboard-movement-card__order">{{ $loop->iteration }}</div>
                        <div class="dashboard-movement-card__person">
                            <div class="dashboard-movement-card__patient">{{ $visit->patient->full_name ?? 'غير معروف' }}</div>
                            <div class="dashboard-movement-card__doctor">د. {{ $visit->doctor->name ?? 'غير محدد' }}</div>
                        </div>
                        <div class="dashboard-movement-card__status {{ $statusClass }}">{{ $statusLabel }}</div>
                    </div>
                @empty
                    <div style="text-align:center; color:#6184A0; padding: 30px 0;">لا توجد حركات اليوم</div>
                @endforelse
            </div>

            <a href="{{ route('reception.visits.waiting') }}" class="dashboard-movements__more">عرض القائمة كاملة</a>
        </div>
    </div>

    <div class="reception-dashboard-panel reception-dashboard-panel--small">
        <div class="dashboard-actions">
            <h2 class="dashboard-actions__title">إجراءات الاستقبال</h2>
            <p class="dashboard-actions__desc">يمكنك تسجيل مريض جديد أو تحويل مريض سابق إلى عيادة الطبيب مباشرة لتنظيم الدور.</p>
            <div class="dashboard-actions__buttons">
                <a href="{{ route('reception.patients.create') }}" class="dashboard-actions__button dashboard-actions__button--new">مريض جديد</a>
                <a href="{{ route('reception.patients.index') }}" class="dashboard-actions__button dashboard-actions__button--transfer">تحويل مريض</a>
            </div>
        </div>
    </div>
</div>
@endsection
