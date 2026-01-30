@extends('layouts.admin')

@section('title', 'لوحة التحكم - Medicare')
@section('page-id', 'dashboard')
@section('content-class', 'dashboard-content')

@section('content')
<div class="dash-title-block">
    <h1 class="page-title">لوحة التحكم</h1>
    <p class="page-subtitle">إحصائيات شاملة عن المستخدمين والمراكز الطبية</p>
</div>

<div class="cards dashboard-cards">
    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/admin/cards/users.svg') }}"/></div>
            <div>
                <div class="card__label">عدد المستخدمين</div>
                <div class="card__value">{{ number_format($stats['users_count']) }}</div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/admin/cards/medical-centers.svg') }}"/></div>
            <div>
                <div class="card__label">عدد المراكز الطبية</div>
                <div class="card__value">{{ number_format($stats['centers_count']) }}</div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/admin/cards/patients.svg') }}"/></div>
            <div>
                <div class="card__label">عدد المرضى</div>
                <div class="card__value">{{ number_format($stats['patients_count']) }}</div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/admin/cards/medicines.svg') }}"/></div>
            <div>
                <div class="card__label">عدد الأدوية</div>
                <div class="card__value">{{ number_format($stats['medicines_count']) }}</div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/admin/cards/points.svg') }}"/></div>
            <div>
                <div class="card__label">إجمالي النقاط المصروفة</div>
                <div class="card__value">{{ number_format($stats['total_points']) }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
