@extends('layouts.manager')

@section('title', 'المرضى - Medicare')
@section('page-id', 'medical-centers')

@push('styles')
    <link href="{{ asset('css/manager/pages-extra.css') }}" rel="stylesheet"/>
    <style>
        /* Specific fixes to maintain original look while centering search */
        .mc-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .mc-search {
            flex: 0 0 auto;
            margin: 0 !important;
        }
        .mc-search-box {
            margin: 0 !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Modal Style Refinement to match user image */
        .patient-view-field {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 15px;
            margin-bottom: 20px;
        }
        .patient-view-input {
            width: 280px;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            text-align: right;
            background: #f9fafb;
            color: #374151;
            font-family: 'Cairo', sans-serif;
            pointer-events: none;
        }
        .patient-view-label {
            font-weight: 600;
            color: #0c3e66;
            min-width: 120px;
            text-align: right;
        }

        /* Ensure table is RTL */
        .mc-table {
            direction: rtl;
        }
        .mc-table-head, .mc-row {
            display: flex;
            flex-direction: row; /* In RTL direction, this puts the first element on the right */
        }
        /* Match column widths to original design or balance them */
        .mc-th, .mc-td {
            flex: 1;
            text-align: right;
            padding: 12px 15px;
        }
        /* Fix modal height and button overlap (OVERRIDING style.css) */
        body[data-page="medical-centers"] .mc-modal {
            height: auto !important;
            min-height: 480px !important;
            padding-bottom: 40px !important;
            display: flex !important;
            flex-direction: column !important;
        }
        body[data-page="medical-centers"] .mc-modal-inner {
            height: auto !important;
            position: relative !important;
            display: flex !important;
            flex-direction: column !important;
            flex: 1 !important;
        }
        body[data-page="medical-centers"] .mc-modal-actions {
            position: relative !important;
            bottom: auto !important;
            left: auto !important;
            transform: none !important;
            margin: 30px auto 0 !important;
            display: flex !important;
            justify-content: center !important;
            width: 100% !important;
        }
        body[data-page="medical-centers"] #v-address {
            height: auto !important;
            min-height: 30px !important;
        }
    </style>
@endpush

@section('content')
<div class="medical-centers-header">
    <h1 class="medical-centers-title"> المرضى</h1>
    <p class="medical-centers-desc">عرض ومتابعة بيانات مرضى المركز الطبي </p>
</div>

<div aria-label="فلترة وبحث الموظفين" class="mc-controls">
    <div class="mc-filter"></div>
    <div class="mc-search">
        <form action="{{ route('manager.patients.index') }}" method="GET" class="mc-search-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0c3e66" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="cursor: pointer;" onclick="this.closest('form').submit()"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input name="search" class="mc-search-input" placeholder="ابحث عن اسم المريض، أو رقم الهوية..." type="text" value="{{ request('search') }}" />
        </form>
    </div>
</div>

<div class="mc-table-wrap">
    <div class="mc-table">
        <div class="mc-table-head">
            <div class="mc-th">اسم المريض</div>
            <div class="mc-th"> رقم الهوية</div>
            <div class="mc-th">الهاتف</div>
            <div class="mc-th">النقاط المتبقية</div>
            <div class="mc-th">اخر عملية الصرف</div>
            <div class="mc-th">الإجراءات</div>
        </div>
        <div class="mc-table-body" id="mcTableBody">
            @forelse($patients as $patient)
                @php
                    $lastDispense = $patient->prescriptions->flatMap->dispenses
                        ->where('medical_center_id', Auth::user()->medical_center_id)
                        ->sortByDesc('created_at')
                        ->first();
                @endphp
                <div class="mc-row">
                    <div class="mc-td mc-name"><strong>{{ $patient->full_name }}</strong></div>
                    <div class="mc-td">{{ $patient->national_id }}</div>
                    <div class="mc-td">{{ $patient->phone }}</div>
                    <div class="mc-td">{{ number_format($patient->points) }}</div>
                    <div class="mc-td">{{ $lastDispense ? $lastDispense->created_at->format('Y/m/d') : '---' }}</div>
                    <div class="mc-td mc-actions">
                        <button type="button" class="mc-edit-action" title="عرض التفاصيل" 
                                onclick="showPatientInfo('{{ $patient->full_name }}', '{{ $patient->national_id }}', '{{ $patient->phone }}', '{{ $patient->address }}', '{{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('Y/m/d') : '---' }}')">
                            <img alt="عرض" src="{{ asset('assets/manager/icons/eye.svg') }}" width="20" height="20"/>
                        </button>
                    </div>
                </div>
            @empty
                <div class="mc-row" style="justify-content: center; padding: 20px;">
                    لا يوجد مرضى حالياً
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="mc-pagination">
    {{ $patients->links() }}
</div>

<!-- Read-Only View Modal -->
<div class="mc-modal-overlay" id="patientViewModal" style="display:none;">
    <div aria-label="بيانات المريض" aria-modal="true" class="mc-modal" role="dialog">
        <div class="mc-modal-inner">
            <h2 class="mc-modal-title">بيانات المريض</h2>
            <hr class="mc-modal-sep" />
            <div class="mc-modal-form" style="padding: 10px 0;">
                <div class="patient-view-field">
                    <input class="patient-view-input" id="v-name" type="text" readonly />
                    <label class="patient-view-label">اسم المريض</label>
                </div>
                <div class="patient-view-field">
                    <input class="patient-view-input" id="v-national-id" type="text" readonly />
                    <label class="patient-view-label">رقم الهوية</label>
                </div>
                <div class="patient-view-field">
                    <input class="patient-view-input" id="v-phone" type="text" readonly />
                    <label class="patient-view-label">الهاتف</label>
                </div>
                <div class="patient-view-field">
                    <input class="patient-view-input" id="v-dob" type="text" readonly />
                    <label class="patient-view-label">تاريخ الميلاد</label>
                </div>
                <div class="patient-view-field">
                    <input class="patient-view-input" id="v-address" type="text" readonly />
                    <label class="patient-view-label">العنوان</label>
                </div>
            </div>
            <div class="mc-modal-actions" style="margin-top: 10px;">
                <button class="mc-modal-btn mc-modal-btn-primary btn-drug-primary" onclick="hidePatientInfo()" type="button" style="width: 100%; max-width: 150px; margin: 0 auto; display: block;">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function showPatientInfo(name, id, phone, address, dob) {
            document.getElementById('v-name').value = name;
            document.getElementById('v-national-id').value = id;
            document.getElementById('v-phone').value = phone;
            document.getElementById('v-address').value = address || '---';
            document.getElementById('v-dob').value = dob || '---';
            document.getElementById('patientViewModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function hidePatientInfo() {
            document.getElementById('patientViewModal').style.display = 'none';
            document.body.style.overflow = '';
        }

        // Close on overlay click
        document.getElementById('patientViewModal').addEventListener('click', function(e) {
            if (e.target === this) hidePatientInfo();
        });
    </script>
@endpush
