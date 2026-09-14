@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

$currentRouteName = Route::currentRouteName();
$authUser = Auth::user();
$isMemberLolos = true;
$isMemberTidakLolos = false;

if ($authUser && $authUser->role === 'calon_anggota') {
    $authUser->loadMissing(['profil', 'berkas']);
    $admStatus = $authUser->profil->seleksi_administrasi ?? null;
    $tesStatus = $authUser->profil->tes_tulis_wawancara ?? null;
    $cakStatus = $authUser->profil->cakruma ?? null;
    $hasRejected = $authUser->berkas->some(fn($b) => $b->status === 'ditolak');
    $isMemberLolos = ($admStatus === 'lolos' && $tesStatus === 'lolos' && $cakStatus === 'lolos' && !$hasRejected);
    $isMemberTidakLolos = ($hasRejected || $admStatus === 'tidak_lolos' || $tesStatus === 'tidak_lolos' || $cakStatus === 'tidak_lolos');
}
@endphp

<!-- Sidebar / Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- App Brand -->
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('images/logo-amanat.png') }}" alt="Logo SKM Amanat" style="width: 28px; height: auto; object-fit: contain;" class="rounded">
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-1 fs-7 text-nowrap">{{ config('variables.templateName', 'SKM Amanat') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="icon-base bx bx-chevron-left icon-sm d-flex align-items-center justify-content-center"></i>
        </a>
    </div>

    <div class="menu-divider mt-0"></div>
    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @if(isset($menuData[0]->menu))
        @foreach ($menuData[0]->menu as $menu)

            {{-- Jika calon anggota TIDAK LOLOS, hanya tampilkan Beranda, Profil & Berkas, dan Info Pendaftaran --}}
            @if($isMemberTidakLolos && !isset($menu->menuHeader) && !in_array($menu->slug ?? '', ['member.dashboard', 'member.profil', 'member.info-pendaftaran']))
                @continue
            @endif

            {{-- Skip Menu Headers if any --}}
            @if (!isset($menu->menuHeader))

            {{-- Active & Lock Menu Determination --}}
            @php
            $isLocked = false;
            if ($authUser && $authUser->role === 'calon_anggota' && in_array($menu->slug ?? '', ['member.magang-spesialis', 'member.hasil-rekomendasi']) && !$isMemberLolos) {
                $isLocked = true;
            }

            $activeClass = '';
            if (!$isLocked) {
                if ($currentRouteName === $menu->slug || (isset($menu->slug) && request()->routeIs($menu->slug . '*'))) {
                    $activeClass = 'active';
                } elseif (isset($menu->submenu)) {
                    if (is_array($menu->slug)) {
                        foreach($menu->slug as $slug){
                            if (str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
                                $activeClass = 'active open';
                            }
                        }
                    } else {
                        if (str_contains($currentRouteName, $menu->slug) && strpos($currentRouteName, $menu->slug) === 0) {
                            $activeClass = 'active open';
                        }
                    }
                }
            }
            @endphp

            {{-- Main Menu Item --}}
            <li class="menu-item {{ $isLocked ? 'opacity-50' : $activeClass }}">
                <a href="{{ $isLocked ? 'javascript:void(0);' : (isset($menu->url) ? url($menu->url) : 'javascript:void(0);') }}" 
                   class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" 
                   @if($isLocked) onclick="alert('Menu ini terkunci. Anda harus dinyatakan LOLOS pada seluruh tahapan seleksi terlebih dahulu.'); return false;" style="cursor: not-allowed;" title="Menu Terkunci (Belum Lolos Seleksi)" @elseif (isset($menu->target) && !empty($menu->target)) target="_blank" @endif>
                    @isset($menu->icon)
                    <i class="{{ $menu->icon }}"></i>
                    @endisset
                    <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                    @if($isLocked)
                        <span class="badge rounded-pill bg-label-secondary ms-auto py-1 px-2" title="Terkunci"><i class="bx bx-lock-alt fs-tiny"></i></span>
                    @elseif(isset($menu->badge))
                        <div class="badge rounded-pill bg-{{ $menu->badge[0] }} text-uppercase ms-auto">{{ $menu->badge[1] }}</div>
                    @endif
                </a>

                {{-- Submenu if available --}}
                @isset($menu->submenu)
                <ul class="menu-sub">
                    @foreach ($menu->submenu as $submenu)
                    @php
                        $subActiveClass = ($currentRouteName === $submenu->slug) ? 'active' : '';
                    @endphp
                    <li class="menu-item {{ $subActiveClass }}">
                        <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}" class="menu-link" @if (isset($submenu->target) && !empty($submenu->target)) target="_blank" @endif>
                            @isset($submenu->icon)
                            <i class="{{ $submenu->icon }}"></i>
                            @endisset
                            <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
                            @isset($submenu->badge)
                            <div class="badge rounded-pill bg-{{ $submenu->badge[0] }} text-uppercase ms-auto">{{ $submenu->badge[1] }}</div>
                            @endisset
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endisset
            </li>
            @endif

        @endforeach
        @endif
    </ul>

</aside>
<!-- / Sidebar / Menu -->
