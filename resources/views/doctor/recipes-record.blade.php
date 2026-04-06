@extends('layouts.doctor')

@section('title', 'سجل الوصفات - Medicare')
@section('page-id', 'recipes-record')

@push('styles')
    <link href="{{ asset('css/doctor/recipes-record.css') }}" rel="stylesheet">
    <link href="{{ asset('css/doctor/views/recipes-record-modal.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="search-patient-page-header">
    <h1 class="search-patient-title">سجل الوصفات</h1>
    <p class="search-patient-subtitle">عرض الوصفات السابقة والأدوية المصروفة</p>
</div>

<div class="recipes-rectangles">
    <div class="recipes-rect rect-small">
        <h2 class="patients-title">المرضى</h2>
        <form action="{{ isset($selectedPatient) ? route('doctor.prescriptions.show', $selectedPatient->id) : route('doctor.prescriptions.index') }}" method="GET" style="display:flex; align-items:center; gap:8px; margin: 20px 22px 0 22px;">
            <input aria-label="بحث عن مريض" class="patients-search" id="recipesPatientSearch" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو رقم الهوية..." type="text" style="flex:1; margin:0;"/>
            <button type="submit" class="search-submit-btn" aria-label="بحث" title="بحث" id="recipesSearchBtn" style="height: 39px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </form>
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
        <div class="mc-pagination" style="padding: 10px; display: flex; justify-content: center; transform: scale(0.85);">
            {{ $patients->appends(['patient_page' => $patients->currentPage()])->links('pagination::simple-bootstrap-4') }}
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
            @forelse ($prescriptions as $prescription)
                @php
                    $allItems = $prescription->items;
                    $dispensedItems = $allItems->filter(function($item) {
                        return $item->dispenses->count() > 0;
                    });
                    $isFullyDispensed = $dispensedItems->count() === $allItems->count() && $allItems->count() > 0;
                    $isPartial = $dispensedItems->count() > 0 && !$isFullyDispensed;

                    $statusClass = $isFullyDispensed ? 'recipe-status--paid' : ($isPartial ? 'recipe-status--partial' : 'recipe-status--unpaid');
                    $statusText = $isFullyDispensed ? 'تم الصرف' : ($isPartial ? 'صرف جزئي' : 'لم يصرف');

                    $lastDispense = $allItems->flatMap(fn($item) => $item->dispenses)->sortByDesc('created_at')->first();
                    $pharmacistName = $lastDispense?->pharmacist?->name ?? ($prescription->pharmacist?->name ?? '—');
                    $centerName = $lastDispense?->medicalCenter?->name ?? (auth()->user()->medicalCenter->name ?? '—');
                    
                    $itemData = $prescription->items->map(function($item) {
                        return [
                            "medicine" => $item->medicine->name ?? "—",
                            "medicine_en" => $item->medicine->name_en ?? null,
                            "quantity" => $item->quantity,
                            "dispensed" => $item->dispenses->count() > 0
                        ];
                    });
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
                            data-items='@json($itemData)'>فتح الوصفة</button>
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
        <div class="mc-pagination" style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $prescriptions->links('pagination::bootstrap-4') }}
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
    <script src="{{ asset('js/doctor/views/recipes-record.js') }}"></script>
@endpush
