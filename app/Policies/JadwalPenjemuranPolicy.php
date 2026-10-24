<?php

namespace App\Policies;

use App\Models\JadwalPenjemuran;
use App\Models\User;

class JadwalPenjemuranPolicy
{
    /**
     * Semua role terdaftar boleh melihat daftar jadwal penjemuran.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Semua role terdaftar boleh melihat detail jadwal penjemuran.
     */
    public function view(User $user, JadwalPenjemuran $jadwalPenjemuran): bool
    {
        return true;
    }

    /**
     * Semua role terdaftar boleh membuat jadwal penjemuran baru.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Semua role terdaftar boleh mengubah jadwal penjemuran.
     */
    public function update(User $user, JadwalPenjemuran $jadwalPenjemuran): bool
    {
        return true;
    }

    /**
     * Hanya admin yang berhak menghapus jadwal penjemuran.
     */
    public function delete(User $user, JadwalPenjemuran $jadwalPenjemuran): bool
    {
        return $user->isAdmin();
    }
}
