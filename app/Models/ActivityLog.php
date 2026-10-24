<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('activity_logs')]
#[Fillable([
    'user_id',
    'aksi',
    'objek_tipe',
    'objek_id',
    'deskripsi',
    'data_lama',
    'data_baru',
    'ip_address',
    'created_at',
])]
class ActivityLog extends Model
{
    /**
     * Tabel activity_logs tidak memiliki kolom updated_at.
     */
    public const UPDATED_AT = null;

    public const AKSI_LOGIN_BERHASIL = 'login_berhasil';

    public const AKSI_LOGIN_GAGAL = 'login_gagal';

    public const AKSI_LOGOUT = 'logout';

    public const AKSI_TAMBAH = 'tambah';

    public const AKSI_UBAH = 'ubah';

    public const AKSI_HAPUS = 'hapus';

    public const AKSI_PULIHKAN = 'pulihkan';

    public const AKSI_STATUS_AKUN = 'status_akun';

    public const AKSI_RESET_SANDI = 'reset_sandi';

    public const AKSI_GANTI_SANDI = 'ganti_sandi';

    public const AKSI_UNDUH_PDF = 'unduh_pdf';

    /**
     * Daftar kode aksi beserta label Bahasa Indonesia untuk filter dan badge.
     *
     * @var array<string, string>
     */
    public const DAFTAR_AKSI = [
        self::AKSI_LOGIN_BERHASIL => 'Login Berhasil',
        self::AKSI_LOGIN_GAGAL => 'Login Gagal',
        self::AKSI_LOGOUT => 'Logout',
        self::AKSI_TAMBAH => 'Tambah Data',
        self::AKSI_UBAH => 'Ubah Data',
        self::AKSI_HAPUS => 'Hapus Data',
        self::AKSI_PULIHKAN => 'Pulihkan Data',
        self::AKSI_STATUS_AKUN => 'Status Akun',
        self::AKSI_RESET_SANDI => 'Reset Kata Sandi',
        self::AKSI_GANTI_SANDI => 'Ganti Kata Sandi',
        self::AKSI_UNDUH_PDF => 'Unduh PDF',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_lama' => 'array',
            'data_baru' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Pengguna yang melakukan aktivitas (nullable jika tamu / login gagal / user dihapus).
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label aksi dalam Bahasa Indonesia.
     */
    public function getAksiLabelAttribute(): string
    {
        return self::DAFTAR_AKSI[$this->aksi] ?? ucfirst(str_replace('_', ' ', (string) $this->aksi));
    }
}
