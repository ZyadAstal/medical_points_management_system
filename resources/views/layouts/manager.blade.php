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
                @include('partials.flash-messages')
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('js/manager/main.js') }}"></script>
    <script src="{{ asset('js/manager/layout.js') }}"></script>
    @stack('scripts')
    @include('components.delete-modal')
</body>
</html>
