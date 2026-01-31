<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>تسجيل الدخول - Medicare</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jomhuria&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}" />
    <style>
        .error-msg {
            position: absolute;
            top: 265px;
            left: 579px;
            width: 420px;
            color: #ff4d4d;
            font-size: 12px;
            text-align: right;
            z-index: 5;
        }
    </style>
</head>
<body>

    <main class="auth-page" aria-label="صفحة تسجيل الدخول">
        <section class="auth-frame" aria-label="إطار تسجيل الدخول">

            <div class="auth-triangle" aria-hidden="true"></div>

            <img class="auth-logo" src="{{ asset('assets/admin/logos/white-logo.svg') }}" alt="Medicare" />

            <h1 class="auth-slogan">
                <span>معًا نحو إدارة صحية</span>
                <span>أكثر كفاءة</span>
            </h1>

            <h2 class="auth-welcome">مرحبًا بعودتك</h2>
            <p class="auth-welcome-sub">قم بتسجيل الدخول لإدارة النظام الصحي</p>

            @if ($errors->any())
                <div class="error-msg">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form class="auth-form" method="POST" action="{{ route('login') }}" autocomplete="off">
                @csrf
                <label class="field-label field-label-email" for="username">اسم المستخدم</label>
                <input
                    id="username"
                    name="username"
                    class="field-input field-input-email"
                    type="text"
                    placeholder="أدخل اسم المستخدم"
                    value="{{ old('username') }}"
                    required
                    autofocus
                />

                <label class="field-label field-label-password" for="password">كلمة المرور</label>
                <div class="password-wrap">
                    <button class="toggle-password field-eye" type="button" aria-label="إظهار/إخفاء كلمة المرور">
                        <img src="{{ asset('assets/admin/icons/eye.svg') }}" alt="" />
                    </button>
                    <input
                        id="password"
                        name="password"
                        class="field-input field-input-password"
                        type="password"
                        placeholder="أدخل كلمة المرور"
                        required
                    />
                </div>

                <div class="login-options" aria-label="خيارات تسجيل الدخول">
                    <a class="forgot-link" href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>

                    <label class="remember-wrap" for="remember">
                        <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }} />
                        <span class="remember-box" aria-hidden="true"></span>
                        <span class="remember-text">تذكرني</span>
                    </label>
                </div>

                <button class="auth-submit" type="submit">تسجيل الدخول</button>
            </form>
            
            <div class="auth-no-account">
                ليس لديك حساب؟ <span class="highlight">يرجى مراجعة الإدارة</span>
            </div>

            {{-- Registration Link Removed per User Request --}}

            <script>
                (function () {
                    const btn = document.querySelector('.toggle-password');
                    const input = document.querySelector('#password');
                    if (!btn || !input) return;

                    btn.addEventListener('click', function () {
                        input.type = input.type === 'password' ? 'text' : 'password';
                        btn.classList.toggle('is-on');
                    });
                })();
            </script>
        </section>
    </main>
</body>
</html>
