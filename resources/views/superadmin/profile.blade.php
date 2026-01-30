@extends('layouts.admin')

@section('title', 'الملف الشخصي - Medicare')
@section('page-id', 'profile')
@section('content-class', 'profile-content')

@push('styles')
    <link href="{{ asset('css/admin/pages-extra.css') }}" rel="stylesheet"/>
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <h1 class="profile-title">الملف الشخصي</h1>
        <p class="profile-desc">إدارة البيانات الشخصية وإعدادات الأمان</p>
    </div>

    @if(session('success'))
        <div class="profile-alert profile-alert--success">
            <div class="alert-body">
                <div class="alert-title">النجاح</div>
                <div class="alert-message">{{ session('success') }}</div>
            </div>
            <div class="alert-icon-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="profile-alert profile-alert--error">
            <div class="alert-body">
                <div class="alert-title">خطأ</div>
                <ul class="alert-message-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="alert-icon-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </div>
        </div>
    @endif

    <div class="profile-frame">
        <img class="profile-personal-icon" src="{{ asset('assets/admin/icons/dark-profile.svg') }}" alt="" />
        <div class="profile-personal-title">المعلومات الشخصية</div>

        <form action="{{ route('profile.update.personal') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="profile-name-label">الاسم</div>
            <input class="profile-name-input" name="name" type="text" value="{{ $user->name }}" required />

            <div class="profile-email-label">البريد الإلكتروني</div>
            <input class="profile-email-input" name="email" type="email" value="{{ $user->email }}" required />

            <button class="profile-save-btn" type="submit">حفظ التعديلات</button>
        </form>
    </div>

    <div class="profile-frame profile-frame-security" aria-label="Security settings">
        <img class="security-icon" src="{{ asset('assets/admin/icons/safety.svg') }}" alt="" />
        <div class="security-title">الأمان</div>

        <form action="{{ route('profile.update.security') }}" method="POST" id="changePassForm">
            @csrf
            @method('PUT')
            
            <label class="security-label security-label-current" for="currentPassword">كلمة المرور الحالية</label>
            <div class="security-field security-field-current">
                <input id="currentPassword" name="current_password" class="security-input" type="password" placeholder="أدخل كلمة المرور الحالية" autocomplete="current-password" required />
                <button class="security-eye-btn" type="button" aria-label="إظهار/إخفاء كلمة المرور" aria-pressed="false" data-toggle-target="currentPassword">
                    <img src="{{ asset('assets/admin/icons/eye.svg') }}" alt="" />
                </button>
            </div>

            <label class="security-label security-label-new" for="newPassword">كلمة المرور الجديدة</label>
            <div class="security-field security-field-new">
                <input id="newPassword" name="password" class="security-input" type="password" placeholder="أدخل كلمة المرور الجديدة" autocomplete="new-password" required />
                <button class="security-eye-btn" type="button" aria-label="إظهار/إخفاء كلمة المرور" aria-pressed="false" data-toggle-target="newPassword">
                    <img src="{{ asset('assets/admin/icons/eye.svg') }}" alt="" />
                </button>
            </div>

            <label class="security-label security-label-confirm" for="confirmPassword">تأكيد كلمة المرور</label>
            <div class="security-field security-field-confirm">
                <input id="confirmPassword" name="password_confirmation" class="security-input" type="password" placeholder="أعد إدخال كلمة المرور الجديدة" autocomplete="new-password" required />
                <button class="security-eye-btn" type="button" aria-label="إظهار/إخفاء كلمة المرور" aria-pressed="false" data-toggle-target="confirmPassword">
                    <img src="{{ asset('assets/admin/icons/eye.svg') }}" alt="" />
                </button>
            </div>

            <div class="security-note">
                <div class="security-note-icon">💡</div>
                <div class="security-note-text">تأكد من أن كلمة المرور تحتوي على 8 أحرف على الأقل وتتضمن أحرف كبيرة وصغيرة وأرقام</div>
            </div>

            <button class="security-change-btn" type="submit">تغيير كلمة المرور</button>
        </form>
    </div>
</div>
@endsection
