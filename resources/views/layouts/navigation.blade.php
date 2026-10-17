@php
    $menu = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
        ['label' => 'Data Sampah', 'route' => 'sampah.index', 'active' => 'sampah.*'],
        ['label' => 'Jadwal Penjemuran', 'route' => 'jadwal.index', 'active' => 'jadwal.*'],
        ['label' => 'Laporan', 'route' => 'laporan.index', 'active' => 'laporan.*'],
    ];
    $user = Auth::user();
    $roleLabel = \App\Models\User::ROLES[$user->role] ?? ucfirst($user->role);
@endphp

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 transition-colors duration-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-8 w-8 text-green-700 dark:text-green-400" />
                        <span class="font-bold text-green-800 dark:text-green-400 text-lg">Buruan Sae</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 lg:-my-px lg:ms-10 lg:flex">
                    @foreach ($menu as $item)
                        <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['active'])">
                            {{ $item['label'] }}
                        </x-nav-link>
                    @endforeach
                </div>
            </div>

            <!-- Theme Toggle + User info + Keluar -->
            <div class="hidden lg:flex lg:items-center lg:gap-4 lg:ms-6">
                <x-theme-toggle />

                <a href="{{ route('profile.edit') }}" class="text-right leading-tight hover:opacity-80" title="Profil saya">
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $user->name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $roleLabel }}</div>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 rounded-md border border-red-200 dark:border-red-800 text-sm font-medium text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-950/50 hover:bg-red-100 dark:hover:bg-red-900/60 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Keluar
                    </button>
                </form>
            </div>

            <!-- Mobile Theme Toggle + Hamburger -->
            <div class="-me-2 flex items-center gap-2 lg:hidden">
                <x-theme-toggle />

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition duration-150 ease-in-out" aria-label="Buka menu">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @foreach ($menu as $item)
                <x-responsive-nav-link :href="route($item['route'])" :active="request()->routeIs($item['active'])">
                    {{ $item['label'] }}
                </x-responsive-nav-link>
            @endforeach
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-100">{{ $user->name }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ $roleLabel }} &middot; {{ $user->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                    Profil Saya
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}" class="px-4 pt-2">
                    @csrf
                    <button type="submit" class="w-full py-2 rounded-md border border-red-200 dark:border-red-800 text-base font-medium text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-950/50 hover:bg-red-100 dark:hover:bg-red-900/60">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
