<x-app-layout>
    <x-slot name="title">{{ $isEdit ? 'Ubah Jadwal Penjemuran' : 'Tambah Jadwal Penjemuran' }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ $isEdit ? 'Ubah Jadwal Penjemuran' : 'Tambah Jadwal Penjemuran Baru' }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">
                    {{ $isEdit ? 'Perbarui tanggal atau catatan jadwal penjemuran yang sudah dibuat.' : 'Pilih data sampah dan tentukan rentang tanggal penjemuran.' }}
                </p>
            </div>
            <div>
                <a href="{{ route('jadwal.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden p-6 sm:p-8">

                <form method="POST" action="{{ $isEdit ? route('jadwal.update', $jadwal) : route('jadwal.store') }}" class="space-y-6">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    {{-- Pilihan Data Sampah (Khusus Kategori Organik) --}}
                    <div>
                        <x-input-label for="sampah_id" value="Pilih Data Sampah (Kategori Organik) *" class="font-medium text-gray-800 dark:text-gray-200" />
                        <select
                            id="sampah_id"
                            name="sampah_id"
                            class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 text-base"
                            required
                        >
                            <option value="">-- Pilih Data Sampah Organik --</option>
                            @foreach ($daftarSampah as $item)
                                <option value="{{ $item->id }}" @selected((int) old('sampah_id', $jadwal->sampah_id) === $item->id)>
                                    {{ $item->tanggal_masuk->translatedFormat('d M Y') }} - {{ $item->jenis }} ({{ number_format($item->berat_kg, 2, ',', '.') }} kg) [{{ $item->status_label }}]
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Hanya sampah kategori <strong>Organik</strong> yang dapat dijadwalkan untuk penjemuran. Jika status sampah masih "Masuk", statusnya otomatis berubah menjadi "Dikelola" saat jadwal disimpan.
                        </p>
                        <x-input-error :messages="$errors->get('sampah_id')" class="mt-2" />
                    </div>

                    {{-- Tanggal Mulai & Tanggal Selesai --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="tanggal_mulai" value="Tanggal Mulai *" class="font-medium text-gray-800 dark:text-gray-200" />
                            <x-text-input
                                id="tanggal_mulai"
                                type="date"
                                name="tanggal_mulai"
                                :value="old('tanggal_mulai', $jadwal->tanggal_mulai?->format('Y-m-d') ?? date('Y-m-d'))"
                                class="block mt-1 w-full text-base"
                                required
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Tanggal dimulainya proses penjemuran.
                            </p>
                            <x-input-error :messages="$errors->get('tanggal_mulai')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tanggal_selesai" value="Tanggal Selesai *" class="font-medium text-gray-800 dark:text-gray-200" />
                            <x-text-input
                                id="tanggal_selesai"
                                type="date"
                                name="tanggal_selesai"
                                :value="old('tanggal_selesai', $jadwal->tanggal_selesai?->format('Y-m-d') ?? date('Y-m-d'))"
                                class="block mt-1 w-full text-base"
                                required
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Tanggal selesai penjemuran (tidak boleh sebelum tanggal mulai).
                            </p>
                            <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Catatan (Opsional) --}}
                    <div>
                        <x-input-label for="catatan" value="Catatan (Opsional)" class="font-medium text-gray-800 dark:text-gray-200" />
                        <textarea
                            id="catatan"
                            name="catatan"
                            rows="3"
                            class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 text-base"
                            placeholder="Contoh: Dijemur di area rak timur, balik setiap pagi..."
                        >{{ old('catatan', $jadwal->catatan) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Instruksi atau keterangan tambahan untuk petugas penjemuran.
                        </p>
                        <x-input-error :messages="$errors->get('catatan')" class="mt-2" />
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                        <a href="{{ route('jadwal.index') }}" class="inline-flex justify-center items-center px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 rounded-lg bg-green-700 hover:bg-green-800 text-white text-sm font-semibold shadow-sm transition">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Jadwal' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
