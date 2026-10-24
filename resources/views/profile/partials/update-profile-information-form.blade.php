<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
            Informasi Profil
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            Perbarui nama lengkap dan alamat email akun Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nama Lengkap" class="font-medium text-gray-800 dark:text-gray-200" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full text-base" :value="old('name', $user->name)" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" class="font-medium text-gray-800 dark:text-gray-200" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-base" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="px-5 py-2.5 text-sm">
                Simpan
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-medium text-green-600 dark:text-green-400"
                >Profil berhasil disimpan.</p>
            @endif
        </div>
    </form>
</section>
