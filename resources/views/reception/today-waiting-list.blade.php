@extends('layouts.reception')

@section('title', 'قائمة الانتظار اليوم - Medicare')
@section('page-id', 'today-waiting-list')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reception/views/waiting-list.css') }}">
    <style>
        /* Custom Modal Styles */
        .mc-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            direction: rtl;
        }
        .mc-modal-overlay.is-visible {
            display: flex;
            opacity: 1;
        }
        .mc-modal-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 400px;
            max-width: 90vw;
            padding: 32px;
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .mc-modal-overlay.is-visible .mc-modal-box {
            transform: scale(1);
        }
        .mc-modal-icon-wrapper {
            width: 64px;
            height: 64px;
            background: #FFF0F0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .mc-modal-icon-wrapper svg {
            width: 32px;
            height: 32px;
            color: #D32F2F;
        }
        .mc-modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 12px;
            font-family: 'Inter', sans-serif;
        }
        .mc-modal-text {
            font-size: 15px;
            color: #64748B;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .mc-modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .mc-btn-confirm {
            padding: 10px 24px;
            background: #D32F2F;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(211, 47, 47, 0.2);
        }
        .mc-btn-confirm:hover {
            background: #B71C1C;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(211, 47, 47, 0.3);
        }
        .mc-btn-cancel {
            padding: 10px 24px;
            background: #F1F5F9;
            color: #475569;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .mc-btn-cancel:hover {
            background: #E2E8F0;
            color: #1E293B;
        }
    </style>
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
                    \App\Models\Visit::STATUS_REGISTERED  => $visit->priority == \App\Models\Visit::PRIORITY_EMERGENCY ? 'مسجل طارئة' : 'مسجل',
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
    {{ $visits->links('vendor.pagination.bootstrap-4') }}
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

{{-- Custom Confirmation Modal --}}
<div id="confirmModal" class="mc-modal-overlay">
    <div class="mc-modal-box">
        <div class="mc-modal-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M12 16.01L12.01 15.999" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
            </svg>
        </div>
        <div class="mc-modal-title">تأكيد الإجراء</div>
        <div class="mc-modal-text" id="confirmModalText">هل أنت متأكد من تنفيذ هذا الإجراء؟</div>
        <div class="mc-modal-actions">
            <button type="button" class="mc-btn-confirm" id="confirmBtnYes">نعم، متأكد</button>
            <button type="button" class="mc-btn-cancel" id="confirmBtnNo">إلغاء</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/reception/views/waiting-list.js') }}"></script>
@endpush
