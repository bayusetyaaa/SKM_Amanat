<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-mark "Tidak Hadir" setiap menit untuk kegiatan yang sudah selesai
Schedule::command('presensi:auto-mark-tidak-hadir')->everyMinute();
