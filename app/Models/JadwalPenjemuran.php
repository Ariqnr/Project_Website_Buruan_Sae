<?php

namespace App\Models;

use Database\Factories\JadwalPenjemuranFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('jadwal_penjemuran')]
#[Fillable(['sampah_id', 'tanggal_mulai', 'tanggal_selesai', 'catatan'])]
class JadwalPenjemuran extends Model
{
    /** @use HasFactory<JadwalPenjemuranFactory> */
    use HasFactory;

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
}
