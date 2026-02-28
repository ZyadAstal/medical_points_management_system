<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>@yield('title', 'Medicare')</title>
    <link href="{{ asset('css/admin/style.css') }}" rel="stylesheet"/>
    @stack('styles')
</head>
<body data-page="@yield('page-id')">
    <div class="app">
        @include('superadmin.sidebar')
        <div class="main">
            @include('superadmin.header')
            <div class="content @yield('content-class')">
                @include('partials.flash-messages')
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('js/admin/main.js') }}"></script>
    <script src="{{ asset('js/admin/layout.js') }}"></script>
    @stack('scripts')
    @include('components.delete-modal')
</body>
</html>
