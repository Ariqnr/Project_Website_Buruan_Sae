<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Perbarui kata sandi pengguna dan matikan penanda must_change_password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag(
            'updatePassword',
            [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'string', 'min:8', 'different:current_password', 'confirmed'],
            ],
            [
                'current_password.required' => 'Kata sandi saat ini wajib diisi.',
                'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
                'password.required' => 'Kata sandi baru wajib diisi.',
                'password.min' => 'Kata sandi baru minimal harus 8 karakter.',
                'password.different' => 'Kata sandi baru tidak boleh sama dengan kata sandi lama.',
                'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            ],
        );

        $user = $request->user();

        $user->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_GANTI_SANDI,
            deskripsi: "{$user->name} mengganti kata sandi akunnya sendiri",
            objek: $user,
            userId: $user->id,
        );

        return redirect()
            ->route('profile.edit')
            ->with('status', 'password-updated')
            ->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
