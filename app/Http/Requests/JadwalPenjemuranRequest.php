<?php

namespace App\Http\Requests;

use App\Models\Sampah;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class JadwalPenjemuranRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berwenang membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk jadwal penjemuran.
     * Hanya sampah kategori Organik yang dapat dijadwalkan untuk penjemuran.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sampah_id' => [
                'required',
                'integer',
                'bail',
                'exists:sampah,id',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $sampah = Sampah::find($value);

                    if ($sampah !== null && $sampah->jenis !== Sampah::JENIS_ORGANIK) {
                        $fail('Hanya sampah kategori Organik yang dapat dijadwalkan untuk penjemuran.');
                    }
                },
            ],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Pesan validasi khusus yang ramah dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sampah_id.required' => 'Data sampah yang akan dijemur wajib dipilih.',
            'sampah_id.exists' => 'Data sampah yang dipilih tidak ditemukan.',
            'tanggal_mulai.required' => 'Tanggal mulai penjemuran wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai penjemuran wajib diisi.',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'catatan.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
