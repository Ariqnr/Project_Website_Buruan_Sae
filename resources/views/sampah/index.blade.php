<x-app-layout>
    <x-slot name="title">Data Sampah</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    Data Pengelolaan Sampah
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">
                    Catat dan kelola data sampah yang masuk ke Buruan Sae.
                </p>
            </div>
            <div>
                <a href="{{ route('sampah.create') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-medium text-sm rounded-lg shadow-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Data Sampah</span>
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

            {{-- Kartu Filter & Pencarian --}}
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form method="GET" action="{{ route('sampah.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
                    <div class="sm:col-span-5">
                        <label for="jenis" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                            Pencarian Jenis Sampah
                        </label>
                        <input type="text" id="jenis" name="jenis" value="{{ $currentJenis }}" placeholder="Ketik jenis sampah (misal: Organik)..." class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm" />
                    </div>

                    <div class="sm:col-span-4">
                        <label for="status" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                            Status Pengelolaan
                        </label>
                        <select id="status" name="status" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $key => $label)
                                <option value="{{ $key }}" @selected($currentStatus === $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-3 flex gap-2">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 dark:bg-green-700 hover:bg-gray-900 dark:hover:bg-green-800 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            Terapkan Filter
                        </button>
                        @if ($currentStatus !== '' || $currentJenis !== '')
                            <a href="{{ route('sampah.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition" title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabel Data Sampah (Ukuran Tabel Menyesuaikan Layar HP & Laptop/PC) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                @if ($sampah->isEmpty())
                    <div class="p-8 sm:p-12 text-center">
                        <div class="mx-auto w-16 h-16 bg-green-50 dark:bg-green-950/60 rounded-full flex items-center justify-center text-green-600 dark:text-green-400 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">
                            @if ($currentStatus !== '' || $currentJenis !== '')
                                Tidak ada data yang sesuai filter
                            @else
                                Belum ada data sampah
                            @endif
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
                            @if ($currentStatus !== '' || $currentJenis !== '')
                                Coba ubah kata kunci pencarian atau reset filter untuk melihat data lainnya.
                            @else
                                Belum ada data sampah. Klik Tambah Data untuk mulai mencatat.
                            @endif
                        </p>
                        @if ($currentStatus !== '' || $currentJenis !== '')
                            <a href="{{ route('sampah.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg">
                                Reset Filter
                            </a>
                        @else
                            <a href="{{ route('sampah.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Tambah Data Baru</span>
                            </a>
                        @endif
                    </div>
                @else
                    <div class="w-full overflow-x-auto">
                        <table class="w-full table-auto text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-5">Tanggal Masuk</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Jenis</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4 text-right">Berat (kg)</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Status</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Diinput Oleh</th>
                                    <th class="py-2.5 px-2 sm:py-3.5 sm:px-5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-xs sm:text-sm">
                                @foreach ($sampah as $item)
                                    <tr class="hover:bg-gray-50/75 dark:hover:bg-gray-700/40 transition align-middle">
                                        {{-- Tanggal Masuk --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-5 whitespace-nowrap font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item->tanggal_masuk->translatedFormat('d M Y') }}
                                        </td>

                                        {{-- Jenis --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-4 font-semibold text-gray-800 dark:text-gray-100">
                                            {{ $item->jenis }}
                                            @if ($item->catatan)
                                                <p class="text-[11px] sm:text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5 max-w-[110px] sm:max-w-xs truncate" title="{{ $item->catatan }}">
                                                    {{ $item->catatan }}
                                                </p>
                                            @endif
                                        </td>

                                        {{-- Berat (kg) --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-4 text-right whitespace-nowrap font-semibold text-gray-900 dark:text-gray-100">
                                            {{ number_format($item->berat_kg, 2, ',', '.') }} kg
                                        </td>

                                        {{-- Status --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-4 whitespace-nowrap">
                                            @if ($item->status === \App\Models\Sampah::STATUS_MASUK)
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                    Masuk
                                                </span>
                                            @elseif ($item->status === \App\Models\Sampah::STATUS_DIKELOLA)
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                                    Dikelola
                                                </span>
                                            @elseif ($item->status === \App\Models\Sampah::STATUS_SELESAI)
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $item->status_label }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Diinput Oleh --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-4 text-gray-600 dark:text-gray-300 max-w-[90px] sm:max-w-none truncate">
                                            {{ $item->user->name ?? '-' }}
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="py-2.5 px-2 sm:py-4 sm:px-5 whitespace-nowrap text-center">
                                            <div class="inline-flex flex-col sm:flex-row items-center gap-1 sm:gap-2">
                                                {{-- Tombol Ubah --}}
                                                <a href="{{ route('sampah.edit', $item) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-md text-[11px] sm:text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition" title="Ubah data">
                                                    Ubah
                                                </a>

                                                {{-- Tombol Hapus (Hanya Admin) --}}
                                                @can('delete', $item)
                                                    <form method="POST" action="{{ route('sampah.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sampah {{ $item->jenis }} ({{ $item->berat_kg }} kg) ini? Tindakan ini tidak dapat dibatalkan.');" class="w-full sm:w-auto inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-md text-[11px] sm:text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 transition" title="Hapus data">
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
                    @if ($sampah->hasPages())
                        <div class="p-3 sm:p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                            {{ $sampah->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
