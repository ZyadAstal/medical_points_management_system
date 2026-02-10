<aside class="sidebar-new">
    <div class="sidebar-brand">
      <img class="sidebar-logo" src="{{ asset('assets/pharmacist/logos/white-logo.svg') }}" alt="Medicare" />
    </div>

    <nav class="sidebar-nav" aria-label="القائمة الجانبية">

      <a class="sidebar-link nav-link @if(request()->routeIs('pharmacist.dashboard')) active @endif" href="{{ route('pharmacist.dashboard') }}" data-page="dashboard">
        <img class="sidebar-icon" src="{{ asset('assets/pharmacist/icons/dashboard-icon.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">لوحة التحكم</span>
      </a>
      <a class="sidebar-link nav-link @if(request()->routeIs('pharmacist.prescriptions.search')) active @endif" href="{{ route('pharmacist.prescriptions.search') }}" data-page="new-exchange">
        <img class="sidebar-icon" src="{{ asset('assets/pharmacist/icons/exchange.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">صرف جديد</span>
      </a>
      <a class="sidebar-link nav-link" href="#" data-page="search-prescription">
        <img class="sidebar-icon" src="{{ asset('assets/pharmacist/icons/search-patient.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">بحث عن وصفة طبية للمريض</span>
      </a>
      <a class="sidebar-link nav-link" href="#" data-page="exchange-history">
        <img class="sidebar-icon" src="{{ asset('assets/pharmacist/icons/recipes.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">سجل الصرف</span>
      </a>
      <a class="sidebar-link nav-link" href="#" data-page="inventory">
        <img class="sidebar-icon" src="{{ asset('assets/pharmacist/icons/inventory.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">المخزون</span>
      </a>
      <a class="sidebar-link nav-link @if(request()->routeIs('profile.show')) active @endif" href="{{ route('profile.show') }}" data-page="profile">
        <img class="sidebar-icon" src="{{ asset('assets/pharmacist/icons/profile-icon.svg') }}" alt="" aria-hidden="true" />
        <span class="sidebar-link-text">الملف الشخصي</span>
      </a>
    </nav>
  </aside>

  <script src="{{ asset('js/sidebar.js') }}"></script>
