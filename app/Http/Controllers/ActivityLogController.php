<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Tampilkan halaman log aktivitas (khusus Admin) dengan pagination 20 per halaman
     * serta filter rentang tanggal, pengguna, dan jenis aksi.
     */
    public function index(Request $request): View
    {
        $tanggalAwal = $request->string('tanggal_awal')->toString();
        $tanggalAkhir = $request->string('tanggal_akhir')->toString();
        $userId = $request->string('user_id')->toString();
        $aksi = $request->string('aksi')->toString();

        $query = ActivityLog::with('user');

        if ($tanggalAwal !== '') {
            $query->whereDate('created_at', '>=', $tanggalAwal);
        }

        if ($tanggalAkhir !== '') {
            $query->whereDate('created_at', '<=', $tanggalAkhir);
        }

        if ($userId !== '') {
            $query->where('user_id', (int) $userId);
        }

        if ($aksi !== '') {
            $query->where('aksi', $aksi);
        }

        $logs = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $daftarPengguna = User::orderBy('name')->get(['id', 'name', 'role']);

        return view('log.index', [
            'logs' => $logs,
            'daftarPengguna' => $daftarPengguna,
            'daftarAksi' => ActivityLog::DAFTAR_AKSI,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'currentUserId' => $userId,
            'currentAksi' => $aksi,
        ]);
    }
}
