@extends('layouts.doctor')

@section('title', 'مرضى اليوم - Medicare')
@section('page-id', 'today-patients')

@section('content')
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

    <div class="today-search" style="width:280px;height:39px;">
        <input class="today-search-input" type="text" id="todaySearchInput" placeholder="ابحث باسم المريض أو رقم الهوية..."
            style="width:280px;height:39px;border:1px solid #053052;border-radius:5px;box-sizing:border-box;background:#FFFFFF;font-family:Inter, sans-serif;font-weight:400;font-size:14px;line-height:39px;text-align:center;color:#053052;outline:none;" />
    </div>
</div>

<style>
    .queue-action-btn {
        padding: 6px 15px;
        border-radius: 5px;
        font-size: 14px;
        cursor: pointer;
        border: none;
        color: white;
        font-family: 'Inter', sans-serif;
        text-decoration: none;
        display: inline-block;
        margin: 0 2px;
    }
    .btn-enter { background-color: #28a745; }
    .btn-wait { background-color: #ffc107; color: #053052; }
    .btn-complete { background-color: #053052; }
    .queue-action-btn:hover { opacity: 0.8; }
    .table-section-title {
        margin: 30px 20px 15px 0;
        font-size: 1.2rem;
        color: #053052;
        font-weight: bold;
        text-align: right;
    }
    .status-badge.status-gray { background-color: #f0f0f0; color: #666; border: 1px solid #ddd; }
    .patients-td, .patients-th {
        text-align: center !important;
    }
</style>

<div class="table-section-title">قائمة الانتظار في الدور</div>
<div class="patients-table">
    <div class="patients-table-header">
        <div class="patients-th">اسم المريض</div>
        <div class="patients-th">الرقم الوطني</div>
        <div class="patients-th">نوع الحالة</div>
        <div class="patients-th">الإجراء</div>
    </div>

    <div class="patients-table-body" id="queueTableBody">

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
                <div class="patients-td patients-action-cell" style="display: flex; gap: 5px; justify-content: center; align-items: center;">
                    @if($visit->status === \App\Models\Visit::STATUS_WAITING)
                        <form action="{{ route('doctor.visits.enter', $visit) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="queue-action-btn btn-enter">إدخال</button>
                        </form>
                        <button type="button" class="queue-action-btn btn-wait">انتظار</button>
                    @elseif($visit->status === \App\Models\Visit::STATUS_IN_PROGRESS)
                        <span style="font-weight: bold; color: #28a745; margin-left: 10px;">داخل العيادة</span>
                        <form action="{{ route('doctor.visits.complete', $visit) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="queue-action-btn btn-complete">تم الفحص</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="patients-tr"><div class="patients-td" style="grid-column: 1/-1; text-align:center;">لا يوجد مرضى في قائمة الانتظار</div></div>
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
            <div class="patients-tr"><div class="patients-td" style="grid-column: 1/-1; text-align:center;">لا يوجد مرضى مفحوصين لهذا اليوم</div></div>
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
                                    <td colspan="5" style="text-align:center;">اختر مريضاً لعرض السجل</td>
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
@endsection

@push('scripts')
<script>
(function () {
    // Date picker icon
    const input = document.getElementById('todayDate');
    const icon = document.getElementById('todayDateIcon');
    if (input && icon) {
        function openPicker() {
            if (typeof input.showPicker === 'function') {
                input.showPicker();
            } else {
                input.focus();
                input.click();
            }
        }
        icon.addEventListener('click', openPicker);
        icon.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openPicker();
            }
        });

        // Reload page with selected date
        input.addEventListener('change', function () {
            const date = input.value;
            if (date) {
                window.location.href = '{{ route("doctor.patients.index") }}?date=' + date;
            }
        });
    }

    const searchInput = document.getElementById('todaySearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = searchInput.value.trim().toLowerCase();
            const rows = document.querySelectorAll('.patients-tr');
            rows.forEach(function (row) {
                const nameTd = row.querySelector('.patients-td:nth-child(1)');
                const nidTd = row.querySelector('.patients-td:nth-child(2)');
                if (!nameTd || !nidTd) return;
                const match = nameTd.textContent.toLowerCase().includes(q) || nidTd.textContent.includes(q);
                row.style.display = match ? '' : 'none';
            });
        });
    }

    // Modal
    const modalOverlay = document.getElementById('patientModalOverlay');
    if (!modalOverlay) return;

    const closeBtn = document.getElementById('patientModalCloseBtn');

    function openModal(data) {
        document.getElementById('modalPatientName').textContent = data.name || '—';
        document.getElementById('modalNationalId').textContent = data.nid || '—';
        document.getElementById('modalPhone').textContent = data.phone || '—';
        const remainingPoints = parseInt(data.points) || 0;
        document.getElementById('modalPoints').textContent = remainingPoints;
        document.getElementById('modalUsedPoints').textContent = 100 - remainingPoints;

        // Fetch patient details via AJAX
        if (data.id) {
            fetch('{{ url("doctor/patients") }}/' + data.id + '?json=1')
                .then(r => r.json())
                .then(info => {
                    document.getElementById('modalTotalPrescriptions').textContent = info.total_prescriptions || '0';
                    document.getElementById('modalTotalMeds').textContent = info.total_meds || '0';
                    document.getElementById('modalTotalPoints').textContent = info.total_points || '0';
                    document.getElementById('modalLastDate').textContent = info.last_visit_date || '—';
                    document.getElementById('modalLastCenter').textContent = info.last_visit_center || '—';
                    document.getElementById('modalLastDoctor').textContent = info.last_visit_doctor || '—';

                    // Fill history table
                    const tbody = document.getElementById('historyTableBody');
                    if (info.dispense_history && info.dispense_history.length > 0) {
                        tbody.innerHTML = info.dispense_history.map(h =>
                            `<tr><td class="drug">${h.medicine}</td><td>${h.quantity}</td><td>${h.points}</td><td>${h.date}</td><td class="pharm">${h.pharmacist}</td></tr>`
                        ).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">لا يوجد سجل صرف</td></tr>';
                    }
                })
                .catch(() => {});
        }

        modalOverlay.classList.add('is-open');
        modalOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modalOverlay.classList.remove('is-open');
        modalOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.patients-action-btn');
        if (!btn) return;
        const tr = btn.closest('.patients-tr');
        if (!tr) return;
        openModal({
            id: tr.dataset.patientId,
            name: tr.dataset.patientName,
            nid: tr.dataset.patientNid,
            phone: tr.dataset.patientPhone,
            points: tr.dataset.patientPoints
        });
    });

    modalOverlay.addEventListener('click', function (e) {
        if (e.target === modalOverlay) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalOverlay.classList.contains('is-open')) closeModal();
    });

    // Modal tabs
    const tabs = Array.from(modalOverlay.querySelectorAll('.patient-modal-tab'));
    const panes = Array.from(modalOverlay.querySelectorAll('.patient-modal-tabpane'));
    tabs.forEach(t => {
        t.addEventListener('click', () => {
            const tabName = t.getAttribute('data-tab');
            tabs.forEach(tb => {
                tb.classList.toggle('is-active', tb.getAttribute('data-tab') === tabName);
                tb.setAttribute('aria-selected', tb.getAttribute('data-tab') === tabName ? 'true' : 'false');
            });
            panes.forEach(p => p.classList.toggle('is-active', p.getAttribute('data-pane') === tabName));
        });
    });
})();
</script>
@endpush
