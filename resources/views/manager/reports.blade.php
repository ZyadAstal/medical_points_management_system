@extends('layouts.manager')

@section('title', 'التقارير - Medicare')
@section('page-id', 'reports')
@section('content-class', 'reports-content')

@push('styles')
    <link href="{{ asset('css/manager/pages-extra.css') }}" rel="stylesheet"/>
@endpush

@section('content')
<div class="page-title-block">
    <h1>التقارير</h1>
    <p><span style="display:inline-block;margin-right:8px;">عرض وتحليل تقارير الصرف والنقاط</span></p>
</div>

<button class="reports-export-btn" type="button" aria-label="تصدير تقرير">
    <span class="reports-export-btn__icon" aria-hidden="true">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M12 3v10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            <path d="M8 9l4-4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M5 14v5h14v-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
        </svg>
    </span>
    <span class="reports-export-btn__text">تصدير تقرير</span>
</button>

<div class="cards reports-cards">
    <div class="card">
        <div class="card__top"></div>
        <section class="reports-filters-panel" aria-label="فلاتر التقارير">
            <form action="{{ route('manager.reports.index') }}" method="GET" class="reports-filters-grid">
                <div class="filter-item">
                    <label class="filter-label" for="reportType">نوع التقرير</label>
                    <select id="reportType" name="report_type" class="filter-control">
                        <option value="">كل التقارير</option>
                        <option value="daily" {{ request('report_type') == 'daily' ? 'selected' : '' }}>تقرير يومي</option>
                        <option value="weekly" {{ request('report_type') == 'weekly' ? 'selected' : '' }}>تقرير أسبوعي</option>
                        <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>تقرير شهري</option>
                    </select>
                </div>
                <div class="filter-item filter-from">
                    <label class="filter-label" for="filterFrom">من تاريخ</label>
                    <div class="date-control">
                        <span class="date-icon" aria-hidden="true"></span>
                        <input id="filterFrom" name="from_date" class="filter-control date-input" type="date" value="{{ request('from_date') }}" />
                    </div>
                </div>
                <div class="filter-item filter-to">
                    <label class="filter-label" for="filterTo">إلى تاريخ</label>
                    <div class="date-control">
                        <span class="date-icon" aria-hidden="true"></span>
                        <input id="filterTo" name="to_date" class="filter-control date-input" type="date" value="{{ request('to_date') }}" />
                    </div>
                </div>
                <div class="filter-item filter-btn" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn-drug-primary" style="padding: 10px 20px; border-radius: 8px;">فلترة</button>
                    <a href="{{ route('manager.reports.index') }}" class="btn-drug-cancel" style="padding: 10px 20px; border-radius: 8px; margin-right: 10px; display: inline-block; text-decoration: none; text-align: center;">إعادة تعيين</a>
                </div>
            </form>
        </section>

        <section class="reports-table-section" aria-label="تقرير صرف الأدوية">
            <h3 class="reports-table-title">تقرير صرف الأدوية بالمركز</h3>
            <div class="reports-table-wrap">
                <table class="reports-table" dir="rtl">
                    <thead>
                        <tr>
                            <th>اسم الدواء</th>
                            <th>عدد مرات الصرف</th>
                            <th>إجمالي النقاط</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicineStats as $stat)
                        <tr style="font-weight: bold;color: #000;">
                            <td>{{ $stat->name }}</td>
                            <td>{{ $stat->count }}</td>
                            <td>{{ number_format($stat->points) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/manager/cards/points.svg') }}" /></div>
            <div>
                <div class="card__label">إجمالي النقاط المصروفة</div>
                <div class="card__value">{{ number_format($totalPointsUsed) }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/manager/cards/medicines.svg') }}" /></div>
            <div>
                <div class="card__label">أكثر دواء صرفاً</div>
                <div class="card__value">{{ $medicineStats->first()->name ?? '---' }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/manager/cards/low-stock.svg') }}" /></div>
            <div>
                <div class="card__label">أدوية تحت الحد الأدنى</div>
                <div class="card__value">{{ number_format($lowStock->count()) }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
