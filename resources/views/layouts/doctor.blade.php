<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>@yield('title', 'Medicare - Doctor')</title>
    <link href="{{ asset('css/doctor/style.css') }}" rel="stylesheet">/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    @stack('styles')
</head>
<body data-page="@yield('page-id')">
    <div class="app">
        @include('doctor.sidebar')
        <div class="main">
            @include('doctor.header')
            <div class="content @yield('content-class')">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('js/doctor/main.js') }}"></script>
    <script src="{{ asset('js/doctor/layout.js') }}"></script>
    @stack('scripts')
</body>
</html>
