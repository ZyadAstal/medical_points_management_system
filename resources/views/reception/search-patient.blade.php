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

                <div class="search-patient-result-actions" style="display:flex; flex-direction:column; gap:8px; top:15px;">
                    <a href="{{ route('reception.visits.create', $patient) }}"
                        class="search-patient-action-button search-patient-search-button"
                        style="text-decoration:none; font-size:13px; padding:0 16px; height:32px; border:none; border-radius:5px; align-items:center; justify-content:center; display:flex;">
                        + إضافة لقائمة الانتظار
                    </a>
                    <button type="button"
                        class="search-patient-action-button search-patient-reset-button"
                        style="font-size:13px; padding:0 16px; height:32px; border:none; border-radius:5px; cursor:pointer; align-items:center; justify-content:center; display:flex;"
                        onclick="openEditPatientModal({{ $patient->id }}, '{{ addslashes($patient->full_name) }}', '{{ $patient->national_id }}', '{{ $patient->phone }}', '{{ $patient->address }}', {{ $patient->points }})">
                        ✏️ تعديل البيانات
                    </button>
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

@push('scripts')
{{-- Modal تعديل بيانات المريض --}}
<div id="editPatientOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:10px; box-shadow:0 8px 32px rgba(0,0,0,0.2); width:460px; max-width:95vw; padding:36px 36px 28px; direction:rtl; position:relative;">
        <h2 style="font-family:Inter,sans-serif; font-size:22px; font-weight:700; color:#053052; margin:0 0 6px; text-align:center;">تعديل بيانات المريض</h2>
        <div style="border-top:1px solid #e0e0e0; margin:0 0 22px;"></div>

        @if(session('success'))
            <div style="background:#f0fff4; border:1px solid #38a169; border-radius:5px; color:#276749; padding:8px 14px; margin-bottom:14px; font-size:13px; text-align:right;">
                {{ session('success') }}
            </div>
        @endif

        <form id="editPatientForm" method="POST" action="">
            @csrf
            @method('PUT')

            <input type="hidden" id="editPatientId" name="_patient_id">

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:14px; font-weight:500; color:#444; margin-bottom:5px;">الاسم الكامل</label>
                <input id="editFullName" name="full_name" type="text" required
                    style="width:100%; height:36px; border:1px solid #ccc; border-radius:5px; padding:0 12px; font-size:14px; box-sizing:border-box;"/>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:14px; font-weight:500; color:#444; margin-bottom:5px;">رقم الهوية</label>
                <input id="editNationalId" name="national_id" type="text" required
                    style="width:100%; height:36px; border:1px solid #ccc; border-radius:5px; padding:0 12px; font-size:14px; box-sizing:border-box;"/>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:14px; font-weight:500; color:#444; margin-bottom:5px;">رقم الهاتف</label>
                <input id="editPhone" name="phone" type="text"
                    style="width:100%; height:36px; border:1px solid #ccc; border-radius:5px; padding:0 12px; font-size:14px; box-sizing:border-box;"/>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:14px; font-weight:500; color:#444; margin-bottom:5px;">العنوان</label>
                <input id="editAddress" name="address" type="text"
                    style="width:100%; height:36px; border:1px solid #ccc; border-radius:5px; padding:0 12px; font-size:14px; box-sizing:border-box;"/>
            </div>

            <div style="margin-bottom:22px;">
                <label style="display:block; font-size:14px; font-weight:500; color:#053052; margin-bottom:5px;">النقاط 🏅</label>
                <input id="editPoints" name="points" type="number" min="0"
                    style="width:100%; height:36px; border:1.5px solid #053052; border-radius:5px; padding:0 12px; font-size:14px; box-sizing:border-box;"/>
            </div>

            <div style="display:flex; gap:12px; justify-content:center;">
                <button type="submit"
                    style="width:160px; height:40px; background:#053052; color:#fff; border:none; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer;">
                    حفظ التعديلات
                </button>
                <button type="button" onclick="closeEditPatientModal()"
                    style="width:130px; height:40px; background:#7a7a7a; color:#fff; border:none; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer;">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>
@endpush
