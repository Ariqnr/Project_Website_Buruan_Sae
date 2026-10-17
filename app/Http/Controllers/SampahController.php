<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampahRequest;
use App\Models\Sampah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SampahController extends Controller
{
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

        Sampah::create($validated);

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
        $sampah->update($request->validated());

        return redirect()
            ->route('sampah.index')
            ->with('success', 'Data sampah berhasil diperbarui.');
    }

    /**
     * Hapus data sampah dari database (Hanya Admin).
     */
    public function destroy(Sampah $sampah): RedirectResponse
    {
        Gate::authorize('delete', $sampah);

        $sampah->delete();

        return redirect()
            ->route('sampah.index')
            ->with('success', 'Data sampah berhasil dihapus.');
    }
}
