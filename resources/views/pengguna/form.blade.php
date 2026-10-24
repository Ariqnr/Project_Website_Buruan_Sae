<x-app-layout>
    <x-slot name="title">{{ $isEdit ? 'Ubah Akun Pengguna' : 'Tambah Akun Pengguna' }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ $isEdit ? 'Ubah Akun Pengguna' : 'Tambah Akun Pengguna Baru' }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">
                    {{ $isEdit ? 'Perbarui nama, email, atau peran (role) pengguna.' : 'Buat akun baru untuk Admin atau Pengelola Buruan Sae.' }}
                </p>
            </div>
            <div>
                <a href="{{ route('pengguna.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden p-6 sm:p-8">

                @if (! $isEdit)
                    <div class="mb-6 p-4 rounded-lg bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-xs sm:text-sm text-blue-800 dark:text-blue-200">
                        <strong>Informasi Kata Sandi:</strong> Kata sandi sementara (12 karakter acak) akan dibuat secara otomatis saat akun disimpan dan ditampilkan <strong>satu kali</strong> di halaman daftar pengguna. Pengguna baru wajib mengganti kata sandi tersebut saat pertama kali masuk.
                    </div>
                @endif

                <form method="POST" action="{{ $isEdit ? route('pengguna.update', $pengguna) : route('pengguna.store') }}" class="space-y-6">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    {{-- Nama Lengkap --}}
                    <div>
                        <x-input-label for="name" value="Nama Lengkap *" class="font-medium text-gray-800 dark:text-gray-200" />
                        <x-text-input
                            id="name"
                            type="text"
                            name="name"
                            :value="old('name', $pengguna->name)"
                            placeholder="Contoh: Siti Rahmawati"
                            class="block mt-1 w-full text-base"
                            required
                            autofocus
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" value="Alamat Email *" class="font-medium text-gray-800 dark:text-gray-200" />
                        <x-text-input
                            id="email"
                            type="email"
                            name="email"
                            :value="old('email', $pengguna->email)"
                            placeholder="Contoh: siti@buruansae.test"
                            class="block mt-1 w-full text-base"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Gunakan huruf kecil. Email ini akan dipakai pengguna untuk masuk ke sistem.
                        </p>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Role --}}
                    <div>
                        <x-input-label for="role" value="Peran (Role) *" class="font-medium text-gray-800 dark:text-gray-200" />
                        <select
                            id="role"
                            name="role"
                            class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 text-base"
                            required
                        >
                            @foreach ($roles as $key => $label)
                                <option value="{{ $key }}" @selected(old('role', $pengguna->role) === $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            @if ($isEdit && $pengguna->id === auth()->id())
                                Catatan: Demi keamanan, Anda tidak dapat mengubah role akun Anda sendiri.
                            @else
                                Admin memiliki akses penuh termasuk kelola pengguna & hapus data. Pengelola mencatat sampah, jadwal, dan laporan.
                            @endif
                        </p>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                        <a href="{{ route('pengguna.index') }}" class="inline-flex justify-center items-center px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 rounded-lg bg-green-700 hover:bg-green-800 text-white text-sm font-semibold shadow-sm transition">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Buat Akun Pengguna' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
