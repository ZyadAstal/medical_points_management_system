<aside class="sidebar-new">
    <div class="sidebar-brand">
      <img class="sidebar-logo" src="{{ asset('assets/doctor/logos/white-logo.svg') }}" alt="Medicare" />
    </div>

    <nav class="sidebar-nav" aria-label="القائمة الجانبية">
      <a class="sidebar-link nav-link @if(request()->routeIs('doctor.dashboard')) active @endif" href="{{ route('doctor.dashboard') }}" data-page="dashboard">
        <img class="sidebar-icon" src="{{ asset('assets/doctor/icons/dashboard-icon.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">لوحة التحكم</span>
      </a>

      <a class="sidebar-link nav-link @if(request()->routeIs('doctor.patients.index')) active @endif" href="{{ route('doctor.patients.index') }}" data-page="today-patients">
        <img class="sidebar-icon" src="{{ asset('assets/doctor/icons/today-icon.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">مرضى اليوم</span>
      </a>

      <a class="sidebar-link nav-link @if(request()->routeIs('doctor.patients.search')) active @endif" href="{{ route('doctor.patients.search') }}" data-page="search-patient">
        <img class="sidebar-icon" src="{{ asset('assets/doctor/icons/search-patient.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">البحث عن مريض</span>
      </a>

      <a class="sidebar-link nav-link" href="#" data-page="recipes-record">
        <img class="sidebar-icon" src="{{ asset('assets/doctor/icons/recipes.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">سجل الوصفات</span>
      </a>

      <a class="sidebar-link nav-link @if(request()->routeIs('profile.show')) active @endif" href="{{ route('profile.show') }}" data-page="profile">
        <img class="sidebar-icon" src="{{ asset('assets/doctor/icons/profile-icon.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">الملف الشخصي</span>
      </a>
    </nav>
  </aside>

  <script src="{{ asset('js/sidebar.js') }}"></script>
