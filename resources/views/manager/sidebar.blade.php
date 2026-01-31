<aside class="sidebar-new">
    <div class="sidebar-brand">
        <img class="sidebar-logo" src="{{ asset('assets/manager/logos/white-logo.svg') }}" alt="Medicare" />
    </div>

    <nav class="sidebar-nav" aria-label="القائمة الجانبية">
        <a class="sidebar-link nav-link {{ Request::routeIs('manager.dashboard') ? 'active' : '' }}" href="{{ route('manager.dashboard') }}">
            <img class="sidebar-icon" src="{{ asset('assets/manager/icons/dashboard.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">لوحة التحكم</span>
        </a>

        <a class="sidebar-link nav-link {{ Request::routeIs('manager.staff.*') ? 'active' : '' }}" href="{{ route('manager.staff.index') }}">
            <img class="sidebar-icon" src="{{ asset('assets/manager/icons/users-management.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">الموظفين</span>
        </a>

        <a class="sidebar-link nav-link {{ Request::routeIs('manager.patients.*') ? 'active' : '' }}" href="{{ route('manager.patients.index') }}">
            <img class="sidebar-icon" src="{{ asset('assets/manager/icons/users-management.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">المرضى</span>
        </a>

        <a class="sidebar-link nav-link {{ Request::routeIs('manager.dispensing.*') ? 'active' : '' }}" href="{{ route('manager.dispensing.index') }}">
            <img class="sidebar-icon" src="{{ asset('assets/manager/icons/side-medical-centers.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">الصرف</span>
        </a>

        <a class="sidebar-link nav-link {{ Request::routeIs('manager.inventory.*') ? 'active' : '' }}" href="{{ route('manager.inventory.index') }}">
            <img class="sidebar-icon" src="{{ asset('assets/manager/icons/side-medicines.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">الأدوية</span>
        </a>

        <a class="sidebar-link nav-link {{ Request::routeIs('manager.reports.*') ? 'active' : '' }}" href="{{ route('manager.reports.index') }}">
            <img class="sidebar-icon" src="{{ asset('assets/manager/icons/reports.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">التقارير</span>
        </a>

        <a class="sidebar-link nav-link {{ Request::routeIs('profile.show') ? 'active' : '' }}" href="{{ route('profile.show') }}">
            <img class="sidebar-icon" src="{{ asset('assets/manager/icons/profile.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">الملف الشخصي</span>
        </a>
    </nav>
</aside>
