<header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 shadow-sm z-10">

    <div class="flex items-center gap-4">
        {{-- Toggle Desktop --}}
        <button @click="sidebarCollapsed = !sidebarCollapsed"
            class="hidden lg:flex w-9 h-9 rounded-xl hover:bg-slate-100 items-center justify-center text-slate-400 hover:text-slate-700 transition-all">
            <i class="fa-solid fa-bars-staggered text-base" :class="sidebarCollapsed ? 'rotate-180' : ''"></i>
        </button>
        {{-- Toggle Mobile --}}
        <button @click="sidebarOpen = true"
            class="lg:hidden w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-all">
            <i class="fa-solid fa-bars text-base"></i>
        </button>

        {{-- Fil d'Ariane --}}
        <div class="hidden sm:flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
            <span>Espace Personnel</span>
            <i class="fa-solid fa-angle-right text-[8px] opacity-50"></i>
            <span class="text-slate-800">@yield('page-title', 'Tableau de bord')</span>
        </div>
    </div>

    <div class="flex items-center gap-3">
        {{-- Profil --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2.5 pl-3 border-l border-slate-100 group">
                <div class="hidden sm:block text-right">
                    <p
                        class="text-[11px] font-black text-slate-900 leading-none mb-0.5 group-hover:text-[#1d3557] transition-colors">
                        {{ auth('user')->user()->name }}</p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Personnel Assurance</p>
                </div>
                <div
                    class="w-9 h-9 rounded-xl bg-[#1d3557] flex items-center justify-center shrink-0 overflow-hidden shadow border border-slate-100">
                    @if (auth('user')->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}"
                            class="w-full h-full object-cover">
                    @else
                        <span
                            class="text-white text-xs font-black">{{ strtoupper(substr(auth('user')->user()->name ?? 'P', 0, 1)) }}</span>
                    @endif
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-300 transition-transform duration-300"
                    :class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-95" @click.outside="open = false"
                class="absolute right-0 mt-3 w-52 bg-white border border-slate-200 rounded-2xl shadow-2xl shadow-slate-900/10 py-2 z-50 overflow-hidden"
                x-cloak>

                <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 mb-1">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Mon Compte</p>
                    <p class="text-xs font-black text-slate-800 truncate mb-0.5">{{ auth('user')->user()->name }}</p>
                    <p class="text-[10px] font-bold text-[#457b9d] truncate">{{ auth('user')->user()->code_user }}</p>
                </div>

                <a href="{{ route('personnel.profile') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-circle-user w-4 text-slate-400"></i>
                    Mon profil
                </a>
                <a href="{{ route('personnel.password.change') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-lock w-4 text-slate-400"></i>
                    Changer le mot de passe
                </a>

                <div class="border-t border-slate-100 mt-1 pt-1">
                    <form method="POST" action="{{ route('personnel.logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-xs font-semibold text-red-500 hover:bg-red-50 transition-colors">
                            <i class="fa-solid fa-right-from-bracket w-4"></i>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
