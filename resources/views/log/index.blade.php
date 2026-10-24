<x-app-layout>
    <x-slot name="title">Log Aktivitas</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    Log Aktivitas Sistem
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">
                    Catatan transparan siapa melakukan apa dan kapan (Zona Waktu: WIB / Asia/Jakarta).
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

            {{-- Kartu Filter Log Aktivitas --}}
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form method="GET" action="{{ route('log.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
                    {{-- Tanggal Awal --}}
                    <div class="sm:col-span-2">
                        <label for="tanggal_awal" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                            Dari Tanggal
                        </label>
                        <input
                            type="date"
                            id="tanggal_awal"
                            name="tanggal_awal"
                            value="{{ $tanggalAwal }}"
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-green-500 focus:ring-green-500 shadow-sm"
                        />
                    </div>

                    {{-- Tanggal Akhir --}}
                    <div class="sm:col-span-2">
                        <label for="tanggal_akhir" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                            Sampai Tanggal
                        </label>
                        <input
                            type="date"
                            id="tanggal_akhir"
                            name="tanggal_akhir"
                            value="{{ $tanggalAkhir }}"
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-green-500 focus:ring-green-500 shadow-sm"
                        />
                    </div>

                    {{-- Filter Pengguna --}}
                    <div class="sm:col-span-3">
                        <label for="user_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                            Pengguna
                        </label>
                        <select
                            id="user_id"
                            name="user_id"
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-green-500 focus:ring-green-500 shadow-sm"
                        >
                            <option value="">Semua Pengguna</option>
                            @foreach ($daftarPengguna as $u)
                                <option value="{{ $u->id }}" @selected($currentUserId === (string) $u->id)>
                                    {{ $u->name }} ({{ $u->role_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Jenis Aksi --}}
                    <div class="sm:col-span-3">
                        <label for="aksi" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                            Jenis Aksi
                        </label>
                        <select
                            id="aksi"
                            name="aksi"
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-green-500 focus:ring-green-500 shadow-sm"
                        >
                            <option value="">Semua Jenis Aksi</option>
                            @foreach ($daftarAksi as $key => $label)
                                <option value="{{ $key }}" @selected($currentAksi === $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Filter & Reset --}}
                    <div class="sm:col-span-2 flex gap-2">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 dark:bg-green-700 hover:bg-gray-900 dark:hover:bg-green-800 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            Terapkan
                        </button>
                        @if ($tanggalAwal !== '' || $tanggalAkhir !== '' || $currentUserId !== '' || $currentAksi !== '')
                            <a href="{{ route('log.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition" title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabel Log Aktivitas (Ukuran Menyesuaikan Layar HP & Laptop/PC) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                @if ($logs->isEmpty())
                    <div class="p-8 sm:p-12 text-center">
                        <div class="mx-auto w-16 h-16 bg-green-50 dark:bg-green-950/60 rounded-full flex items-center justify-center text-green-600 dark:text-green-400 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">
                            Belum ada catatan log aktivitas
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                            @if ($tanggalAwal !== '' || $tanggalAkhir !== '' || $currentUserId !== '' || $currentAksi !== '')
                                Tidak ada aktivitas yang sesuai dengan filter yang dipilih. Silakan ubah atau reset filter.
                            @else
                                Belum ada aktivitas yang tercatat di dalam sistem.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="w-full overflow-x-auto">
                        <table class="w-full table-auto text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-5">Waktu</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Pengguna</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Aksi</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Deskripsi</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-5">Alamat IP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-xs sm:text-sm">
                                @foreach ($logs as $log)
                                    @php
                                        $hasComparison = is_array($log->data_lama) && is_array($log->data_baru) && (! empty($log->data_lama) || ! empty($log->data_baru));
                                    @endphp
                                    <tr x-data="{ openDiff: false }" class="hover:bg-gray-50/75 dark:hover:bg-gray-700/40 transition align-top">
                                        {{-- Waktu (Zona Asia/Jakarta, format 23/10/2026 14:05) --}}
                                        <td class="py-2.5 px-2 sm:py-3.5 sm:px-5 whitespace-nowrap font-mono text-[11px] sm:text-xs text-gray-700 dark:text-gray-300">
                                            {{ $log->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                        </td>

                                        {{-- Pengguna --}}
                                        <td class="py-2.5 px-2 sm:py-3.5 sm:px-4 font-medium text-gray-900 dark:text-gray-100">
                                            {{ $log->user?->name ?? 'Tamu / Sistem' }}
                                        </td>

                                        {{-- Aksi (Badge Berwarna) --}}
                                        <td class="py-2.5 px-2 sm:py-3.5 sm:px-4 whitespace-nowrap">
                                            @if (in_array($log->aksi, [\App\Models\ActivityLog::AKSI_TAMBAH, \App\Models\ActivityLog::AKSI_LOGIN_BERHASIL, \App\Models\ActivityLog::AKSI_PULIHKAN], true))
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                    {{ $log->aksi_label }}
                                                </span>
                                            @elseif (in_array($log->aksi, [\App\Models\ActivityLog::AKSI_UBAH, \App\Models\ActivityLog::AKSI_STATUS_AKUN, \App\Models\ActivityLog::AKSI_RESET_SANDI, \App\Models\ActivityLog::AKSI_GANTI_SANDI], true))
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                                    {{ $log->aksi_label }}
                                                </span>
                                            @elseif (in_array($log->aksi, [\App\Models\ActivityLog::AKSI_HAPUS, \App\Models\ActivityLog::AKSI_LOGIN_GAGAL], true))
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                                    {{ $log->aksi_label }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                    {{ $log->aksi_label }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Deskripsi & Tombol Lihat Perubahan --}}
                                        <td class="py-2.5 px-2 sm:py-3.5 sm:px-4 text-gray-800 dark:text-gray-200">
                                            <div>{{ $log->deskripsi }}</div>

                                            @if ($hasComparison)
                                                <div class="mt-2">
                                                    <button
                                                        type="button"
                                                        @click="openDiff = !openDiff"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 hover:bg-amber-100 transition"
                                                    >
                                                        <span x-text="openDiff ? 'Sembunyikan Perubahan' : 'Lihat Perubahan'">Lihat Perubahan</span>
                                                    </button>

                                                    {{-- Panel Perbandingan Data Lama vs Data Baru --}}
                                                    <div
                                                        x-show="openDiff"
                                                        x-transition
                                                        style="display: none;"
                                                        class="mt-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs"
                                                    >
                                                        <div>
                                                            <div class="font-bold text-red-700 dark:text-red-400 uppercase tracking-wider mb-1">
                                                                Data Lama
                                                            </div>
                                                            <dl class="space-y-1">
                                                                @foreach ($log->data_lama as $field => $val)
                                                                    <div class="flex justify-between gap-2 border-b border-gray-200/60 dark:border-gray-700/60 pb-0.5">
                                                                        <dt class="font-medium text-gray-600 dark:text-gray-400">{{ $field }}:</dt>
                                                                        <dd class="font-mono text-gray-900 dark:text-gray-100 text-right">
                                                                            {{ is_bool($val) ? ($val ? 'Aktif' : 'Nonaktif') : ($val ?? '-') }}
                                                                        </dd>
                                                                    </div>
                                                                @endforeach
                                                            </dl>
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-green-700 dark:text-green-400 uppercase tracking-wider mb-1">
                                                                Data Baru
                                                            </div>
                                                            <dl class="space-y-1">
                                                                @foreach ($log->data_baru as $field => $val)
                                                                    <div class="flex justify-between gap-2 border-b border-gray-200/60 dark:border-gray-700/60 pb-0.5">
                                                                        <dt class="font-medium text-gray-600 dark:text-gray-400">{{ $field }}:</dt>
                                                                        <dd class="font-mono text-gray-900 dark:text-gray-100 text-right">
                                                                            {{ is_bool($val) ? ($val ? 'Aktif' : 'Nonaktif') : ($val ?? '-') }}
                                                                        </dd>
                                                                    </div>
                                                                @endforeach
                                                            </dl>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Alamat IP --}}
                                        <td class="py-2.5 px-2 sm:py-3.5 sm:px-5 whitespace-nowrap font-mono text-[11px] sm:text-xs text-gray-600 dark:text-gray-400">
                                            {{ $log->ip_address ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination 20 Data per Halaman --}}
                    @if ($logs->hasPages())
                        <div class="p-3 sm:p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
