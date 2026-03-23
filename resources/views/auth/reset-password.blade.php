<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>تعيين كلمة المرور الجديدة - Medicare</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jomhuria&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}" />

    <style>
        .auth-welcome { width: 420px !important; left: 579.36px !important; text-align: center; }
        .auth-welcome-sub { width: 420px !important; left: 579.36px !important; text-align: center; }

        .auth-form-normal {
            position: absolute !important;
            top: 260px !important;
            left: 579.36px !important;
            width: 420px !important;
            z-index: 4;
            display: flex;
            flex-direction: column;
        }

        .auth-form-normal .field-label {
            position: static !important;
            text-align: right !important;
            display: block;
            margin-bottom: 8px;
            width: 100% !important;
        }

        .auth-form-normal .password-wrap {
            position: relative !important;
            top: auto !important;
            left: auto !important;
            width: 100% !important;
            margin-bottom: 20px;
        }

        .auth-form-normal .auth-submit {
            position: static !important;
            margin-top: 8px;
            width: 100% !important;
        }

        .password-wrap .field-eye { position: absolute; left: 16px; top: 15px; background: none; border: none; cursor: pointer; }
        
        .invalid-feedback {
            color: #dc3545;
            font-size: 14px;
            margin-top: -15px;
            margin-bottom: 15px;
            display: block;
            text-align: right;
        }
    </style>
</head>
<body>

    <main class="auth-page" aria-label="صفحة تعيين كلمة المرور الجديدة">
        <section class="auth-frame" aria-label="إطار تعيين كلمة المرور الجديدة">

            <div class="auth-triangle" aria-hidden="true"></div>

            <img class="auth-logo" src="{{ asset('assets/admin/logos/white-logo.svg') }}" alt="Medicare" />

            <h1 class="auth-slogan">
                <span>معًا نحو إدارة صحية</span>
                <span>أكثر كفاءة</span>
            </h1>

            <h2 class="auth-welcome">تعيين كلمة مرور جديدة</h2>
            <p class="auth-welcome-sub">أدخل كلمة المرور الجديدة لحسابك</p>

            <div style="position: absolute; top: 80px; left: 579.36px; width: 420px; z-index: 10;">
                @include('partials.flash-messages')
            </div>

            <form class="auth-form-normal" method="POST" action="{{ route('password.update') }}" autocomplete="off">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}">
                
                <label class="field-label" for="email">البريد الإلكتروني</label>
                <div class="password-wrap">
                    <input
                        id="email"
                        name="email"
                        class="field-input @error('email') is-invalid @enderror"
                        type="email"
                        placeholder="أدخل بريدك الإلكتروني"
                        value="{{ $email ?? old('email') }}"
                        required
                        readonly
                    />
                </div>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <label class="field-label" for="password">كلمة المرور الجديدة</label>
                <div class="password-wrap">
                    <button class="toggle-password field-eye" type="button" aria-label="إظهار/إخفاء كلمة المرور">
                        <img src="{{ asset('assets/admin/icons/eye.svg') }}" alt="" />
                    </button>
                    <input
                        id="password"
                        name="password"
                        class="field-input @error('password') is-invalid @enderror"
                        type="password"
                        placeholder="أدخل كلمة المرور الجديدة"
                        required
                        autofocus
                    />
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <label class="field-label" for="password-confirm">تأكيد كلمة المرور</label>
                <div class="password-wrap">
                    <button class="toggle-password-confirm field-eye" type="button" aria-label="إظهار/إخفاء كلمة المرور">
                        <img src="{{ asset('assets/admin/icons/eye.svg') }}" alt="" />
                    </button>
                    <input
                        id="password-confirm"
                        name="password_confirmation"
                        class="field-input"
                        type="password"
                        placeholder="تأكيد كلمة المرور الجديدة"
                        required
                    />
                </div>

                <button class="auth-submit" type="submit">حفظ كلمة المرور</button>
            </form>

            <script>
                (function () {
                    const setupEye = (btnClass, inputId) => {
                        const btn = document.querySelector(btnClass);
                        const input = document.querySelector(inputId);
                        if (!btn || !input) return;

                        btn.addEventListener('click', function () {
                            input.type = input.type === 'password' ? 'text' : 'password';
                            btn.classList.toggle('is-on');
                        });
                    };

                    setupEye('.toggle-password', '#password');
                    setupEye('.toggle-password-confirm', '#password-confirm');
                })();
            </script>
        </section>
    </main>
</body>
</html>
