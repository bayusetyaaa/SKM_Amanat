@isset($pageConfigs)
{!! Helper::updatePageConfig($pageConfigs) !!}
@endisset
@extends('layouts/commonMaster')

@php
/* Display elements */
$contentNavbar = $contentNavbar ?? true;
$containerNav = $containerNav ?? 'container-xxl';
$isNavbar = $isNavbar ?? true;
$isMenu = $isMenu ?? true;
$isFlex = $isFlex ?? false;
$isFooter = $isFooter ?? true;
$customizerHidden = $customizerHidden ?? '';

/* HTML Classes */
$navbarDetached = 'navbar-detached';
$menuFixed = isset($configData['menuFixed']) ? $configData['menuFixed'] : '';
if (isset($navbarType)) {
    $configData['navbarType'] = $navbarType;
}
$navbarType = isset($configData['navbarType']) ? $configData['navbarType'] : '';
$footerFixed = isset($configData['footerFixed']) ? $configData['footerFixed'] : '';
$menuCollapsed = isset($configData['menuCollapsed']) ? $configData['menuCollapsed'] : '';

/* Content classes */
$container = ($container ?? 'container-xxl');
@endphp

@section('layoutContent')
<div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">
    <div class="layout-container">

        {{-- 1. SIDEBAR --}}
        @if ($isMenu)
        @include('layouts.sidebar')
        @endif

        <!-- Layout page -->
        <div class="layout-page">

            {{-- 2. HEADER / NAVBAR --}}
            @if ($isNavbar)
            @include('layouts.header')
            @endif

            <!-- Content wrapper -->
            <div class="content-wrapper">

                <!-- Main Content -->
                @if ($isFlex)
                <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                @else
                <div class="{{ $container }} flex-grow-1 container-p-y">
                @endif

                    <!-- Flash Messages -->
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bx bx-error me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="bx bx-info-circle me-1"></i> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="fw-bold mb-1"><i class="bx bx-error-circle me-1"></i> Terjadi beberapa kesalahan:</div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Page Content Slot -->
                    @yield('content')

                </div>
                <!-- / Main Content -->

                {{-- 3. FOOTER --}}
                @if ($isFooter)
                @include('layouts.footer')
                @endif

                <div class="content-backdrop fade"></div>
            </div>
            <!--/ Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    @if ($isMenu)
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    @endif
    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
</div>
<!-- / Layout wrapper -->
@endsection
