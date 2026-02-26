@extends('layouts.pharmacist')

@section('title', 'المخزون')
@section('page-id', 'inventory')

@section('styles')
<style>
.inventory-card {
    background: #fff;
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.table-header h2 { font-size: 20px; font-weight: 700; margin: 0; color: #0f3554; }

.inventory-table {
    width: 100%;
    border-collapse: collapse;
}

.inventory-table th {
    background: #f9fafb;
    padding: 15px;
    text-align: right;
    font-size: 14px;
    color: #4b5563;
    border-bottom: 1px solid #e5e7eb;
}

.inventory-table td {
    padding: 15px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
}

.badge {
    padding: 6px 12px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
}

.badge-available { background: #dcfce7; color: #166534; }
.badge-low { background: #fee2e2; color: #991b1b; }
.badge-empty { background: #f3f4f6; color: #4b5563; }
</style>
@endsection

@section('content')
<div class="inventory-card">
    <div class="table-header">
        <h2>مخزون الصيدلية - {{ auth()->user()->medicalCenter->name ?? 'المركز' }}</h2>
    </div>

    <table class="inventory-table">
        <thead>
            <tr>
                <th>اسم الدواء</th>
                <th>التصنيف</th>
                <th>الكمية المتوفرة</th>
                <th>تكلفة النقاط</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventory as $item)
            <tr>
                <td style="font-weight:600;">{{ $item->medicine->name }}</td>
                <td style="color: #6b7280;">{{ $item->medicine->category }}</td>
                <td>{{ $item->quantity }} عبوة</td>
                <td>{{ $item->medicine->points_cost }} نقطة</td>
                <td>
                    @if($item->quantity > 10)
                        <span class="badge badge-available">متوفر</span>
                    @elseif($item->quantity > 0)
                        <span class="badge badge-low">قارب على النفاد</span>
                    @else
                        <span class="badge badge-empty">نفذ</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #888; padding: 30px;">لا يوجد بيانات أدوية في هذا المركز.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($inventory->hasPages())
    <div class="custom-pagination-nav">
        {{ $inventory->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
