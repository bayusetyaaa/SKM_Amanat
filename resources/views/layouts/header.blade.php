@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
$user = Auth::user();
$containerNav = $containerNav ?? 'container-xxl';
$navbarDetached = $navbarDetached ?? 'navbar-detached';
@endphp

<!-- Header / Navbar -->
<nav class="layout-navbar {{ $containerNav }} {{ $navbarDetached }} navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    
    <!-- Brand demo for navbar-full -->
    @if(isset($navbarFull))
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="{{ url('/') }}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
                <img src="{{ asset('images/logo-amanat.png') }}" alt="Logo SKM Amanat" style="width: 28px; height: auto; object-fit: contain;" class="rounded">
            </span>
            <span class="app-brand-text demo menu-text fw-bold text-heading">{{ config('variables.templateName', 'SKM Amanat') }}</span>
        </a>
    </div>
    @endif

    <!-- Toggle button on small screens -->
    @if(!isset($navbarHideToggle))
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 {{ isset($contentNavbar) ? 'd-xl-none' : '' }}">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="icon-base bx bx-menu icon-md"></i>
        </a>
    </div>
    @endif

    <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">
        <!-- Left Title / Badge Info -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
                <span class="fw-semibold text-heading d-none d-md-inline-block">
                    @if($user && $user->role === 'admin')
                        <span class="badge bg-label-primary me-2">Pengurus</span>
                        Sistem Informasi Keanggotaan SKM Amanat
                    @else
                        <span class="badge bg-label-info me-2">Cakruma</span>
                        Sistem Rekrutmen & Magang Spesialis
                    @endif
                </span>
            </div>
        </div>

        <!-- Right: User Dropdown -->
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            @if($user)
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <div class="dropdown-item py-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->role === 'admin' ? 'Pengurus / HRD' : 'Calon Anggota' }}</small>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li><div class="dropdown-divider my-1"></div></li>

                    @if($user->role === 'calon_anggota')
                    <li>
                        <a class="dropdown-item" href="{{ route('member.profil') }}">
                            <i class="icon-base bx bx-user icon-md me-3"></i><span>Profil Saya</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('member.penugasan') }}">
                            <i class="icon-base bx bx-task icon-md me-3"></i><span>Tugas Saya</span>
                        </a>
                    </li>
                    <li><div class="dropdown-divider my-1"></div></li>
                    @endif

                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Keluar / Log Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
            @endif
        </ul>
    </div>
</nav>
<!-- / Header / Navbar -->
