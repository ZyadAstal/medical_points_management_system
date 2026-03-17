@extends('layouts.doctor')

@section('title', 'البحث عن مريض - Medicare')
@section('page-id', 'search-patient')

@section('content')
<div class="search-patient-page-header">
    <h1 class="search-patient-title">البحث عن مريض</h1>
    <p class="search-patient-subtitle">ابحث عن مريض بالاسم أو رقم الهوية أو رقم الهاتف</p>
</div>

<div class="search-patient-box">
    <form method="GET" action="{{ route('doctor.patients.search') }}">
        <div class="search-patient-fields">
            <div class="sp-field">
                <label class="sp-label" for="sp-name">الاسم</label>
                <input class="sp-input" id="sp-name" name="name" type="text"
                       placeholder="أدخل اسم المريض ..."
                       value="{{ request('name') }}" />
            </div>

            <div class="sp-field">
                <label class="sp-label" for="sp-id">رقم الهوية</label>
                <input class="sp-input" id="sp-id" name="national_id" type="text"
                       placeholder="أدخل رقم الهوية..."
                       value="{{ request('national_id') }}" />
            </div>

            <div class="sp-field">
                <label class="sp-label" for="sp-phone">رقم الهاتف</label>
                <input class="sp-input" id="sp-phone" name="phone" type="text"
                       placeholder="أدخل رقم الهاتف..."
                       value="{{ request('phone') }}" />
            </div>
        </div>

        <div class="search-patient-actions" style="display:flex; align-items:center; gap:12px;">
            <button class="search-submit-btn" type="submit" style="width: auto; padding: 0 20px; gap: 8px;">
                <span class="sp-btn-text">بحث</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>

            <a href="{{ route('doctor.patients.search') }}" class="search-submit-btn" style="text-decoration:none; background:#64748b; width: auto; padding: 0 20px; gap: 8px;">
                <span class="sp-btn-text">إعادة تعيين</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            </a>
        </div>
    </form>
</div>

{{-- Results Section --}}
@if(isset($patients) && $patients->count() > 0)
<div class="search-results-section">
    <div class="search-results-header">
        <h2 class="search-results-title">نتائج البحث</h2>
        <span class="search-results-count">{{ $patients->count() }} {{ $patients->count() === 1 ? 'نتيجة' : 'نتائج' }}</span>
    </div>

    <div class="search-results-table-wrap">
        <table class="search-results-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم المريض</th>
                    <th>رقم الهوية</th>
                    <th>العنوان</th>
                    <th>رقم الهاتف</th>
                    <th>النقاط</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $index => $patient)
                <tr>
                    <td class="row-num">{{ $index + 1 }}</td>
                    <td class="patient-name-cell">
                        <div class="patient-avatar">{{ mb_substr($patient->full_name, 0, 1) }}</div>
                        <span>{{ $patient->full_name }}</span>
                    </td>
                    <td>{{ $patient->national_id }}</td>
                    <td>{{ $patient->address }}</td>
                    <td dir="ltr" style="text-align: center;">{{ $patient->phone }}</td>
                    <td>
                        <span class="points-badge">{{ $patient->points ?? 0 }} نقطة</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@elseif(request()->hasAny(['name', 'national_id', 'phone']))
<div class="search-no-results">
    <div class="no-results-icon">🔍</div>
    <h3>لا توجد نتائج مطابقة</h3>
    <p>جرّب البحث باستخدام كلمات مختلفة أو تحقق من صحة البيانات المدخلة</p>
</div>
@endif
@endsection

@push('styles')
    <link href="{{ asset('css/doctor/views/search-patient.css') }}" rel="stylesheet">
@endpush
