@php
$containerFooter = $containerNav ?? 'container-fluid';
@endphp

<!-- Footer -->
<footer class="content-footer footer bg-footer-theme">
    <div class="{{ $containerFooter }}">
        <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
            <div class="text-body text-sm">
                © {{ date('Y') }} <strong>SKM Amanat</strong> — Sistem Informasi Keanggotaan & Penempatan Magang Spesialis
            </div>
            <div class="d-none d-lg-inline-block text-muted text-sm">
                SPK Metode Profile Matching
            </div>
        </div>
    </div>
</footer>
<!-- / Footer -->
