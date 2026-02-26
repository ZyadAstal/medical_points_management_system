<aside class="sidebar-new">
    <div class="sidebar-brand">
        <img class="sidebar-logo" src="{{ asset('assets/pharmacist/logos/white-logo.svg') }}" alt="Medicare" />
    </div>

    <nav class="sidebar-nav" aria-label="القائمة الجانبية">
        <a class="sidebar-link nav-link @if(request()->routeIs('pharmacist.dashboard')) active @endif" href="{{ route('pharmacist.dashboard') }}" data-page="dashboard">
            <img class="sidebar-icon" src="{{ asset('assets/pharmacist/icons/dashboard.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">لوحة التحكم</span>
        </a>

        <a class="sidebar-link nav-link @if(request()->routeIs('pharmacist.prescriptions.create')) active @endif" href="{{ route('pharmacist.prescriptions.create') }}" data-page="employee">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="0.5" y="0.5" width="23" height="23" rx="4.5" stroke="white"/>
                <path d="M10.8516 6.10795V17.6307H13.1442V6.10795H10.8516ZM6.24077 10.7273V13.0199H17.7635V10.7273H6.24077Z" fill="white"/>
            </svg>
            <span class="sidebar-link-text"> عملية صرف جديدة</span>
        </a>

        <a class="sidebar-link nav-link @if(request()->routeIs('pharmacist.prescriptions.index')) active @endif" href="{{ route('pharmacist.prescriptions.index') }}" data-page="roles">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <rect width="24" height="24" fill="url(#pattern0_1071_247)"/>
                <defs>
                    <pattern id="pattern0_1071_247" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <use xlink:href="#image0_1071_247" transform="scale(0.0333333)"/>
                    </pattern>
                    <image id="image0_1071_247" width="30" height="30" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAACXBIWXMAAAsTAAALEwEAmpwYAAABdUlEQVR4nO2WwUpCQRiFL5lusnYJvYL1DtneRUb5CmJYWk8RvoZZjxJEmyxISdu31kWtvvjtXJiVMjMXCfLAgOCc882d+e8/N0nW+isCCkAduAOGwEzDfvf1XyFr6AkwYbnGQC0L4AbQdYKfgUugDGxp7ANtYODMuzFvDLiroC+gsShMi2xq7hwes70o6NDDV3HgxyGFNJG5EbDoc3nfgbyPse6cqfdZATngRRmnPsZ7mS58oU5GRxm3PqaRTOUIsFW7aehjmspUjABvK2O6avBOCHiUwVYfpJXtY+rL1I4AXyujF/I6DSJep1dlnPk2kLGMzQBwS94PYNPXXJPZ2l/Fw3cEfMtb9V30XLplUri1wVyyeHtbDvQT2E1CxO+Nk8JRG+yoORQ1rHqvnDNNoen8UhDcZLeMGv4y2ZlW7UmdXh0Nz1vDt94LvKnJzLSgnlWvW0gGywzuKz15+mVii91L/gu85Gz748rADvwJeFgpeK1E+gHiePXNoZbl0QAAAABJRU5ErkJggg=="/>
                </defs>
            </svg>
            <span class="sidebar-link-text"> البحث عن وصفة</span>
        </a>

        <a class="sidebar-link nav-link @if(request()->routeIs('pharmacist.dispensing.history')) active @endif" href="{{ route('pharmacist.dispensing.history') }}" data-page="medical-centers">
            <img class="sidebar-icon" src="{{ asset('assets/pharmacist/icons/side-medical-centers.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">سجل العمليات </span>
        </a>

        <a class="sidebar-link nav-link @if(request()->routeIs('pharmacist.inventory.index')) active @endif" href="{{ route('pharmacist.inventory.index') }}" data-page="medicines">
            <img class="sidebar-icon" src="{{ asset('assets/pharmacist/icons/side-medicines.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">المخزون</span>
        </a>

        <a class="sidebar-link nav-link @if(request()->routeIs('pharmacist.profile')) active @endif" href="{{ route('pharmacist.profile') }}" data-page="profile">
            <img class="sidebar-icon" src="{{ asset('assets/pharmacist/icons/profile.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text">الملف الشخصي</span>
        </a>
    </nav>
</aside>
