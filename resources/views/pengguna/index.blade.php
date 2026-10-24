<x-app-layout>
    <x-slot name="title">Manajemen Pengguna</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    Manajemen Pengguna
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">
                    Kelola akun Admin dan Pengelola Buruan Sae.
                </p>
            </div>
            <div>
                <a href="{{ route('pengguna.create') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-medium text-sm rounded-lg shadow-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Pengguna</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

            {{-- Pesan Sukses --}}
            @if (session('success'))
                <div class="p-4 rounded-lg bg-green-50 dark:bg-green-950/60 border border-green-200 dark:border-green-800 flex items-start gap-3 text-green-800 dark:text-green-200 shadow-sm" role="alert">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <div class="font-medium text-sm">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- Pesan Kesalahan / Peringatan --}}
            @if (session('error'))
                <div class="p-4 rounded-lg bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 flex items-start gap-3 text-red-800 dark:text-red-200 shadow-sm" role="alert">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div class="font-medium text-sm">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            {{-- Kotak Kata Sandi Sementara (Hanya Ditampilkan SATU KALI) --}}
            @if (session('temp_password'))
                <div
                    x-data="{ copied: false, tempPass: @js(session('temp_password')) }"
                    class="p-5 rounded-xl bg-amber-50 dark:bg-amber-950/70 border-2 border-amber-300 dark:border-amber-700 shadow-sm space-y-3"
                    role="region"
                    aria-label="Informasi Kata Sandi Sementara"
                >
                    <div class="flex items-start gap-3">
                        <div class="p-2 rounded-lg bg-amber-100 dark:bg-amber-900/80 text-amber-700 dark:text-amber-300 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-amber-900 dark:text-amber-100">
                                Kata Sandi Sementara untuk {{ session('temp_password_user') }} ({{ session('temp_password_email') }})
                            </h3>
                            <p class="text-xs sm:text-sm text-amber-800 dark:text-amber-200 mt-1">
                                <strong>Penting:</strong> Kata sandi sementara ini hanya ditampilkan <strong>SATU KALI</strong>. Silakan salin dan berikan kepada pengguna terkait. Pengguna wajib mengganti kata sandi ini saat pertama kali masuk.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-1">
                        <div class="flex-1 px-4 py-2.5 rounded-lg bg-white dark:bg-gray-900 border border-amber-300 dark:border-amber-700 font-mono text-base sm:text-lg font-bold tracking-wider text-gray-900 dark:text-gray-100 select-all text-center sm:text-left">
                            {{ session('temp_password') }}
                        </div>
                        <button
                            type="button"
                            @click="navigator.clipboard.writeText(tempPass); copied = true; setTimeout(() => copied = false, 2500)"
                            class="inline-flex justify-center items-center gap-2 px-5 py-2.5 rounded-lg bg-amber-700 hover:bg-amber-800 text-white text-sm font-semibold shadow-sm transition shrink-0"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                            <span x-text="copied ? 'Berhasil Disalin!' : 'Salin Kata Sandi'">Salin Kata Sandi</span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Tabel Daftar Pengguna (Ukuran Tabel Menyesuaikan Layar HP & Laptop/PC) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                @if ($pengguna->isEmpty())
                    <div class="p-8 sm:p-12 text-center">
                        <div class="mx-auto w-16 h-16 bg-green-50 dark:bg-green-950/60 rounded-full flex items-center justify-center text-green-600 dark:text-green-400 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">
                            Belum ada data pengguna
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
                            Belum ada akun pengguna yang terdaftar. Klik Tambah Pengguna untuk membuat akun baru.
                        </p>
                        <a href="{{ route('pengguna.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg shadow-sm">
                            <span>Tambah Pengguna</span>
                        </a>
                    </div>
                @else
                    <div class="w-full overflow-x-auto">
                        <table class="w-full table-auto text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-5">Nama</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Email</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Role</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Status</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-xs sm:text-sm">
                                @foreach ($pengguna as $item)
                                    <tr class="hover:bg-gray-50/75 dark:hover:bg-gray-700/40 transition align-middle">
                                        {{-- Nama --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-5 font-semibold text-gray-900 dark:text-gray-100">
                                            <div>
                                                {{ $item->name }}
                                                @if ($item->id === auth()->id())
                                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800">
                                                        Anda
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($item->must_change_password)
                                                <span class="inline-block mt-0.5 text-[11px] font-normal text-amber-600 dark:text-amber-400">
                                                    Wajib ganti kata sandi
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Email --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-4 text-gray-700 dark:text-gray-300 break-all sm:break-normal">
                                            {{ $item->email }}
                                        </td>

                                        {{-- Role --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-4 whitespace-nowrap">
                                            @if ($item->isAdmin())
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                                    Admin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                    Pengelola
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Status (Aktif / Nonaktif) --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-4 whitespace-nowrap">
                                            @if ($item->is_active)
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-5 whitespace-nowrap text-center">
                                            <div class="inline-flex flex-col sm:flex-row items-center gap-1 sm:gap-2">
                                                {{-- Tombol Ubah --}}
                                                <a href="{{ route('pengguna.edit', $item) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-md text-[11px] sm:text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition" title="Ubah data pengguna">
                                                    Ubah
                                                </a>

                                                {{-- Tombol Reset Kata Sandi --}}
                                                <form method="POST" action="{{ route('pengguna.reset-password', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin mereset kata sandi untuk {{ $item->name }}? Kata sandi sementara baru akan dibuat.');" class="w-full sm:w-auto inline">
                                                    @csrf
                                                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-md text-[11px] sm:text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition" title="Reset kata sandi">
                                                        Reset Sandi
                                                    </button>
                                                </form>

                                                {{-- Tombol Nonaktifkan / Aktifkan --}}
                                                @if ($item->id !== auth()->id())
                                                    <form method="POST" action="{{ route('pengguna.toggle-status', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin {{ $item->is_active ? 'menonaktifkan' : 'mengaktifkan kembali' }} akun {{ $item->name }}?');" class="w-full sm:w-auto inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        @if ($item->is_active)
                                                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-md text-[11px] sm:text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 transition" title="Nonaktifkan akun">
                                                                Nonaktifkan
                                                            </button>
                                                        @else
                                                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-md text-[11px] sm:text-xs font-medium bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 transition" title="Aktifkan akun">
                                                                Aktifkan
                                                            </button>
                                                        @endif
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($pengguna->hasPages())
                        <div class="p-3 sm:p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                            {{ $pengguna->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
