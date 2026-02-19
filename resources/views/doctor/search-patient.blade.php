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

        <div class="search-patient-actions">
            <button class="sp-btn sp-btn-search" type="submit">
                <span class="sp-btn-text">بحث</span>
                <img class="sp-btn-icon" src="{{ asset('assets/doctor/icons/search.svg') }}" alt="" />
            </button>

            <a href="{{ route('doctor.patients.search') }}" class="sp-btn sp-btn-reset" style="text-decoration:none;">
                <span class="sp-btn-text">إعادة تعيين</span>
                <img class="sp-btn-icon" src="{{ asset('assets/doctor/icons/Reset.svg') }}" alt="" />
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
<style>
/* ===== Search Results Section ===== */
.search-results-section {
    margin-top: 28px;
    animation: fadeInUp 0.4s ease;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(15px); }
    to   { opacity: 1; transform: translateY(0); }
}

.search-results-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    direction: rtl;
}

.search-results-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--navy, #0b3a5a);
    margin: 0;
}

.search-results-count {
    background: var(--navy, #0b3a5a);
    color: #fff;
    padding: 5px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
}

/* Table wrapper */
.search-results-table-wrap {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.search-results-table {
    width: 100%;
    border-collapse: collapse;
    direction: rtl;
    text-align: right;
}

.search-results-table thead th {
    background: linear-gradient(135deg, #053052 0%, #0b3a5a 100%);
    color: #fff;
    padding: 14px 16px;
    font-size: 14px;
    font-weight: 700;
    border-left: 1px solid rgba(255,255,255,0.12);
    white-space: nowrap;
}

.search-results-table thead th:last-child {
    border-left: none;
}

.search-results-table tbody tr {
    transition: background 0.2s ease;
}

.search-results-table tbody tr:hover {
    background: #f0f7ff;
}

.search-results-table tbody tr:nth-child(even) {
    background: #f8fafc;
}

.search-results-table tbody tr:nth-child(even):hover {
    background: #f0f7ff;
}

.search-results-table tbody td {
    padding: 14px 16px;
    font-size: 14px;
    color: #334155;
    border-top: 1px solid #e8edf2;
    border-left: 1px solid #e8edf2;
    vertical-align: middle;
}

.search-results-table tbody td:last-child {
    border-left: none;
}

/* Row number */
.row-num {
    color: #94a3b8;
    font-weight: 700;
    text-align: center !important;
    width: 40px;
}

/* Patient name with avatar */
.patient-name-cell {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 700;
    color: #0f172a;
}

.patient-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #053052 0%, #1e6091 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 800;
    flex-shrink: 0;
}

/* Points badge */
.points-badge {
    display: inline-block;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}

/* No results state */
.search-no-results {
    text-align: center;
    margin-top: 50px;
    padding: 40px 20px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.05);
    animation: fadeInUp 0.4s ease;
}

.no-results-icon {
    font-size: 48px;
    margin-bottom: 12px;
}

.search-no-results h3 {
    font-size: 18px;
    font-weight: 800;
    color: var(--navy, #0b3a5a);
    margin: 0 0 8px;
}

.search-no-results p {
    color: #64748b;
    font-size: 14px;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .search-results-table-wrap {
        overflow-x: auto;
    }
    .search-results-table {
        min-width: 700px;
    }
    .search-results-header {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
}
</style>
@endpush
