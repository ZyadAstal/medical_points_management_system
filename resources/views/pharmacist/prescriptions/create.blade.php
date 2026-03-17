@extends('layouts.pharmacist')

@section('title', 'إجراء عملية صرف نقاط')
@section('page-id', 'new-exchange-wizard')

@push('styles')
    <link href="{{ asset('css/pharmacist/views/prescriptions.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="pharmacist-prescription-create-wrapper" data-search-url="{{ route('pharmacist.prescriptions.search') }}">
<div class="dash-title-block">
    <h1 style="color: #053052; font-size: 32px;">إجراء عملية صرف نقاط</h1>
    <p style="color: #6184A0; font-size: 18px;">متابعة فورية لعمليات الصرف وحالة النقاط اليوميّة</p>
</div>

<div class="steps">
    <div class="step active" id="step1icon">1</div>
    <div class="line"></div>
    <div class="step" id="step2icon">2</div>
    <div class="line"></div>
    <div class="step" id="step3icon">3</div>
    <div class="line"></div>
    <div class="step" id="step4icon">4</div>
</div>

<!-- STEP 1: Search Patient -->
<div class="card" id="step1">
    <h3 style="color:#000;">الخطوة 1: البحث عن المريض</h3>
    <p>ادخل رقم الهوية أو اسم المريض للبدء</p>

    <div class="form-group">
        <label>رقم الهوية / اسم المريض</label>
        <input type="text" id="national_id_input" placeholder="ادخل بيانات المريض هنا">
        <div id="search_error" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>
    </div>

    <div style="text-align: left; margin-top: 10px;">
        <button class="btn btn-primary" onclick="searchPatient()">
            <span style="display: inline-block; vertical-align: middle; margin-left: 5px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </span>
            بحث
        </button>
    </div>
</div>

<!-- STEP 2: Prescription Metadata -->
<div class="card hidden" id="step2">
    <h3 style="color: #000;">بيانات المريض</h3>
    <p>تحقق من بيانات المريض قبل المتابعة</p>
    
    <div class="patient-row">
        <div class="patient-box" style="background: #D2E0EB;">
            <h2 style="color:#076DBE;">👤 الاسم</h2>
            <div class="value" id="display_patient_name">---</div>
        </div>
        <div class="balance-box" style="background: #D0F7DA; border: none;">
            <h2>الرصيد المتاح</h2>
            <div class="value" id="display_patient_points">---</div>
        </div>
    </div>

    <h3 style="color: #000;">الخطوة 2: معلومات الوصفة الورقية</h3>
    
    <div class="form-group">
        <label>اسم الطبيب</label>
        <select id="doctor_id" class="form-control">
            <option value="">اختر الطبيب</option>
            @foreach($doctors as $doctor)
                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>تاريخ الوصفة</label>
            <input type="date" id="prescription_date" value="{{ date('Y-m-d') }}">
        </div>
    </div>

    <div class="form-group">
        <label>ملاحظات إضافية</label>
        <input type="text" id="prescription_notes" placeholder="اكتب أي ملاحظات هنا">
    </div>

    <div class="buttons">
        <button class="btn btn-secondary" onclick="goStep(1)">السابق</button>
        <button class="btn btn-primary" onclick="goStep(3)">متابعة</button>
    </div>
</div>

<!-- STEP 3: Add Medicines -->
<div class="card hidden" id="step3">
    <h3 style="color: #000;">الخطوة 3: إضافة الأدوية</h3>
    
    <div class="drug-section">
        <!-- Right: Selection -->
        <div class="drug-actions">
            <div class="form-group">
                <label>الدواء</label>
                <select id="drugSelect">
                    <option value="">اختر الدواء</option>
                    @foreach($medicines as $med)
                        @php $stock = $med->inventories->first()->quantity ?? 0; @endphp
                        <option value="{{ $med->id }}" data-name="{{ $med->name }}" data-points="{{ $med->points_cost }}" data-stock="{{ $stock }}">
                            {{ $med->name }} ({{ $med->points_cost }} نقطة) - المتوفر: {{ $stock }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label>الكمية</label>
                <input type="number" id="drugQuantity" value="1" min="1">
            </div>

            <button class="btn btn-primary" style="width: 100%;" onclick="addDrugToList()">+ إضافة للدائمة</button>
        </div>

        <!-- Left: Table -->
        <div class="drug-table">
            <div class="table-header">
                <div style="flex: 2; text-align: right;">الدواء</div>
                <div style="flex: 1;">الكمية</div>
                <div style="flex: 1;">النقاط</div>
                <div style="width: 30px;"></div>
            </div>
            <div id="drug_rows_container">
                <!-- Dynamic rows -->
            </div>

            <div class="total-box">
                <p>اجمالي النقاط المطلوبة</p>
                <div class="total-number"><span id="wizard_total_cost">0</span> نقطة</div>
            </div>
        </div>
    </div>

    <div class="buttons" style="margin-top: 30px;">
        <button class="btn btn-secondary" onclick="goStep(2)">السابق</button>
        <button class="btn btn-primary" onclick="prepareStep4()">مراجعة الطلب</button>
    </div>
</div>

<!-- STEP 4: Final Review -->
<div class="card hidden" id="step4">
    <h3 style="color: #000;">الخطوة الأخيرة: تأكيد عملية الصرف</h3>

    <div class="info-grid">
        <div class="info-box">
            <h3 style="margin-bottom: 15px;">ملخص المريض</h3>
            <p id="review_patient_name" style="font-weight: 700;">---</p>
            <p id="review_patient_id" style="color: #666;">---</p>
            <div style="color: #059669; font-weight: 600; margin-top: 15px; display: flex; align-items: center; gap: 8px;">
                <span class="step-check" style="width:10px; height:10px; background:#059669; border-radius:50%;"></span>
                الرصيد بعد الصرف: <span id="review_final_balance">---</span> نقطة
            </div>
        </div>

        <div class="info-box" style="text-align: left;">
            <h3 style="margin-bottom: 15px;">تفاصيل النظام</h3>
            <p>المركز: {{ auth()->user()->medicalCenter->name ?? '---' }}</p>
            <p>الصيدلي: {{ auth()->user()->name }}</p>
            <p>التاريخ: {{ now()->format('Y/m/d') }}</p>
        </div>
    </div>

    <div class="info-box" style="width: 100%; margin-bottom: 20px;">
        <h3 style="margin-bottom: 15px;">الأدوية التي سيتم صرفها</h3>
        <div id="review_items_list">
            <!-- Dynamic review items -->
        </div>
    </div>

    <form id="dispenseForm" action="{{ route('pharmacist.dispense.manual') }}" method="POST">
        @csrf
        <input type="hidden" name="patient_id" id="form_patient_id">
        <input type="hidden" name="doctor_id" id="form_doctor_id">
        <input type="hidden" name="prescription_date" id="form_prescription_date">
        <input type="hidden" name="notes" id="form_notes">
        <div id="selected_items_hidden_container"></div>

        <div class="buttons">
            <button type="button" class="btn btn-secondary" onclick="goStep(3)">تعديل الأدوية</button>
            <button type="submit" class="btn btn-primary">تأكيد الصرف النهائي</button>
        </div>
    </form>
</div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/pharmacist/views/prescriptions-wizard.js') }}"></script>
@endpush
