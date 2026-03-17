@extends('layouts.reception')

@section('title', 'البحث عن مريض - Medicare')
@section('page-id', 'search-patient')

@push('scripts')
    <script src="{{ asset('js/reception/views/search.js') }}"></script>
@endpush

@section('content')
<div class="search-patient-wrapper" data-search-reset-url="{{ route('reception.patients.index') }}">
<div class="search-patient-page-header reception-search-patient-page-header">
    <h1 class="search-patient-title reception-search-patient-title">البحث عن مريض</h1>
    <p class="search-patient-subtitle reception-search-patient-subtitle">ابحث عن مريض بالاسم أو رقم الهوية أو رقم الهاتف</p>
</div>

<form method="GET" action="{{ route('reception.patients.index') }}" id="searchForm">
    <div class="search-patient-box-panel">
        <div class="search-patient-fields-row">
            <div class="search-patient-field-group search-patient-field-name">
                <label class="search-patient-field-label" for="patient-name">الاسم</label>
                <input class="search-patient-field-input" id="patient-name" name="name" type="text"
                    placeholder="ادخل اسم المريض ..."
                    value="{{ request('name') }}" />
            </div>

            <div class="search-patient-field-group search-patient-field-id">
                <label class="search-patient-field-label" for="patient-id">رقم الهوية</label>
                <input class="search-patient-field-input" id="patient-id" name="national_id" type="text"
                    placeholder="ادخل رقم الهوية..."
                    value="{{ request('national_id') }}" />
            </div>

            <div class="search-patient-field-group search-patient-field-phone">
                <label class="search-patient-field-label" for="patient-phone">رقم الهاتف</label>
                <input class="search-patient-field-input" id="patient-phone" name="phone" type="text"
                    placeholder="ادخل رقم الهاتف..."
                    value="{{ request('phone') }}" />
            </div>

            <div class="search-patient-actions">
                <button class="search-patient-action-button search-patient-reset-button" type="button" id="resetBtn">
                    <span class="search-patient-action-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 12a8 8 0 1 1-2.34-5.66" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20 4v6h-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="search-patient-action-text">إعادة تعيين</span>
                </button>

                <button class="search-patient-action-button search-patient-search-button" type="submit">
                    <span class="search-patient-action-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="2"/>
                            <path d="M16 16L21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="search-patient-action-text">بحث</span>
                </button>
            </div>
        </div>
    </div>
</form>

{{-- نتائج البحث --}}
<div class="search-patient-results-panel">
    @if($searched)
        @forelse($patients as $patient)
            @php
                $lastVisit = $patient->visits->first();
                $todayVisit = $patient->visits()->whereDate('visit_date', today())->latest()->first();
                $prescStatus = 'لا توجد وصفة';
                $prescClass  = '';
                if ($todayVisit && $todayVisit->prescriptions()->exists()) {
                    $presc = $todayVisit->prescriptions()->latest()->first();
                    $hasPartial = $presc && $presc->items()->where('is_dispensed', false)->exists();
                    $prescStatus = $hasPartial ? 'وصفة جزئية' : 'تم الصرف';
                    $prescClass  = $hasPartial ? 'partial' : 'dispensed';
                }
            @endphp
            <div class="search-patient-result-card">
                <div class="search-patient-result-profile-box">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"/>
                        <path d="M4 21C4 17.6863 7.58172 15 12 15C16.4183 15 20 17.6863 20 21" fill="currentColor"/>
                    </svg>
                </div>

                <div class="search-patient-result-identity-block">
                    <div class="search-patient-result-name">{{ $patient->full_name }}</div>
                    <div class="search-patient-result-id">رقم الهوية : {{ $patient->national_id }}</div>
                </div>

                <div class="search-patient-result-visits-block">
                    <div class="search-patient-result-label">عدد الزيارات :</div>
                    <div class="search-patient-result-value">{{ $patient->visits_count }}</div>
                </div>

                <div class="search-patient-result-last-visit-block">
                    <div class="search-patient-result-label">اخر زيارة</div>
                    <div class="search-patient-result-value">
                        {{ $lastVisit ? \Carbon\Carbon::parse($lastVisit->visit_date)->format('d/m/Y') : 'لا توجد' }}
                    </div>
                </div>

                <div class="search-patient-result-prescription-block">
                    <div class="search-patient-result-label">حالة وصفة اليوم</div>
                    <div class="search-patient-result-prescription-pill {{ $prescClass }}">{{ $prescStatus }}</div>
                </div>

                <div class="search-patient-result-actions" style="display:flex; gap:8px; margin-top:12px;">
                    <a href="{{ route('reception.visits.create', $patient) }}"
                        class="search-patient-action-button search-patient-search-button"
                        style="text-decoration:none; font-size:13px; padding:8px 16px;">
                        + إضافة لقائمة الانتظار
                    </a>
                </div>
            </div>
        @empty
            <div class="search-patient-no-results" style="text-align:center; color:#6184A0; padding:40px 0;">
                <p>لم يتم العثور على نتائج للبحث</p>
            </div>
        @endforelse

        @if($patients instanceof \Illuminate\Pagination\LengthAwarePaginator && $patients->hasPages())
            <div style="margin-top:16px;">
                {{ $patients->links() }}
            </div>
        @endif
    @else
        <div class="search-patient-no-results" style="text-align:center; color:#6184A0; padding:40px 0;">
            <p>أدخل معايير البحث وانقر على زر "بحث"</p>
        </div>
    @endif
</div>
</div>
@endsection
