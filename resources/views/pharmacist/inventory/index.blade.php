@extends('layouts.pharmacist')

@section('title', 'المخزون')
@section('page-id', 'inventory')

@push('styles')
    <link href="{{ asset('css/pharmacist/views/inventory.css') }}" rel="stylesheet">
@endpush

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
