@extends('layouts.manager')

@section('title', 'إدارة الأدوية - Medicare')
@section('page-id', 'medicines')

@section('content')
<div class="medicines-page-wrapper">
    <div class="medicines-page-header">
        <h1 class="medicines-title"> الأدوية</h1>
        <p class="medicines-desc">عرض قائمة الأدوية المتاحة في المركز</p>
    </div>

    <div class="medicines-search">
        <input class="medicines-search-input" type="text" placeholder="ابحث عن اسم الدواء..." />
    </div>

    <div class="medicines-table" id="medicines" style="margin-top: 14rem;margin-right: -14rem;">
        <table class="styled-table" style="border: 1px solid #053052; border-collapse: collapse">
            <thead>
                <tr>
                    <th style="font-family: sans-serif;">اسم الدواء</th>
                    <th style="font-family: sans-serif;">تكلفة النقاط</th>
                    <th style="font-family: sans-serif;">تاريخ الانتهاء</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventories as $inventory)
                <tr>
                    <td style="text-align: center;font-weight: bold;font-size: 20px;">{{ $inventory->medicine->name }}</td>
                    <td style="text-align: center;font-weight: bold;font-size: 20px;">{{ $inventory->medicine->points_cost }}</td>
                    <td style="text-align: center;font-weight: bold;font-size: 20px;">{{ $inventory->medicine->expiry_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="medicines-pagination" aria-label="pagination">
            <button type="button" class="pg-prev" id="pgPrev"><span class="pg-arrow">→</span><span class="pg-text">السابق</span></button>
            <div class="pg-pages" id="pgPages">
                <button type="button" class="pg-page is-active" data-page="1">١</button>
                <button type="button" class="pg-page" data-page="2">٢</button>
                <button type="button" class="pg-page" data-page="3">٣</button>
            </div>
            <button type="button" class="pg-next" id="pgNext"><span class="pg-text">التالي</span><span class="pg-arrow">←</span></button>
        </div>
    </div>
</div>
@endsection
