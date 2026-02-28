@extends('layouts.admin')

@section('title', 'إدارة الأدوار - Medicare')
@section('page-id', 'roles')

@section('content')
<div class="page-title-block">
    <h1>إدارة الأدوار</h1>
    <p>عرض وإدارة أدوار مستخدمي النظام</p>
</div>



<div class="controls controls--roles">
    <div class="controls__wrap roles-controls">
        <form action="{{ route('superadmin.roles.index') }}" method="GET" class="roles-filters">
            <div class="roles-filters__right">
                <div class="filter-block">
                    <div class="filter-label">الدور</div>
                    <select aria-label="فلتر الدور" class="select select--filter select--role" name="role" onchange="this.form.submit()">
                        <option value="">كل الأدوار</option>
                        @foreach($allRoles as $r)
                            <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="roles-filters__left">
                <div class="filter-block filter-block--search">
                    <input aria-label="بحث عن اسم الدور" name="search" value="{{ request('search') }}" class="input input--search roles-search" placeholder="ابحث عن اسم الدور..." type="text"/>
                </div>
            </div>
        </form>
        <div class="table-card roles-table">
            <div class="roles-table-wrap">
                <table class="users-table roles-table">
                    <colgroup>
                        <col style="width:50%;"/>
                        <col style="width:30%;"/>
                        <col style="width:20%;"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>اسم الدور</th>
                            <th>عدد المستخدمين</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ number_format($role->users_count) }}</td>
                            <td class="cell-actions">
                                <div class="actions-grid">
                                    <button class="action-square" title="تعديل" type="button" 
                                            onclick="editRole({{ $role->id }}, '{{ $role->name }}', {{ $role->users_count }})">
                                        <img alt="edit" src="{{ asset('assets/admin/icons/edit.svg') }}"/>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div aria-label="Pagination" class="users-pagination">
                {{ $roles->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div aria-hidden="true" class="modal-overlay" id="roleEditOverlay">
    <div aria-labelledby="roleEditTitle" aria-modal="true" class="role-modal" role="dialog">
        <h2 class="role-modal__title" id="roleEditTitle">تعديل الدور</h2>
        <div aria-hidden="true" class="role-modal__line"></div>
        <form id="roleEditForm" method="POST">
            @csrf
            @method('PUT')
            <div class="role-modal__field">
                <label class="role-modal__label" for="roleNameInput">اسم الدور</label>
                <input autocomplete="off" class="role-modal__input" id="roleNameInput" name="name" type="text" required/>
            </div>
            <div class="role-modal__field">
                <label class="role-modal__label" for="roleUsersInput">عدد المستخدمين (للعرض فقط)</label>
                <input class="role-modal__input" id="roleUsersInput" type="number" readonly disabled/>
            </div>
            <div class="role-modal__actions">
                <button class="role-modal__btn role-modal__btn--save btn-drug-primary" type="submit">حفظ</button>
                <button class="role-modal__btn role-modal__btn--cancel btn-drug-cancel" id="roleCancelBtn" type="button">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/roles-modal.js') }}"></script>
@endpush
