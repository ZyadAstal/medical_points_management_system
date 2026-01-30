@php
    $user = Auth::user();
    $managerName = $user->name;
    $initials = mb_substr($managerName, 0, 1) . mb_substr($managerName, mb_strpos($managerName, ' ') + 1, 1);
@endphp

<header class="header header-v2" aria-label="Header">
    <div class="header-v2__inner">
        <div class="header-v2__user">
            <span class="header-v2__divider" aria-hidden="true"></span>
            <div class="header-v2__avatar" id="adminAvatar">{{ $initials }}</div>
            <div class="header-v2__meta">
                <div class="header-v2__name" id="adminNameText">{{ $managerName }}</div>
                <div class="header-v2__role">{{ $user->medicalCenter->name }}</div>
            </div>
            <div class="header-v2__menuWrap">
                <button class="header-v2__arrow" id="userMenuBtn" type="button" aria-label="قائمة المستخدم">
                    <img src="{{ asset('assets/manager/icons/arrow.svg') }}" alt="" />
                </button>
                <div class="header-v2__userDropdown" id="userDropdown" aria-hidden="true">
                    <div class="user-dd__roleLabel">الدور</div>
                    <div class="user-dd__roleValue">مدير مركز</div>
                    <span class="user-dd__line user-dd__line--1" aria-hidden="true"></span>
                    <div class="user-dd__emailLabel">البريد الالكتروني</div>
                    <div class="user-dd__emailValue">{{ $user->username }}</div>
                    <span class="user-dd__line user-dd__line--2" aria-hidden="true"></span>
                    <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
                        @csrf
                    </form>
                    <div class="user-dd__logoutText" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">تسجيل خروج</div>
                    <img class="user-dd__logoutIcon" src="{{ asset('assets/manager/icons/exit.svg') }}" alt="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" />
                </div>
            </div>
        </div>
        <button class="header-v2__notifBtn" id="notifBtn" type="button" aria-label="الإشعارات">
            <img src="{{ asset('assets/manager/icons/notfacation.svg') }}" alt="" />
            <span class="header-v2__notifDot" id="notifDot" aria-hidden="true"></span>
        </button>
        <div class="header-v2__notifMenu" id="notifMenu" aria-hidden="true">
            <div class="header-v2__notifTitle">الإشعارات</div>
            <div class="header-v2__notifEmpty" id="notifEmpty">لا توجد إشعارات جديدة</div>
        </div>
    </div>
</header>
