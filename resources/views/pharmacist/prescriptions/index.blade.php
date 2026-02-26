@extends('layouts.pharmacist')

@section('title', 'البحث عن وصفة')

@section('styles')
<style>
/* ===== Content ===== */
.container{
  padding:20px;
}

.title{
  font-size:32px;
  font-weight:700;
  margin-bottom:5px;
  color: #053052;
  margin-right: 30px;
}

.subtitle{
  font-size:18px;
  color:#6184A0;
  margin-bottom:20px;
  margin-right: 30px;
}

/* ===== Filters ===== */
.filters {
  display: flex;
  justify-content: space-between;
  margin-bottom: 25px;
}

.filter-buttons {
  display: flex;
  gap: 10px;
}

.filter-btn {
  padding: 6px 14px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
  font-size: 17px;
  text-decoration: none;
  color: #333;
}

.filter-btn.active {
  background: #1d4ed8;
  color: #fff;
  border-color: #1d4ed8;
}

.search-box {
  position: relative;
}

.search-box input {
  padding: 6px 35px 6px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  width: 400px;
  font-size: 15px;
}

.search-icon {
  position: absolute;
  right: 10px;
  top: 45%;
  transform: translateY(-50%);
  font-size: 14px;
  color: #cbd5e1;
  pointer-events: none;
}

/* ===== Table ===== */
.card{
  background:#fff;
  border-radius:12px;
  padding:10px;
  box-shadow:0 4px 12px rgba(0,0,0,0.05);
}

table{
  width:100%;
  border-collapse:collapse;
  font-size:13px;
}

thead{
  background:#f9fafb;
}

th,td{
  padding:12px 8px;
  border-bottom:1px solid #eee;
}

thead th{
  background: #F8F8F8;
  color: #7F7676;
  text-align: center;
  font-weight: 600;
}

tbody td {
    text-align: center;
}

.points{
  color:#2563eb;
  font-weight:600;
}

/* ===== Status Badges ===== */
.badge{
  padding:4px 10px;
  border-radius:6px;
  font-size:12px;
  font-weight:600;
  display:inline-block;
}

.badge.full{
  background:#d1fae5;
  color:#065f46;
}

.badge.partial{
  background:#fef3c7;
  color:#92400e;
}

.badge.new {
  background: #e0e7ff;
  color: #3730a3;
}

.show-more{
  text-align:center;
  padding:12px;
  font-size:13px;
  color:#6b7280;
  cursor:pointer;
}

.show-more:hover{
  color:#2563eb;
}

.pagination-wrapper {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}
</style>
@endsection

@section('content')
<div class="container">
    <div class="title">البحث عن الوصفات والمراجعات</div>
    <div class="subtitle">البحث عن الوصفات الطبية والاطلاع على سجل المراجعات</div>

    <div class="filters">
        <div class="search-box">
            <form action="{{ route('pharmacist.prescriptions.index') }}" method="GET">
                @if(request('filter'))
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif
                <span class="search-icon">
                    <svg width="16" height="16" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المريض أو رقم الهوية..." />
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

@section('scripts')
<script>
// Logic handled by backend links now
</script>
@endsection
