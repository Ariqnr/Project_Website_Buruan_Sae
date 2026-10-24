<?php

namespace App\Models;

use App\Policies\JadwalPenjemuranPolicy;
use Database\Factories\JadwalPenjemuranFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('jadwal_penjemuran')]
#[UsePolicy(JadwalPenjemuranPolicy::class)]
#[Fillable(['sampah_id', 'tanggal_mulai', 'tanggal_selesai', 'catatan'])]
class JadwalPenjemuran extends Model
{
    /** @use HasFactory<JadwalPenjemuranFactory> */
    use HasFactory;

    public const STATUS_AKAN_DATANG = 'akan_datang';

    public const STATUS_SEDANG_BERJALAN = 'sedang_berjalan';

    public const STATUS_SELESAI = 'selesai';

    /**
     * Daftar status jadwal penjemuran yang dihitung otomatis dari tanggal.
     *
     * @var array<string, string>
     */
    public const STATUSES = [
        self::STATUS_AKAN_DATANG => 'Akan Datang',
        self::STATUS_SEDANG_BERJALAN => 'Sedang Berjalan',
        self::STATUS_SELESAI => 'Selesai',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    /**
     * Sampah yang dijemur.
     *
     * @return BelongsTo<Sampah, $this>
     */
    public function sampah(): BelongsTo
    {
        return $this->belongsTo(Sampah::class);
    }

    /**
     * Jadwal yang sedang berjalan hari ini (mulai <= hari ini <= selesai).
     *
     * @param  Builder<JadwalPenjemuran>  $query
     */
    public function scopeAktif(Builder $query): void
    {
        $today = now()->toDateString();

        $query->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today);
    }

    /**
     * Filter berdasarkan status jadwal yang dihitung dari tanggal.
     *
     * @param  Builder<JadwalPenjemuran>  $query
     */
    public function scopeFilterStatus(Builder $query, ?string $status): void
    {
        $today = now()->toDateString();

        if ($status === self::STATUS_AKAN_DATANG) {
            $query->whereDate('tanggal_mulai', '>', $today);
        } elseif ($status === self::STATUS_SEDANG_BERJALAN) {
            $query->aktif();
        } elseif ($status === self::STATUS_SELESAI) {
            $query->whereDate('tanggal_selesai', '<', $today);
        }
    }

    /**
     * Kode status jadwal otomatis berdasarkan perbandingan tanggal hari ini.
     */
    public function getStatusJadwalAttribute(): string
    {
        $today = now()->startOfDay();

        if ($this->tanggal_mulai !== null && $this->tanggal_mulai->startOfDay()->gt($today)) {
            return self::STATUS_AKAN_DATANG;
        }

        if ($this->tanggal_selesai !== null && $this->tanggal_selesai->startOfDay()->lt($today)) {
            return self::STATUS_SELESAI;
        }

        return self::STATUS_SEDANG_BERJALAN;
    }

    /**
     * Label status jadwal dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status_jadwal] ?? 'Sedang Berjalan';
    }
}
