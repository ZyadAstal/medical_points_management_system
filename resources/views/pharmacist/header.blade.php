@php
    $user = Auth::user();
    $pharmacistName = $user->name;
    // Initials logic
    $words = explode(' ', $pharmacistName);
    $initials = (count($words) >= 2) 
        ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1)
        : mb_substr($pharmacistName, 0, 1);
@endphp

<header class="header header-v2" aria-label="Header">
    <div class="header-v2__inner">
        <div class="header-v2__user">
            <span class="header-v2__divider" aria-hidden="true"></span>
            <div class="header-v2__avatar" id="adminAvatar">{{ strtoupper($initials) }}</div>
            <div class="header-v2__meta">
                <div class="header-v2__name" id="adminNameText">{{ $pharmacistName }}</div>
                <div class="header-v2__role">صيدلي</div>
            </div>
            <div class="header-v2__menuWrap">
                <button class="header-v2__arrow" id="userMenuBtn" type="button" aria-label="قائمة المستخدم">
                    <img src="{{ asset('assets/pharmacist/icons/arrow.svg') }}" alt="" />
                </button>
                <div class="header-v2__userDropdown" id="userDropdown" aria-hidden="true">
                    <div class="user-dd__roleLabel">الدور</div>
                    <div class="user-dd__roleValue">صيدلي</div>
                    <span class="user-dd__line user-dd__line--1" aria-hidden="true"></span>
                    <div class="user-dd__emailLabel">البريد الالكتروني</div>
                    <div class="user-dd__emailValue">{{ $user->email }}</div>
                    <span class="user-dd__line user-dd__line--2" aria-hidden="true"></span>
                    <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
                        @csrf
                    </form>
                    <div class="user-dd__logoutText" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">تسجيل خروج</div>
                    <img class="user-dd__logoutIcon" src="{{ asset('assets/pharmacist/icons/exit.svg') }}" alt="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" />
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');

        function closeAll() {
            userDropdown?.classList.remove('open');
        }

        userMenuBtn?.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = userDropdown.classList.contains('open');
            closeAll();
            if (!isOpen) userDropdown.classList.add('open');
        });

        document.addEventListener('click', closeAll);
    });
</script>
