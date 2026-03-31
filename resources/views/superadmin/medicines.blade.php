@extends('layouts.admin')

@section('title', 'إدارة الأدوية - Medicare')
@section('page-id', 'medicines')

@section('content')
<div class="medicines-page-wrapper">
    <div class="medicines-page-header">
        <h1 class="medicines-title">إدارة الأدوية</h1>
        <p class="medicines-desc">إدارة مخزون الأدوية وتتبع الصرف وتواريخ الانتهاء</p>
    </div>



    <div class="medicines-search">
        <form action="{{ route('superadmin.medicines.index') }}" method="GET" style="display:flex; align-items:center; gap:8px;">
            <input class="medicines-search-input" name="search" value="{{ request('search') }}" type="text" placeholder="ابحث عن اسم الدواء..." />
            <button type="submit" class="search-submit-btn" aria-label="بحث" title="بحث">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </form>
        <button type="button" class="medicines-add-btn" id="openAddDrugModal">
            <span class="medicines-add-plus">+</span>
            <span class="medicines-add-text">اضافة دواء</span>
        </button>
    </div>

    <div class="medicines-table-wrap">
        <table class="medicines-table">

            <thead>
                <tr>
                    <th class="col-drug">اسم الدواء (عربي)</th>
                    <th class="col-drug">الاسم الإنجليزي</th>
                    <th class="col-points">تكلفة النقاط</th>
                    <th class="col-expiry">تاريخ الانتهاء</th>
                    <th class="col-actions">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medicines as $medicine)
                <tr>
                    <td class="col-drug-cell">{{ $medicine->name }}</td>
                    <td class="col-drug-cell" style="direction:ltr; text-align:left;">{{ $medicine->name_en ?? '-' }}</td>
                    <td class="col-points-cell">{{ $medicine->points_cost }}</td>
                    <td class="col-expiry-cell">{{ $medicine->expiry_date->format('Y-m-d') }}</td>
                    <td class="col-actions-cell">
                        <div class="actions-group" style="display: flex; gap: 8px; justify-content: center;">
                            <div class="action-edit" role="button" tabindex="0" aria-label="تعديل الدواء"
                                 onclick="editMedicine({{ $medicine->id }}, '{{ addslashes($medicine->name) }}', '{{ addslashes($medicine->name_en ?? '') }}', {{ $medicine->points_cost }}, '{{ $medicine->expiry_date->format('Y-m-d') }}')">
                                <img src="{{ asset('assets/admin/icons/edit.svg') }}" alt="edit" />
                            </div>
                            <form action="{{ route('superadmin.medicines.destroy', $medicine) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدواء؟')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    <img src="{{ asset('assets/admin/icons/delete.svg') }}" alt="delete" width="20" />
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="medicines-pagination">
        {{ $medicines->links('pagination::bootstrap-4') }}
    </div>
</div>

<!-- Modals -->
<div class="medicines-modal-overlay" id="editDrugModal" aria-hidden="true">
    <div class="medicines-modal-box" role="dialog" aria-modal="true" aria-label="تعديل دواء">
        <h2 class="modal-title">تعديل دواء</h2>
        <div class="modal-divider"></div>
        <form id="editDrugForm" method="POST">
            @csrf
            @method('PUT')
            <label class="modal-label modal-label-name" for="editDrugName">اسم الدواء (عربي)</label>
            <input id="editDrugName" name="name" class="modal-input modal-input-name" type="text" required />

            <label class="modal-label" for="editDrugNameEn" style="margin-top:10px;">الاسم بالإنجليزي <span style="font-size:12px;opacity:.7;">(اختياري)</span></label>
            <input id="editDrugNameEn" name="name_en" class="modal-input modal-input-name" type="text" placeholder="English name" style="direction:ltr;" />
            
            <label class="modal-label modal-label-points" for="editDrugPoints">عدد النقاط</label>
            <input id="editDrugPoints" name="points_cost" class="modal-input modal-input-points" type="number" required />
            
            <label class="modal-label modal-label-expiry" for="e_expiry">تاريخ الانتهاء</label>
            <div class="modal-date-field">
                <img class="modal-date-icon" src="{{ asset('assets/admin/icons/date.svg') }}" alt="date" />
                <div class="modal-date-divider"></div>
                <input id="e_expiry" name="expiry_date" class="modal-input modal-input-expiry" type="date" required style="padding-left: 0;" />
            </div>
            
            <button type="button" class="modal-btn modal-btn-cancel" id="editDrugCancel">إلغاء</button>
            <button type="submit" class="modal-btn modal-btn-update">تحديث</button>
        </form>
    </div>
</div>

<div class="medicines-modal-overlay" id="addDrugModal" aria-hidden="true">
    <div class="medicines-modal-box" role="dialog" aria-modal="true" aria-label="اضافة دواء">
        <h2 class="modal-title">اضافة دواء</h2>
        <div class="modal-divider"></div>
        <form action="{{ route('superadmin.medicines.store') }}" method="POST">
            @csrf
            <label class="modal-label modal-label-name" for="addDrugName">اسم الدواء (عربي)</label>
            <input id="addDrugName" name="name" class="modal-input modal-input-name" type="text" placeholder="ادخل اسم الدواء بالعربي" required />

            <label class="modal-label" for="addDrugNameEn" style="margin-top:10px;">الاسم بالإنجليزي <span style="font-size:12px;opacity:.7;">(اختياري)</span></label>
            <input id="addDrugNameEn" name="name_en" class="modal-input modal-input-name" type="text" placeholder="English name" style="direction:ltr;" />
            
            <label class="modal-label modal-label-points" for="addDrugPoints">تكلفة النقاط</label>
            <input id="addDrugPoints" name="points_cost" class="modal-input modal-input-points" type="number" placeholder="ادخل تكلفة النقاط" required />
            
            <label class="modal-label modal-label-expiry" for="a_expiry">تاريخ الانتهاء</label>
            <div class="modal-date-field">
                <img class="modal-date-icon" src="{{ asset('assets/admin/icons/date.svg') }}" alt="date" />
                <div class="modal-date-divider"></div>
                <input id="a_expiry" name="expiry_date" class="modal-input modal-input-expiry" type="date" required style="padding-left: 0;" />
            </div>
            
            <button type="button" class="modal-btn modal-btn-cancel" id="addDrugCancel">إلغاء</button>
            <button type="submit" class="modal-btn modal-btn-update" style="background: linear-gradient(90deg, #053052, #0B6CB8); border-color: #5B91E9;">حفظ</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/medicines-modals.js') }}"></script>
@endpush
