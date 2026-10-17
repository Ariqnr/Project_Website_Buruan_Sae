<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Masuk - {{ config('app.name', 'Buruan Sae') }}</title>

        <!-- Theme Script (No-FOUC) -->
        <x-theme-script />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased transition-colors duration-200">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-green-50 dark:bg-gray-900 relative">
            <div class="absolute top-4 right-4">
                <x-theme-toggle />
            </div>

            <div class="flex flex-col items-center gap-2">
                <x-application-logo class="w-16 h-16 text-green-700 dark:text-green-400" />
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-800 dark:text-green-400">Buruan Sae</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Sistem Pengelolaan Sampah</div>
                </div>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white dark:bg-gray-800 border border-transparent dark:border-gray-700 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
