@extends('layouts.doctor')

@section('title', 'سجل الوصفات - Medicare')
@section('page-id', 'recipes-record')

@push('styles')
<link href="{{ asset('css/doctor/recipes-record.css') }}" rel="stylesheet"/>
<style>
/* ===== Prescription Modal ===== */
.rx-modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.2s ease;
}
.rx-modal-overlay.open {
    display: flex;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
}

.rx-modal {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    width: min(600px, 92vw);
    max-height: 85vh;
    overflow-y: auto;
    animation: slideUp 0.3s ease;
    direction: rtl;
}

.rx-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    background: linear-gradient(135deg, #053052 0%, #0b3a5a 100%);
    color: #fff;
    border-radius: 16px 16px 0 0;
}

.rx-modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
}

.rx-modal-close {
    background: rgba(255,255,255,0.15);
    border: none;
    color: #fff;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.rx-modal-close:hover {
    background: rgba(255,255,255,0.3);
}

.rx-modal-meta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    padding: 16px 24px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-size: 13px;
    color: #475569;
}

.rx-modal-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.rx-modal-meta-item strong {
    color: #0f172a;
}

.rx-modal-body {
    padding: 20px 24px;
}

.rx-modal-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
}

.rx-modal-table thead th {
    background: #f1f5f9;
    color: #0f172a;
    padding: 12px 14px;
    font-weight: 700;
    font-size: 13px;
    text-align: right;
    border-bottom: 2px solid #e2e8f0;
    border-left: 1px solid #e2e8f0;
}

.rx-modal-table thead th:last-child {
    border-left: none;
}

.rx-modal-table tbody td {
    padding: 12px 14px;
    font-size: 13px;
    border-top: 1px solid #f1f5f9;
    border-left: 1px solid #f1f5f9;
    vertical-align: middle;
}

.rx-modal-table tbody td:last-child {
    border-left: none;
}

.rx-modal-table tbody tr:hover {
    background: #f8fafc;
}

.rx-status-dispensed {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    background: #dcfce7;
    color: #16a34a;
}

.rx-status-pending {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    background: #fef2f2;
    color: #dc2626;
}

.rx-modal-notes {
    margin-top: 16px;
    padding: 12px 16px;
    background: #fffbeb;
    border-radius: 10px;
    border: 1px solid #fde68a;
    font-size: 13px;
    color: #92400e;
}

.rx-modal-notes strong {
    color: #78350f;
}

.rx-modal-footer {
    padding: 14px 24px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: center;
}

.rx-modal-footer-btn {
    background: var(--navy, #0b3a5a);
    color: #fff;
    border: none;
    padding: 10px 32px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
}

.rx-modal-footer-btn:hover {
    background: #053052;
}

/* Responsive */
@media (max-width: 600px) {
    .rx-modal-meta {
        flex-direction: column;
        gap: 8px;
    }
}
</style>
@endpush

@section('content')
<div class="search-patient-page-header">
    <h1 class="search-patient-title">سجل الوصفات</h1>
    <p class="search-patient-subtitle">عرض الوصفات السابقة والأدوية المصروفة</p>
</div>

<div class="recipes-rectangles">
    <div class="recipes-rect rect-small">
        <h2 class="patients-title">المرضى</h2>
        <input aria-label="بحث عن مريض" class="patients-search" id="recipesPatientSearch" placeholder="بحث..." type="text"/>
        <div aria-hidden="true" class="patients-divider"></div>
        <div class="patients-list" role="list">
            @forelse($patients as $patient)
                <a href="{{ route('doctor.prescriptions.show', $patient->id) }}"
                   class="patient-btn @if(isset($selectedPatient) && $selectedPatient->id === $patient->id) is-active @endif"
                   role="listitem">{{ $patient->name }}</a>
            @empty
                <div style="text-align:center; padding:20px; color:#999;">لا يوجد مرضى</div>
            @endforelse
        </div>
    </div>

    <div class="recipes-left-area">
        @if(!isset($selectedPatient))
        <div class="recipes-rect rect-large recipes-empty-rect">
            <div class="recipes-empty">
                <img alt="" aria-hidden="true" class="recipes-empty-icon" src="{{ asset('assets/doctor/icons/file.svg') }}"/>
                <p class="recipes-empty-text">اختر مريضاً لعرض سجل الوصفات</p>
            </div>
        </div>
        @else
        <div class="recipes-list">
            @forelse($prescriptions as $prescription)
                @php
                    $allItems = $prescription->items;
                    $dispensedItems = $allItems->filter(fn($item) => $item->dispenses->count() > 0);
                    $isFullyDispensed = $dispensedItems->count() === $allItems->count() && $allItems->count() > 0;
                    $isPartial = $dispensedItems->count() > 0 && !$isFullyDispensed;

                    $statusClass = $isFullyDispensed ? 'recipe-status--paid' : ($isPartial ? 'recipe-status--partial' : 'recipe-status--unpaid');
                    $statusText = $isFullyDispensed ? 'تم الصرف' : ($isPartial ? 'صرف جزئي' : 'لم يصرف');

                    $lastDispense = $dispensedItems->flatMap->dispenses->sortByDesc('created_at')->first();
                    $pharmacistName = $lastDispense?->pharmacist?->name ?? '—';
                    $centerName = $lastDispense?->medicalCenter?->name ?? (auth()->user()->medicalCenter->name ?? '—');
                @endphp
                <div class="recipes-rect recipes-card">
                    <div class="recipe-status {{ $statusClass }}">{{ $statusText }}</div>
                    <div class="recipe-date">{{ $prescription->created_at->format('d/m/Y') }}</div>
                    <div class="recipe-pharmacist">الصيدلي: {{ $pharmacistName }}</div>
                    <div class="recipe-hospital">{{ $centerName }}</div>
                    <div aria-hidden="true" class="recipe-docbox">
                        <img alt="" aria-hidden="true" src="{{ asset('assets/doctor/icons/file-navy.svg') }}"/>
                    </div>
                    <button class="recipe-open-btn" type="button"
                            data-prescription-id="{{ $prescription->id }}"
                            data-date="{{ $prescription->created_at->format('d/m/Y') }}"
                            data-status="{{ $statusText }}"
                            data-pharmacist="{{ $pharmacistName }}"
                            data-center="{{ $centerName }}"
                            data-patient="{{ $selectedPatient->name }}"
                            data-notes="{{ $prescription->notes ?? '' }}"
                            data-items='@json($prescription->items->map(fn($item) => [
                                "medicine" => $item->medicine->name ?? "—",
                                "quantity" => $item->quantity,
                                "dispensed" => $item->dispenses->count() > 0
                            ]))'>فتح الوصفة</button>
                </div>
            @empty
                <div class="recipes-rect rect-large recipes-empty-rect">
                    <div class="recipes-empty">
                        <img alt="" aria-hidden="true" class="recipes-empty-icon" src="{{ asset('assets/doctor/icons/file.svg') }}"/>
                        <p class="recipes-empty-text">لا توجد وصفات لهذا المريض</p>
                    </div>
                </div>
            @endforelse
        </div>
        @endif
    </div>
</div>

{{-- Prescription Modal --}}
<div class="rx-modal-overlay" id="rxModalOverlay">
    <div class="rx-modal">
        <div class="rx-modal-header">
            <h3 id="rxModalTitle">تفاصيل الوصفة</h3>
            <button class="rx-modal-close" id="rxModalClose" type="button">&times;</button>
        </div>
        <div class="rx-modal-meta">
            <div class="rx-modal-meta-item">
                <span>📅</span>
                <strong>التاريخ:</strong>
                <span id="rxModalDate"></span>
            </div>
            <div class="rx-modal-meta-item">
                <span>👤</span>
                <strong>المريض:</strong>
                <span id="rxModalPatient"></span>
            </div>
            <div class="rx-modal-meta-item">
                <span>💊</span>
                <strong>الصيدلي:</strong>
                <span id="rxModalPharmacist"></span>
            </div>
            <div class="rx-modal-meta-item">
                <span>🏥</span>
                <strong>المركز:</strong>
                <span id="rxModalCenter"></span>
            </div>
        </div>
        <div class="rx-modal-body">
            <table class="rx-modal-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الدواء</th>
                        <th>الكمية</th>
                        <th>حالة الصرف</th>
                    </tr>
                </thead>
                <tbody id="rxModalItems">
                </tbody>
            </table>
            <div class="rx-modal-notes" id="rxModalNotes" style="display:none;">
                <strong>📝 ملاحظات:</strong>
                <span id="rxModalNotesText"></span>
            </div>
        </div>
        <div class="rx-modal-footer">
            <button class="rx-modal-footer-btn" id="rxModalCloseBtn" type="button">إغلاق</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    // Patient search filter in sidebar
    const searchInput = document.getElementById('recipesPatientSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = searchInput.value.trim().toLowerCase();
            document.querySelectorAll('.patient-btn').forEach(function (btn) {
                btn.style.display = btn.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    // Modal elements
    const overlay = document.getElementById('rxModalOverlay');
    const closeBtn = document.getElementById('rxModalClose');
    const closeBtnFooter = document.getElementById('rxModalCloseBtn');

    function openModal(btn) {
        // Populate modal data
        document.getElementById('rxModalDate').textContent = btn.dataset.date;
        document.getElementById('rxModalPatient').textContent = btn.dataset.patient;
        document.getElementById('rxModalPharmacist').textContent = btn.dataset.pharmacist;
        document.getElementById('rxModalCenter').textContent = btn.dataset.center;

        // Populate items table
        const items = JSON.parse(btn.dataset.items);
        const tbody = document.getElementById('rxModalItems');
        tbody.innerHTML = '';
        items.forEach(function(item, i) {
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td style="text-align:center; color:#94a3b8; font-weight:700;">' + (i + 1) + '</td>' +
                '<td style="font-weight:600;">' + item.medicine + '</td>' +
                '<td style="text-align:center;">' + item.quantity + '</td>' +
                '<td style="text-align:center;">' +
                    (item.dispensed
                        ? '<span class="rx-status-dispensed">✓ مصروف</span>'
                        : '<span class="rx-status-pending">✗ غير مصروف</span>') +
                '</td>';
            tbody.appendChild(tr);
        });

        // Notes
        const notes = btn.dataset.notes;
        const notesDiv = document.getElementById('rxModalNotes');
        if (notes && notes.trim()) {
            document.getElementById('rxModalNotesText').textContent = notes;
            notesDiv.style.display = 'block';
        } else {
            notesDiv.style.display = 'none';
        }

        // Show modal
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Open buttons
    document.querySelectorAll('.recipe-open-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openModal(btn);
        });
    });

    // Close handlers
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (closeBtnFooter) closeBtnFooter.addEventListener('click', closeModal);

    // Close on overlay click
    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });
    }

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('open')) closeModal();
    });
})();
</script>
@endpush
