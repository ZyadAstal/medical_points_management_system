@extends('layouts.manager')

@section('title', 'الموظفين - Medicare')
@section('page-id', 'users')

@push('styles')
    <link href="{{ asset('css/manager/pages-extra.css') }}" rel="stylesheet"/>
@endpush

@section('content')
<div class="users-frame19">
    <div class="users-title-wrap">
        <h1 class="users-title">إدارة الموظفين</h1>
        <p class="users-desc">إدارة وتنظيم موظفي المركز الطبي </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <strong>نجاح:</strong> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <strong>خطأ في البيانات:</strong>
            </div>
            <ul style="margin-right: 30px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="users-controls-wrap">
        <div class="users-left-controls">
            <button aria-label="إضافة مستخدم" class="users-add-btn" type="button" onclick="openAddModal()">
                <span class="users-add-plus">+</span>
                <span class="users-add-text">إضافة مستخدم</span>
            </button>
        </div>
        <form action="{{ route('manager.staff.index') }}" method="GET" class="users-filters" id="filterForm">
            <div class="users-filter filter-role">
                <div class="filter-label">الدور</div>
                <input type="hidden" name="role" id="roleInput" value="{{ request('role', 'all') }}">
                @php
                    $currentRoleName = 'الكل';
                    if(request('role') && request('role') != 'all') {
                        $currentRoleName = [
                            'Doctor' => 'طبيب',
                            'Pharmacist' => 'صيدلي',
                            'Reception' => 'استقبال'
                        ][request('role')] ?? request('role');
                    }
                @endphp
                <button class="filter-box" type="button" onclick="toggleMenu('role-menu')">
                    <span class="filter-text">{{ $currentRoleName }}</span>
                    <img alt="dropdown" class="filter-icon" src="{{ asset('assets/manager/icons/arrow.svg') }}" />
                </button>
                <div aria-hidden="true" class="filter-menu role-menu" id="role-menu">
                    <button class="menu-item" type="button" onclick="selectRole('all', 'الكل')">الكل</button>
                    @foreach($availableRoles as $role)
                        @php
                            $roleAr = ['Doctor' => 'طبيب', 'Pharmacist' => 'صيدلي', 'Reception' => 'استقبال'][$role->name] ?? $role->name;
                        @endphp
                        <button class="menu-item" type="button" onclick="selectRole('{{ $role->name }}', '{{ $roleAr }}')">{{ $roleAr }}</button>
                    @endforeach
                </div>
            </div>
            <input name="search" id="staffSearchInput" class="users-search" placeholder="ابحث عن اسم المستخدم..." type="text" value="{{ request('search') }}" style="margin-top: 1.5rem;" />
        </form>
    </div>
    <div class="users-table-wrap">
        <table class="users-table">
            <colgroup>
                <col class="col-name" />
                <col class="col-email" />
                <col class="col-role" />
                <col class="col-actions" />
            </colgroup>
            <thead>
                <tr>
                    <th class="th-name">الاسم</th>
                    <th class="th-email">البريد الإلكتروني</th>
                    <th class="th-role">الدور</th>
                    <th class="th-actions">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staff as $employee)
                <tr class="user-row">
                    <td class="cell-name">{{ $employee->name }}</td>
                    <td class="cell-email">{{ $employee->email }}</td>
                    <td class="cell-role">
                        @php
                            $roleNameAr = [
                                'Doctor' => 'طبيب',
                                'Pharmacist' => 'صيدلي',
                                'Reception' => 'استقبال'
                            ][$employee->role->name] ?? $employee->role->name;
                        @endphp
                        <span class="role-badge role-{{ strtolower($employee->role->name) }}">
                            {{ $roleNameAr }}
                        </span>
                    </td>
                    <td class="cell-actions">
                        <div class="actions-grid">
                            <button class="action-square" title="تعديل" type="button"
                                    onclick="openEditModal({{ $employee->id }}, '{{ $employee->name }}', '{{ $employee->username }}', '{{ $employee->email }}', {{ $employee->role_id }})">
                                <img src="{{ asset('assets/manager/icons/edit.svg') }}" width="20" height="20" alt="تعديل">
                            </button>
                            <form action="{{ route('manager.staff.destroy', $employee) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف الموظف ؟')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="action-square" title="حذف" type="submit">
                                    <img src="{{ asset('assets/manager/icons/delete.svg') }}" width="20" height="20" alt="حذف">
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">لا يوجد موظفين حالياً في هذا التصنيف</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div aria-label="ترقيم الصفحات" class="users-pagination">
        {{ $staff->links() }}
    </div>
</div>

<!-- Modals same as superadmin but with manager assets if needed -->
@include('shared.manager-user-modals')
@endsection

@push('scripts')
    <script src="{{ asset('js/manager/employee.js') }}"></script>
@endpush
