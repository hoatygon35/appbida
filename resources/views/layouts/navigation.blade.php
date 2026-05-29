<nav class="main-navbar">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo & App Name -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-gradient-to-tr theme-primary-bg to-purple-600 rounded-lg flex items-center justify-center shadow-md theme-primary-glow-shadow">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <span class="font-extrabold text-lg text-slate-800 tracking-wide">{{ Auth::user()->role === 'manager' ? 'Hệ thống quản lý Cafe/Bida' : (Auth::user()->club->name ?? 'Quản lý Cafe-Billards') }}</span>
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown / Theme selectors (Desktop only) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                
                <!-- Desktop Theme Dot Selectors -->
                <div class="flex items-center gap-2.5 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                    <span class="text-xs text-slate-400 font-bold uppercase mr-1">Màu:</span>
                    <!-- Indigo -->
                    <button @click="setTheme('indigo')" class="w-4 h-4 rounded-full bg-indigo-600 border border-white hover:scale-125 transition" title="Indigo Theme"></button>
                    <!-- Emerald -->
                    <button @click="setTheme('emerald')" class="w-4 h-4 rounded-full bg-emerald-600 border border-white hover:scale-125 transition" title="Emerald Theme"></button>
                    <!-- Rose -->
                    <button @click="setTheme('rose')" class="w-4 h-4 rounded-full bg-rose-600 border border-white hover:scale-125 transition" title="Rose Theme"></button>
                    <!-- Amber -->
                    <button @click="setTheme('amber')" class="w-4 h-4 rounded-full bg-amber-500 border border-white hover:scale-125 transition" title="Amber Theme"></button>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-xl text-slate-650 bg-slate-50 hover:text-slate-800 transition duration-150">
                            <div>{{ Auth::user()->role === 'manager' ? 'Hệ thống quản lý Cafe/Bida' : Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link href="#" @click.prevent="showProfileModal = true">
                            {{ __('Hồ sơ cá nhân') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Đăng xuất') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Quick Header Profile/Logout (Mobile only) -->
            <div class="flex items-center gap-3 sm:hidden">
                <button @click.prevent="showProfileModal = true" class="text-xs font-extrabold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 px-3 py-1.5 rounded-xl active:scale-95 transition">
                    {{ Auth::user()->role === 'manager' ? 'Hệ thống quản lý Cafe/Bida' : Auth::user()->name }}
                </button>
                
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-500 p-1 rounded-lg active:scale-90 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
