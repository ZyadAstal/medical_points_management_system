@extends($layout)

@section('title', 'الملف الشخصي - Medicare')
@section('page-id', 'profile')
@section('content-class', 'profile-content')

@section('styles')
<style>
    /* 
       Reset some shared.profile defaults to allow the rigid absolute 
       system from the global CSS to take over for Pharmacist/Doctor roles.
    */
    .profile-container {
        width: 1080px;
        margin: 0 auto;
        padding-top: 20px;
        position: relative;
        min-height: 1200px;
    }
    
    .profile-header {
        margin-bottom: 30px;
        text-align: right;
    }

    /* Support for form tags within the absolute layout */
    .profile-frame form {
        display: block;
        width: 100%;
        height: 100%;
        position: relative;
    }

    /* Fixed icons sizing */
    .profile-personal-icon, .security-icon {
        object-fit: contain;
    }
    
    /* Eye button adjustment */
    .security-eye-btn {
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Ensure the main content doesn't truncate the absolute elements */
    .main {
        min-height: 100vh;
        overflow-x: hidden;
    }
</style>
@endsection

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <h1 class="profile-title" style="font-size: 28px; color: #053052; font-family: 'Inter', sans-serif;">الملف الشخصي</h1>
        <p class="profile-desc" style="color: #6184A0; font-family: 'Inter', sans-serif;">إدارة البيانات الشخصية وإعدادات الأمان</p>
    </div>



    @php
        $assetPrefix = 'assets/' . strtolower($role) . '/icons/';
    @endphp

    <!-- Personal Info Frame -->
    <div class="profile-frame">
        <form action="{{ route('profile.update.personal') }}" method="POST">
            @csrf
            @method('PUT')
            <img class="profile-personal-icon" src="{{ asset($assetPrefix . 'dark-profile.svg') }}" onerror="this.src='{{ asset('assets/pharmacist/icons/dark-profile.svg') }}'" alt="" />
            <div class="profile-personal-title">المعلومات الشخصية</div>

            <div class="profile-name-label">الاسم</div>
            <input class="profile-name-input" name="name" type="text" value="{{ old('name', $user->name) }}" required />

            <div class="profile-email-label">البريد الإلكتروني</div>
            <input class="profile-email-input" name="email" type="email" value="{{ old('email', $user->email) }}" required />

            <button class="profile-save-btn" type="submit">حفظ التعديلات</button>
        </form>
    </div>

    <!-- Security Frame -->
    <div class="profile-frame profile-frame-security">
        <form action="{{ route('profile.update.security') }}" method="POST" id="changePassForm">
            @csrf
            @method('PUT')
            <img class="security-icon" src="{{ asset($assetPrefix . 'safety.svg') }}" onerror="this.src='{{ asset('assets/pharmacist/icons/safety.svg') }}'" alt="" />
            <div class="security-title">الأمان</div>

            <label class="security-label security-label-current" for="currentPassword">كلمة المرور الحالية</label>
            <div class="security-field security-field-current">
                <input id="currentPassword" name="current_password" class="security-input" type="password" placeholder="أدخل كلمة المرور الحالية" autocomplete="current-password" required />
                <button class="security-eye-btn" type="button" data-toggle-target="currentPassword">
                    <img src="{{ asset($assetPrefix . 'eye.svg') }}" onerror="this.src='{{ asset('assets/pharmacist/icons/eye.svg') }}'" alt="" />
                </button>
            </div>

            <label class="security-label security-label-new" for="newPassword">كلمة المرور الجديدة</label>
            <div class="security-field security-field-new">
                <input id="newPassword" name="password" class="security-input" type="password" placeholder="أدخل كلمة المرور الجديدة" autocomplete="new-password" required />
                <button class="security-eye-btn" type="button" data-toggle-target="newPassword">
                    <img src="{{ asset($assetPrefix . 'eye.svg') }}" onerror="this.src='{{ asset('assets/pharmacist/icons/eye.svg') }}'" alt="" />
                </button>
            </div>

            <label class="security-label security-label-confirm" for="confirmPassword">تأكيد كلمة المرور</label>
            <div class="security-field security-field-confirm">
                <input id="confirmPassword" name="password_confirmation" class="security-input" type="password" placeholder="أعد إدخال كلمة المرور الجديدة" autocomplete="new-password" required />
                <button class="security-eye-btn" type="button" data-toggle-target="confirmPassword">
                    <img src="{{ asset($assetPrefix . 'eye.svg') }}" onerror="this.src='{{ asset('assets/pharmacist/icons/eye.svg') }}'" alt="" />
                </button>
            </div>

            <div class="security-note">
                <div class="security-note-text">
                    💡 تأكد من أن كلمة المرور تحتوي على 8 أحرف على الأقل وتتضمن أحرف كبيرة وصغيرة وأرقام
                </div>
            </div>

            <button class="security-change-btn" type="submit">تغيير كلمة المرور</button>
        </form>
    </div>
</div>
@endsection
