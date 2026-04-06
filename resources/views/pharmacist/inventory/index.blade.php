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

    <div class="inventory-search">
        <form action="{{ route('pharmacist.inventory.index') }}" method="GET">
            <input class="inventory-search-input" name="search" value="{{ request('search') }}" type="text" placeholder="ابحث عن اسم الدواء (عربي أو إنجليزي)..." />
            <button type="submit" class="search-submit-btn" aria-label="بحث" title="بحث">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </form>
    </div>

    <table class="inventory-table">
        <thead>
            <tr>
                <th>اسم الدواء (عربي)</th>
                <th>الاسم الإنجليزي</th>
                <th>الكمية المتوفرة</th>
                <th>تكلفة النقاط</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventory as $item)
            <tr>
                <td style="font-weight:600;">{{ $item->medicine->name }}</td>
                <td style="direction:ltr; text-align:left;">{{ $item->medicine->name_en ?? '-' }}</td>
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
