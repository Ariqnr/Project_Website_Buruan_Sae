<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Dashboard Operasional
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Selamat datang di Sistem Pengelolaan Sampah Buruan Sae.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Masuk sebagai:</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                    {{ \App\Models\User::ROLES[auth()->user()->role] ?? ucfirst(auth()->user()->role) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Kartu Sapaan --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">
                        Halo, {{ auth()->user()->name }}! 👋
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Berikut ringkasan aktivitas dan data penanganan sampah Buruan Sae hari ini.
                    </p>
                </div>
                <div>
                    <a href="{{ route('sampah.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Catat Sampah Baru</span>
                    </a>
                </div>
            </div>

            {{-- Kartu Ringkasan (FR-02) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Kartu 1: Total Berat Bulan Ini --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-green-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Total Berat Masuk ({{ now()->translatedFormat('F Y') }})
                        </p>
                        <p class="text-3xl font-extrabold text-green-700 mt-2">
                            {{ number_format($totalBeratBulanIni, 2, ',', '.') }} <span class="text-base font-medium text-gray-600">kg</span>
                        </p>
                        <div class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                            <span>Total Seluruh Waktu:</span>
                            <span class="font-bold text-gray-800">{{ number_format($totalBeratSemua, 2, ',', '.') }} kg</span>
                        </div>
                    </div>
                    <div class="w-14 h-14 bg-green-50 rounded-xl flex items-center justify-center text-green-600 shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                    </div>
                </div>

                {{-- Kartu 2: Sampah Berstatus Dikelola --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-amber-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Sampah Sedang Dikelola
                        </p>
                        <p class="text-3xl font-extrabold text-amber-600 mt-2">
                            {{ $jumlahDikelola }} <span class="text-base font-medium text-gray-600">data</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Sedang dalam proses pengolahan
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                </div>

                {{-- Kartu 3: Jadwal Penjemuran Aktif --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-blue-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Jadwal Penjemuran Aktif
                        </p>
                        <p class="text-3xl font-extrabold text-blue-600 mt-2">
                            {{ $jumlahJadwalAktif }} <span class="text-base font-medium text-gray-600">jadwal</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Sedang berjalan hari ini
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Grafik Batang Berat Sampah per Bulan (Larapex Charts / ApexCharts) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">
                            Grafik Sampah Masuk Bulanan
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Visualisasi akumulasi berat sampah (kg) per bulan menggunakan Larapex Charts.
                        </p>
                    </div>
                </div>
                <div class="w-full">
                    {!! $chart->container() !!}
                </div>
            </div>

            {{-- Dua Kolom Informasi: Catatan Terbaru & Jadwal Berjalan --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- Kolom 1: 5 Catatan Sampah Terbaru --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-gray-800">
                                    5 Catatan Sampah Terbaru
                                </h3>
                                <p class="text-xs text-gray-500">Data sampah terakhir yang masuk ke sistem.</p>
                            </div>
                            <a href="{{ route('sampah.index') }}" class="text-xs font-semibold text-green-700 hover:text-green-800">
                                Lihat Semua &rarr;
                            </a>
                        </div>

                        @if ($catatanTerbaru->isEmpty())
                            <div class="py-8 text-center text-gray-400 text-sm">
                                Belum ada catatan data sampah.
                            </div>
                        @else
                            <div class="divide-y divide-gray-100">
                                @foreach ($catatanTerbaru as $item)
                                    <div class="py-3 flex items-center justify-between gap-3 text-sm">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-gray-800 truncate">
                                                {{ $item->jenis }}
                                                <span class="font-normal text-gray-500 text-xs">
                                                    ({{ number_format($item->berat_kg, 2, ',', '.') }} kg)
                                                </span>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5 truncate">
                                                {{ $item->tanggal_masuk->translatedFormat('d M Y') }} &middot; Oleh: {{ $item->user->name ?? '-' }}
                                            </p>
                                        </div>
                                        <div class="shrink-0">
                                            @if ($item->status === \App\Models\Sampah::STATUS_MASUK)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                    Masuk
                                                </span>
                                            @elseif ($item->status === \App\Models\Sampah::STATUS_DIKELOLA)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                    Dikelola
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                    Selesai
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Kolom 2: Jadwal Penjemuran yang Sedang Berjalan --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                            <div>
                                <h3 class="font-bold text-base text-gray-800">
                                    Jadwal Penjemuran Aktif
                                </h3>
                                <p class="text-xs text-gray-500">Penjemuran sampah yang sedang berlangsung hari ini.</p>
                            </div>
                            <a href="{{ route('jadwal.index') }}" class="text-xs font-semibold text-green-700 hover:text-green-800">
                                Kelola Jadwal &rarr;
                            </a>
                        </div>

                        @if ($jadwalSedangBerjalan->isEmpty())
                            <div class="py-8 text-center text-gray-400 text-sm">
                                Tidak ada jadwal penjemuran yang sedang aktif hari ini.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($jadwalSedangBerjalan as $jadwal)
                                    <div class="p-3.5 rounded-lg border border-gray-100 bg-gray-50/70 hover:bg-gray-50 transition">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <h4 class="font-semibold text-sm text-gray-800">
                                                    {{ $jadwal->sampah->jenis ?? 'Sampah' }}
                                                    <span class="text-xs font-normal text-gray-500">
                                                        ({{ number_format($jadwal->sampah->berat_kg ?? 0, 2, ',', '.') }} kg)
                                                    </span>
                                                </h4>
                                                <p class="text-xs text-green-700 font-medium mt-1">
                                                    📅 {{ $jadwal->tanggal_mulai->translatedFormat('d M') }} s/d {{ $jadwal->tanggal_selesai->translatedFormat('d M Y') }}
                                                </p>
                                                @if ($jadwal->catatan)
                                                    <p class="text-xs text-gray-500 mt-1 italic">
                                                        "{{ $jadwal->catatan }}"
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Aktif
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

    @push('scripts')
        <script src="{{ $chart->cdn() }}"></script>
        {{ $chart->script() }}
    @endpush
</x-app-layout>
