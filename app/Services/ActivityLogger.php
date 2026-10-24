<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ActivityLogger
{
    /**
     * Daftar kolom sensitif yang TIDAK BOLEH disimpan ke dalam log aktivitas.
     *
     * @var list<string>
     */
    private const SENSITIVE_KEYS = [
        'password',
        'remember_token',
        'current_password',
        'password_confirmation',
        'token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Catat aktivitas ke tabel activity_logs secara aman.
     * Kegagalan pencatatan log tidak akan menghentikan aksi utama.
     *
     * @param  array<string, mixed>|null  $dataLama
     * @param  array<string, mixed>|null  $dataBaru
     */
    public static function catat(
        string $aksi,
        string $deskripsi,
        ?Model $objek = null,
        ?array $dataLama = null,
        ?array $dataBaru = null,
        ?int $userId = null,
        ?string $objekTipe = null,
        ?int $objekId = null,
    ): ?ActivityLog {
        try {
            $resolvedUserId = $userId ?? Auth::id();
            $resolvedObjekTipe = $objekTipe ?? ($objek !== null ? class_basename($objek) : null);
            $resolvedObjekId = $objekId ?? ($objek?->getKey() !== null ? (int) $objek->getKey() : null);

            $cleanLama = self::bersihkanDataSensitif($dataLama);
            $cleanBaru = self::bersihkanDataSensitif($dataBaru);

            $ipAddress = request()?->ip();

            return ActivityLog::create([
                'user_id' => $resolvedUserId,
                'aksi' => $aksi,
                'objek_tipe' => $resolvedObjekTipe,
                'objek_id' => $resolvedObjekId,
                'deskripsi' => $deskripsi,
                'data_lama' => $cleanLama,
                'data_baru' => $cleanBaru,
                'ip_address' => $ipAddress,
                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::error('Gagal mencatat log aktivitas: '.$e->getMessage(), [
                'aksi' => $aksi,
                'deskripsi' => $deskripsi,
            ]);

            return null;
        }
    }

    /**
     * Buang seluruh field sensitif seperti password dan remember_token secara rekursif.
     *
     * @param  array<string, mixed>|null  $data
     * @return array<string, mixed>|null
     */
    public static function bersihkanDataSensitif(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $cleaned = [];

        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), self::SENSITIVE_KEYS, true)) {
                continue;
            }

            $cleaned[$key] = is_array($value)
                ? self::bersihkanDataSensitif($value)
                : $value;
        }

        return $cleaned;
    }

    /**
     * Ambil atribut penting dari model untuk disimpan sebagai snapshot data_lama / data_baru.
     *
     * @param  list<string>  $kolom
     * @return array<string, mixed>
     */
    public static function ringkasModel(Model $model, array $kolom): array
    {
        $hasil = [];

        foreach ($kolom as $atribut) {
            $nilai = $model->getAttribute($atribut);

            if ($nilai instanceof \DateTimeInterface) {
                $nilai = $nilai->format('Y-m-d');
            }

            $hasil[$atribut] = $nilai;
        }

        return self::bersihkanDataSensitif($hasil) ?? [];
    }
}
