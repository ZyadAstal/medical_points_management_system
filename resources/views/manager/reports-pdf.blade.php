<!DOCTYPE html>
<html lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>تقرير المركز الطبي - Medicare</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: right;
            direction: ltr; /* Ar-PHP shapes for LTR rendering in DomPDF */
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0062AF;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .title {
            color: #0062AF;
            font-size: 24px;
        }
        .meta {
            margin-bottom: 15px;
            font-size: 14px;
        }
        .summary-row {
            margin-bottom: 20px;
            width: 100%;
        }
        .summary-box {
            display: inline-block;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            width: 45%;
            text-align: center;
            margin-bottom: 10px;
        }
        h3 {
            color: #0062AF;
            border-right: 4px solid #0062AF;
            padding-right: 10px;
            margin-top: 25px;
            margin-bottom: 10px;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 6px;
            text-align: center;
            font-size: 11px;
        }
        th {
            background-color: #0062AF;
            color: #ffffff;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">{{ $reportTitle }}</h1>
        <p>{{ $exportDateLabel }}</p>
    </div>

    <div class="meta">
        <strong>{{ $selectedCenter }}</strong> <br>
        <strong>{{ $selectedMedicine }}</strong> <br>
        @if($fromDate || $toDate)
            <strong>
            {{ $fromDate ? ' ' . $fromDate : '' }}
            {{ $toDate ? ' ' . $toDate : '' }}
            </strong>
        @endif
    </div>

    <div class="summary-row">
        <div class="summary-box">
            <strong>{{ $totalDispensesSummary }}</strong>
        </div>
        <div class="summary-box">
            <strong>{{ $totalPointsUsedFormatted }}</strong>
        </div>
    </div>

    @if($medicineStats->count() > 0)
    <h3>{{ $statsTitle }}</h3>
    <table>
        <thead>
            <tr>
                <th>{{ $headers['points'] }}</th>
                <th>{{ $headers['count'] }}</th>
                <th>{{ $headers['medicine'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicineStats as $stat)
            <tr>
                <td>{{ number_format($stat->points) }}</td>
                <td>{{ number_format($stat->count) }}</td>
                <td>{{ $stat->shaped_name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($lowStock->count() > 0)
    <h3>{{ $lowStockTitle }}</h3>
    <table>
        <thead>
            <tr>
                <th>{{ $headers['status'] }}</th>
                <th>{{ $headers['qty'] }}</th>
                <th>{{ $headers['medicine'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lowStock as $inv)
            <tr>
                <td style="color: {{ $inv->quantity <= 3 ? '#ef4444' : '#f59e0b' }};">
                    {{ $inv->shaped_status }}
                </td>
                <td>{{ $inv->quantity }}</td>
                <td>{{ $inv->shaped_name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        {{ $footerText }}
    </div>
</body>
</html>
