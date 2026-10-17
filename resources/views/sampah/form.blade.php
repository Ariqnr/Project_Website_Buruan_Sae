<x-app-layout>
    <x-slot name="title">{{ $isEdit ? 'Ubah Data Sampah' : 'Tambah Data Sampah' }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ $isEdit ? 'Ubah Data Sampah' : 'Tambah Data Sampah Baru' }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $isEdit ? 'Perbarui informasi pencatatan sampah yang sudah tersimpan.' : 'Masukkan data sampah baru yang masuk ke Buruan Sae.' }}
                </p>
            </div>
            <div>
                <a href="{{ route('sampah.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6 sm:p-8">

                <form method="POST" action="{{ $isEdit ? route('sampah.update', $sampah) : route('sampah.store') }}" class="space-y-6">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    {{-- Tanggal Masuk --}}
                    <div>
                        <x-input-label for="tanggal_masuk" value="Tanggal Masuk *" class="font-medium text-gray-800" />
                        <x-text-input 
                            id="tanggal_masuk" 
                            type="date" 
                            name="tanggal_masuk" 
                            :value="old('tanggal_masuk', $sampah->tanggal_masuk?->format('Y-m-d') ?? date('Y-m-d'))" 
                            max="{{ date('Y-m-d') }}" 
                            class="block mt-1 w-full text-base" 
                            required 
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            Pilih tanggal saat sampah diterima (maksimal hari ini).
                        </p>
                        <x-input-error :messages="$errors->get('tanggal_masuk')" class="mt-2" />
                    </div>

                    {{-- Jenis Sampah --}}
                    <div>
                        <x-input-label for="jenis" value="Jenis Sampah *" class="font-medium text-gray-800" />
                        <select 
                            id="jenis" 
                            name="jenis" 
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-base" 
                            required
                        >
                            <option value="">-- Pilih Jenis Sampah --</option>
                            @foreach ($jenisList as $jenis)
                                <option value="{{ $jenis }}" @selected(old('jenis', $sampah->jenis) === $jenis)>
                                    {{ $jenis }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Pilih klasifikasi jenis sampah sesuai peruntukan.
                        </p>
                        <x-input-error :messages="$errors->get('jenis')" class="mt-2" />
                    </div>

                    {{-- Berat (kg) --}}
                    <div>
                        <x-input-label for="berat_kg" value="Berat (kg) *" class="font-medium text-gray-800" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <x-text-input 
                                id="berat_kg" 
                                type="number" 
                                step="0.01" 
                                min="0.01" 
                                name="berat_kg" 
                                :value="old('berat_kg', $sampah->exists ? $sampah->berat_kg : '')" 
                                placeholder="Contoh: 12.5" 
                                class="block w-full pr-12 text-base" 
                                required 
                            />
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-500 sm:text-sm font-medium">kg</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Masukkan berat sampah dalam kilogram. Gunakan tanda titik untuk desimal.
                        </p>
                        <x-input-error :messages="$errors->get('berat_kg')" class="mt-2" />
                    </div>

                    {{-- Status Pengelolaan --}}
                    <div>
                        <x-input-label for="status" value="Status Pengelolaan *" class="font-medium text-gray-800" />
                        <select 
                            id="status" 
                            name="status" 
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-base" 
                            required
                        >
                            @foreach ($statuses as $val => $label)
                                <option value="{{ $val }}" @selected(old('status', $sampah->status ?? \App\Models\Sampah::STATUS_MASUK) === $val)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Status saat ini: Masuk (baru datang), Dikelola (sedang diolah), atau Selesai.
                        </p>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    {{-- Catatan (Opsional) --}}
                    <div>
                        <x-input-label for="catatan" value="Catatan (Opsional)" class="font-medium text-gray-800" />
                        <textarea 
                            id="catatan" 
                            name="catatan" 
                            rows="3" 
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-base" 
                            placeholder="Contoh: Dari warga RT 02, siap untuk dijadikan bahan kompos..."
                        >{{ old('catatan', $sampah->catatan) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">
                            Informasi tambahan mengenai asal sampah atau instruksi pengelolaan.
                        </p>
                        <x-input-error :messages="$errors->get('catatan')" class="mt-2" />
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pt-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                        <a href="{{ route('sampah.index') }}" class="inline-flex justify-center items-center px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 rounded-lg bg-green-700 hover:bg-green-800 text-white text-sm font-semibold shadow-sm transition">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Data Sampah' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
