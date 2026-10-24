<x-app-layout>
    <x-slot name="title">Jadwal Penjemuran</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    Jadwal Penjemuran Sampah
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">
                    Kelola dan pantau jadwal penjemuran sampah di Buruan Sae.
                </p>
            </div>
            <div>
                <a href="{{ route('jadwal.create') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-medium text-sm rounded-lg shadow-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Jadwal</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

            {{-- Pesan Sukses / Notifikasi --}}
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

            {{-- Kartu Filter Status Jadwal --}}
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form method="GET" action="{{ route('jadwal.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
                    <div class="sm:col-span-8">
                        <label for="status" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                            Filter Status Jadwal
                        </label>
                        <select id="status" name="status" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-green-500 focus:ring-green-500 shadow-sm">
                            <option value="">Semua Status Jadwal</option>
                            @foreach ($statuses as $key => $label)
                                <option value="{{ $key }}" @selected($currentStatus === $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-4 flex gap-2">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 dark:bg-green-700 hover:bg-gray-900 dark:hover:bg-green-800 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            Terapkan Filter
                        </button>
                        @if ($currentStatus !== '')
                            <a href="{{ route('jadwal.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition" title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabel Jadwal Penjemuran (Ukuran Tabel Menyesuaikan Layar HP & Laptop/PC) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                @if ($jadwal->isEmpty())
                    <div class="p-8 sm:p-12 text-center">
                        <div class="mx-auto w-16 h-16 bg-green-50 dark:bg-green-950/60 rounded-full flex items-center justify-center text-green-600 dark:text-green-400 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">
                            @if ($currentStatus !== '')
                                Tidak ada jadwal yang sesuai filter
                            @else
                                Belum ada jadwal penjemuran
                            @endif
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
                            @if ($currentStatus !== '')
                                Coba ubah atau reset filter status untuk melihat jadwal penjemuran lainnya.
                            @else
                                Belum ada jadwal penjemuran. Klik Tambah Jadwal untuk membuat.
                            @endif
                        </p>
                        @if ($currentStatus !== '')
                            <a href="{{ route('jadwal.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg">
                                Reset Filter
                            </a>
                        @else
                            <a href="{{ route('jadwal.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Tambah Jadwal</span>
                            </a>
                        @endif
                    </div>
                @else
                    <div class="w-full overflow-x-auto">
                        <table class="w-full table-auto text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-5">Jenis Sampah</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4 text-right">Berat (kg)</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Tanggal Mulai</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Tanggal Selesai</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Status Jadwal</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Catatan</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-xs sm:text-sm">
                                @foreach ($jadwal as $item)
                                    <tr class="hover:bg-gray-50/75 dark:hover:bg-gray-700/40 transition align-middle">
                                        {{-- Jenis Sampah --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-5 font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $item->sampah->jenis ?? '-' }}
                                            @if ($item->sampah?->tanggal_masuk)
                                                <p class="text-[11px] sm:text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 whitespace-nowrap">
                                                    Masuk: {{ $item->sampah->tanggal_masuk->translatedFormat('d M Y') }}
                                                </p>
                                            @endif
                                        </td>

                                        {{-- Berat (kg) --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-4 text-right whitespace-nowrap font-semibold text-gray-900 dark:text-gray-100">
                                            {{ number_format($item->sampah->berat_kg ?? 0, 2, ',', '.') }} kg
                                        </td>

                                        {{-- Tanggal Mulai --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-4 whitespace-nowrap font-medium text-gray-800 dark:text-gray-200">
                                            {{ $item->tanggal_mulai->translatedFormat('d M Y') }}
                                        </td>

                                        {{-- Tanggal Selesai --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-4 whitespace-nowrap font-medium text-gray-800 dark:text-gray-200">
                                            {{ $item->tanggal_selesai->translatedFormat('d M Y') }}
                                        </td>

                                        {{-- Status Jadwal (Otomatis dari Tanggal) --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-4 whitespace-nowrap">
                                            @if ($item->status_jadwal === \App\Models\JadwalPenjemuran::STATUS_AKAN_DATANG)
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                    Akan Datang
                                                </span>
                                            @elseif ($item->status_jadwal === \App\Models\JadwalPenjemuran::STATUS_SEDANG_BERJALAN)
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                                    Sedang Berjalan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                    Selesai
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Catatan --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-4 text-gray-600 dark:text-gray-300 max-w-[110px] sm:max-w-xs truncate" title="{{ $item->catatan }}">
                                            {{ $item->catatan ?: '-' }}
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-5 whitespace-nowrap text-center">
                                            <div class="inline-flex flex-col sm:flex-row items-center gap-1 sm:gap-2">
                                                {{-- Tombol Ubah --}}
                                                <a href="{{ route('jadwal.edit', $item) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-md text-[11px] sm:text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition" title="Ubah jadwal">
                                                    Ubah
                                                </a>

                                                {{-- Tombol Hapus (Hanya Admin) --}}
                                                @can('delete', $item)
                                                    <form method="POST" action="{{ route('jadwal.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal penjemuran untuk {{ $item->sampah->jenis ?? 'sampah' }} ini? Tindakan ini tidak dapat dibatalkan.');" class="w-full sm:w-auto inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-md text-[11px] sm:text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 transition" title="Hapus jadwal">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($jadwal->hasPages())
                        <div class="p-3 sm:p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                            {{ $jadwal->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
