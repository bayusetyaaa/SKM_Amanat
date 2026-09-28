<?php

namespace App\Console\Commands;

use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\User;
use App\Services\EvaluasiNilaiService;
use App\Services\ProfileMatchingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoMarkTidakHadir extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'presensi:auto-mark-tidak-hadir';

    /**
     * The console command description.
     */
    protected $description = 'Otomatis menandai "Tidak Hadir" pada anggota yang tidak melakukan presensi setelah kegiatan selesai.';

    public function handle()
    {
        $now = Carbon::now();

        // Cari semua kegiatan yang SUDAH selesai (tanggal_waktu_selesai sudah lewat)
        // dan belum pernah di-auto-mark (atau kita cek per user)
        $kegiatanSelesai = Kegiatan::where(function ($q) use ($now) {
                // Kegiatan dengan waktu selesai yang sudah lewat
                $q->where('tanggal_waktu_selesai', '<', $now)
                  ->whereNotNull('tanggal_waktu_selesai');
            })
            ->orWhere(function ($q) use ($now) {
                // Atau kegiatan tanpa waktu selesai tapi hari-nya sudah lewat
                $q->whereNull('tanggal_waktu_selesai')
                  ->whereDate('tanggal_waktu', '<', $now->toDateString());
            })
            ->get();

        if ($kegiatanSelesai->isEmpty()) {
            $this->info('Tidak ada kegiatan yang perlu diproses.');
            return 0;
        }

        // Ambil semua calon anggota aktif
        $users = User::where('role', 'calon_anggota')->get();

        $marked = 0;

        foreach ($kegiatanSelesai as $kegiatan) {
            foreach ($users as $user) {
                // Cek apakah sudah ada presensi untuk kegiatan ini
                $exists = Presensi::where('user_id', $user->id)
                    ->where('kegiatan_id', $kegiatan->id)
                    ->exists();

                if (!$exists) {
                    // Buat presensi "Tidak Hadir" otomatis
                    Presensi::create([
                        'user_id'      => $user->id,
                        'kegiatan_id'  => $kegiatan->id,
                        'waktu_hadir'  => $kegiatan->tanggal_waktu_selesai ?? $kegiatan->tanggal_waktu,
                        'status'       => 'Tidak Hadir',
                        'keterangan'   => 'Otomatis: tidak melakukan presensi hingga batas waktu kegiatan.',
                    ]);

                    $marked++;
                }
            }
        }

        if ($marked > 0) {
            $this->info("✅ {$marked} presensi 'Tidak Hadir' berhasil ditambahkan.");

            // Ambil ID admin pertama yang valid untuk input_oleh
            $adminId = User::where('role', 'admin')->value('id');

            // Sync ulang nilai evaluasi dan ranking
            $evalService = app(EvaluasiNilaiService::class);
            $evalService->syncAllScores($adminId);

            $pmService = app(ProfileMatchingService::class);
            $pmService->calculateAndRankAll();

            $this->info('✅ Nilai evaluasi dan ranking berhasil disinkronisasi.');
        } else {
            $this->info('Semua anggota sudah memiliki presensi untuk kegiatan yang selesai.');
        }

        return 0;
    }
}
