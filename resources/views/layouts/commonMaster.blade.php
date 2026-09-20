<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="{{ asset('/assets') . '/' }}" dir="ltr" data-skin="default" data-base-url="{{ url('/') }}" data-framework="laravel" data-bs-theme="light" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>
        @yield('title') | {{ config('variables.templateName') ? config('variables.templateName') : 'SKM Amanat' }}
    </title>
    <meta name="description" content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- DNS Prefetch & Preconnect for Fast Asset Loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin />
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com" />

    <!-- Google Fonts & Boxicons -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Core & Vendor CSS -->
    @vite([
        'resources/assets/vendor/scss/core.scss',
        'resources/assets/css/demo.css',
        'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss'
    ])

    @yield('vendor-style')
    @yield('page-style')

    <!-- Custom Utility Styles -->
    <style>
        [x-cloak] { display: none !important; }
        .badge.bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
        .badge.bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
        .badge.bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
        .badge.bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
        .badge.bg-label-danger { background-color: #ffe0db !important; color: #ff3e1d !important; }
        .badge.bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
    </style>

    <!-- Head Helpers & Scripts -->
    @vite(['resources/assets/vendor/js/helpers.js', 'resources/assets/js/config.js'])
</head>

<body>
    <!-- Layout Content -->
    @hasSection('layoutContent')
        @yield('layoutContent')
    @else
        @yield('content')
    @endif

    <!-- Core & Vendor JS Scripts -->
    @vite([
        'resources/assets/vendor/libs/jquery/jquery.js',
        'resources/assets/vendor/libs/popper/popper.js',
        'resources/assets/vendor/js/bootstrap.js',
        'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
        'resources/assets/vendor/js/menu.js',
        'resources/assets/js/main.js'
    ])

    @yield('vendor-script')
    @stack('pricing-script')
    @yield('page-script')
    @yield('scripts')
    @stack('page-scripts')
    @stack('scripts')

    @vite(['resources/js/app.js'])
</body>

</html>