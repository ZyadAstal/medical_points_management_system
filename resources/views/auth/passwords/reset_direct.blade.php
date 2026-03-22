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
            top: 280px !important;
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

            <form class="auth-form-normal" method="POST" action="{{ route('password.reset_direct') }}" autocomplete="off">
                @csrf
                
                <input type="hidden" name="email" value="{{ $email }}" />

                <label class="field-label field-label-password" for="password">كلمة المرور الجديدة</label>
                <div class="password-wrap">
                    <button class="toggle-password field-eye" type="button" aria-label="إظهار/إخفاء كلمة المرور">
                        <img src="{{ asset('assets/admin/icons/eye.svg') }}" alt="" />
                    </button>
                    <input
                        id="password"
                        name="password"
                        class="field-input field-input-password"
                        type="password"
                        placeholder="أدخل كلمة المرور الجديدة"
                        required
                        autofocus
                    />
                </div>

                <label class="field-label field-label-password" for="password_confirmation">تأكيد كلمة المرور</label>
                <div class="password-wrap">
                    <button class="toggle-password-confirm field-eye" type="button" aria-label="إظهار/إخفاء كلمة المرور">
                        <img src="{{ asset('assets/admin/icons/eye.svg') }}" alt="" />
                    </button>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        class="field-input field-input-password"
                        type="password"
                        placeholder="تأكيد كلمة المرور الجديدة"
                        required
                    />
                </div>

                <button class="auth-submit" type="submit">حفظ كلمة المرور وتسجيل الدخول</button>
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
                    setupEye('.toggle-password-confirm', '#password_confirmation');
                })();
            </script>
        </section>
    </main>
</body>
</html>
