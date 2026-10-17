<?php

namespace App\Http\Requests;

use App\Models\Sampah;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SampahRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berwenang membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk data sampah.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal_masuk' => ['required', 'date', 'before_or_equal:today'],
            'jenis' => ['required', 'string', Rule::in(Sampah::JENIS)],
            'berat_kg' => ['required', 'numeric', 'gt:0'],
            'status' => ['required', 'string', Rule::in(array_keys(Sampah::STATUSES))],
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
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'tanggal_masuk.date' => 'Format tanggal masuk tidak valid.',
            'tanggal_masuk.before_or_equal' => 'Tanggal masuk tidak boleh di masa depan (maksimal hari ini).',
            'jenis.required' => 'Jenis sampah wajib dipilih.',
            'jenis.in' => 'Pilihan jenis sampah tidak valid.',
            'berat_kg.required' => 'Berat sampah wajib diisi.',
            'berat_kg.numeric' => 'Berat sampah harus berupa angka.',
            'berat_kg.gt' => 'Berat sampah harus lebih besar dari 0 kg.',
            'status.required' => 'Status sampah wajib dipilih.',
            'status.in' => 'Pilihan status tidak valid.',
            'catatan.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
