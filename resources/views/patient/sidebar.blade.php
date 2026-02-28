<aside class="sidebar-new">
    <div class="sidebar-brand">
      <img class="sidebar-logo" src="{{ asset('assets/patient/logos/white-logo.svg') }}" alt="Medicare" />
    </div>

    <nav class="sidebar-nav" aria-label="القائمة الجانبية">

      <a class="sidebar-link nav-link @if(request()->routeIs('patient.dashboard')) active @endif" href="{{ route('patient.dashboard') }}" data-page="dashboard">
        <img class="sidebar-icon" src="{{ asset('assets/patient/icons/dashboard-icon.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">لوحة التحكم</span>
      </a>
      <a class="sidebar-link nav-link @if(request()->routeIs('patient.prescriptions.index')) active @endif" href="{{ route('patient.prescriptions.index') }}" data-page="my-prescriptions">
        <img class="sidebar-icon" src="{{ asset('assets/patient/icons/prescriptions.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">وصفاتي الطبية</span>
      </a>
      <a class="sidebar-link nav-link @if(request()->routeIs('patient.dispense.index')) active @endif" href="{{ route('patient.dispense.index') }}" data-page="my-exchanges">
        <img class="sidebar-icon" src="{{ asset('assets/patient/icons/exchanges.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">صرفاتي الطبية</span>
      </a>
      <a class="sidebar-link nav-link @if(request()->routeIs('patient.profile')) active @endif" href="{{ route('patient.profile') }}" data-page="profile">
        <img class="sidebar-icon" src="{{ asset('assets/patient/icons/profile-icon.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">الملف الشخصي</span>
      </a>
    </nav>
  </aside>

  <script src="{{ asset('js/sidebar.js') }}"></script>
