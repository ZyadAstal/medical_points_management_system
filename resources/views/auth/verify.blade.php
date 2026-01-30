<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>التحقق من الهوية - Medicare</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f2f4f6; margin: 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .box { max-width: 620px; width: 90%; background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 12px 30px rgba(0,0,0,.08); text-align: center; }
        h1 { margin: 0 0 16px; font-size: 28px; color: #053052; }
        p { margin: 0 0 20px; color: #5b6670; line-height: 1.6; }
        a { color: #0C6BB8; text-decoration: none; font-weight: 500; }
        a:hover { text-decoration: underline; }
        .back-link { margin-top: 24px; display: block; }
    </style>
</head>
<body>
    <div class="box">
        <h1>التحقق من الهوية</h1>
        <p>هنا سيتم التحقق من هوية المستخدم لاسترداد كلمة المرور.</p>
        <p>يرجى مراجعة بريدك الإلكتروني للحصول على تعليمات إعادة تعيين كلمة المرور.</p>
        <a href="{{ route('login') }}" class="back-link">العودة لتسجيل الدخول</a>
    </div>
</body>
</html>
