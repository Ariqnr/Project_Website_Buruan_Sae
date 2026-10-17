<?php

namespace App\Http\Controllers;

use App\Charts\SampahBulananChart;
use App\Models\JadwalPenjemuran;
use App\Models\Sampah;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard dengan kartu ringkasan, grafik, catatan terbaru, dan jadwal aktif.
     */
    public function __invoke(Request $request, SampahBulananChart $chart): View
    {
        $today = now();

        $totalBeratBulanIni = (float) Sampah::whereYear('tanggal_masuk', $today->year)
            ->whereMonth('tanggal_masuk', $today->month)
            ->sum('berat_kg');

        $totalBeratSemua = (float) Sampah::sum('berat_kg');

        $jumlahDikelola = Sampah::where('status', Sampah::STATUS_DIKELOLA)->count();

        $jumlahJadwalAktif = JadwalPenjemuran::aktif()->count();

        $catatanTerbaru = Sampah::with('user')
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $jadwalSedangBerjalan = JadwalPenjemuran::with('sampah')
            ->aktif()
            ->orderBy('tanggal_selesai')
            ->get();

        return view('dashboard', [
            'totalBeratBulanIni' => $totalBeratBulanIni,
            'totalBeratSemua' => $totalBeratSemua,
            'jumlahDikelola' => $jumlahDikelola,
            'jumlahJadwalAktif' => $jumlahJadwalAktif,
            'chart' => $chart->build(),
            'catatanTerbaru' => $catatanTerbaru,
            'jadwalSedangBerjalan' => $jadwalSedangBerjalan,
        ]);
    }
}
