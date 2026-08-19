<aside
    class="flex-shrink-0 bg-[#1a1a1a] border-r border-[#3a3a3a] flex flex-col h-[100dvh] fixed top-0 left-0 z-50 transition-[width,transform] duration-300 ease-in-out w-64 transform -translate-x-full lg:translate-x-0 lg:w-auto"
    :class="{
        'translate-x-0': mobileOpen,
        'lg:w-64': expanded,
        'lg:w-20': !expanded
    }">

    {{-- HEADER SIDEBAR --}}
    <div class="h-16 flex items-center border-b border-[#3a3a3a] flex-shrink-0 px-4">
        <div class="flex items-center w-full relative">
            {{-- Logo dan Nama --}}
            <div class="flex items-center gap-3 overflow-hidden transition-all duration-300"
                :class="expanded ? 'flex-shrink-0' : 'lg:hidden'">
                <img src="{{ asset('images/Magnet.png') }}" alt="Logo MagNet"
                    class="h-8 w-auto flex-shrink-0 transition-opacity duration-500">
                <h1 class="claude-title text-xl text-white font-semibold whitespace-nowrap overflow-hidden transition-all duration-200"
                    :class="expanded ? 'opacity-100 max-w-xs' : 'lg:opacity-0 lg:max-w-0'">
                    MagNet
                </h1>
            </div>

            {{-- Tombol Close (Mobile) --}}
            <button @click="mobileOpen = false"
                class="text-gray-300 p-2 rounded-lg hover:bg-white/5 hover:text-white transition-colors lg:hidden ml-auto">
                <i class="fas fa-times w-5 text-center"></i>
            </button>

            {{-- Tombol Burger (Desktop) --}}
            <button @click="expanded = !expanded; localStorage.setItem('_x_expanded', expanded)"
                class="text-gray-300 p-2 rounded-lg hover:bg-white/5 hover:text-white transition-colors hidden lg:block"
                :class="expanded ? 'absolute right-0 top-1/2 transform -translate-y-1/2' : 'mx-auto w-full flex justify-center'">
                <i class="fas fa-bars w-5 text-center"></i>
            </button>
        </div>
    </div>

    {{-- NAVIGASI --}}
    <nav class="flex-1 overflow-y-auto py-4 space-y-2">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 py-2.5 mx-2 rounded-lg text-sm font-medium transition-all duration-200
            {{ request()->routeIs('dashboard') ? 'bg-[#d97757]/20 text-[#e88968]' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}"
            :class="expanded ? 'px-4' : 'px-3 justify-center'" :x-tooltip="!expanded ? 'Dashboard' : null">
            <i class="fas fa-home w-5 text-center flex-shrink-0"></i>
            <span class="whitespace-nowrap" x-show="expanded" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                Dashboard
            </span>
        </a>

        {{-- Lowongan Magang (MENU BARU) --}}
        <a href="{{ route('lowongan.index') }}"
            class="flex items-center gap-3 py-2.5 mx-2 rounded-lg text-sm font-medium transition-all duration-200
            {{ request()->routeIs('lowongan.*') ? 'bg-[#d97757]/20 text-[#e88968]' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}"
            :class="expanded ? 'px-4' : 'px-3 justify-center'" :x-tooltip="!expanded ? 'Lowongan Magang' : null">
            <i class="fas fa-briefcase w-5 text-center flex-shrink-0"></i>
            <span class="whitespace-nowrap" x-show="expanded" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                Lowongan Magang
            </span>
        </a>

        {{-- Pendaftaran Magang --}}
        <a href="{{ route('daftar.index') }}"
            class="flex items-center gap-3 py-2.5 mx-2 rounded-lg text-sm font-medium transition-all duration-200
            {{ request()->routeIs('daftar.*') ? 'bg-[#d97757]/20 text-[#e88968]' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}"
            :class="expanded ? 'px-4' : 'px-3 justify-center'" :x-tooltip="!expanded ? 'Pendaftaran Magang' : null">
            <i class="fas fa-user-plus w-5 text-center flex-shrink-0"></i>
            <span class="whitespace-nowrap" x-show="expanded" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                Pendaftaran Magang
            </span>
        </a>

        {{-- Data Magang --}}
        <a href="{{ route('magang.index') }}"
            class="flex items-center gap-3 py-2.5 mx-2 rounded-lg text-sm font-medium transition-all duration-200
            {{ request()->routeIs('magang.index') || request()->routeIs('magang.show') || request()->routeIs('magang.edit') ? 'bg-[#d97757]/20 text-[#e88968]' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}"
            :class="expanded ? 'px-4' : 'px-3 justify-center'" :x-tooltip="!expanded ? 'Data Magang' : null">
            <i class="fas fa-th w-5 text-center flex-shrink-0"></i>
            <span class="whitespace-nowrap" x-show="expanded" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                Data Magang
            </span>
        </a>

        @if (auth()->user()->isAdmin())
            {{-- Link Pengaturan Magang --}}
            <a href="{{ route('settings.index') }}"
                class="flex items-center gap-3 py-2.5 mx-2 rounded-lg text-sm font-medium transition-all duration-200
                {{ request()->routeIs('settings.*') ? 'bg-[#d97757]/20 text-[#e88968]' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}"
                :class="expanded ? 'px-4' : 'px-3 justify-center'"
                :x-tooltip="!expanded ? 'Pengaturan Magang' : null">
                <i class="fas fa-sliders-h w-5 text-center flex-shrink-0"></i>
                <span class="whitespace-nowrap" x-show="expanded" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">
                    Pengaturan Magang
                </span>
            </a>
        @endif
    </nav>

    {{-- AREA USER & LOGOUT --}}
    <div class="p-3.5 border-t border-[#3a3a3a] flex-shrink-0">
        
        {{-- Kartu Akun / Profil Interaktif --}}
        @if(auth()->user()->isAdmin())
            <a href="{{ route('profile.edit') }}" 
               class="group flex items-center p-2 rounded-xl border transition-all duration-200 
                      {{ request()->routeIs('profile.*') 
                         ? 'bg-[#d97757]/15 border-[#d97757]/60 text-white shadow-sm' 
                         : 'border-transparent hover:border-[#3a3a3a] hover:bg-white/5 text-gray-300 hover:text-white' }}"
               :class="expanded ? 'gap-3 justify-between' : 'justify-center'"
               :x-tooltip="!expanded ? 'Edit Profil Admin' : null">
                
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#d97757] to-[#c4623e] text-white flex items-center justify-center font-bold flex-shrink-0 shadow-md shadow-[#d97757]/20 group-hover:scale-105 transition-transform">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>

                    <div class="overflow-hidden whitespace-nowrap text-left" x-show="expanded"
                        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                        <p class="text-sm font-semibold truncate text-white group-hover:text-[#e88968] transition-colors">
                            {{ auth()->user()->name }}
                        </p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="bg-red-500/20 text-red-400 px-1.5 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase border border-red-500/30">
                                <i class="fas fa-crown text-[9px] mr-0.5"></i>Admin
                            </span>
                        </div>
                    </div>
                </div>

                <i x-show="expanded" 
                   class="fas fa-gear text-gray-400 group-hover:text-[#e88968] group-hover:rotate-45 transition-all text-xs flex-shrink-0 ml-1"></i>
            </a>
        @else
            {{-- User Biasa (Non-Admin) --}}
            <div class="flex items-center p-2 rounded-xl" :class="expanded ? 'gap-3' : 'justify-center'">
                <div class="w-10 h-10 rounded-xl bg-[#d97757]/20 text-[#e88968] flex items-center justify-center font-bold flex-shrink-0">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>

                <div class="overflow-hidden whitespace-nowrap" x-show="expanded"
                    x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <span class="bg-blue-500/20 text-blue-400 px-1.5 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase border border-blue-500/30 mt-0.5 inline-block">
                        <i class="fas fa-user text-[9px] mr-0.5"></i>User
                    </span>
                </div>
            </div>
        @endif

        {{-- TOMBOL TOGGLE TEMA (DARK / LIGHT MODE) --}}
        <div class="mt-4" x-data="{ currentTheme: localStorage.getItem('magnet_theme') || 'dark' }"
             @magnet-theme-changed.window="currentTheme = $event.detail.theme">
            <button type="button"
                onclick="window.toggleMagnetTheme()"
                class="w-full flex items-center py-2.5 rounded-xl text-sm font-medium transition-all duration-200 text-gray-300 hover:text-[#d97757] border border-[#3a3a3a] hover:border-[#d97757]/40 bg-[#1a1a1a]/50"
                :class="expanded ? 'px-3 justify-between' : 'px-2 justify-center'"
                :x-tooltip="!expanded ? (currentTheme === 'light' ? 'Mode Gelap' : 'Mode Terang') : null">
                
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <i class="fas fa-fw flex-shrink-0"
                       :class="currentTheme === 'light' ? 'fa-sun text-yellow-500' : 'fa-moon text-blue-400'"></i>
                    <span class="whitespace-nowrap text-xs font-semibold" x-show="expanded"
                          x-text="currentTheme === 'light' ? 'Mode Terang' : 'Mode Gelap'">
                    </span>
                </div>

                {{-- Pill Toggle Switch --}}
                <div x-show="expanded" 
                     class="w-9 h-5 rounded-full relative p-0.5 border transition-colors flex items-center"
                     :class="currentTheme === 'light' ? 'bg-[#d97757] border-[#d97757]' : 'bg-[#2a2a2a] border-[#4a4a4a]'">
                    <div class="w-4 h-4 rounded-full bg-white transition-transform shadow-sm"
                         :class="currentTheme === 'light' ? 'translate-x-4' : 'translate-x-0'"></div>
                </div>
            </button>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit"
                class="w-full bg-gradient-to-r from-red-600/80 to-red-700/80 hover:from-red-600 hover:to-red-700 text-white py-2.5 rounded-lg font-medium transition-all duration-300 inline-flex items-center justify-center gap-2 text-sm shadow-lg shadow-red-600/20 hover:shadow-red-600/30"
                :class="expanded ? 'px-4' : 'px-2.5'">
                <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0"></i>
                <span class="whitespace-nowrap" x-show="expanded"
                    x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    Logout
                </span>
            </button>
        </form>
    </div>
</aside>

{{-- Style untuk Tooltip (hanya relevan di desktop jika tooltip tidak kosong) --}}
<style>
    [x-tooltip] {
        position: relative;
    }

    @media (min-width: 1024px) {
        [x-tooltip]:not([x-tooltip=""]):not([x-tooltip="null"]):hover::after {
            content: attr(x-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 12px;
            padding: 5px 10px;
            background-color: #1a1a1a;
            color: white;
            border: 1px solid #3a3a3a;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
            z-index: 100;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            pointer-events: none;
        }

        html.light-theme [x-tooltip]:not([x-tooltip=""]):not([x-tooltip="null"]):hover::after {
            background-color: #ffffff;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    }
</style>

<script src="//unpkg.com/alpinejs" defer></script>
