<x-app-layout>
    <x-slot name="title">{{ $judul }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $judul }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-10 text-center">
                <svg class="mx-auto h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-4 text-lg font-medium text-gray-900">Halaman ini sedang disiapkan</p>
                <p class="mt-1 text-gray-600">Fitur <strong>{{ $judul }}</strong> akan segera tersedia.</p>
                <a href="{{ route('dashboard') }}" class="mt-6 inline-flex items-center px-4 py-2 rounded-md bg-green-700 text-white text-sm font-semibold hover:bg-green-800">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
