@extends('layouts.doctor')

@section('title', 'الملف الشخصي - Medicare')
@section('page-id', 'profile')
@section('content-class', 'profile-content')

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <h1 class="profile-title">الملف الشخصي</h1>
        <p class="profile-desc">إدارة البيانات الشخصية وإعدادات الأمان</p>
    </div>



    <form method="POST" action="{{ route('doctor.profile.update.personal') }}">
        @csrf
        @method('PUT')
        <div class="profile-frame">
            <img class="profile-personal-icon" src="{{ asset('assets/doctor/icons/dark-profile.svg') }}" alt="" />
            <div class="profile-personal-title">المعلومات الشخصية</div>

            <div class="profile-name-label">الاسم</div>
            <input class="profile-name-input" type="text" name="name" value="{{ old('name', Auth::user()->name) }}" />

            <div class="profile-email-label">البريد الإلكتروني</div>
            <input class="profile-email-input" type="email" name="email" value="{{ old('email', Auth::user()->email) }}" />

            <button class="profile-save-btn" type="submit">حفظ التعديلات</button>
        </div>
    </form>

    <form method="POST" action="{{ route('doctor.profile.update.security') }}" id="changePassForm">
        @csrf
        @method('PUT')
        <div class="profile-frame profile-frame-security" aria-label="Security settings">
            <img class="security-icon" src="{{ asset('assets/doctor/icons/safety.svg') }}" alt="" />
            <div class="security-title">الأمان</div>

            <label class="security-label security-label-current" for="currentPassword">كلمة المرور الحالية</label>
            <div class="security-field security-field-current">
                <input id="currentPassword" class="security-input" type="password" name="current_password"
                       placeholder="أدخل كلمة المرور الحالية" autocomplete="current-password" />
                <button class="security-eye-btn" type="button" aria-label="إظهار/إخفاء كلمة المرور" aria-pressed="false" data-toggle-target="currentPassword">
                    <img src="{{ asset('assets/doctor/icons/eye.svg') }}" alt="" />
                </button>
            </div>

            <label class="security-label security-label-new" for="newPassword">كلمة المرور الجديدة</label>
            <div class="security-field security-field-new">
                <input id="newPassword" class="security-input" type="password" name="password"
                       placeholder="أدخل كلمة المرور الجديدة" autocomplete="new-password" />
                <button class="security-eye-btn" type="button" aria-label="إظهار/إخفاء كلمة المرور" aria-pressed="false" data-toggle-target="newPassword">
                    <img src="{{ asset('assets/doctor/icons/eye.svg') }}" alt="" />
                </button>
            </div>

            <label class="security-label security-label-confirm" for="confirmPassword">تأكيد كلمة المرور</label>
            <div class="security-field security-field-confirm">
                <input id="confirmPassword" class="security-input" type="password" name="password_confirmation"
                       placeholder="أعد إدخال كلمة المرور الجديدة" autocomplete="new-password" />
                <button class="security-eye-btn" type="button" aria-label="إظهار/إخفاء كلمة المرور" aria-pressed="false" data-toggle-target="confirmPassword">
                    <img src="{{ asset('assets/doctor/icons/eye.svg') }}" alt="" />
                </button>
            </div>

            <div class="security-note">
                <div class="security-note-icon">💡</div>
                <div class="security-note-text">تأكد من أن كلمة المرور تحتوي على 8 أحرف على الأقل وتتضمن أحرف كبيرة وصغيرة وأرقام</div>
            </div>

            <button class="security-change-btn" type="submit">تغيير كلمة المرور</button>
        </div>
    </form>
</div>
@endsection
