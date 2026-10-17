<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Akses Ditolak - {{ config('app.name', 'Buruan Sae') }}</title>
        <x-theme-script />
        @vite(['resources/css/app.css'])
    </head>
    <body class="font-sans antialiased bg-green-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="min-h-screen flex items-center justify-center px-4">
            <div class="w-full max-w-md bg-white dark:bg-gray-800 border border-transparent dark:border-gray-700 shadow-md rounded-lg p-8 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
                    <svg class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>

                <p class="mt-4 text-sm font-semibold text-amber-600">Kode 403</p>
                <h1 class="mt-1 text-2xl font-bold">Akses Ditolak</h1>
                <p class="mt-3 text-gray-600">
                    {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki izin untuk membuka halaman ini.' }}
                </p>
                <p class="mt-1 text-sm text-gray-500">
                    Jika Anda merasa ini keliru, silakan hubungi admin Buruan Sae.
                </p>

                <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/dashboard') }}" class="inline-flex justify-center items-center px-4 py-2 rounded-md border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Kembali
                    </a>
                    <a href="{{ url('/dashboard') }}" class="inline-flex justify-center items-center px-4 py-2 rounded-md bg-green-700 text-sm font-semibold text-white hover:bg-green-800">
                        Ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
