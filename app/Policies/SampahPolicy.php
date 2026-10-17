<?php

namespace App\Policies;

use App\Models\Sampah;
use App\Models\User;

class SampahPolicy
{
    /**
     * Semua role terdaftar boleh melihat daftar sampah.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Semua role terdaftar boleh melihat detail sampah.
     */
    public function view(User $user, Sampah $sampah): bool
    {
        return true;
    }

    /**
     * Semua role terdaftar boleh mencatat sampah baru.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Semua role terdaftar boleh memperbarui data sampah.
     */
    public function update(User $user, Sampah $sampah): bool
    {
        return true;
    }

    /**
     * Hanya admin yang berhak menghapus data sampah.
     */
    public function delete(User $user, Sampah $sampah): bool
    {
        return $user->isAdmin();
    }
}
