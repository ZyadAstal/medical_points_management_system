<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Medicare') - صيدلي</title>
    
    <!-- CSS Links -->
    <link href="{{ asset('css/pharmacist/style.css') }}" rel="stylesheet">
    @yield('styles')
    
    <!-- Fonts (if needed) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body data-page="@yield('page-id')">
    <div class="app">
        @include('pharmacist.sidebar')
        
        <div class="main">
            @include('pharmacist.header')
            
            <div class="content @yield('content-class')">
                @include('partials.flash-messages')
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('js/pharmacist/main.js') }}"></script>
    @yield('scripts')
    @include('components.delete-modal')
</body>
</html>
