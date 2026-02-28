@extends('layouts.admin')

@section('title', 'إدارة المستخدمين - Medicare')
@section('page-id', 'users')

@push('styles')
    <link href="{{ asset('css/admin/pages-extra.css') }}" rel="stylesheet"/>
@endpush

@section('content')
<div class="users-frame19">
    <div class="users-title-wrap">
        <h1 class="users-title">إدارة المستخدمين</h1>
        <p class="users-desc">عرض وإدارة مستخدمي النظام</p>
    </div>


        <form action="{{ route('superadmin.users.index') }}" method="GET" class="users-filters-form" style="width: 100%;">
            <div class="users-controls-wrap">
                <div class="users-left-controls">
                    <button aria-label="إضافة مستخدم" class="users-add-btn" id="mcAddUserBtn" type="button">
                        <span class="users-add-plus">+</span>
                        <span class="users-add-text">إضافة مستخدم</span>
                    </button>
                    <div class="users-search-wrap">
                        <input aria-label="بحث عن اسم المستخدم" name="search" value="{{ request('search') }}" class="users-search" placeholder="ابحث عن اسم المستخدم..." type="text"/>
                    </div>
                </div>
                <div class="users-filters">
                    <!-- Hidden inputs for form submission -->
                    <input type="hidden" name="role" id="hidden_filter_role" value="{{ request('role') }}">
                    <input type="hidden" name="center" id="hidden_filter_center" value="{{ request('center') }}">

                    <div class="users-filter filter-role">
                        <div class="filter-label">الدور</div>
                        <button class="filter-box" type="button">
                            <span class="filter-text">
                                @if(request('role'))
                                    {{ request('role') }}
                                @else
                                    اختر الدور
                                @endif
                            </span>
                            <img alt="dropdown" class="filter-icon" src="{{ asset('assets/admin/icons/arrow.svg') }}"/>
                        </button>
                        <div aria-hidden="true" class="filter-menu role-menu">
                            <button class="menu-item" data-value="" type="button">الكل</button>
                            @foreach($roles as $role)
                                <button class="menu-item" data-value="{{ $role->name }}" type="button">{{ $role->name }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div class="users-filter filter-center">
                        <div class="filter-label">المركز الطبي</div>
                        <button class="filter-box" type="button">
                            <span class="filter-text">
                                @if(request('center'))
                                    @php $c = $centers->firstWhere('id', request('center')); @endphp
                                    {{ $c ? $c->name : 'اختر المركز الطبي' }}
                                @else
                                    اختر المركز الطبي
                                @endif
                            </span>
                            <img alt="dropdown" class="filter-icon" src="{{ asset('assets/admin/icons/arrow.svg') }}"/>
                        </button>
                        <div aria-hidden="true" class="filter-menu center-menu">
                            <button class="menu-item" data-value="" type="button">الكل</button>
                            @foreach($centers as $cnter)
                                <button class="menu-item" data-value="{{ $cnter->id }}" type="button">{{ $cnter->name }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <div class="users-table-wrap">
        <table class="users-table">
            <colgroup>
                <col class="col-name"/>
                <col class="col-email"/>
                <col class="col-role"/>
                <col class="col-center"/>
                <col class="col-actions"/>
            </colgroup>
            <thead>
                <tr>
                    <th class="th-name">الاسم</th>
                    <th class="th-email">البريد الإلكتروني</th>
                    <th class="th-role">الدور</th>
                    <th class="th-center">المركز الطبي</th>
                    <th class="th-actions">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="user-row">
                    <td class="cell-name">{{ $user->name }}</td>
                    <td class="cell-email">{{ $user->email }}</td>
                    <td class="cell-role">{{ $user->role->name }}</td>
                    <td class="cell-center">{{ $user->medicalCenter->name ?? '---' }}</td>
                    <td class="cell-actions">
                        <div aria-label="إجراءات المستخدم" class="actions-grid">
                            <button class="action-square" title="تعديل" type="button" 
                                    onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->username }}', '{{ $user->email }}', {{ $user->role_id }}, {{ $user->medical_center_id ?? 'null' }})">
                                <img alt="تعديل" height="20" src="{{ asset('assets/admin/icons/edit.svg') }}" width="20"/>
                            </button>
                            <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف المستخدم ؟')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="action-square" title="حذف" type="submit">
                                    <img alt="حذف" height="20" src="{{ asset('assets/admin/icons/delete.svg') }}" width="20"/>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div aria-label="ترقيم الصفحات" class="users-pagination">
        {{ $users->links('pagination::bootstrap-4') }}
    </div>
</div>

<!-- Modals -->
<div aria-hidden="true" class="modal-overlay" id="userAddOverlay">
    <div aria-labelledby="userAddTitle" aria-modal="true" class="user-add-modal" role="dialog">
        <section aria-label="Add User Form" class="user-add-panel">
            <h2 class="user-add-title" id="userAddTitle">اضافة مستخدم جديد</h2>
            <div aria-hidden="true" class="user-add-divider"></div>
            <form autocomplete="off" class="user-add-fields" id="userAddForm" action="{{ route('superadmin.users.store') }}" method="POST">
                @csrf
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_name">الاسم</label>
                    <input class="user-add-input" id="ua_name" name="name" placeholder="أدخل الاسم كامل" type="text" required/>
                </div>
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_username">اسم المستخدم (بالإنجليزي)</label>
                    <input class="user-add-input" id="ua_username" name="username" placeholder="أدخل اسم المستخدم" type="text" required/>
                </div>
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_email">البريد الالكتروني</label>
                    <input class="user-add-input" id="ua_email" name="email" placeholder="أدخل البريد الالكتروني" type="email" required/>
                </div>
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_role">الدور</label>
                    <select class="user-add-input user-add-select" id="ua_role" name="role_id" required>
                        <option disabled="" selected="" value="">اختر الدور</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_center">المركز الطبي</label>
                    <select class="user-add-input user-add-select" id="ua_center" name="medical_center_id">
                        <option value="">لا يوجد (مدير نظام)</option>
                        @foreach($centers as $cntr)
                            <option value="{{ $cntr->id }}">{{ $cntr->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_password">كلمة المرور</label>
                    <input class="user-add-input" id="ua_password" name="password" placeholder="أنشئ كلمة المرور" type="password" required/>
                </div>
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_password_confirm">تأكيد كلمة المرور</label>
                    <input class="user-add-input" id="ua_password_confirm" name="password_confirmation" placeholder="تأكيد كلمة المرور" type="password" required/>
                </div>
            </form>
            <div class="user-add-actions">
                <button class="user-add-btn save btn-drug-primary" onclick="document.getElementById('userAddForm').submit()" type="button">حفظ</button>
                <button class="user-add-btn cancel btn-drug-cancel" id="userAddCancelBtn" type="button">إلغاء</button>
            </div>
        </section>
    </div>
</div>

<div aria-hidden="true" class="modal-overlay" id="userEditOverlay">
    <div aria-labelledby="userEditTitle" aria-modal="true" class="user-edit-modal" role="dialog">
        <section aria-label="Edit User Form" class="user-edit-panel">
            <h2 class="user-edit-title" id="userEditTitle">تعديل مستخدم</h2>
            <div aria-hidden="true" class="user-edit-divider"></div>
            <form autocomplete="off" class="user-edit-fields" id="userEditForm" method="POST">
                @csrf
                @method('PUT')
                <div class="user-edit-field">
                    <label class="user-edit-label" for="edit-name">الاسم</label>
                    <input class="user-edit-input" id="edit-name" name="name" type="text" required/>
                </div>
                <div class="user-edit-field">
                    <label class="user-edit-label" for="edit-username">اسم المستخدم (بالإنجليزي)</label>
                    <input class="user-edit-input" id="edit-username" name="username" type="text" required/>
                </div>
                <div class="user-edit-field">
                    <label class="user-edit-label" for="edit-email">البريد الالكتروني</label>
                    <input class="user-edit-input" id="edit-email" name="email" type="email" required/>
                </div>
                <div class="user-edit-field">
                    <label class="user-edit-label" for="edit-role">الدور</label>
                    <select class="user-edit-select" id="edit-role" name="role_id" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="user-edit-field">
                    <label class="user-edit-label" for="edit-center">المركز الطبي</label>
                    <select class="user-edit-select" id="edit-center" name="medical_center_id">
                        <option value="">لا يوجد (مدير نظام)</option>
                        @foreach($centers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="user-edit-change-pass" id="openChangePassBtn" type="button">تغيير كلمة المرور</button>
                <div class="user-edit-actions">
                    <button class="user-edit-btn update btn-drug-primary" onclick="document.getElementById('userEditForm').submit()" type="button">تحديث</button>
                    <button class="user-edit-btn cancel btn-drug-cancel" id="userEditCancelBtn" type="button">إلغاء</button>
                </div>
            </form>
        </section>
    </div>
</div>

<div aria-hidden="true" class="modal-overlay" id="userDeleteOverlay">
    <div aria-labelledby="deleteTitle" aria-modal="true" class="user-delete-modal" role="dialog">
        <section aria-labelledby="deleteTitle" class="delete-panel">
            <h2 class="delete-title" id="deleteTitle">تحذير</h2>
            <p class="delete-question">هل أنت متأكد من حذف المستخدم ؟</p>
            <p class="delete-desc">سيتم حذف المستخدم نهائيًا ولا يمكن استرجاعه لاحقًا</p>
            <div class="delete-actions">
                <button class="delete-btn cancel btn-drug-cancel" id="userDeleteCancelBtn" type="button">إلغاء</button>
                <button class="delete-btn danger" id="userDeleteConfirmBtn" type="button">حذف</button>
            </div>
        </section>
    </div>
</div>

<div aria-hidden="true" class="modal-overlay" id="userChangePassOverlay">
    <div aria-labelledby="changePassTitle" aria-modal="true" class="user-change-pass-modal" role="dialog">
        <section aria-label="Change Password Panel" class="change-pass-panel">
            <div aria-hidden="true" class="change-pass-icon-wrap">
                <img alt="" class="change-pass-icon" src="{{ asset('assets/admin/icons/password-change.svg') }}"/>
            </div>
            <h2 class="change-pass-title" id="changePassTitle">تغيير كلمة المرور</h2>
            <form action="#" class="change-pass-form" id="changePassForm" onsubmit="return false;">
                <div class="change-pass-group">
                    <label class="change-pass-label" for="newPassword">كلمة المرور الجديدة</label>
                    <div class="change-pass-input-wrap">
                        <button aria-label="إظهار/إخفاء كلمة المرور" class="change-pass-eye" data-toggle-password="newPassword" type="button">
                            <img alt="" src="{{ asset('assets/admin/icons/eye.svg') }}"/>
                        </button>
                        <input class="change-pass-input" id="newPassword" minlength="8" placeholder="أنشئ كلمة المرور" required="required" type="password"/>
                    </div>
                </div>
                <div class="change-pass-group">
                    <label class="change-pass-label" for="confirmPassword">تأكيد كلمة المرور</label>
                    <div class="change-pass-input-wrap">
                        <button aria-label="إظهار/إخفاء كلمة المرور" class="change-pass-eye" data-toggle-password="confirmPassword" type="button">
                            <img alt="" src="{{ asset('assets/admin/icons/eye.svg') }}"/>
                        </button>
                        <input class="change-pass-input" id="confirmPassword" minlength="8" placeholder="تأكيد كلمة المرور" required="required" type="password"/>
                    </div>
                </div>
                <div class="change-pass-actions">
                    <button class="btn-update-change btn-drug-primary" id="changePassUpdateBtn" type="submit">تحديث</button>
                    <button class="btn-cancel-change btn-drug-cancel" id="changePassCancelBtn" type="button">إلغاء</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/users.js') }}"></script>
@endpush
