@extends('layouts.patient')

@section('title', 'البحث عن الأدوية - Medicare')
@section('page-id', 'medicine-search')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/patient/search.css') }}">
@endpush

@section('content')
<div class="container" style="padding: 20px;">
    <div class="profile-header">
      <h1 class="profile-title" style="margin-bottom: 10px; font-family: 'Tajawal', sans-serif; font-size: 28px; color: #002d52;">البحث عن توفر الأدوية</h1>
      <p class="profile-desc" style="font-family: 'Tajawal', sans-serif; color: #6184A0;">اعرف إن كان الدواء متوفرًا قبل التوجه إلى المركز الطبي</p>
    </div>

    <div class="search-section">
        <div class="search-card">
            <h3 style="font-family: 'Tajawal', sans-serif; margin-bottom: 15px;">البحث عن توفر الأدوية</h3>
            <p style="font-size: 14px; color: #8E8E8E; margin-bottom: 20px;">ابحث عن الدواء في المراكز الطبية المختلفة</p>
            <form action="{{ route('patient.medicines.search') }}" method="GET" class="input-group">
                <input type="text" name="query" value="{{ request('query') }}" placeholder="أدخل اسم الدواء هنا .." required>
                <select name="medical_center_id">
                    <option value="all">جميع المراكز</option>
                    @foreach($medicalCenters as $center)
                        <option value="{{ $center->id }}" {{ request('medical_center_id') == $center->id ? 'selected' : '' }}>
                            {{ $center->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> بحث
                </button>
            </form>
        </div>
    </div>

    @if(request()->has('query'))
        <div class="results-container">
            <div style="margin-bottom: 15px; font-size: 14px; color: #666;">
                نتائج البحث لـ "{{ request('query') }}" ({{ $medicines->count() }})
            </div>
            
            @forelse($medicines as $medicine)
                @foreach($medicine->inventories as $inventory)
                    @if(request('medical_center_id') == 'all' || request('medical_center_id') == $inventory->medical_center_id)
                        @php
                            $statusClass = 'status-available';
                            $statusText = 'متوفر';
                            $qtyText = 'كمية جيدة';
                            
                            if ($inventory->quantity <= 0) {
                                $statusClass = 'status-none';
                                $statusText = 'غير متوفر';
                                $qtyText = '0';
                            } elseif ($inventory->quantity <= 10) {
                                $statusClass = 'status-limited';
                                $statusText = 'كمية محدودة';
                                $qtyText = 'أقل من 10';
                            }
                        @endphp
                        <div class="medicine-card">
                            <div class="med-info-main">
                                <i class="fas fa-pills" style="font-size: 2rem; color: #0B6CB8"></i>
                                <div class="med-details">
                                    <h3 style="margin: 0;">{{ $medicine->name }}</h3>
                                    <p style="font-size: 0.8rem; color: #999; margin-top: 5px;">
                                        {{ $inventory->medicalCenter->name }} | انتهاء: {{ $medicine->expiry_date?->format('m/Y') ?? '---' }}
                                    </p>
                                </div>
                            </div>
                            <div style="text-align: center">
                                <small style="color: #999">الكمية التقريبية</small><br>
                                <b>{{ $qtyText }}</b>
                            </div>
                            <div class="status-badge {{ $statusClass }}">{{ $statusText }}</div>
                        </div>
                    @endif
                @endforeach
            @empty
                <div class="white-card" style="text-align: center; padding: 40px; color: #888;">
                    لم يتم العثور على أدوية تطابق بحثك.
                </div>
            @endforelse
        </div>
    @endif
</div>
@endsection
