@extends('layouts.pharmacist')

@section('title', 'البحث عن وصفة')

@push('styles')
    <link href="{{ asset('css/pharmacist/views/prescriptions-index.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <div class="title">البحث عن الوصفات والمراجعات</div>
    <div class="subtitle">البحث عن الوصفات الطبية والاطلاع على سجل المراجعات</div>

    <div class="filters">
        <div class="search-box">
            <form action="{{ route('pharmacist.prescriptions.index') }}" method="GET" style="display:flex; align-items:center; gap:10px;">
                @if(request('filter'))
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif
                <div style="position:relative;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المريض أو رقم الهوية..." style="padding-right: 12px;" />
                </div>
                <button type="submit" class="search-submit-btn" aria-label="بحث" title="بحث">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </form>
        </div>

        <div class="filter-buttons">
            <a href="{{ route('pharmacist.prescriptions.index', ['filter' => 'today', 'search' => request('search')]) }}" 
               class="filter-btn {{ request('filter', 'today') == 'today' ? 'active' : '' }}">اليوم</a>
            <a href="{{ route('pharmacist.prescriptions.index', ['filter' => 'week', 'search' => request('search')]) }}" 
               class="filter-btn {{ request('filter') == 'week' ? 'active' : '' }}">الأسبوع</a>
            <a href="{{ route('pharmacist.prescriptions.index', ['filter' => 'patients', 'search' => request('search')]) }}" 
               class="filter-btn {{ request('filter') == 'patients' ? 'active' : '' }}">المرضى</a>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>التاريخ والوقت</th>
                    <th>المريض</th>
                    <th>الأدوية المصروفة</th>
                    <th>النقاط</th>
                    <th>الحالة</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prescriptions as $p)
                @php
                    $totalPoints = $p->items->sum(function($item) {
                        return ($item->medicine->points_cost ?? 0) * $item->quantity;
                    });
                    
                    $statusLabel = $p->status;
                    $badgeClass = ($statusLabel == 'تم الصرف' ? 'full' : ($statusLabel == 'لم يتم الصرف' ? 'new' : 'partial'));
                @endphp
                <tr>
                    <td>
                        {{ $p->created_at->format('Y/m/d') }}<br>
                        {{ $p->created_at->format('h:i') }} {{ $p->created_at->format('A') == 'AM' ? 'ص' : 'م' }}
                    </td>
                    <td>
                        {{ $p->patient->name }}<br>
                        <small>ID:{{ $p->patient->national_id }}</small>
                    </td>
                    <td>
                        @foreach($p->items as $item)
                            {{ $item->medicine->name }}{{ !$loop->last ? '، ' : '' }}
                        @endforeach
                    </td>
                    <td class="points">
                        {{ $totalPoints }} نقطة
                    </td>
                    <td>
                         <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td>
                        <a href="{{ route('pharmacist.prescriptions.show', $p->id) }}" style="color: #0764AE; font-weight: 700; text-decoration: underline;">التفاصيل</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 30px; color: #777;">لا توجد نتائج مطابقة لبحثك.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($prescriptions->hasPages())
        <div class="custom-pagination-nav">
            {{ $prescriptions->links('pagination::bootstrap-4') }}
        </div>
        @endif
        
        <div class="show-more">عرض باقي السجلات</div>
    </div>
</div>
@endsection
