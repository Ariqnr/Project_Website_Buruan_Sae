<?php

namespace App\Services;

use App\Models\JadwalPenjemuran;
use App\Models\Sampah;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LaporanService
{
    /**
     * Ambil seluruh data laporan pengelolaan sampah dan jadwal penjemuran
     * untuk rentang periode tanggal_awal s/d tanggal_akhir secara efisien.
     * Dipisahkan ke Service agar mudah digunakan ulang untuk Web, PDF, maupun ekspor Excel.
     *
     * @return array{
     *     tanggalAwal: string,
     *     tanggalAkhir: string,
     *     periodeAwalLabel: string,
     *     periodeAkhirLabel: string,
     *     daftarSampah: Collection<int, Sampah>,
     *     totalBerat: float,
     *     rekapPerJenis: Collection<string, float>,
     *     jadwalPeriode: Collection<int, JadwalPenjemuran>,
     *     isEmpty: bool
     * }
     */
    public function ambilDataLaporan(string $tanggalAwal, string $tanggalAkhir): array
    {
        $awal = Carbon::parse($tanggalAwal)->toDateString();
        $akhir = Carbon::parse($tanggalAkhir)->toDateString();

        // 1. Data sampah pada periode filter dengan eager loading user
        $daftarSampah = Sampah::with('user')
            ->whereDate('tanggal_masuk', '>=', $awal)
            ->whereDate('tanggal_masuk', '<=', $akhir)
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->get();

        // 2. Agregasi di database untuk total berat per jenis sampah
        $rekapRows = Sampah::query()
            ->selectRaw('jenis, COALESCE(SUM(berat_kg), 0) as total_berat')
            ->whereDate('tanggal_masuk', '>=', $awal)
            ->whereDate('tanggal_masuk', '<=', $akhir)
            ->groupBy('jenis')
            ->orderByDesc('total_berat')
            ->get();

        /** @var Collection<string, float> $rekapPerJenis */
        $rekapPerJenis = $rekapRows->pluck('total_berat', 'jenis')
            ->map(fn ($val): float => round((float) $val, 2));

        $totalBerat = round((float) $rekapPerJenis->sum(), 2);

        // 3. Jadwal penjemuran yang rentang tanggalnya beririsan dengan periode filter
        // Irisan dua rentang [tanggal_mulai, tanggal_selesai] dan [awal, akhir]:
        // tanggal_mulai <= akhir DAN tanggal_selesai >= awal
        $jadwalPeriode = JadwalPenjemuran::with('sampah')
            ->whereDate('tanggal_mulai', '<=', $akhir)
            ->whereDate('tanggal_selesai', '>=', $awal)
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('id')
            ->get();

        $isEmpty = $daftarSampah->isEmpty() && $jadwalPeriode->isEmpty();

        return [
            'tanggalAwal' => $awal,
            'tanggalAkhir' => $akhir,
            'periodeAwalLabel' => Carbon::parse($awal)->translatedFormat('d F Y'),
            'periodeAkhirLabel' => Carbon::parse($akhir)->translatedFormat('d F Y'),
            'daftarSampah' => $daftarSampah,
            'totalBerat' => $totalBerat,
            'rekapPerJenis' => $rekapPerJenis,
            'jadwalPeriode' => $jadwalPeriode,
            'isEmpty' => $isEmpty,
        ];
    }
}
