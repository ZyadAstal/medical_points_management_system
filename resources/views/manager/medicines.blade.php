@extends('layouts.manager')

@section('title', 'إدارة الأدوية - Medicare')
@section('page-id', 'medicines')

@push('styles')
    <link href="{{ asset('css/manager/medicines.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="medicines-container">
    
    <div class="page-header">
        <h1 class="page-title">الأدوية</h1>
        <p class="page-subtitle">عرض وتحديث كميات الأدوية المتاحة في المركز</p>
    </div>



    <!-- Update Section -->
    <div class="update-card">
        <h2 class="update-title">تحديث الكميات</h2>
        <form action="{{ route('manager.inventory.update') }}" method="POST" class="update-form">
            @csrf
            
            <div class="form-group">
                <label>اختر الدواء</label>
                <select name="medicine_id" class="form-control" required>
                    <option value="" disabled selected>-- اختر الدواء من القائمة --</option>
                    @foreach($all_medicines as $med)
                        <option value="{{ $med->id }}">{{ $med->name }}{{ $med->name_en ? ' - ' . $med->name_en : '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>الكمية الكلية الجديدة</label>
                <input type="number" name="quantity" class="form-control" value="0" min="0" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-update">تحديث المخزون</button>
            </div>
        </form>
    </div>

    <!-- Search Section -->
    <div class="search-wrapper">
        <div class="search-box" style="display:flex; align-items:center; gap:8px;">
            <form action="{{ route('manager.inventory.index') }}" method="GET" style="display:flex; align-items:center; gap:8px; width:100%;">
                <input type="text" name="search" class="search-input" placeholder="ابحث عن اسم دواء معين..." value="{{ request('search') }}">
                <button type="submit" class="search-submit-btn" aria-label="بحث" title="بحث">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-card">
        <table class="medicine-table">
            <thead>
                <tr>
                    <th>اسم الدواء (عربي)</th>
                    <th>الاسم الإنجليزي</th>
                    <th>تكلفة النقاط</th>
                    <th>الكمية المتوفرة</th>
                    <th>الحالة</th>
                    <th>تاريخ الانتهاء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventories as $inv)
                <tr>
                    <td style="font-size: 18px;">{{ $inv->medicine->name }}</td>
                    <td style="font-size: 16px; direction:ltr; text-align:left;">{{ $inv->medicine->name_en ?? '-' }}</td>
                    <td>{{ $inv->medicine->points_cost }}</td>
                    <td style="color: var(--primary-navy); font-size: 20px;">{{ $inv->quantity }}</td>
                    <td>
                        @if($inv->quantity <= 0)
                            <span class="badge-out">غير متوفر</span>
                        @elseif($inv->quantity < 10)
                            <span class="badge-warning">مخزون<br>منخفض</span>
                        @else
                            <span class="badge-available">متوفر</span>
                        @endif
                    </td>
                    <td style="font-family: 'Courier New', monospace; font-size: 16px;">
                        {{ $inv->medicine->expiry_date ? \Carbon\Carbon::parse($inv->medicine->expiry_date)->format('Y-m-d') : '---' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 40px; color: #999;">لا توجد أدوية متوفرة في المخزون حالياً</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $inventories->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
