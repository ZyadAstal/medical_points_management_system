<!DOCTYPE html>
<html lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>تقرير الصرف - Medicare</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: right;
            direction: ltr; /* Ar-PHP shapes for LTR rendering in DomPDF */
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #053052;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .title {
            color: #053052;
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
            color: #053052;
            border-right: 4px solid #053052;
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
            background-color: #053052;
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
        <!-- Reordered boxes for RTL look -->
        <div class="summary-box">
            <strong>{{ $totalDispensesSummary }}</strong>
        </div>
        <div class="summary-box">
            <strong>{{ $totalPointsUsedFormatted }}</strong>
        </div>
    </div>

    @if($centersActivity->sum('total_dispenses') > 0)
    <h3>{{ $activityTitle }}</h3>
    <table>
        <thead>
            <tr>
                <!-- Reordered headers -->
                <th>{{ $headers['points'] }}</th>
                <th>{{ $headers['count'] }}</th>
                <th>{{ $headers['center'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($centersActivity as $center)
            @if($center->total_dispenses > 0)
            <tr>
                <!-- Reordered cells -->
                <td>{{ number_format($center->points_used) }}</td>
                <td>{{ number_format($center->total_dispenses) }}</td>
                <td>{{ $center->shaped_name }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    @if($topMedicines->count() > 0)
    <h3>{{ $topMedTitle }}</h3>
    <table>
        <thead>
            <tr>
                <!-- Reordered headers -->
                <th>{{ $headers['sum'] }}</th>
                <th>{{ $headers['count'] }}</th>
                <th>{{ $headers['medicine'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topMedicines as $med)
            <tr>
                <!-- Reordered cells -->
                <td>{{ number_format($med->total_points) }}</td>
                <td>{{ number_format($med->count) }}</td>
                <td>{{ $med->shaped_name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h3>{{ $detailedTitle }}</h3>
    <table>
        <thead>
            <tr>
                <!-- Reordered headers -->
                <th>{{ $headers['date'] }}</th>
                <th>{{ $headers['points'] }}</th>
                <th>{{ $headers['qty'] }}</th>
                <th>{{ $headers['medicine'] }}</th>
                <th>{{ $headers['center'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailedDispenses as $dispense)
            <tr>
                <!-- Reordered cells -->
                <td>{{ $dispense->created_at->format('Y-m-d') }}</td>
                <td>{{ number_format($dispense->points_used) }}</td>
                <td>{{ number_format($dispense->quantity) }}</td>
                <td>{{ $dispense->shaped_medicine }}</td>
                <td>{{ $dispense->shaped_center }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ $footerText }}
    </div>
</body>
</html>
