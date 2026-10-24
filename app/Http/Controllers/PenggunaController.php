<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenggunaRequest;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    /**
     * Kolom pengguna yang boleh disimpan ke dalam log perubahan (tanpa password).
     *
     * @var list<string>
     */
    private const LOG_COLUMNS = [
        'name',
        'email',
        'role',
        'is_active',
    ];

    /**
     * Tampilkan daftar pengguna dengan pagination 10 data per halaman.
     */
    public function index(): View
    {
        $pengguna = User::orderBy('id')
            ->paginate(10);

        return view('pengguna.index', [
            'pengguna' => $pengguna,
        ]);
    }

    /**
     * Tampilkan formulir untuk menambah pengguna baru.
     */
    public function create(): View
    {
        $pengguna = new User([
            'role' => User::ROLE_PENGELOLA,
        ]);

        return view('pengguna.form', [
            'pengguna' => $pengguna,
            'roles' => User::ROLES,
            'isEdit' => false,
        ]);
    }

    /**
     * Simpan pengguna baru dengan kata sandi sementara acak minimal 10 karakter
     * dan wajib ganti kata sandi saat login pertama.
     */
    public function store(PenggunaRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $tempPassword = Str::random(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $tempPassword,
            'is_active' => true,
            'must_change_password' => true,
            'email_verified_at' => now(),
        ]);

        $actor = $request->user()?->name ?? 'Admin';

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_TAMBAH,
            deskripsi: "{$actor} menambah akun pengguna {$user->name} ({$user->role_label})",
            objek: $user,
            dataBaru: ActivityLogger::ringkasModel($user, self::LOG_COLUMNS),
        );

        return redirect()
            ->route('pengguna.index')
            ->with('success', "Akun pengguna {$user->name} berhasil ditambahkan.")
            ->with('temp_password', $tempPassword)
            ->with('temp_password_user', $user->name)
            ->with('temp_password_email', $user->email);
    }

    /**
     * Tampilkan formulir untuk mengubah data pengguna.
     */
    public function edit(User $pengguna): View
    {
        return view('pengguna.form', [
            'pengguna' => $pengguna,
            'roles' => User::ROLES,
            'isEdit' => true,
        ]);
    }

    /**
     * Perbarui data nama, email, dan role pengguna dengan aturan keamanan admin.
     */
    public function update(PenggunaRequest $request, User $pengguna): RedirectResponse
    {
        $validated = $request->validated();

        // Admin tidak boleh mengubah role-nya sendiri
        if ($pengguna->id === $request->user()?->id && $validated['role'] !== $pengguna->role) {
            return back()
                ->withInput()
                ->withErrors([
                    'role' => 'Anda tidak dapat mengubah peran (role) akun Anda sendiri.',
                ]);
        }

        // Harus selalu ada minimal satu admin aktif
        if (
            $pengguna->isAdmin()
            && $pengguna->is_active
            && $validated['role'] !== User::ROLE_ADMIN
            && $this->jumlahAdminAktif() <= 1
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'role' => 'Perubahan ditolak. Harus selalu ada minimal satu akun Admin yang aktif.',
                ]);
        }

        $dataLama = ActivityLogger::ringkasModel($pengguna, self::LOG_COLUMNS);

        $pengguna->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        $dataBaru = ActivityLogger::ringkasModel($pengguna, self::LOG_COLUMNS);
        $actor = $request->user()?->name ?? 'Admin';

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_UBAH,
            deskripsi: "{$actor} mengubah akun pengguna {$pengguna->name}",
            objek: $pengguna,
            dataLama: $dataLama,
            dataBaru: $dataBaru,
        );

        return redirect()
            ->route('pengguna.index')
            ->with('success', "Data pengguna {$pengguna->name} berhasil diperbarui.");
    }

    /**
     * Aktifkan atau nonaktifkan akun pengguna.
     */
    public function toggleStatus(Request $request, User $pengguna): RedirectResponse
    {
        // Admin tidak boleh menonaktifkan akunnya sendiri
        if ($pengguna->id === $request->user()?->id) {
            return redirect()
                ->route('pengguna.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        // Harus selalu ada minimal satu admin aktif
        if ($pengguna->is_active && $pengguna->isAdmin() && $this->jumlahAdminAktif() <= 1) {
            return redirect()
                ->route('pengguna.index')
                ->with('error', 'Tindakan ditolak. Harus selalu ada minimal satu akun Admin yang aktif.');
        }

        $dataLama = ['is_active' => $pengguna->is_active];
        $statusBaru = ! $pengguna->is_active;

        $pengguna->update([
            'is_active' => $statusBaru,
        ]);

        $actor = $request->user()?->name ?? 'Admin';
        $aksiTeks = $statusBaru ? 'mengaktifkan' : 'menonaktifkan';

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_STATUS_AKUN,
            deskripsi: "{$actor} {$aksiTeks} akun pengguna {$pengguna->name}",
            objek: $pengguna,
            dataLama: $dataLama,
            dataBaru: ['is_active' => $statusBaru],
        );

        $pesan = $statusBaru
            ? "Akun {$pengguna->name} berhasil diaktifkan kembali."
            : "Akun {$pengguna->name} berhasil dinonaktifkan.";

        return redirect()
            ->route('pengguna.index')
            ->with('success', $pesan);
    }

    /**
     * Reset kata sandi pengguna dengan membuat kata sandi sementara acak baru
     * dan mewajibkan pengguna menggantinya saat login berikutnya.
     */
    public function resetPassword(Request $request, User $pengguna): RedirectResponse
    {
        $tempPassword = Str::random(12);

        $pengguna->update([
            'password' => $tempPassword,
            'must_change_password' => true,
        ]);

        $actor = $request->user()?->name ?? 'Admin';

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_RESET_SANDI,
            deskripsi: "{$actor} mereset kata sandi untuk akun pengguna {$pengguna->name}",
            objek: $pengguna,
        );

        return redirect()
            ->route('pengguna.index')
            ->with('success', "Kata sandi untuk {$pengguna->name} berhasil diatur ulang.")
            ->with('temp_password', $tempPassword)
            ->with('temp_password_user', $pengguna->name)
            ->with('temp_password_email', $pengguna->email);
    }

    /**
     * Hitung jumlah akun admin yang berstatus aktif.
     */
    private function jumlahAdminAktif(): int
    {
        return User::where('role', User::ROLE_ADMIN)
            ->where('is_active', true)
            ->count();
    }
}
