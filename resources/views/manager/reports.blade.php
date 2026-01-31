@extends('layouts.manager')

@section('title', 'التقارير - Medicare')
@section('page-id', 'reports')

@push('styles')
    <link href="{{ asset('css/manager/reports.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="reports-container" dir="rtl" style="direction: rtl;">

    <div class="action-bar">
        <a href="{{ route('manager.reports.pdf', request()->all()) }}" class="btn-export">
            تصدير تقرير (PDF)
            <img src="{{ asset('assets/manager/icons/export.svg') }}" width="18" alt="">
        </a>
    </div>

    <!-- Stats summary -->
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-icon-wrapper">
                <img src="{{ asset('assets/manager/cards/points.svg') }}" width="24" alt="">
            </div>
            <div class="stat-label">إجمالي النقاط المصروفة</div>
            <div class="stat-value">{{ number_format($totalPointsUsed) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-icon-wrapper">
                <img src="{{ asset('assets/manager/cards/number-of-exchange-operations.svg') }}" width="24" alt="">
            </div>
            <div class="stat-label">عدد عمليات الصرف</div>
            <div class="stat-value">{{ number_format($totalDispenses) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-icon-wrapper">
                <img src="{{ asset('assets/manager/cards/most-prescribed-medicine.svg') }}" width="24" alt="">
            </div>
            <div class="stat-label">أكثر دواء صرفاً</div>
            <div class="stat-value" style="font-size: 18px;">{{ $medicineStats->first()->name ?? '---' }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-icon-wrapper">
                <img src="{{ asset('assets/manager/cards/users.svg') }}" width="24" alt="">
            </div>
            <div class="stat-label">أدوية منخفضة المخزون</div>
            <div class="stat-value">{{ $lowStock->count() }}</div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <form action="{{ route('manager.reports.index') }}" method="GET">
            <div class="filter-row">
                <div class="filter-group">
                    <label>المركز الطبي</label>
                    <select class="filter-control" disabled>
                        <option>{{ Auth::user()->medicalCenter->name }}</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>الدواء</label>
                    <select name="medicine_id" class="filter-control">
                        <option value="">كل الأدوية</option>
                        @foreach($allMedicines as $m)
                            <option value="{{ $m->id }}" {{ request('medicine_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>من تاريخ</label>
                    <input type="date" name="from_date" class="filter-control" value="{{ request('from_date') }}">
                </div>

                <div class="filter-group">
                    <label>إلى تاريخ</label>
                    <input type="date" name="to_date" class="filter-control" value="{{ request('to_date') }}">
                </div>

                <div class="filter-actions" style="margin-right: 10px;">
                    <button type="submit" class="btn-apply">تطبيق الفلترة</button>
                    <a href="{{ route('manager.reports.index') }}" class="btn-reset">إعادة تعيين</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tables -->
    <div class="report-section">
        <h2 class="section-title">تقرير أكثر الأدوية صرفاً</h2>
        <div class="table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>اسم الدواء</th>
                        <th>عدد مرات الصرف</th>
                        <th>النقاط المصروفة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicineStats as $stat)
                    <tr>
                        <td>{{ $stat->name }}</td>
                        <td>{{ $stat->count }}</td>
                        <td>{{ number_format($stat->points) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="padding: 40px; color: #999;">لا توجد بيانات متاحة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="report-section">
        <h2 class="section-title">تنبيهات المخزون المنخفض</h2>
        <div class="table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>الترتيب</th>
                        <th>اسم الدواء</th>
                        <th>الكمية المتبقية</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStock as $index => $inv)
                    <tr>
                        <td style="color: #000;">{{ $index + 1 }}</td>
                        <td>{{ $inv->medicine->name }}</td>
                        <td>{{ $inv->quantity }}</td>
                        <td class="{{ $inv->quantity <= 3 ? 'status-critical' : 'status-warning' }}">
                            {{ $inv->quantity <= 3 ? 'حرجة' : 'منخفضة' }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding: 40px; color: #999;">المخزون سليم، لا توجد تنبيهات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
