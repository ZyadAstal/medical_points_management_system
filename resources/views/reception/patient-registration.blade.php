@extends('layouts.reception')

@section('title', 'تسجيل المريض - Medicare')
@section('page-id', 'patient-registration')
@section('content-class', 'patient-registration-content')

@section('content')
<div class="patient-registration-wrapper" 
     data-initial-panel="{{ $errors->any() ? 'panel-new-patient' : '' }}"
     data-search-url="{{ route('reception.patients.searchById') }}">
<div class="pr-page-title">
    <h1>إدارة ملفات المرضى والتسجيل</h1>
    <p>إدخال بيانات المريض الأساسية وربطه بالطبيب المختص</p>
</div>

<div class="pr-tabs" role="tablist" aria-label="تبويبات تسجيل المريض">
    <button class="pr-tab is-active" id="tab-new-patient" type="button" role="tab"
        aria-selected="true" aria-controls="panel-new-patient" data-target="panel-new-patient">مريض جديد</button>
    <button class="pr-tab" id="tab-existing-patient" type="button" role="tab"
        aria-selected="false" aria-controls="panel-existing-patient" data-target="panel-existing-patient">مريض سابق</button>
</div>

{{-- تبويب: مريض جديد --}}
<form method="POST" action="{{ route('reception.patients.store') }}" class="pr-panel is-active" id="panel-new-patient" role="tabpanel" aria-labelledby="tab-new-patient">
    @csrf
    <div class="pr-panel-head">
        <h2>تسجيل مريض جديد</h2>
        <p>تأكد من دقة البيانات الأساسية للمريض</p>
    </div>

        <div class="pr-field pr-field--full pr-name-field">
            <label for="patientFullName">الاسم الكامل</label>
            <input id="patientFullName" name="full_name" type="text"
                placeholder="أدخل الاسم ثلاثي أو رباعي..."
                value="{{ old('full_name') }}" required />
            @error('full_name')
                <span class="pr-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="pr-field pr-id-field">
            <label for="patientId">رقم الهوية</label>
            <input id="patientId" name="national_id" type="text"
                placeholder="أدخل رقم الهوية..."
                value="{{ old('national_id') }}" required />
            @error('national_id')
                <span class="pr-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="pr-field pr-phone-field">
            <label for="patientPhone">رقم الهاتف</label>
            <input id="patientPhone" name="phone" type="text"
                placeholder="أدخل رقم الهاتف..."
                value="{{ old('phone') }}" />
        </div>

        <div class="pr-field pr-address-field">
            <label for="patientAddress">العنوان</label>
            <input id="patientAddress" name="address" type="text"
                placeholder="المدينة، الحي، الشارع..."
                value="{{ old('address') }}" />
        </div>

        <div class="pr-field pr-dob-field">
            <label for="patientDob">تاريخ الميلاد</label>
            <input id="patientDob" name="date_of_birth" type="date"
                value="{{ old('date_of_birth') }}" required />
            @error('date_of_birth')
                <span class="pr-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="pr-field">
            <label for="patientUsername">اسم المستخدم</label>
            <input id="patientUsername" name="username" type="text"
                placeholder="أدخل اسم المستخدم..."
                value="{{ old('username') }}" required />
            @error('username')
                <span class="pr-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="pr-field">
            <label for="patientEmail">البريد الإلكتروني</label>
            <input id="patientEmail" name="email" type="email"
                placeholder="أدخل البريد الإلكتروني..."
                value="{{ old('email') }}" required />
            @error('email')
                <span class="pr-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="pr-field">
            <label for="patientPassword">كلمة المرور</label>
            <input id="patientPassword" name="password" type="password"
                placeholder="أدخل كلمة المرور..." required />
            @error('password')
                <span class="pr-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="pr-field pr-doctor-field">
            <label for="patientDoctor">اختيار الطبيب</label>
            <select id="patientDoctor" name="doctor_id" required>
                <option value="">-- اختر الطبيب --</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                        د. {{ $doctor->name }}
                    </option>
                @endforeach
            </select>
            @error('doctor_id')
                <span class="pr-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="pr-field">
            <label for="patientPriority">نوع الحالة</label>
            <select id="patientPriority" name="priority" required>
                <option value="0" {{ old('priority') == '0' ? 'selected' : '' }}>عادية</option>
                <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>طارئة</option>
            </select>
            @error('priority')
                <span class="pr-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="pr-field pr-points-field">
            <label for="patientPoints">النقاط (تبدأ بـ 100)</label>
            <input id="patientPoints" type="text" value="100 نقطة" readonly />
        </div>

    <div class="pr-submit-row">
        <button type="submit" class="pr-submit-btn">تسجيل المريض وإضافته للدور</button>
    </div>
</form>

{{-- تبويب: مريض سابق --}}
<section class="pr-panel pr-panel--alt" id="panel-existing-patient" role="tabpanel"
    aria-labelledby="tab-existing-patient" hidden>

    <div class="pr-existing-box pr-existing-box--top">
        <div class="pr-existing-search-title">البحث عن مريض مسجل مسبقًا</div>
        <input class="pr-existing-search-input" id="existingNationalId" type="text"
            placeholder="أدخل رقم الهوية للبحث.." />
        <button class="pr-existing-search-btn" type="button" id="searchExistingBtn">
            <span>بحث في الملفات</span>
            <img src="{{ asset('assets/reception/icons/search-patient.svg') }}" alt="بحث" />
        </button>
        <div id="searchMsg" style="display:none; color:#e74c3c; margin-top:8px;"></div>
    </div>

    <div class="pr-existing-box pr-existing-box--bottom" id="existingPatientResult" style="display:none;">
        <div class="pr-existing-patient-icon-bg"></div>
        <img class="pr-existing-patient-icon" src="{{ asset('assets/reception/icons/profile-icon.svg') }}" alt="مستخدم" />

        <div class="pr-existing-patient-name" id="existingName">---</div>
        <div class="pr-existing-patient-id" id="existingIdDisplay">رقم الهوية : ---</div>

        <input type="hidden" id="existingPatientId" value="">

        <div class="pr-field">
            <label for="existingAddress">تحديث العنوان</label>
            <input class="pr-existing-address-field" id="existingAddress" type="text" placeholder="العنوان..." />
        </div>

        <div class="pr-field">
            <label for="existingPhone">تحديث رقم الهاتف</label>
            <input class="pr-existing-phone-field" id="existingPhone" type="text" placeholder="رقم الهاتف..." />
        </div>

        <div class="pr-field pr-existing-doctor-wrapper">
            <label for="existingDoctor">اختيار الطبيب</label>
            <select class="pr-existing-doctor-field" id="existingDoctor">
                <option value="">-- اختر الطبيب --</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}">د. {{ $doctor->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="pr-field">
            <label for="existingPriority">نوع الحالة</label>
            <select class="pr-existing-priority-field" id="existingPriority">
                <option value="0">عادية</option>
                <option value="1">طارئة</option>
            </select>
        </div>

        <div class="existing-btn-row">
            <button class="pr-existing-save-btn" type="button" id="saveExistingBtn">حفظ وتحديث البيانات</button>
            <button class="pr-existing-send-btn" type="button" id="sendToDoctorBtn">إرسال للطبيب</button>
            <button class="pr-existing-cancel-btn" type="button" id="cancelExistingBtn">إلغاء</button>
        </div>
    </div>
</section>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/reception/views/registration.js') }}"></script>
@endpush
