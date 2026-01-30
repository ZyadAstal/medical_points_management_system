<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>@yield('title', 'Medicare - Manager')</title>
    <link href="{{ asset('css/manager/style.css') }}" rel="stylesheet"/>
    @stack('styles')
</head>
<body data-page="@yield('page-id')">
    <div class="app">
        @include('manager.sidebar')
        <div class="main">
            @include('manager.header')
            <div class="content @yield('content-class')">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('js/manager/main.js') }}"></script>
    <script>
        (function () {
            const notifBtn = document.getElementById('notifBtn');
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userDropdown = document.getElementById('userDropdown');
            const notifMenu = document.getElementById('notifMenu');
            const notifDot = document.getElementById('notifDot');

            function closeAll() {
                notifMenu?.classList.remove('open');
                notifMenu?.setAttribute('aria-hidden', 'true');
                userDropdown?.classList.remove('open');
                userDropdown?.setAttribute('aria-hidden', 'true');
            }

            userMenuBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                closeAll();
                const open = userDropdown.classList.toggle('open');
                userDropdown.setAttribute('aria-hidden', open ? 'false' : 'true');
            });

            notifBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                const open = notifMenu.classList.toggle('open');
                notifMenu.setAttribute('aria-hidden', open ? 'false' : 'true');
            });

            document.addEventListener('click', () => closeAll());
        })();
    </script>
    @stack('scripts')
</body>
</html>
