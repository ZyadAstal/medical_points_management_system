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
                @if(session('success'))
                    <div class="alert-success" style="padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert-error" style="padding: 15px; background: #f8d7da; color: #721c24; border-radius: 8px; margin-bottom: 20px;">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- JS Links -->
    <script src="{{ asset('js/pharmacist/main.js') }}"></script>
    @yield('scripts')
</body>
</html>
