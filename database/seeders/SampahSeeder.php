<?php

namespace Database\Seeders;

use App\Models\JadwalPenjemuran;
use App\Models\Sampah;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampahSeeder extends Seeder
{
    /**
     * Data contoh sampah selama 6 bulan terakhir + beberapa jadwal penjemuran.
     * Tanggal dihitung relatif terhadap hari ini supaya grafik dashboard selalu terisi.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@buruansae.test')->firstOrFail();
        $pengelola = User::where('email', 'pengelola@buruansae.test')->firstOrFail();

        // [hari yang lalu, jenis, berat_kg, status, catatan, pencatat]
        $data = [
            [170, 'Organik', 32.50, 'selesai', 'Sisa sayuran dari kebun', $pengelola],
            [160, 'Plastik', 8.25, 'selesai', null, $pengelola],
            [140, 'Kertas', 12.00, 'selesai', 'Kardus bekas bibit', $admin],
            [125, 'Organik', 41.75, 'selesai', null, $pengelola],
            [110, 'Logam', 4.30, 'selesai', 'Kaleng bekas', $pengelola],
            [95, 'Organik', 28.00, 'selesai', null, $admin],
            [80, 'Plastik', 10.50, 'selesai', 'Polybag rusak', $pengelola],
            [65, 'Kaca', 6.80, 'selesai', null, $pengelola],
            [50, 'Organik', 36.20, 'selesai', 'Daun kering', $pengelola],
            [40, 'Anorganik', 9.90, 'dikelola', null, $admin],
            [25, 'Organik', 45.00, 'dikelola', 'Untuk dijadikan kompos', $pengelola],
            [7, 'Organik', 22.40, 'dikelola', 'Sedang dijemur', $pengelola],
            [4, 'Plastik', 5.60, 'masuk', null, $pengelola],
            [2, 'Kertas', 7.15, 'masuk', 'Koran bekas warga', $admin],
            [0, 'Organik', 18.30, 'masuk', null, $pengelola],
        ];

        $sampah = [];

        foreach ($data as [$hariLalu, $jenis, $berat, $status, $catatan, $user]) {
            $sampah[] = Sampah::create([
                'user_id' => $user->id,
                'tanggal_masuk' => now()->subDays($hariLalu)->toDateString(),
                'jenis' => $jenis,
                'berat_kg' => $berat,
                'status' => $status,
                'catatan' => $catatan,
            ]);
        }

        // Jadwal penjemuran: 2 sedang berjalan, 1 sudah lewat, 1 akan datang.
        $jadwal = [
            [$sampah[10], -3, 4, 'Jemur di area belakang'],   // aktif
            [$sampah[11], -1, 2, null],                       // aktif
            [$sampah[8], -45, -40, 'Selesai dijemur'],        // sudah lewat
            [$sampah[14], 2, 6, 'Menunggu cuaca cerah'],      // akan datang
        ];

        foreach ($jadwal as [$item, $mulai, $selesai, $catatan]) {
            JadwalPenjemuran::create([
                'sampah_id' => $item->id,
                'tanggal_mulai' => now()->addDays($mulai)->toDateString(),
                'tanggal_selesai' => now()->addDays($selesai)->toDateString(),
                'catatan' => $catatan,
            ]);
        }
    }
}
