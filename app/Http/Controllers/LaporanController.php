<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaporanFilterRequest;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use App\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Tampilkan halaman laporan pengelolaan sampah beserta filter periode.
     */
    public function index(LaporanFilterRequest $request, LaporanService $laporanService): View
    {
        $validated = $request->validated();

        $data = $laporanService->ambilDataLaporan(
            $validated['tanggal_awal'],
            $validated['tanggal_akhir']
        );

        return view('laporan.index', $data);
    }

    /**
     * Unduh laporan pengelolaan sampah dalam format PDF (ukuran A4).
     */
    public function unduh(LaporanFilterRequest $request, LaporanService $laporanService): Response
    {
        $validated = $request->validated();

        $data = $laporanService->ambilDataLaporan(
            $validated['tanggal_awal'],
            $validated['tanggal_akhir']
        );

        $data['tanggalCetak'] = now()->translatedFormat('d F Y H:i');
        $data['dicetakOleh'] = $request->user()?->name ?? '-';

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_UNDUH_PDF,
            deskripsi: "{$data['dicetakOleh']} mengunduh laporan PDF periode {$data['tanggalAwal']} s/d {$data['tanggalAkhir']}",
            dataBaru: [
                'tanggal_awal' => $data['tanggalAwal'],
                'tanggal_akhir' => $data['tanggalAkhir'],
            ],
            objekTipe: 'Laporan',
        );

        $pdf = Pdf::loadView('laporan.pdf', $data)
            ->setPaper('a4', 'portrait');

        $namaFile = "laporan-sampah-{$data['tanggalAwal']}-{$data['tanggalAkhir']}.pdf";

        return $pdf->download($namaFile);
    }
}
