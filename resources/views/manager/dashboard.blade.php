@extends('layouts.manager')

@section('title', 'لوحة التحكم - Medicare')
@section('page-id', 'dashboard')
@section('content-class', 'dashboard-content')

@section('content')
<div class="dash-title-block">
    <h1 class="page-title">لوحة التحكم</h1>
    <p class="page-subtitle">نظرة عامة على أداء المركز الطبي</p>
</div>

<div class="cards dashboard-cards">
    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/manager/cards/patients.svg') }}" /></div>
            <div>
                <div class="card__label">عدد المرضى</div>
                <div class="card__value">{{ number_format($patients_count) }}</div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/manager/cards/20748a65b51d4f56f1622328b7922a950c25b265.png') }}" /></div>
            <div>
                <div class="card__label">عدد الصيادلة</div>
                <div class="card__value">{{ number_format($pharmacists_count) }}</div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/manager/cards/2a8c783548a60568857121842c5b0647d93cc5f1.png') }}" /></div>
            <div>
                <div class="card__label">عدد الأطباء</div>
                <div class="card__value">{{ number_format($doctors_count) }}</div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/manager/cards/cc4cbb8f4d4896675e0ded8a86afae00c447d764.png') }}" /></div>
            <div>
                <div class="card__label">عمليات الصرف اليوم</div>
                <div class="card__value">{{ number_format($dispensed_today) }}</div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/manager/cards/points.svg') }}" /></div>
            <div>
                <div class="card__label">النقاط المصروفة اليوم</div>
                <div class="card__value">{{ number_format($points_today) }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
