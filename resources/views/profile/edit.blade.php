<x-app-layout>
    <x-slot name="title">Profil & Kata Sandi</x-slot>

    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                Profil & Kata Sandi
            </h2>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">
                Kelola informasi profil dan perbarui kata sandi akun Anda.
            </p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Peringatan Wajib Ganti Kata Sandi Sementara --}}
            @if (session('warning') || $user->must_change_password)
                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-300 dark:border-amber-800 flex items-start gap-3 text-amber-900 dark:text-amber-200 shadow-sm" role="alert">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div class="text-sm font-semibold">
                        {{ session('warning') ?? 'Demi keamanan, silakan ganti kata sandi sementara Anda terlebih dahulu.' }}
                    </div>
                </div>
            @endif

            {{-- Notifikasi Sukses --}}
            @if (session('success'))
                <div class="p-4 rounded-xl bg-green-50 dark:bg-green-950/60 border border-green-200 dark:border-green-800 flex items-start gap-3 text-green-800 dark:text-green-200 shadow-sm" role="alert">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <div class="text-sm font-medium">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- Form Ubah Kata Sandi --}}
            <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Form Informasi Profil --}}
            @if (! $user->must_change_password)
                <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            @endif

            {{-- Catatan: Fitur Hapus Akun sengaja disembunyikan/dinonaktifkan agar riwayat data sampah tetap utuh --}}
        </div>
    </div>
</x-app-layout>
