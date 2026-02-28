<aside class="sidebar-new">
    <div class="sidebar-brand">
        <img class="sidebar-logo" src="{{ asset('assets/admin/logos/white-logo.svg') }}" alt="Medicare" />
    </div>

    <nav class="sidebar-nav" aria-label="القائمة الجانبية">
        <a class="sidebar-link nav-link {{ Request::routeIs('superadmin.dashboard') ? 'active' : '' }}" href="{{ route('superadmin.dashboard') }}">
            <img class="sidebar-icon" src="{{ asset('assets/admin/icons/dashboard.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">لوحة التحكم</span>
        </a>

        <a class="sidebar-link nav-link {{ Request::routeIs('superadmin.users.*') ? 'active' : '' }}" href="{{ route('superadmin.users.index') }}">
            <img class="sidebar-icon" src="{{ asset('assets/admin/icons/users-management.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">إدارة المستخدمين</span>
        </a>

        <a class="sidebar-link nav-link {{ Request::routeIs('superadmin.roles.*') ? 'active' : '' }}" href="{{ route('superadmin.roles.index') }}">
            <img class="sidebar-icon" src="{{ asset('assets/admin/icons/role-management.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">إدارة الأدوار</span>
        </a>

        <a class="sidebar-link nav-link {{ Request::routeIs('superadmin.centers.*') ? 'active' : '' }}" href="{{ route('superadmin.centers.index') }}">
            <img class="sidebar-icon" src="{{ asset('assets/admin/icons/side-medical-centers.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">المراكز الطبية</span>
        </a>

        <a class="sidebar-link nav-link {{ Request::routeIs('superadmin.medicines.*') ? 'active' : '' }}" href="{{ route('superadmin.medicines.index') }}">
            <img class="sidebar-icon" src="{{ asset('assets/admin/icons/side-medicines.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">الأدوية</span>
        </a>

        <a class="sidebar-link nav-link {{ Request::routeIs('superadmin.reports.*') ? 'active' : '' }}" href="{{ route('superadmin.reports.index') }}">
            <img class="sidebar-icon" src="{{ asset('assets/admin/icons/reports.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">التقارير</span>
        </a>

        <a class="sidebar-link nav-link {{ Request::routeIs('superadmin.profile') ? 'active' : '' }}" href="{{ route('superadmin.profile') }}">
            <img class="sidebar-icon" src="{{ asset('assets/admin/icons/profile.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">الملف الشخصي</span>
        </a>
    </nav>
</aside>
