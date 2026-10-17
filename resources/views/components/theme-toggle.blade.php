<button
    type="button"
    onclick="window.toggleBuruanSaeTheme()"
    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition"
    title="Ubah tampilan Mode Siang / Mode Malam"
    aria-label="Ubah tampilan Mode Siang atau Mode Malam"
>
    {{-- Ikon Bulan (tampil saat mode terang, klik untuk ke mode gelap) --}}
    <svg class="h-4 w-4 text-indigo-600 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
    </svg>
    <span class="dark:hidden">Mode Gelap</span>

    {{-- Ikon Matahari (tampil saat mode gelap, klik untuk ke mode terang) --}}
    <svg class="hidden h-4 w-4 text-amber-400 dark:inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>
    <span class="hidden dark:inline">Mode Terang</span>
</button>
