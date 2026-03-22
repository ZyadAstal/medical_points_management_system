<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>استعادة كلمة المرور - Medicare</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jomhuria&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}" />

</head>
<body>

    <main class="auth-page" aria-label="صفحة استعادة كلمة المرور">
        <section class="auth-frame" aria-label="إطار استعادة كلمة المرور">

            <div class="auth-triangle" aria-hidden="true"></div>

            <img class="auth-logo" src="{{ asset('assets/admin/logos/white-logo.svg') }}" alt="Medicare" />

            <h1 class="auth-slogan">
                <span>معًا نحو إدارة صحية</span>
                <span>أكثر كفاءة</span>
            </h1>

            <h2 class="auth-welcome">استعادة كلمة المرور</h2>
            <p class="auth-welcome-sub">أدخل بريدك الإلكتروني لمعرفة كيفية الاستعادة</p>

            <div style="position: absolute; top: 80px; left: 579.36px; width: 420px; z-index: 10;">
                @include('partials.flash-messages')
            </div>

            <form class="auth-form" method="POST" action="{{ route('password.email') }}" autocomplete="off">
                @csrf
                <label class="field-label field-label-email" for="email">البريد الإلكتروني</label>
                <input
                    id="email"
                    name="email"
                    class="field-input field-input-email"
                    type="email"
                    placeholder="أدخل بريدك الإلكتروني"
                    value="{{ old('email') }}"
                    required
                    autofocus
                />

                <button class="auth-submit" type="submit" style="margin-top: 24px;">إرسال طلب الاستعادة</button>
            </form>
            
            <div class="auth-no-account" style="margin-top: 16px;">
                تذكرت كلمة المرور؟ <a href="{{ route('login') }}" class="highlight" style="display:inline-block; margin-right:4px;">تسجيل الدخول</a>
            </div>

        </section>
    </main>
</body>
</html>
