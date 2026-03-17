<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <title>طباعة قائمة الانتظار - {{ $date }}</title>
    <link rel="stylesheet" href="{{ asset('css/reception/views/print.css') }}">
</head>
<body>
    <div class="print-btn-container no-print">
        <button class="print-btn" onclick="window.print()">تأكيد الطباعة</button>
    </div>

    <div class="header">
        <h1>قائمة انتظار المرضى</h1>
        <p>التاريخ: {{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>رقم الدور</th>
                <th>اسم المريض</th>
                <th>رقم الهوية</th>
                <th>الطبيب المعالج</th>
                <th>وقت الوصول</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visits as $index => $visit)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $visit->patient->full_name ?? '---' }}</td>
                    <td>{{ $visit->patient->national_id ?? '---' }}</td>
                    <td>د. {{ $visit->doctor->name ?? '---' }}</td>
                    <td>{{ \Carbon\Carbon::parse($visit->created_at)->format('h:i A') }}</td>
                    <td>
                        @php
                            $label = match($visit->status) {
                                \App\Models\Visit::STATUS_REGISTERED  => 'مسجل',
                                \App\Models\Visit::STATUS_WAITING     => 'بانتظار',
                                \App\Models\Visit::STATUS_IN_PROGRESS => 'يتم الفحص',
                                \App\Models\Visit::STATUS_COMPLETED   => 'خرج',
                                \App\Models\Visit::STATUS_CANCELLED   => 'ملغي',
                                default                   => '---',
                            };
                        @endphp
                        {{ $label }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        تمت الطباعة بواسطة نظام Medicare - {{ now()->format('Y-m-d H:i') }}
    </div>

    <script src="{{ asset('js/reception/views/print.js') }}"></script>
</body>
</html>
