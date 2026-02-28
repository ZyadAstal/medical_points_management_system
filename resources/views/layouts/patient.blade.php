<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>@yield('title', 'Medicare - Patient')</title>
    <link href="{{ asset('css/patient/style.css') }}" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    @stack('styles')
</head>
<body data-page="@yield('page-id')">
    <div class="app">
        @include('patient.sidebar')
        <div class="main">
            @include('patient.header')
            <div class="content @yield('content-class')">
                @include('partials.flash-messages')
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('js/patient/main.js') }}"></script>
    <script src="{{ asset('js/patient/layout.js') }}"></script>
    @stack('scripts')
    @include('components.delete-modal')
</body>
</html>
