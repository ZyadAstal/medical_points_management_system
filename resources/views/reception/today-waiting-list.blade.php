@extends('layouts.reception')

@section('title', 'قائمة الانتظار اليوم - Medicare')
@section('page-id', 'today-waiting-list')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reception/views/waiting-list.css') }}">
@endpush

@section('content')
<div class="reception-waiting-list-content-wrapper" data-waiting-list-url="{{ route('reception.visits.waiting') }}">
<div class="reception-waiting-list-page-header">
    <h1 class="reception-waiting-list-title">قائمة الانتظار وحركة المرضى</h1>
    <p class="reception-waiting-list-subtitle">إدارة ومتابعة الدور اليومي للمرضى</p>
</div>

<form method="GET" action="{{ route('reception.visits.waiting') }}" id="dateFilterForm">
    <div class="reception-waiting-list-controls">
        <div class="waiting-date-box" aria-label="اختيار التاريخ">
            <input class="waiting-date-input" id="waitingDate" name="date" type="date"
                value="{{ $date }}" aria-label="التاريخ" />
            <span class="waiting-date-divider" aria-hidden="true"></span>
            <button class="waiting-date-trigger" type="button" aria-label="فتح اختيار التاريخ"
                onclick="document.getElementById('waitingDate').showPicker ? document.getElementById('waitingDate').showPicker() : document.getElementById('waitingDate').focus()">
                <img class="waiting-date-icon" src="{{ asset('assets/reception/icons/date.svg') }}" alt="" />
            </button>
        </div>

        <button class="waiting-order-btn waiting-order-btn-primary waiting-refresh-btn" type="submit">
            <span class="waiting-refresh-icon" aria-hidden="true">↻</span>
            <span class="waiting-order-btn-text">تحديث القائمة</span>
        </button>

        <button class="waiting-order-btn waiting-order-btn-secondary waiting-print-btn" type="button"
            onclick="printWaitingList()">
            <span class="waiting-order-btn-text waiting-order-btn-text-secondary">طباعة الدور</span>
        </button>

        <a href="{{ route('reception.patients.create') }}"
            class="waiting-order-btn waiting-order-btn-primary waiting-organize-btn"
            style="text-decoration:none;">
            <span class="waiting-order-btn-text">+ تسجيل مريض</span>
        </a>
    </div>
</form>

<div class="reception-waiting-list-panel">
    <div class="waiting-table-header" aria-hidden="true">
        <span class="waiting-col waiting-col-order">رقم الدور</span>
        <span class="waiting-col waiting-col-patient">المريض</span>
        <span class="waiting-col waiting-col-doctor">الطبيب المعالج</span>
        <span class="waiting-col waiting-col-arrival">وقت الوصول</span>
        <span class="waiting-col waiting-col-status">الحالة</span>
        <span class="waiting-col waiting-col-actions">الإجراءات</span>
        <span class="waiting-vline waiting-vline-1"></span>
        <span class="waiting-vline waiting-vline-2"></span>
        <span class="waiting-vline waiting-vline-3"></span>
        <span class="waiting-vline waiting-vline-4"></span>
        <span class="waiting-vline waiting-vline-5"></span>
    </div>

    <div class="waiting-table-body">
        @forelse($visits as $index => $visit)
            @php
                $statusClass = match($visit->status) {
                    \App\Models\Visit::STATUS_REGISTERED  => 'waiting-status-registered',
                    \App\Models\Visit::STATUS_WAITING     => 'waiting-status-waiting',
                    \App\Models\Visit::STATUS_IN_PROGRESS => 'waiting-status-doctor',
                    \App\Models\Visit::STATUS_COMPLETED   => 'waiting-status-exit',
                    \App\Models\Visit::STATUS_CANCELLED   => 'waiting-status-cancelled',
                    default                   => 'waiting-status-registered',
                };
                $statusLabel = match($visit->status) {
                    \App\Models\Visit::STATUS_REGISTERED  => 'مسجل',
                    \App\Models\Visit::STATUS_WAITING     => 'بانتظار',
                    \App\Models\Visit::STATUS_IN_PROGRESS => 'يتم الفحص',
                    \App\Models\Visit::STATUS_COMPLETED   => 'خرج',
                    \App\Models\Visit::STATUS_CANCELLED   => 'ملغي',
                    default                   => 'مسجل',
                };
            @endphp
            <div class="waiting-row waiting-row-{{ ($index % 3) + 1 }}" data-visit-id="{{ $visit->id }}">
                <div class="waiting-row-order">
                    <span class="waiting-order-circle">{{ $visits->firstItem() + $index }}</span>
                </div>
                <div class="waiting-row-patient">
                    <span class="waiting-row-patient-name">{{ $visit->patient->full_name ?? 'غير معروف' }}</span>
                    <span class="waiting-row-patient-id">ID:{{ $visit->patient->national_id ?? '---' }}</span>
                </div>
                <div class="waiting-row-doctor">د. {{ $visit->doctor->name ?? 'غير محدد' }}</div>
                <div class="waiting-row-arrival">
                    {{ \Carbon\Carbon::parse($visit->created_at)->format('h:i A') }}
                </div>
                <div class="waiting-row-status">
                    <span class="waiting-status-badge {{ $statusClass }} js-status-badge">{{ $statusLabel }}</span>
                </div>
                <div class="waiting-row-actions">
                    @php
                        $isRegistered = $visit->status === \App\Models\Visit::STATUS_REGISTERED;
                        $isWaiting = $visit->status === \App\Models\Visit::STATUS_WAITING;
                        $canEnter = $isRegistered;
                        $canUndo = $isRegistered || $isWaiting;
                    @endphp

                    <button class="waiting-text-action-btn js-enter-btn"
                        type="button" 
                        {{ !$canEnter ? 'disabled' : '' }}
                        style="background-color: {{ $canEnter ? '#E6F3FF' : '#F3F3F3' }}; color: {{ $canEnter ? '#0B6CB8' : '#A0A0A0' }}; cursor: {{ $canEnter ? 'pointer' : 'not-allowed' }};"
                        data-visit-id="{{ $visit->id }}"
                        data-url="{{ route('reception.visits.sendToDoctor', $visit) }}">
                        {{ $isWaiting ? 'تم الإدخال' : 'إدخال للعيادة' }}
                    </button>

                    <button class="waiting-text-action-btn js-undo-btn"
                        type="button"
                        {{ !$canUndo ? 'disabled' : '' }}
                        style="background-color: {{ $canUndo ? '#FFF0F0' : '#F3F3F3' }}; color: {{ $canUndo ? '#D32F2F' : '#A0A0A0' }}; cursor: {{ $canUndo ? 'pointer' : 'not-allowed' }};"
                        data-visit-id="{{ $visit->id }}"
                        data-url="{{ route('reception.visits.cancel', $visit) }}">
                        تراجع
                    </button>
                </div>
                <span class="waiting-row-divider"></span>
            </div>
        @empty
            <div class="waiting-list-empty-state">
                <p>لا توجد زيارات في هذا اليوم</p>
            </div>
        @endforelse


    </div>
</div>

{{-- روابط التصفح (Pagination) --}}
<div class="mc-pagination">
    {{ $visits->links('pagination::bootstrap-4') }}
</div>

{{-- إحصاء سريع --}}
<div style="display:flex; justify-content:center; gap:16px; margin-top:20px; flex-wrap:wrap;">
    <div style="background:#f0f7ff; border:1px solid #c6d9f1; border-radius:8px; padding:10px 20px; font-family:'Inter',sans-serif;">
        <strong>الإجمالي:</strong> {{ $stats['total'] }}
    </div>
    <div class="waiting-stats-bin waiting-stats-bin--registered">
        <strong>مسجل:</strong> {{ $stats['registered'] ?? 0 }}
    </div>
    <div class="waiting-stats-bin waiting-stats-bin--waiting">
        <strong>بانتظار:</strong> {{ $stats['waiting'] }}
    </div>
    <div class="waiting-stats-bin waiting-stats-bin--in-progress">
        <strong>يتم الفحص:</strong> {{ $stats['in_progress'] }}
    </div>
    <div class="waiting-stats-bin waiting-stats-bin--completed">
        <strong>خروج:</strong> {{ $stats['completed'] }}
    </div>
</div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/reception/views/waiting-list.js') }}"></script>
@endpush
