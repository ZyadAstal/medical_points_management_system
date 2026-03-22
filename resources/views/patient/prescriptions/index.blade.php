@extends('layouts.patient')

@section('title', 'وصفاتي الطبية - Medicare')
@section('page-id', 'prescriptions')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/patient/prescriptions.css') }}">
@endpush

@section('content')
<div class="container" style="padding: 20px;">
    <div class="section-title" style="color: #053052; font-size: 32px; font-weight: 700;">سجل الوصفات الطبية</div>
    <div class="section-subtitle" style="color: #6184A0; font-size: 18px; margin-bottom: 30px;">عرض جميع الوصفات الطبية الخاصة بك وحالة صرفها</div>

    <div id="prescriptionsList">
        @forelse($prescriptions as $prescription)
            @php
                $allItems = $prescription->items;
                $dispensedCount = $allItems->where('is_dispensed', true)->count();
                $statusClass = 'status-none';
                $statusText = 'لم تُصرف';
                
                if ($dispensedCount > 0) {
                    if ($dispensedCount == $allItems->count()) {
                        $statusClass = 'status-full';
                        $statusText = 'صُرفت بالكامل';
                    } else {
                        $statusClass = 'status-partial';
                        $statusText = 'صُرفت جزئياً';
                    }
                }
            @endphp
            <div class="pres-card">
                <div class="pres-right">
                    <div class="doc-icon"><i class="far fa-file-alt"></i></div>
                    <div class="pres-info">
                        <h3>د. {{ $prescription->doctor->name }}</h3>
                        <span>{{ $prescription->doctor->medicalCenter?->name ?? '---' }} &nbsp; | &nbsp; {{ $prescription->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
                <div class="pres-left">
                    <div class="status-tag {{ $statusClass }}">{{ $statusText }}</div>
                    <button class="eye-btn" onclick="openPresModal({{ $prescription->toJson() }})">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="white-card" style="text-align: center; padding: 40px;">
                <p style="color: #888;">لا توجد وصفات طبية مسجلة.</p>
            </div>
        @endforelse
    </div>

    <div class="mc-pagination" style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $prescriptions->links('pagination::bootstrap-4') }}
    </div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-box">
        <div class="modal-title">
            <h2>تفاصيل الوصفة الطبية</h2>
            <span id="modalDate">---</span>
        </div>

        <div class="details-grid">
            <div class="detail-item">
                <label>الطبيب المعالج</label>
                <p id="modalDoc">---</p>
            </div>
            <div class="detail-item">
                <label>المركز الطبي</label>
                <p id="modalCenter">---</p>
            </div>
        </div>

        <div class="note-container">
            <i class="fas fa-info-circle"></i>
            <div class="note-text">
                <b>ملاحظات الطبيب :</b>
                <p id="modalNote">---</p>
            </div>
        </div>

        <h3 style="margin-bottom: 12px; font-size: 1rem; color: #1e293b;">الأدوية المكتوبة</h3>
        <table class="med-table">
            <thead>
                <tr>
                    <th>اسم الدواء</th>
                    <th>الكمية المقررة</th>
                    <th>المصروفة</th>
                </tr>
            </thead>
            <tbody id="modalTableBody">
                <!-- سيتم ملؤه بواسطة JS -->
            </tbody>
        </table>

        <button class="btn-close" onclick="closeModal()">إغلاق</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('modalOverlay');

    function openPresModal(data) {
        document.getElementById('modalDoc').innerText = 'د. ' + data.doctor.name;
        document.getElementById('modalCenter').innerText = (data.doctor && data.doctor.medical_center) ? data.doctor.medical_center.name : '---';
        document.getElementById('modalDate').innerText = new Date(data.created_at).toLocaleDateString('ar-EG');
        document.getElementById('modalNote').innerText = data.notes || 'لا توجد ملاحظات';

        const tbody = document.getElementById('modalTableBody');
        tbody.innerHTML = "";
        
        data.items.forEach(item => {
            tbody.innerHTML += `
                <tr>
                    <td>${item.medicine.name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.dispense ? item.dispense.quantity : 0}</td>
                </tr>
            `;
        });

        modal.style.display = 'flex';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    window.onclick = function(e) { if (e.target == modal) closeModal(); }
</script>
@endpush
