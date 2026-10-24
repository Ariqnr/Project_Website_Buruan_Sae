<x-app-layout>
    <x-slot name="title">Laporan Pengelolaan Sampah</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    Laporan Pengelolaan Sampah
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">
                    Periode: <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $periodeAwalLabel }}</span> s/d <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $periodeAkhirLabel }}</span>
                </p>
            </div>
            <div>
                @if ($isEmpty)
                    <button
                        type="button"
                        disabled
                        aria-disabled="true"
                        title="Tidak ada data untuk diunduh pada periode ini"
                        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-semibold text-sm rounded-lg cursor-not-allowed opacity-75"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Unduh PDF</span>
                    </button>
                @else
                    <a
                        href="{{ route('laporan.unduh', ['tanggal_awal' => $tanggalAwal, 'tanggal_akhir' => $tanggalAkhir]) }}"
                        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold text-sm rounded-lg shadow-sm transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Unduh PDF</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-5 sm:space-y-6">

            {{-- Filter Tanggal Awal & Tanggal Akhir --}}
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form method="GET" action="{{ route('laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
                    <div class="sm:col-span-4">
                        <label for="tanggal_awal" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                            Tanggal Awal
                        </label>
                        <input
                            type="date"
                            id="tanggal_awal"
                            name="tanggal_awal"
                            value="{{ old('tanggal_awal', $tanggalAwal) }}"
                            required
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-green-500 focus:ring-green-500 shadow-sm"
                        />
                        <x-input-error :messages="$errors->get('tanggal_awal')" class="mt-1" />
                    </div>

                    <div class="sm:col-span-4">
                        <label for="tanggal_akhir" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                            Tanggal Akhir
                        </label>
                        <input
                            type="date"
                            id="tanggal_akhir"
                            name="tanggal_akhir"
                            value="{{ old('tanggal_akhir', $tanggalAkhir) }}"
                            required
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-green-500 focus:ring-green-500 shadow-sm"
                        />
                        <x-input-error :messages="$errors->get('tanggal_akhir')" class="mt-1" />
                    </div>

                    <div class="sm:col-span-4 flex gap-2">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-5 py-2.5 bg-gray-800 dark:bg-green-700 hover:bg-gray-900 dark:hover:bg-green-800 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                            Tampilkan
                        </button>
                        <a href="{{ route('laporan.index') }}" class="inline-flex items-center px-3.5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition" title="Reset ke Bulan Ini">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            @if ($isEmpty)
                {{-- Empty State jika tidak ada data sama sekali pada periode ini --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 sm:p-12 text-center">
                    <div class="mx-auto w-16 h-16 bg-green-50 dark:bg-green-950/60 rounded-full flex items-center justify-center text-green-600 dark:text-green-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">
                        Tidak ada data laporan pada periode ini
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        Belum ada data sampah maupun jadwal penjemuran untuk rentang tanggal {{ $periodeAwalLabel }} sampai {{ $periodeAkhirLabel }}. Silakan pilih rentang tanggal lainnya.
                    </p>
                </div>
            @else
                {{-- Bagian 1: Tabel Hasil Data Sampah --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-base sm:text-lg text-gray-800 dark:text-gray-100">
                                Data Pencatatan Sampah Masuk
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Daftar sampah yang dicatat pada periode {{ $periodeAwalLabel }} s/d {{ $periodeAkhirLabel }} ({{ $daftarSampah->count() }} data).
                            </p>
                        </div>
                    </div>

                    @if ($daftarSampah->isEmpty())
                        <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data sampah yang masuk pada rentang tanggal ini.
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
                                        <th class="py-2.5 px-2 sm:py-3.5 sm:px-5">Diinput Oleh</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-xs sm:text-sm">
                                    @foreach ($daftarSampah as $item)
                                        <tr class="hover:bg-gray-50/75 dark:hover:bg-gray-700/40 transition align-middle">
                                            <td class="py-2.5 px-2 sm:py-3.5 sm:px-5 whitespace-nowrap font-medium text-gray-900 dark:text-gray-100">
                                                {{ $item->tanggal_masuk->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="py-2.5 px-2 sm:py-3.5 sm:px-4 font-semibold text-gray-800 dark:text-gray-100">
                                                {{ $item->jenis }}
                                            </td>
                                            <td class="py-2.5 px-2 sm:py-3.5 sm:px-4 text-right whitespace-nowrap font-semibold text-gray-900 dark:text-gray-100">
                                                {{ number_format($item->berat_kg, 2, ',', '.') }} kg
                                            </td>
                                            <td class="py-2.5 px-2 sm:py-3.5 sm:px-4 whitespace-nowrap">
                                                @if ($item->status === \App\Models\Sampah::STATUS_MASUK)
                                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                        Masuk
                                                    </span>
                                                @elseif ($item->status === \App\Models\Sampah::STATUS_DIKELOLA)
                                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                                        Dikelola
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                        Selesai
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-2.5 px-2 sm:py-3.5 sm:px-5 text-gray-600 dark:text-gray-300">
                                                {{ $item->user->name ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Ringkasan di Bawah Tabel: Total Berat Seluruhnya & Per Jenis Sampah --}}
                    <div class="p-4 sm:p-6 bg-gray-50/70 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Total Berat Seluruhnya
                            </span>
                            <span class="text-xl sm:text-2xl font-extrabold text-green-700 dark:text-green-400">
                                {{ number_format($totalBerat, 2, ',', '.') }} kg
                            </span>
                        </div>

                        <div>
                            <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-3">
                                Ringkasan Total Berat per Jenis Sampah
                            </h4>
                            @if ($rekapPerJenis->isEmpty())
                                <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada rincian berat per jenis sampah.</p>
                            @else
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @foreach ($rekapPerJenis as $jenis => $berat)
                                        <div class="p-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 truncate">
                                                {{ $jenis }}
                                            </p>
                                            <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mt-0.5">
                                                {{ number_format($berat, 2, ',', '.') }} <span class="text-xs font-normal text-gray-500">kg</span>
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Jadwal Penjemuran pada Periode Ini --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-base sm:text-lg text-gray-800 dark:text-gray-100">
                            Jadwal Penjemuran pada Periode Ini
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Jadwal penjemuran yang rentang tanggalnya beririsan dengan periode {{ $periodeAwalLabel }} s/d {{ $periodeAkhirLabel }}.
                        </p>
                    </div>

                    @if ($jadwalPeriode->isEmpty())
                        <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada jadwal penjemuran yang berlangsung pada periode ini.
                        </div>
                    @else
                        <div class="w-full overflow-x-auto">
                            <table class="w-full table-auto text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700 text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2.5 px-2 sm:py-3.5 sm:px-5">Jenis</th>
                                        <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Tanggal Mulai</th>
                                        <th class="py-2.5 px-2 sm:py-3.5 sm:px-4">Tanggal Selesai</th>
                                        <th class="py-2.5 px-2 sm:py-3.5 sm:px-5">Status Jadwal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-xs sm:text-sm">
                                    @foreach ($jadwalPeriode as $jadwal)
                                        <tr class="hover:bg-gray-50/75 dark:hover:bg-gray-700/40 transition align-middle">
                                            <td class="py-2.5 px-2 sm:py-3.5 sm:px-5 font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $jadwal->sampah->jenis ?? '-' }}
                                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400">
                                                    ({{ number_format($jadwal->sampah->berat_kg ?? 0, 2, ',', '.') }} kg)
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-2 sm:py-3.5 sm:px-4 whitespace-nowrap text-gray-800 dark:text-gray-200">
                                                {{ $jadwal->tanggal_mulai->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="py-2.5 px-2 sm:py-3.5 sm:px-4 whitespace-nowrap text-gray-800 dark:text-gray-200">
                                                {{ $jadwal->tanggal_selesai->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="py-2.5 px-2 sm:py-3.5 sm:px-5 whitespace-nowrap">
                                                @if ($jadwal->status_jadwal === \App\Models\JadwalPenjemuran::STATUS_AKAN_DATANG)
                                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                        Akan Datang
                                                    </span>
                                                @elseif ($jadwal->status_jadwal === \App\Models\JadwalPenjemuran::STATUS_SEDANG_BERJALAN)
                                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                                        Sedang Berjalan
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 rounded-full text-[11px] sm:text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                        Selesai
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
