<aside
    class="flex-shrink-0 bg-[#1a1a1a] border-r border-[#3a3a3a] flex flex-col h-[100dvh] fixed top-0 left-0 z-50 transition-all duration-500 ease-in-out w-64 transform -translate-x-full lg:translate-x-0 lg:w-auto"
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
            :class="expanded ? 'px-4' : 'px-3 justify-center'" x-tooltip.right="!expanded ? 'Dashboard' : ''">
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
            :class="expanded ? 'px-4' : 'px-3 justify-center'" x-tooltip.right="!expanded ? 'Lowongan Magang' : ''">
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
            :class="expanded ? 'px-4' : 'px-3 justify-center'" x-tooltip.right="!expanded ? 'Pendaftaran Magang' : ''">
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
            :class="expanded ? 'px-4' : 'px-3 justify-center'" x-tooltip.right="!expanded ? 'Data Magang' : ''">
            <i class="fas fa-th w-5 text-center flex-shrink-0"></i>
            <span class="whitespace-nowrap" x-show="expanded" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                Data Magang
            </span>
        </a>

        @if (auth()->user()->isAdmin())
            {{-- Link Profil Saya --}}
            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 py-2.5 mx-2 rounded-lg text-sm font-medium transition-all duration-200
                {{ request()->routeIs('profile.edit') ? 'bg-[#d97757]/20 text-[#e88968]' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}"
                :class="expanded ? 'px-4' : 'px-3 justify-center'" x-tooltip.right="!expanded ? 'Profil Saya' : ''">
                <i class="fas fa-user-circle w-5 text-center flex-shrink-0"></i>
                <span class="whitespace-nowrap" x-show="expanded" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">
                    Profil Saya
                </span>
            </a>

            {{-- Link Pengaturan Magang --}}
            <a href="{{ route('settings.index') }}"
                class="flex items-center gap-3 py-2.5 mx-2 rounded-lg text-sm font-medium transition-all duration-200
                {{ request()->routeIs('settings.*') ? 'bg-[#d97757]/20 text-[#e88968]' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}"
                :class="expanded ? 'px-4' : 'px-3 justify-center'"
                x-tooltip.right="!expanded ? 'Pengaturan Magang' : ''">
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
    <div class="p-4 border-t border-[#3a3a3a] flex-shrink-0">
        <div class="flex items-center" :class="expanded ? 'gap-3' : 'justify-center'">
            <div
                class="w-10 h-10 rounded-full bg-[#d97757]/20 text-[#e88968] flex items-center justify-center font-bold flex-shrink-0">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>

            <div class="overflow-hidden whitespace-nowrap" x-show="expanded"
                x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>

                @if (auth()->user()->isAdmin())
                    <span
                        style="background-color: rgba(220, 38, 38, 0.2); color: #f87171; padding: 0.125rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 600;">
                        <i class="fas fa-crown" style="font-size: 0.625rem; margin-right: 0.25rem;"></i>ADMIN
                    </span>
                @else
                    <span
                        style="background-color: rgba(59, 130, 246, 0.2); color: #60a5fa; padding: 0.125rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 600;">
                        <i class="fas fa-user" style="font-size: 0.625rem; margin-right: 0.25rem;"></i>USER
                    </span>
                @endif
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="mt-4">
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

{{-- Style untuk Tooltip (hanya relevan di desktop) --}}
<style>
    [x-tooltip] {
        position: relative;
    }

    @media (min-width: 1024px) {
        [x-tooltip]:hover::after {
            content: attr(x-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 12px;
            padding: 4px 8px;
            background-color: #1a1a1a;
            color: white;
            border: 1px solid #3a3a3a;
            border-radius: 4px;
            font-size: 0.875rem;
            white-space: nowrap;
            z-index: 100;
        }
    }
</style>

<script src="//unpkg.com/alpinejs" defer></script>
