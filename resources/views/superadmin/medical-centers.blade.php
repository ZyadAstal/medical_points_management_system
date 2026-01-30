@extends('layouts.admin')

@section('title', 'المراكز الطبية - Medicare')
@section('page-id', 'medical-centers')

@section('content')
<div class="medical-centers-header">
    <h1 class="medical-centers-title">المراكز الطبية</h1>
    <p class="medical-centers-desc">عرض ومتابعة جميع المراكز الطبية المعتمدة</p>
</div>

@if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <ul style="margin: 0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div aria-label="فلترة وبحث المراكز الطبية" class="mc-controls">
    <div class="mc-filter">
        <div class="mc-filter-label">المركز الطبي</div>
        <div class="mc-filter-box">
            <select aria-label="اختر المركز الطبي" class="mc-filter-select" onchange="window.location.href = '{{ route('superadmin.centers.index') }}?search=' + this.value">
                <option value="">كل المراكز</option>
                @foreach($centers as $center)
                    <option value="{{ $center->name }}" {{ request('search') == $center->name ? 'selected' : '' }}>{{ $center->name }}</option>
                @endforeach
            </select>
            <img alt="" class="mc-filter-arrow" src="{{ asset('assets/admin/icons/arrow.svg') }}"/>
        </div>
    </div>
    <form action="{{ route('superadmin.centers.index') }}" method="GET" class="mc-search">
        <div class="mc-search-box">
            <input class="mc-search-input" name="search" value="{{ request('search') }}" placeholder="ابحث عن اسم المركز الطبي..." type="text"/>
            <button type="submit" style="display:none;"></button>
        </div>
    </form>
    <div class="mc-add-btn" id="mcAddCenterBtn" role="button" tabindex="0">
        <span class="mc-add-plus">+</span>
        <span class="mc-add-text">إضافة مركز طبي</span>
    </div>
</div>
<div class="mc-table-wrap">
    <div class="mc-table">
        <div class="mc-table-head">
            <div class="mc-th">اسم المركز</div>
            <div class="mc-th">عدد المستخدمين</div>
            <div class="mc-th">الموقع</div>
            <div class="mc-th">الهاتف</div>
            <div class="mc-th">عدد عمليات الصرف</div>
            <div class="mc-th">الإجراءات</div>
        </div>
        <div class="mc-table-body" id="mcTableBody">
            @foreach($centers as $center)
            <div class="mc-row">
                <div class="mc-td mc-name">{{ $center->name }}</div>
                <div class="mc-td">{{ number_format($center->users_count) }}</div>
                <div class="mc-td">{{ $center->location }}</div>
                <div class="mc-td">{{ $center->phone }}</div>
                <div class="mc-td">{{ number_format($center->dispenses_count) }}</div>
                <div class="mc-td mc-actions">
                    <button aria-label="تعديل" class="mc-edit-action" type="button" 
                            onclick="editCenter({{ $center->id }}, '{{ $center->name }}', '{{ $center->location }}', '{{ $center->phone }}')">
                        <img alt="تعديل" src="{{ asset('assets/admin/icons/edit.svg') }}"/>
                    </button>
                    <form action="{{ route('superadmin.centers.destroy', $center) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف المركز؟')" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button aria-label="حذف" class="mc-delete-action" type="submit" title="حذف" style="background:none; border:none; cursor:pointer; padding:0; margin-right:8px; vertical-align: middle;">
                            <img alt="حذف" src="{{ asset('assets/admin/icons/delete.svg') }}" width="22"/>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<div class="mc-pagination">
    {{ $centers->links('pagination::bootstrap-4') }}
</div>

<!-- Modals -->
<div class="mc-modal-overlay" id="mcAddModal">
    <div aria-label="اضافة مركز طبي" aria-modal="true" class="mc-modal" role="dialog">
        <div class="mc-modal-inner">
            <h2 class="mc-modal-title">اضافة مركز طبي</h2>
            <hr class="mc-modal-sep"/>
            <form id="mcAddForm" action="{{ route('superadmin.centers.store') }}" method="POST" autocomplete="off" class="mc-modal-form">
                @csrf
                <div class="mc-field">
                    <label class="mc-field-label" for="mcAddCenterName">اسم المركز</label>
                    <input class="mc-field-input" dir="rtl" id="mcAddCenterName" name="name" placeholder="أدخل اسم المركز" type="text" required/>
                </div>
                <div class="mc-field">
                    <label class="mc-field-label" for="mcAddCenterLocation">الموقع</label>
                    <input class="mc-field-input" dir="rtl" id="mcAddCenterLocation" name="location" placeholder="أدخل موقع المركز" type="text"/>
                </div>
                <div class="mc-field">
                    <label class="mc-field-label" for="mcAddCenterPhone">الهاتف</label>
                    <input class="mc-field-input" dir="rtl" id="mcAddCenterPhone" name="phone" placeholder="أدخل رقم الهاتف" type="text"/>
                </div>
                <div class="mc-modal-actions">
                    <button class="mc-modal-btn mc-modal-btn-cancel btn-drug-cancel" id="mcAddCancelBtn" type="button">إلغاء</button>
                    <button class="mc-modal-btn mc-modal-btn-primary btn-drug-primary" type="submit">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="mc-modal-overlay" id="mcEditModal">
    <div aria-label="تعديل مركز طبي" aria-modal="true" class="mc-modal" role="dialog">
        <div class="mc-modal-inner">
            <h2 class="mc-modal-title">تعديل مركز طبي</h2>
            <hr class="mc-modal-sep"/>
            <form autocomplete="off" class="mc-modal-form" id="mcEditForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mc-field">
                    <label class="mc-label" for="mcEditName">اسم المركز</label>
                    <input class="mc-input" id="mcEditName" name="name" type="text" required/>
                </div>
                <div class="mc-field">
                    <label class="mc-label" for="mcEditLocation">الموقع</label>
                    <input class="mc-input" id="mcEditLocation" name="location" type="text"/>
                </div>
                <div class="mc-field">
                    <label class="mc-label" for="mcEditPhone">الهاتف</label>
                    <input class="mc-input" id="mcEditPhone" name="phone" type="text"/>
                </div>
                <div class="mc-modal-actions">
                    <button class="mc-modal-btn mc-modal-btn-cancel btn-drug-cancel" id="mcEditCancelBtn" type="button">إلغاء</button>
                    <button class="mc-modal-btn mc-modal-btn-primary btn-drug-primary" type="submit">تحديث</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/medical-centers-modals.js') }}"></script>
@endpush
