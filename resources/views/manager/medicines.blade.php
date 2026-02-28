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
                        <option value="{{ $med->id }}">{{ $med->name }}</option>
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
        <div class="search-box">
            <form action="{{ route('manager.inventory.index') }}" method="GET">
                <input type="text" name="search" class="search-input" placeholder="ابحث عن اسم دواء معين..." value="{{ request('search') }}">
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-card">
        <table class="medicine-table">
            <thead>
                <tr>
                    <th>اسم الدواء</th>
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
                        {{ $inv->medicine->expiry_date ? \Carbon\Carbon::parse($inv->medicine->expiry_date)->format('H:i:s Y-m-d') : '---' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 40px; color: #999;">لا توجد أدوية متوفرة في المخزون حالياً</td>
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
