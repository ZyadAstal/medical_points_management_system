@extends('layouts.doctor')

@section('title', 'مرضى اليوم - Medicare')
@section('page-id', 'today-patients')

@push('styles')
    <link href="{{ asset('css/doctor/views/today-patients.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="today-patients-wrapper" data-index-url="{{ route('doctor.patients.index') }}" data-patient-base-url="{{ url('doctor/patients') }}">
<div class="today-page-header">
    <h1 class="today-page-title">مرضى اليوم</h1>
    <p class="today-page-subtitle">قائمة المرضى المسجلين لليوم</p>
</div>

<div class="today-filters">
    <div class="today-date-filter">
        <label class="today-filter-label" for="todayDate">التاريخ</label>
        <div class="today-date-input">
            <div aria-label="اختيار التاريخ" class="today-date-icon" id="todayDateIcon" role="button" tabindex="0">
                <img alt="" src="{{ asset('assets/doctor/icons/date.svg') }}" />
            </div>
            <div aria-hidden="true" class="today-date-divider"></div>
            <input class="today-date-field" id="todayDate" type="date" value="{{ $selectedDate ?? now()->format('Y-m-d') }}" />
        </div>
    </div>

    <div class="today-search">
        <input class="today-search-input" type="text" id="todaySearchInput" placeholder="ابحث باسم المريض أو رقم الهوية..." />
        <button type="button" class="search-submit-btn" aria-label="بحث" title="بحث" id="todaySearchBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
    </div>
</div>

<div class="table-section-title">قائمة الانتظار في الدور</div>
<div class="patients-table">
    <div class="patients-table-header">
        <div class="patients-th">اسم المريض</div>
        <div class="patients-th">الرقم الوطني</div>
        <div class="patients-th">نوع الحالة</div>
        <div class="patients-th">الإجراء</div>
    </div>

    <div class="patients-table-body" id="queueTableBody">
        <div class="patients-vline v1"></div>
        <div class="patients-vline v2"></div>
        <div class="patients-vline v3"></div>

        @forelse($queueVisits as $visit)
            @php $patient = $visit->patient; @endphp
            <div class="patients-tr" data-patient-id="{{ $patient->id }}">
                <div class="patients-td">{{ $patient->name }}</div>
                <div class="patients-td">{{ $patient->national_id }}</div>
                <div class="patients-td">
                    @if($visit->priority == \App\Models\Visit::PRIORITY_EMERGENCY)
                        <span class="status-badge status-red">حالة طارئة</span>
                    @else
                        <span class="status-badge status-green">عادية</span>
                    @endif
                </div>
                <div class="patients-td patients-action-cell">
                    @if($visit->status === \App\Models\Visit::STATUS_REGISTERED)
                        <button type="button" class="queue-action-btn btn-disabled-queue" disabled>
                            بانتظار الاستقبال
                        </button>
                    @elseif($visit->status === \App\Models\Visit::STATUS_WAITING)
                        <form action="{{ route('doctor.visits.enter', $visit) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="queue-action-btn btn-enter">بدء الكشف</button>
                        </form>
                    @elseif($visit->status === \App\Models\Visit::STATUS_IN_PROGRESS)
                        <span style="font-weight: bold; color: #28a745; margin-left: 10px;">يتم الفحص</span>
                        <form action="{{ route('doctor.visits.complete', $visit) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="queue-action-btn btn-complete">إنهاء الكشف</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="patients-tr"><div class="patients-td" style="grid-column: 1/-1;">لا يوجد مرضى في قائمة الانتظار</div></div>
        @endforelse
    </div>
</div>

<div class="table-section-title">المرضى الذين تم فحصهم</div>
<div class="patients-table">
    <div class="patients-table-header">
        <div class="patients-th">اسم المريض</div>
        <div class="patients-th">الرقم الوطني</div>
        <div class="patients-th">حالة الصرف</div>
        <div class="patients-th">الإجراء</div>
    </div>

    <div class="patients-table-body" id="pastTableBody">
        <div class="patients-vline v1"></div>
        <div class="patients-vline v2"></div>
        <div class="patients-vline v3"></div>

        @forelse($pastVisits as $visit)
            @php
                $patient = $visit->patient;
                $prescriptions = $patient->prescriptions()
                    ->whereDate('created_at', $visit->visit_date)
                    ->with('items.dispenses')
                    ->get();

                if ($prescriptions->isEmpty()) {
                    $statusClass = 'status-gray';
                    $statusText = 'معاينة فقط';
                } else {
                    $allItems = $prescriptions->flatMap->items;
                    $dispensedCount = $allItems->where('is_dispensed', true)->count();
                    // is_dispensed might not be updated yet, check dispenses table
                    $dispensedCountByMeds = $allItems->filter(fn($item) => $item->dispenses->count() > 0)->count();

                    if ($dispensedCountByMeds === 0) {
                        $statusClass = 'status-red';
                        $statusText = 'لم يتم الصرف';
                    } elseif ($dispensedCountByMeds === $allItems->count()) {
                        $statusClass = 'status-green';
                        $statusText = 'تم الصرف';
                    } else {
                        $statusClass = 'status-yellow';
                        $statusText = 'صرف جزئي';
                    }
                }
            @endphp
            <div class="patients-tr"
                 data-patient-id="{{ $patient->id }}"
                 data-patient-name="{{ $patient->name }}"
                 data-patient-nid="{{ $patient->national_id }}"
                 data-patient-phone="{{ $patient->phone ?? '—' }}"
                 data-patient-points="{{ $patient->points ?? 0 }}">
                <div class="patients-td">{{ $patient->name }}</div>
                <div class="patients-td">{{ $patient->national_id }}</div>
                <div class="patients-td">
                    <div class="status-badge {{ $statusClass }}">{{ $statusText }}</div>
                </div>
                <div class="patients-td patients-action-cell">
                    <button type="button" class="patients-action-btn" aria-label="عرض تفاصيل المريض">
                        <img src="{{ asset('assets/doctor/icons/eye.svg') }}" alt="">
                    </button>
                </div>
            </div>
        @empty
            <div class="patients-tr"><div class="patients-td" style="grid-column: 1/-1;">لا يوجد مرضى مفحوصين لهذا اليوم</div></div>
        @endforelse
    </div>
</div>

{{-- Patient Detail Modal --}}
<div aria-hidden="true" class="patient-modal-overlay" id="patientModalOverlay">
    <div aria-label="تفاصيل المريض" aria-modal="true" class="patient-modal" role="dialog">
        <h2 class="patient-modal-title">تفاصيل المريض</h2>
        <div aria-hidden="true" class="patient-modal-divider"></div>

        <div class="patient-modal-card">
            <div class="patient-info-grid">
                <div class="patient-info-item">
                    <div class="patient-info-label">اسم المريض</div>
                    <div class="patient-info-value" id="modalPatientName">—</div>
                </div>
                <div class="patient-info-item">
                    <div class="patient-info-label">رقم الهوية</div>
                    <div class="patient-info-value patient-info-value-90" id="modalNationalId">—</div>
                </div>
                <div class="patient-info-item">
                    <div class="patient-info-label">رقم الهاتف</div>
                    <div class="patient-info-value" id="modalPhone">—</div>
                </div>
                <div class="patient-info-item">
                    <div class="patient-info-label">النقاط المستخدمة (من 100)</div>
                    <div class="patient-info-value" id="modalUsedPoints">—</div>
                </div>
                <div class="patient-info-item">
                    <div class="patient-info-label">النقاط المتبقية</div>
                    <div class="patient-info-value" id="modalPoints">—</div>
                </div>
            </div>
        </div>

        <div class="patient-modal-card patient-modal-card-lg">
            <div aria-label="تبويبات تفاصيل المريض" class="patient-modal-tabs" role="tablist">
                <button aria-selected="true" class="patient-modal-tab is-active" data-tab="overview" role="tab" type="button">نظرة عامة</button>
                <button aria-selected="false" class="patient-modal-tab" data-tab="history" role="tab" type="button">سجل الصرف</button>
            </div>
            <div aria-hidden="true" class="patient-modal-tabs-divider"></div>

            <div class="patient-modal-tabpanes">
                <div class="patient-modal-tabpane is-active" data-pane="overview" role="tabpanel">
                    <div class="overview-cards" id="modalOverviewCards">
                        <div class="overview-card overview-card-prescriptions">
                            <div class="overview-card-title">إجمالي الوصفات</div>
                            <div class="overview-card-value" id="modalTotalPrescriptions">0</div>
                        </div>
                        <div class="overview-card overview-card-meds">
                            <div class="overview-card-title">إجمالي الأدوية المصروفة</div>
                            <div class="overview-card-value" id="modalTotalMeds">0</div>
                        </div>
                        <div class="overview-card overview-card-points">
                            <div class="overview-card-title">إجمالي النقاط المستخدمة</div>
                            <div class="overview-card-value" id="modalTotalPoints">0</div>
                        </div>
                        <div aria-hidden="false" class="overview-last-visit">
                            <div class="last-visit-title">آخر زيارة</div>
                            <div class="last-visit-content">
                                <div class="last-visit-labels">
                                    <div class="lv-label">التاريخ</div>
                                    <div class="lv-label">المركز الطبي</div>
                                    <div class="lv-label">الطبيب</div>
                                </div>
                                <div class="last-visit-values">
                                    <div class="lv-value lv-date" id="modalLastDate">—</div>
                                    <div class="lv-value lv-center" id="modalLastCenter">—</div>
                                    <div class="lv-value lv-pharmacist" id="modalLastDoctor">—</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="patient-modal-tabpane" data-pane="history" role="tabpanel">
                    <div class="history-title">سجل الأدوية المصروفة</div>
                    <div aria-label="سجل الأدوية المصروفة" class="history-table-wrap">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th class="w-drug">اسم الدواء</th>
                                    <th class="w-qty">الكمية</th>
                                    <th class="w-points">النقاط</th>
                                    <th class="w-date">التاريخ</th>
                                    <th class="w-pharm">الصيدلي</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <tr>
                                    <td colspan="5">اختر مريضاً لعرض السجل</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="patient-modal-footer">
            <button class="patient-modal-close" id="patientModalCloseBtn" type="button">إغلاق</button>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/doctor/views/today-patients.js') }}"></script>
@endpush
