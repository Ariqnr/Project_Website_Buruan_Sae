<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LaporanFilterRequest extends FormRequest
{
    /**
     * Semua pengguna terautentikasi (admin & pengelola) diizinkan mengakses laporan.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Siapkan nilai default tanggal_awal (awal bulan ini) dan tanggal_akhir (hari ini)
     * apabila parameter belum dikirimkan pada query string.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'tanggal_awal' => $this->filled('tanggal_awal')
                ? $this->input('tanggal_awal')
                : now()->startOfMonth()->toDateString(),
            'tanggal_akhir' => $this->filled('tanggal_akhir')
                ? $this->input('tanggal_akhir')
                : now()->toDateString(),
        ]);
    }

    /**
     * Aturan validasi filter periode laporan.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal_awal' => ['required', 'date'],
            'tanggal_akhir' => ['required', 'date', 'after_or_equal:tanggal_awal'],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tanggal_awal.required' => 'Tanggal awal wajib diisi.',
            'tanggal_awal.date' => 'Format tanggal awal tidak valid.',
            'tanggal_akhir.required' => 'Tanggal akhir wajib diisi.',
            'tanggal_akhir.date' => 'Format tanggal akhir tidak valid.',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.',
        ];
    }
}
