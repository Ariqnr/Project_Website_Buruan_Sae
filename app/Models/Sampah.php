<?php

namespace App\Models;

use App\Policies\SampahPolicy;
use Database\Factories\SampahFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('sampah')]
#[UsePolicy(SampahPolicy::class)]
#[Fillable(['user_id', 'tanggal_masuk', 'jenis', 'berat_kg', 'status', 'catatan'])]
class Sampah extends Model
{
    /** @use HasFactory<SampahFactory> */
    use HasFactory;

    public const STATUS_MASUK = 'masuk';

    public const STATUS_DIKELOLA = 'dikelola';

    public const STATUS_SELESAI = 'selesai';

    /**
     * Daftar status beserta label tampilannya.
     *
     * @var array<string, string>
     */
    public const STATUSES = [
        self::STATUS_MASUK => 'Masuk',
        self::STATUS_DIKELOLA => 'Dikelola',
        self::STATUS_SELESAI => 'Selesai',
    ];

    /**
     * Pilihan jenis sampah yang umum di Buruan Sae.
     *
     * @var list<string>
     */
    public const JENIS = [
        'Organik',
        'Anorganik',
        'Daun Kering',
        'Plastik',
        'Kertas',
        'Logam',
        'Kaca',
        'Lainnya',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'berat_kg' => 'decimal:2',
        ];
    }

    /**
     * User yang mencatat sampah ini.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Jadwal penjemuran untuk sampah ini.
     *
     * @return HasMany<JadwalPenjemuran, $this>
     */
    public function jadwalPenjemuran(): HasMany
    {
        return $this->hasMany(JadwalPenjemuran::class);
    }

    /**
     * Label status dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }
}
