<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
            Ubah Kata Sandi
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            Pastikan akun Anda menggunakan kata sandi minimal 8 karakter yang kuat dan berbeda dari kata sandi sebelumnya.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Kata Sandi Saat Ini" class="font-medium text-gray-800 dark:text-gray-200" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full text-base" autocomplete="current-password" required />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Kata Sandi Baru" class="font-medium text-gray-800 dark:text-gray-200" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full text-base" autocomplete="new-password" required />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Minimal 8 karakter dan tidak boleh sama dengan kata sandi saat ini.
            </p>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Konfirmasi Kata Sandi Baru" class="font-medium text-gray-800 dark:text-gray-200" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full text-base" autocomplete="new-password" required />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="px-5 py-2.5 text-sm">
                Simpan
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-medium text-green-600 dark:text-green-400"
                >Kata sandi berhasil disimpan.</p>
            @endif
        </div>
    </form>
</section>
