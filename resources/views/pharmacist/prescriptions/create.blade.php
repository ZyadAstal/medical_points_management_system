@extends('layouts.pharmacist')

@section('title', 'إجراء عملية صرف نقاط')
@section('page-id', 'new-exchange-wizard')

@section('styles')
<style>
/* CSS from original HTML */
.title h2{font-size:20px;color:#0d3b66;margin-bottom:5px}
.title p{font-size:13px;color:#6c757d}
.step{
    width:36px; height:36px; border-radius:50%; background:#e9ecef; color:#adb5bd;
    display:flex; align-items:center; justify-content:center; font-weight:600; font-size:14px; transition:.3s;
}
.steps{ display:flex; align-items:center; margin:25px 0 35px }
.step.active{ background:#0764AE; color:#fff; }
.line{ flex:1; height:3px; background:#e9ecef }

.card{
    background:#f8f9fa;
    padding:25px;
    border-radius:8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.patient-row{
    display:flex;
    gap:15px;
    margin-bottom:30px
}

.patient-box, .balance-box{
    flex:1;
    background:#ffffff;
    border:1px solid #dee2e6;
    border-radius:10px;
    padding:18px;
}

.patient-box h2, .balance-box h2{
    font-size:16px;
    color:#0d3b66;
    margin-bottom:8px;
    font-weight:700
}

.patient-box .value{ font-size:15px; font-weight:600; color:#343a40 }
.balance-box .value{ font-size:15px; font-weight:600; color:#155724 }

.form-group{margin-bottom:15px}
.form-group label{display:block;font-size:13px;margin-bottom:6px;color:#495057}
.form-group input, .form-group select{
    width:100%; padding:9px 12px; border:1px solid #dee2e6; border-radius:6px; font-size:13px; background:#fff
}

.form-row { display: flex; gap: 15px; }
.form-row .form-group { flex: 1; }

.btn{
    padding:8px 42px;
    border-radius:5px;
    border:none;
    font-size:13px;
    cursor:pointer;
    font-weight: 600;
}

.btn-primary{ background: linear-gradient(90deg, #77A0C2 0%, #053052 100%); color: #fff; box-shadow: 0px 2px 4px 0px #00000040; }
.btn-secondary{ background: linear-gradient(90deg, #CACACA 0%, #DEDEDE 100%); color: #333; box-shadow: 0px 2px 4px 0px #00000040; }

.hidden{ display:none }

/* Drug Section Styles */
.drug-section{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:40px;
}

.drug-actions{ width: 40%; }
.drug-table{
    flex:1;
}

.table-header, .table-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    text-align:center;
    margin-bottom: 10px;
}

.table-header{ color:#777; font-weight:600; font-size: 14px; padding: 0 10px;}

.value-box{
    background:#ececec;
    padding:8px 18px;
    border-radius:12px;
    min-width:45px;
    text-align:center;
    font-weight:600;
    font-size: 13px;
}

.total-box{ margin-top:30px; }
.total-box p{ color:#777; margin-bottom:10px; }
.total-number{
    background:#ececec;
    padding:10px 20px;
    border-radius:12px;
    display:inline-block;
}
.total-number span{ color:#2a6cf0; font-weight:700; }

.delete-row {
    color: #999;
    font-size: 18px;
    cursor: pointer;
    margin-right: 10px;
}

.prescription-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 15px 20px;
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;
}
.view-btn { color: #0f3554; font-weight: 700; cursor: pointer; text-decoration: underline; }

/* Step 4 Review */
.info-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:20px;
  margin-bottom:20px;
}

.info-box{
  background:#f9fafb;
  padding:15px;
  border-radius:8px;
}

.info-box h3{
  font-size:14px;
  color:#6b7280;
  margin-bottom:10px;
}

.review-item {
    background: #eef2f7;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.quantity-summary {
  background:#eef2f7;
  padding:10px;
  border-radius:6px;
  font-size:14px;
  margin-top:10px;
  display: flex;
  justify-content: space-between;
}
</style>
@endsection

@section('content')
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
@endsection

@section('scripts')
<script>
let currentPatient = null;
let selectedMedicines = []; // Array of {id, name, points, quantity}

function goStep(step) {
    for(let i=1; i<=4; i++) {
        document.getElementById('step'+i).classList.add('hidden');
        let icon = document.getElementById('step'+i+'icon');
        icon.classList.remove('active');
        icon.innerHTML = i;
    }

    document.getElementById('step'+step).classList.remove('hidden');

    for(let i=1; i<step; i++) {
        let icon = document.getElementById('step'+i+'icon');
        icon.classList.add('active');
        icon.innerHTML = "✔";
    }

    let current = document.getElementById('step'+step+'icon');
    current.classList.add('active');
}

async function searchPatient() {
    const nid = document.getElementById('national_id_input').value;
    const errorDiv = document.getElementById('search_error');
    errorDiv.style.display = 'none';

    if(!nid) {
        errorDiv.innerText = "يرجى إدخال رقم الهوية";
        errorDiv.style.display = 'block';
        return;
    }

    try {
        const response = await fetch(`{{ route('pharmacist.prescriptions.search') }}?national_id=${nid}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();

        if(data.success) {
            currentPatient = data.patient;
            
            document.getElementById('display_patient_name').innerText = currentPatient.name || currentPatient.full_name || "غير معروف";
            document.getElementById('display_patient_points').innerText = (currentPatient.points || 0) + " نقطة";

            document.getElementById('review_patient_name').innerText = currentPatient.name || currentPatient.full_name || "غير معروف";
            document.getElementById('review_patient_id').innerText = currentPatient.national_id || nid;
            document.getElementById('form_patient_id').value = currentPatient.id;

            goStep(2);
        } else {
            errorDiv.innerText = data.message;
            errorDiv.style.display = 'block';
        }
    } catch (e) {
        console.error(e);
        errorDiv.innerText = "حدث خطأ أثناء البحث";
        errorDiv.style.display = 'block';
    }
}

function addDrugToList() {
    const select = document.getElementById('drugSelect');
    const qtyInput = document.getElementById('drugQuantity');
    
    if(!select.value) return;
    
    const id = select.value;
    const name = select.options[select.selectedIndex].getAttribute('data-name');
    const points = parseInt(select.options[select.selectedIndex].getAttribute('data-points'));
    const quantity = parseInt(qtyInput.value);

    if(isNaN(quantity) || quantity < 0) {
        alert("يرجى إدخال كمية صالحة");
        return;
    }

    // Check if duplicate
    const existing = selectedMedicines.find(m => m.id === id);
    if(existing) {
        existing.quantity += quantity;
    } else {
        selectedMedicines.push({ id, name, points, quantity });
    }

    renderMedicinesTable();
    updateWizardTotal();
    
    // Reset inputs
    select.value = "";
    qtyInput.value = 1;
}

function renderMedicinesTable() {
    const container = document.getElementById('drug_rows_container');
    container.innerHTML = selectedMedicines.map((m, index) => `
        <div class="table-row" style="background: #fff; padding: 10px; border-radius: 8px; margin-bottom: 8px;">
            <div style="flex: 2; text-align: right; font-weight: 600;">${m.name}</div>
            <div style="flex: 1;"><div class="value-box">${m.quantity}</div></div>
            <div style="flex: 1;"><div class="value-box" style="color: #0764AE;">${m.points * m.quantity}</div></div>
            <div style="width: 30px;">
                <span class="delete-row" onclick="removeMedicine(${index})">🗑</span>
            </div>
        </div>
    `).join('');
}

function removeMedicine(index) {
    selectedMedicines.splice(index, 1);
    renderMedicinesTable();
    updateWizardTotal();
}

function updateWizardTotal() {
    const total = selectedMedicines.reduce((sum, m) => sum + (m.points * m.quantity), 0);
    document.getElementById('wizard_total_cost').innerText = total;
}

function prepareStep4() {
    if(selectedMedicines.length === 0) {
        alert("يرجى إضافة دواء واحد على الأقل.");
        return;
    }

    // Capture metadata
    const doctorSelect = document.getElementById('doctor_id');
    if(!doctorSelect.value) {
        alert("يرجى اختيار الطبيب");
        return;
    }
    document.getElementById('form_doctor_id').value = doctorSelect.value;
    document.getElementById('form_prescription_date').value = document.getElementById('prescription_date').value;
    document.getElementById('form_notes').value = document.getElementById('prescription_notes').value;

    const listContainer = document.getElementById('review_items_list');
    const hiddenContainer = document.getElementById('selected_items_hidden_container');
    
    listContainer.innerHTML = '';
    hiddenContainer.innerHTML = '';

    let totalPoints = 0;

    selectedMedicines.forEach(m => {
        const cost = m.points * m.quantity;
        totalPoints += cost;

        // Review UI
        const div = document.createElement('div');
        div.className = 'review-item';
        div.innerHTML = `<span>${m.name}</span> <span style="color: #0B6CB8;">الكمية: ${m.quantity} (${cost} نقطة)</span>`;
        listContainer.appendChild(div);

        // Hidden Inputs for Laravel array format
        hiddenContainer.innerHTML += `
            <input type="hidden" name="items[${m.id}][medicine_id]" value="${m.id}">
            <input type="hidden" name="items[${m.id}][quantity]" value="${m.quantity}">
        `;
    });

    const finalBalance = currentPatient.points - totalPoints;
    document.getElementById('review_final_balance').innerText = finalBalance;
    
    if(finalBalance < 0) {
        document.getElementById('review_final_balance').parentElement.style.color = 'red';
    } else {
        document.getElementById('review_final_balance').parentElement.style.color = '#059669';
    }

    goStep(4);
}

// Init
goStep(1);
</script>
@endsection
