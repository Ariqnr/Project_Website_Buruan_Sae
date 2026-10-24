<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampahRequest;
use App\Models\ActivityLog;
use App\Models\Sampah;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SampahController extends Controller
{
    /**
     * Kolom sampah yang disimpan ke dalam log perubahan.
     *
     * @var list<string>
     */
    private const LOG_COLUMNS = [
        'tanggal_masuk',
        'jenis',
        'berat_kg',
        'status',
        'catatan',
    ];

    /**
     * Tampilkan daftar data sampah dengan pagination dan filter.
     */
    public function index(Request $request): View
    {
        $query = Sampah::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', 'like', '%'.$request->string('jenis').'%');
        }

        $sampah = $query->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('sampah.index', [
            'sampah' => $sampah,
            'statuses' => Sampah::STATUSES,
            'jenisList' => Sampah::JENIS,
            'currentStatus' => $request->string('status')->toString(),
            'currentJenis' => $request->string('jenis')->toString(),
        ]);
    }

    /**
     * Tampilkan formulir untuk mencatat sampah baru.
     */
    public function create(): View
    {
        $sampah = new Sampah([
            'tanggal_masuk' => now()->toDateString(),
            'status' => Sampah::STATUS_MASUK,
        ]);

        return view('sampah.form', [
            'sampah' => $sampah,
            'statuses' => Sampah::STATUSES,
            'jenisList' => Sampah::JENIS,
            'isEdit' => false,
        ]);
    }

    /**
     * Simpan data sampah baru ke database.
     */
    public function store(SampahRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $sampah = Sampah::create($validated);

        $actor = $request->user()->name;
        $berat = number_format((float) $sampah->berat_kg, 2, ',', '.');

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_TAMBAH,
            deskripsi: "{$actor} menambah data sampah {$sampah->jenis} {$berat} kg",
            objek: $sampah,
            dataBaru: ActivityLogger::ringkasModel($sampah, self::LOG_COLUMNS),
        );

        return redirect()
            ->route('sampah.index')
            ->with('success', 'Data sampah berhasil disimpan.');
    }

    /**
     * Tampilkan formulir untuk mengubah data sampah.
     */
    public function edit(Sampah $sampah): View
    {
        return view('sampah.form', [
            'sampah' => $sampah,
            'statuses' => Sampah::STATUSES,
            'jenisList' => Sampah::JENIS,
            'isEdit' => true,
        ]);
    }

    /**
     * Perbarui data sampah di database.
     */
    public function update(SampahRequest $request, Sampah $sampah): RedirectResponse
    {
        $dataLama = ActivityLogger::ringkasModel($sampah, self::LOG_COLUMNS);

        $sampah->update($request->validated());

        $dataBaru = ActivityLogger::ringkasModel($sampah, self::LOG_COLUMNS);
        $actor = $request->user()->name;
        $berat = number_format((float) $sampah->berat_kg, 2, ',', '.');

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_UBAH,
            deskripsi: "{$actor} mengubah data sampah {$sampah->jenis} {$berat} kg",
            objek: $sampah,
            dataLama: $dataLama,
            dataBaru: $dataBaru,
        );

        return redirect()
            ->route('sampah.index')
            ->with('success', 'Data sampah berhasil diperbarui.');
    }

    /**
     * Hapus data sampah dari database (Hanya Admin).
     */
    public function destroy(Request $request, Sampah $sampah): RedirectResponse
    {
        Gate::authorize('delete', $sampah);

        $dataLama = ActivityLogger::ringkasModel($sampah, self::LOG_COLUMNS);
        $id = $sampah->id;
        $jenis = $sampah->jenis;
        $berat = number_format((float) $sampah->berat_kg, 2, ',', '.');
        $actor = $request->user()?->name ?? 'Admin';

        $sampah->delete();

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_HAPUS,
            deskripsi: "{$actor} menghapus data sampah {$jenis} {$berat} kg",
            dataLama: $dataLama,
            objekTipe: 'Sampah',
            objekId: $id,
        );

        return redirect()
            ->route('sampah.index')
            ->with('success', 'Data sampah berhasil dihapus.');
    }
}
