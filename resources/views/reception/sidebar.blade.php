<aside class="sidebar-new">
    <div class="sidebar-brand">
      <img class="sidebar-logo" src="{{ asset('assets/reception/logos/white-logo.svg') }}" alt="Medicare" />
    </div>

    <nav class="sidebar-nav" aria-label="القائمة الجانبية">

      <a class="sidebar-link nav-link @if(request()->routeIs('reception.dashboard')) active @endif" href="{{ route('reception.dashboard') }}" data-page="dashboard">
        <img class="sidebar-icon" src="{{ asset('assets/reception/icons/dashboard-icon.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">لوحة التحكم</span>
      </a>
      <a class="sidebar-link nav-link @if(request()->routeIs('reception.patients.create')) active @endif" href="{{ route('reception.patients.create') }}" data-page="patient-registration">
        <img class="sidebar-icon" src="{{ asset('assets/reception/icons/register-patient.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">تسجيل المريض</span>
      </a>
      <a class="sidebar-link nav-link" href="#" data-page="today-waiting-list">
        <img class="sidebar-icon" src="{{ asset('assets/reception/icons/waiting-list.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">قائمة الانتظار اليوم</span>
      </a>
      <a class="sidebar-link nav-link @if(request()->routeIs('reception.patients.index')) active @endif" href="{{ route('reception.patients.index') }}" data-page="search-patient">
        <img class="sidebar-icon" src="{{ asset('assets/reception/icons/search-patient.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">البحث عن مريض</span>
      </a>
      <a class="sidebar-link nav-link @if(request()->routeIs('profile.show')) active @endif" href="{{ route('profile.show') }}" data-page="profile">
        <img class="sidebar-icon" src="{{ asset('assets/reception/icons/profile-icon.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">الملف الشخصي</span>
      </a>
    </nav>
  </aside>

  <script src="{{ asset('js/sidebar.js') }}"></script>
