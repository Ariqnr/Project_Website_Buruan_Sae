<?php

namespace App\Http\Controllers;

use App\Http\Requests\JadwalPenjemuranRequest;
use App\Models\ActivityLog;
use App\Models\JadwalPenjemuran;
use App\Models\Sampah;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class JadwalPenjemuranController extends Controller
{
    /**
     * Kolom jadwal penjemuran yang disimpan ke dalam log perubahan.
     *
     * @var list<string>
     */
    private const LOG_COLUMNS = [
        'sampah_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'catatan',
    ];

    /**
     * Tampilkan daftar jadwal penjemuran dengan pagination dan filter status jadwal.
     */
    public function index(Request $request): View
    {
        $currentStatus = $request->string('status')->toString();

        $jadwal = JadwalPenjemuran::with('sampah')
            ->filterStatus($currentStatus)
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('jadwal.index', [
            'jadwal' => $jadwal,
            'statuses' => JadwalPenjemuran::STATUSES,
            'currentStatus' => $currentStatus,
        ]);
    }

    /**
     * Tampilkan formulir untuk membuat jadwal penjemuran baru.
     */
    public function create(): View
    {
        $jadwal = new JadwalPenjemuran([
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
        ]);

        $daftarSampah = Sampah::organik()
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->get();

        return view('jadwal.form', [
            'jadwal' => $jadwal,
            'daftarSampah' => $daftarSampah,
            'isEdit' => false,
        ]);
    }

    /**
     * Simpan jadwal penjemuran baru dan ubah status sampah dari 'masuk' menjadi 'dikelola'.
     */
    public function store(JadwalPenjemuranRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $jadwal = JadwalPenjemuran::create($validated);

        $sampah = Sampah::find($jadwal->sampah_id);
        if ($sampah !== null && $sampah->status === Sampah::STATUS_MASUK) {
            $sampah->update(['status' => Sampah::STATUS_DIKELOLA]);
        }

        $actor = $request->user()->name;
        $jenis = $sampah->jenis ?? 'Organik';
        $mulai = $jadwal->tanggal_mulai->format('d/m/Y');
        $selesai = $jadwal->tanggal_selesai->format('d/m/Y');

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_TAMBAH,
            deskripsi: "{$actor} menambah jadwal penjemuran sampah {$jenis} ({$mulai} s/d {$selesai})",
            objek: $jadwal,
            dataBaru: ActivityLogger::ringkasModel($jadwal, self::LOG_COLUMNS),
        );

        return redirect()
            ->route('jadwal.index')
            ->with('success', 'Jadwal penjemuran berhasil disimpan.');
    }

    /**
     * Tampilkan formulir untuk mengubah jadwal penjemuran.
     */
    public function edit(JadwalPenjemuran $jadwal): View
    {
        $daftarSampah = Sampah::organik()
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->get();

        return view('jadwal.form', [
            'jadwal' => $jadwal,
            'daftarSampah' => $daftarSampah,
            'isEdit' => true,
        ]);
    }

    /**
     * Perbarui data jadwal penjemuran di database.
     */
    public function update(JadwalPenjemuranRequest $request, JadwalPenjemuran $jadwal): RedirectResponse
    {
        $dataLama = ActivityLogger::ringkasModel($jadwal, self::LOG_COLUMNS);

        $jadwal->update($request->validated());

        $dataBaru = ActivityLogger::ringkasModel($jadwal, self::LOG_COLUMNS);
        $actor = $request->user()->name;
        $jenis = $jadwal->sampah->jenis ?? 'Organik';
        $mulai = $jadwal->tanggal_mulai->format('d/m/Y');
        $selesai = $jadwal->tanggal_selesai->format('d/m/Y');

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_UBAH,
            deskripsi: "{$actor} mengubah jadwal penjemuran sampah {$jenis} ({$mulai} s/d {$selesai})",
            objek: $jadwal,
            dataLama: $dataLama,
            dataBaru: $dataBaru,
        );

        return redirect()
            ->route('jadwal.index')
            ->with('success', 'Jadwal penjemuran berhasil diperbarui.');
    }

    /**
     * Hapus jadwal penjemuran dari database (Hanya Admin).
     */
    public function destroy(Request $request, JadwalPenjemuran $jadwal): RedirectResponse
    {
        Gate::authorize('delete', $jadwal);

        $dataLama = ActivityLogger::ringkasModel($jadwal, self::LOG_COLUMNS);
        $id = $jadwal->id;
        $jenis = $jadwal->sampah->jenis ?? 'Organik';
        $mulai = $jadwal->tanggal_mulai->format('d/m/Y');
        $selesai = $jadwal->tanggal_selesai->format('d/m/Y');
        $actor = $request->user()?->name ?? 'Admin';

        $jadwal->delete();

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_HAPUS,
            deskripsi: "{$actor} menghapus jadwal penjemuran sampah {$jenis} ({$mulai} s/d {$selesai})",
            dataLama: $dataLama,
            objekTipe: 'JadwalPenjemuran',
            objekId: $id,
        );

        return redirect()
            ->route('jadwal.index')
            ->with('success', 'Jadwal penjemuran berhasil dihapus.');
    }
}
