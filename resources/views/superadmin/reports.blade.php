@extends('layouts.admin')

@section('title', 'التقارير - Medicare')
@section('page-id', 'reports')
@section('content-class', 'reports-content')

@push('styles')
    <link href="{{ asset('css/admin/pages-extra.css') }}" rel="stylesheet"/>
@endpush

@section('content')
<div class="page-title-block">
    <h1>التقارير</h1>
    <p><span style="display:inline-block;margin-right:8px;">عرض وتحليل تقارير الصرف والنقاط</span></p>
</div>

<button class="reports-export-btn" type="button" aria-label="تصدير تقرير" onclick="const form = document.querySelector('.reports-filters-grid'); const params = new URLSearchParams(new FormData(form)).toString(); window.location.href = '{{ route('superadmin.reports.pdf') }}?' + params;">
    <span class="reports-export-btn__icon" aria-hidden="true">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M12 3v10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M8 9l4-4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5 14v5h14v-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </span>
    <span class="reports-export-btn__text">تصدير تقرير</span>
</button>

<div class="cards reports-cards">
    <div class="card">
        <div class="card__top"></div>
        <section class="reports-filters-panel" aria-label="فلاتر التقارير">
            <form action="{{ route('superadmin.reports.index') }}" method="GET" class="reports-filters-grid">
                <div class="filter-item filter-center">
                    <label class="filter-label" for="filterCenter">المركز الطبي</label>
                    <select id="filterCenter" class="filter-control" name="center_id">
                        <option value="">كل المراكز</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-item filter-medicine">
                    <label class="filter-label" for="filterMedicine">الدواء</label>
                    <select id="filterMedicine" class="filter-control" name="medicine_id">
                        <option value="">كل الأدوية</option>
                        @foreach($medicines as $medicine)
                            <option value="{{ $medicine->id }}" {{ request('medicine_id') == $medicine->id ? 'selected' : '' }}>{{ $medicine->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-item filter-from">
                    <label class="filter-label" for="filterFrom">من تاريخ</label>
                    <div class="date-control">
                        <span class="date-icon" aria-hidden="true"></span>
                        <input id="filterFrom" class="filter-control date-input" type="date" name="from_date" value="{{ request('from_date') }}" />
                    </div>
                </div>
                <div class="filter-item filter-to">
                    <label class="filter-label" for="filterTo">إلى تاريخ</label>
                    <div class="date-control">
                        <span class="date-icon" aria-hidden="true"></span>
                        <input id="filterTo" class="filter-control date-input" type="date" name="to_date" value="{{ request('to_date') }}" />
                    </div>
                </div>
                <div class="filter-item filter-actions">
                    <button type="submit" class="btn-reports-apply">تطبيق الفلترة</button>
                    <a href="{{ route('superadmin.reports.index') }}" class="btn-reports-reset">إعادة تعيين</a>
                </div>
            </form>
        </section>

        <section class="reports-table-section" aria-label="تقرير الصرف التفصيلي">
            <h3 class="reports-table-title">تقرير الصرف التفصيلي</h3>
            <div class="reports-table-wrap">
                <table class="reports-table" dir="rtl">
                    <thead>
                        <tr>
                            <th>اسم المركز</th>
                            <th>اسم الدواء</th>
                            <th>عدد نقاط الصرف</th>
                            <th>النقاط المصروفة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailedDispenses as $dispense)
                        <tr>
                            <td>{{ $dispense->medicalCenter->name }}</td>
                            <td>{{ $dispense->prescriptionItem->medicine->name }}</td>
                            <td>{{ $dispense->quantity }}</td>
                            <td>{{ number_format($dispense->points_used) }}</td>
                            <td>{{ $dispense->created_at->format('Y/m/d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="reports-section2" aria-label="تحليل نشاط المراكز">
            <h3 class="reports-section2-title">تحليل نشاط المراكز الطبية</h3>
            <div class="reports-section2-table-wrap">
                <table class="reports-table reports-table-2" dir="rtl">
                    <thead>
                        <tr>
                            <th>اسم المركز</th>
                            <th>عدد عمليات الصرف</th>
                            <th>مجموع النقاط</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($centersActivity as $center)
                        <tr>
                            <td>{{ $center->name }}</td>
                            <td>{{ number_format($center->total_dispenses) }}</td>
                            <td>{{ number_format($center->points_used) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="reports-chart-title" aria-hidden="true">رسم بياني - مقارنة المرضى</div>
            <div class="reports-chart-area" aria-label="رسم بياني - مقارنة المرضى">
                <div class="reports-chart-axis reports-axis-y" aria-hidden="true"></div>
                <div class="reports-chart-axis reports-axis-x" aria-hidden="true"></div>
                <div class="reports-chart-bars" aria-hidden="true">
                    @php
                        $baseY = 279;
                        $pointScale = 247 / 140; // Based on 140 units = 247px height
                        $xPositions = [
                            ['blue' => 924, 'gray' => 862],
                            ['blue' => 765, 'gray' => 703],
                            ['blue' => 599, 'gray' => 537],
                            ['blue' => 411, 'gray' => 349],
                            ['blue' => 240, 'gray' => 178],
                        ];
                    @endphp

                    @foreach($patientsActivityChart as $index => $patient)
                        @if(isset($xPositions[$index]))
                            @php
                                $pointsHeight = min(260, $patient->total_points * $pointScale);
                                $pointsTop = $baseY - $pointsHeight;
                                
                                // Operations might need a different scale to be visible, or use the same if values are large.
                                // In the mockup, ops were smaller but visible. Let's use a 5x multiplier for ops to make them legible if they are small.
                                $opsHeight = min(260, $patient->total_ops * $pointScale * 5); 
                                $opsTop = $baseY - $opsHeight;
                            @endphp
                            <div class="reports-bar" style="height: {{ $pointsHeight }}px; top: {{ $pointsTop }}px; left: {{ $xPositions[$index]['blue'] }}px; background: #0B6CB8;" title="{{ $patient->full_name }}: {{ number_format($patient->total_points) }} نقطة"></div>
                            <div class="reports-bar" style="height: {{ $opsHeight }}px; top: {{ $opsTop }}px; left: {{ $xPositions[$index]['gray'] }}px; background: #929292;" title="{{ $patient->full_name }}: {{ $patient->total_ops }} عملية"></div>
                        @endif
                    @endforeach
                </div>
                <div class="reports-chart-ylabels" aria-hidden="true">
                    <div class="reports-chart-ylabel y140">١٤٠</div>
                    <div class="reports-chart-ylabel y105">١٠٥</div>
                    <div class="reports-chart-ylabel y70">٧٠</div>
                    <div class="reports-chart-ylabel y35">٣٥</div>
                    <div class="reports-chart-ylabel y0">٠</div>
                </div>
                <div class="reports-chart-xlabels" aria-hidden="true">
                    @php
                        $labelPositions = [920.5, 761.5, 595.5, 407.5, 236.5];
                    @endphp
                    @foreach($patientsActivityChart as $index => $patient)
                        @if(isset($labelPositions[$index]))
                            <div class="reports-chart-xlabel" style="left: {{ $labelPositions[$index] }}px; transform: translateX(-50%); position: absolute; top: 0;">{{ $patient->full_name }}</div>
                        @endif
                    @endforeach
                </div>
                <div class="reports-chart-legend" aria-hidden="true">
                    <div class="reports-legend-square legend-blue"></div>
                    <div class="reports-legend-text legend-points">النقاط ( / ١٠٠ )</div>
                    <div class="reports-legend-square legend-gray"></div>
                    <div class="reports-legend-text legend-ops">عدد العمليات</div>
                </div>
            </div>
        </section>

        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/admin/cards/points.svg') }}"/></div>
            <div>
                <div class="card__label">إجمالي النقاط المصروفة</div>
                <div class="card__value">{{ number_format($totalPointsUsed) }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/admin/cards/number-of-exchange-operations.svg') }}"/></div>
            <div>
                <div class="card__label">عدد عمليات الصرف</div>
                <div class="card__value">{{ number_format($centersActivity->sum('total_dispenses')) }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/admin/cards/most-prescribed-medicine.svg') }}"/></div>
            <div>
                <div class="card__label">أكثر دواء صرفاً</div>
                <div class="card__value card__value--single">{{ $topMedicines->first()->name ?? '---' }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__top"></div>
        <div class="card__body">
            <div class="card__icon"><img alt="" src="{{ asset('assets/admin/cards/most-used-patient.svg') }}"/></div>
            <div>
                <div class="card__label">أكثر مركز نشاطاً</div>
                <div class="card__value">{{ $centersActivity->sortByDesc('total_dispenses')->first()->name ?? '---' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
