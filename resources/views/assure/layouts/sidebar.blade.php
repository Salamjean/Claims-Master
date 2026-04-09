<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 text-slate-300 transition-all duration-300 cubic-bezier(0.4, 0, 0.2, 1) transform flex flex-col border-r border-white/5"
    style="background: linear-gradient(180deg, #263b70 0%, #1a2a54 100%);" :class="[
        sidebarOpen ? 'w-72 translate-x-0 shadow-2xl' : 'lg:w-20 -translate-x-full lg:translate-x-0 shadow-none',
    ]">

    {{-- Decorative element --}}
    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 blur-[80px] -z-10"></div>

    {{-- Logo & Brand --}}
    <div class="h-20 flex items-center border-b border-white/5 shrink-0 px-4"
        :class="sidebarOpen ? 'px-8' : 'justify-center px-0'">
        <a href="{{ route('assure.dashboard') }}" class="flex items-center gap-3.5 group decoration-none">
            <div
                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-all duration-300 shrink-0">
                <i class="fa-solid fa-shield-halved text-white text-xl"></i>
            </div>
            <div class="flex flex-col overflow-hidden transition-all duration-300" x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <span
                    class="text-white font-black text-base tracking-tight leading-none uppercase whitespace-nowrap">Claims
                    Master</span>
                <span
                    class="text-[9px] text-blue-300/60 font-black uppercase tracking-[0.2em] mt-1.5 ml-0.5 whitespace-nowrap">Assurance
                    Digitale</span>
            </div>
        </a>
    </div>

    {{-- Navigation Links --}}
    <div class="flex-1 overflow-y-auto pt-8 pb-4 px-3 space-y-10 custom-scrollbar overflow-x-hidden">

        {{-- Main Section --}}
        <div class="space-y-4">
            <p class="px-4 text-[10px] font-black uppercase tracking-[0.25em] text-white/20 whitespace-nowrap transition-opacity duration-300"
                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 h-0 overflow-hidden mb-0'">Menu Principal</p>
            <nav class="space-y-1.5">
                <a href="{{ route('assure.dashboard') }}"
                    class="flex items-center rounded-2xl transition-all duration-300 group {{ request()->routeIs('assure.dashboard') ? 'bg-white/10 text-white border border-white/10 shadow-lg shadow-black/5' : 'hover:bg-white/5 hover:text-white' }}"
                    :class="sidebarOpen ? 'px-4 py-3.5 gap-3.5' : 'px-0 py-3 justify-center border-transparent'"
                    title="Tableau de bord">
                    <div
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors shrink-0 {{ request()->routeIs('assure.dashboard') ? 'bg-blue-500 text-white' : 'bg-white/5 text-white/40 group-hover:bg-blue-500/20 group-hover:text-blue-400' }}">
                        <i class="fa-solid fa-dashboard text-sm"></i>
                    </div>
                    <span class="text-sm font-bold tracking-tight whitespace-nowrap" x-show="sidebarOpen"
                        x-transition:enter="delay-100 transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">Tableau de bord</span>
                </a>

                {{-- Sinistres --}}
                @php
                    $isActiveSinistres = request()->routeIs('assure.sinistres.*') && !request()->routeIs('assure.sinistres.historique');
                @endphp
                <div x-data="{ open: {{ $isActiveSinistres ? 'true' : 'false' }} }" class="space-y-1.5">
                    <button @click="sidebarOpen ? (open = !open) : (sidebarOpen = true, open = true)"
                        class="w-full flex items-center transition-all duration-300 group {{ $isActiveSinistres ? 'text-white' : 'hover:bg-white/5 hover:text-white' }}"
                        :class="sidebarOpen ? 'px-4 py-3.5 rounded-2xl justify-between' : 'px-0 py-3 justify-center rounded-2xl'"
                        title="Mes Sinistres">
                        <div class="flex items-center" :class="sidebarOpen ? 'gap-3.5' : 'gap-0'">
                            <div
                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors shrink-0 {{ $isActiveSinistres ? 'bg-orange-500 text-white' : 'bg-white/5 text-white/40 group-hover:bg-orange-500/20 group-hover:text-orange-400' }}">
                                <i class="fa-solid fa-bolt text-sm"></i>
                            </div>
                            <span class="text-sm font-bold tracking-tight whitespace-nowrap" x-show="sidebarOpen"
                                x-transition:enter="delay-100 transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-x-2"
                                x-transition:enter-end="opacity-100 translate-x-0">Mes Sinistres</span>
                            @if(isset($countMesSinistresTotal) && $countMesSinistresTotal > 0)
                                <span x-show="sidebarOpen" class="ml-2 px-2 py-0.5 text-[10px] font-black bg-orange-500/20 text-orange-400 rounded-full border border-orange-500/20">
                                    {{ $countMesSinistresTotal }}
                                </span>
                            @endif
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-white/20 transition-transform duration-300"
                            x-show="sidebarOpen" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open && sidebarOpen" x-collapse x-cloak class="mt-1 space-y-1 pl-12 pr-2">
                        <a href="{{ route('assure.sinistres.create') }}"
                            class="flex items-center gap-2 py-2.5 text-[13px] font-bold transition-colors {{ request()->routeIs('assure.sinistres.create') ? 'text-blue-400' : 'text-white/40 hover:text-white' }}">
                            <div
                                class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('assure.sinistres.create') ? 'bg-blue-400' : 'bg-white/10' }}">
                            </div>
                            Déclarer
                        </a>
                        <a href="{{ route('assure.sinistres.en_attente') }}"
                            class="flex items-center gap-2 py-2.5 text-[13px] font-bold transition-colors {{ request()->routeIs('assure.sinistres.en_attente') ? 'text-blue-400' : 'text-white/40 hover:text-white' }}">
                            <div
                                class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('assure.sinistres.en_attente') ? 'bg-blue-400' : 'bg-white/10' }}">
                            </div>
                            Suivi
                            @if(isset($countSuivi) && $countSuivi > 0)
                                <span class="ml-auto px-2 py-0.5 text-[10px] font-black bg-white/10 text-white/40 rounded-full border border-white/5">
                                    {{ $countSuivi }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('assure.sinistres.en_cours') }}"
                            class="flex items-center gap-2 py-2.5 text-[13px] font-bold transition-colors {{ request()->routeIs('assure.sinistres.en_cours') ? 'text-blue-400' : 'text-white/40 hover:text-white' }}">
                            <div
                                class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('assure.sinistres.en_cours') ? 'bg-blue-400' : 'bg-white/10' }}">
                            </div>
                            En cours
                            @if(isset($countEnCours) && $countEnCours > 0)
                                <span class="ml-auto px-2 py-0.5 text-[10px] font-black bg-white/10 text-white/40 rounded-full border border-white/5">
                                    {{ $countEnCours }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('assure.sinistres.documents') }}"
                            class="flex items-center gap-2 py-2.5 text-[13px] font-bold transition-colors {{ request()->routeIs('assure.sinistres.documents') ? 'text-blue-400' : 'text-white/40 hover:text-white' }}">
                            <div
                                class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('assure.sinistres.documents') ? 'bg-blue-400' : 'bg-white/10' }}">
                            </div>
                            Pièces
                        </a>
                    </div>
                </div>

                <a href="{{ route('assure.contrats.index') }}"
                    class="flex items-center rounded-2xl transition-all duration-300 group {{ request()->routeIs('assure.contrats.*') ? 'bg-white/10 text-white border border-white/10 shadow-lg shadow-black/5' : 'hover:bg-white/5 hover:text-white' }}"
                    :class="sidebarOpen ? 'px-4 py-3.5 gap-3.5' : 'px-0 py-3 justify-center border-transparent'"
                    title="Mes Assurances">
                    <div
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors shrink-0 {{ request()->routeIs('assure.contrats.*') ? 'bg-indigo-500 text-white' : 'bg-white/5 text-white/40 group-hover:bg-indigo-500/20 group-hover:text-indigo-400' }}">
                        <i class="fa-solid fa-file-shield text-sm"></i>
                    </div>
                    <span class="text-sm font-bold tracking-tight whitespace-nowrap" x-show="sidebarOpen"
                        x-transition:enter="delay-100 transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">Mes Assurances</span>
                </a>

                {{-- Constats Prêts (Top Level) --}}
                <a href="{{ route('assure.constats.prets') }}"
                    class="flex items-center rounded-2xl transition-all duration-300 group {{ request()->routeIs('assure.constats.prets') || request()->routeIs('assure.constats.paiement') ? 'bg-white/10 text-white border border-white/10 shadow-lg shadow-black/5' : 'hover:bg-white/5 hover:text-white' }}"
                    :class="sidebarOpen ? 'px-4 py-3.5 gap-3.5' : 'px-0 py-3 justify-center border-transparent'"
                    title="Constats Prêts">
                    <div
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors shrink-0 {{ request()->routeIs('assure.constats.prets') || request()->routeIs('assure.constats.paiement') ? 'bg-violet-600 text-white' : 'bg-white/5 text-white/40 group-hover:bg-violet-600/20 group-hover:text-violet-400' }}">
                        <i class="fa-solid fa-file-invoice text-sm"></i>
                    </div>
                    <span class="text-sm font-bold tracking-tight whitespace-nowrap" x-show="sidebarOpen"
                        x-transition:enter="delay-100 transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">Constats Prêts</span>
                    <span x-show="sidebarOpen" class="ml-auto px-1.5 py-0.5 text-[9px] font-black bg-blue-500/20 text-blue-400 rounded-md border border-blue-500/10">NOUVEAU</span>
                </a>

                {{-- Historique (Top Level) --}}
                <a href="{{ route('assure.sinistres.historique') }}"
                    class="flex items-center rounded-2xl transition-all duration-300 group {{ request()->routeIs('assure.sinistres.historique') ? 'bg-white/10 text-white border border-white/10 shadow-lg shadow-black/5' : 'hover:bg-white/5 hover:text-white' }}"
                    :class="sidebarOpen ? 'px-4 py-3.5 gap-3.5' : 'px-0 py-3 justify-center border-transparent'"
                    title="Historique">
                    <div
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors shrink-0 {{ request()->routeIs('assure.sinistres.historique') ? 'bg-teal-500 text-white' : 'bg-white/5 text-white/40 group-hover:bg-teal-500/20 group-hover:text-teal-400' }}">
                        <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                    </div>
                    <span class="text-sm font-bold tracking-tight whitespace-nowrap" x-show="sidebarOpen"
                        x-transition:enter="delay-100 transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">Historique</span>
                    @if(isset($countHistoriquePending) && $countHistoriquePending > 0)
                        <span x-show="sidebarOpen" class="ml-auto px-2 py-0.5 text-[10px] font-black bg-teal-500/20 text-teal-400 rounded-full border border-teal-500/20">
                            {{ $countHistoriquePending }}
                        </span>
                    @endif
                </a>
            </nav>
        </div>

        {{-- Support --}}
        <div class="space-y-4">
            <p class="px-4 text-[10px] font-black uppercase tracking-[0.25em] text-white/20 whitespace-nowrap transition-opacity duration-300"
                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 h-0 overflow-hidden mb-0'">Assistance</p>
            <nav class="space-y-1.5">
                <a href="{{ route('assure.support') }}"
                    class="flex items-center rounded-2xl transition-all duration-300 group {{ request()->routeIs('assure.support') ? 'bg-white/10 text-white border border-white/10 shadow-lg shadow-black/5' : 'hover:bg-white/5 hover:text-white' }}"
                    :class="sidebarOpen ? 'px-4 py-3.5 gap-3.5' : 'px-0 py-3 justify-center'"
                    title="Service Client 24/7">
                    <div
                        class="w-8 h-8 rounded-lg bg-white/5 text-white/40 flex items-center justify-center group-hover:bg-emerald-500/20 group-hover:text-emerald-400 transition-colors shrink-0">
                        <i class="fa-solid fa-headset text-sm"></i>
                    </div>
                    <span class="text-sm font-bold tracking-tight whitespace-nowrap" x-show="sidebarOpen"
                        x-transition:enter="delay-100 transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">Support 24/7</span>
                </a>
            </nav>
        </div>
    </div>

    {{-- Footer: User Profile Card --}}
    <div class="p-4 mt-auto border-t border-white/5 bg-white/5 backdrop-blur-md transition-all duration-300"
        :class="sidebarOpen ? 'p-6' : 'p-3'">
        <div class="flex items-center mb-6" :class="sidebarOpen ? 'gap-4' : 'justify-center gap-0'">
            <div class="relative group shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center font-black text-white text-base shadow-xl shadow-blue-500/20 border-2 border-white/20 group-hover:scale-105 transition-transform duration-300 overflow-hidden"
                    :class="sidebarOpen ? 'w-12 h-12' : 'w-10 h-10'">
                    @if(auth('user')->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}"
                            class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth('user')->user()->name ?? 'A', 0, 1)) }}
                    @endif
                </div>
                <div
                    class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-500 border-2 border-[#1a2a54] rounded-full shadow-lg shadow-emerald-500/40">
                </div>
            </div>
            <div class="min-w-0 transition-all duration-300" x-show="sidebarOpen"
                x-transition:enter="delay-100 transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
                <p class="text-sm font-black text-white truncate leading-none mb-1.5">
                    {{ auth('user')->user()->name . ' ' . auth('user')->user()->prenom }}</p>
                <div class="flex items-center gap-1.5">
                    <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                    <p class="text-[10px] text-blue-300 font-black uppercase tracking-widest">
                        {{ auth('user')->user()->code_user }}</p>
                </div>
            </div>
        </div>

        <nav class="flex transition-all duration-300"
            :class="sidebarOpen ? 'flex-row gap-2' : 'flex-col gap-3 items-center'">
            <a href="{{ route('assure.profile') }}"
                class="flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white transition-all duration-300 group"
                :class="sidebarOpen ? 'flex-1 py-2.5' : 'w-10 h-10 py-0'" title="Mon Profil">
                <i
                    class="fa-solid fa-user-gear text-sm opacity-60 group-hover:opacity-100 group-hover:rotate-45 transition-all"></i>
            </a>
            <form action="{{ route('assure.logout') }}" method="POST" :class="sidebarOpen ? 'flex-1' : 'w-10'">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 transition-all duration-300 group"
                    :class="sidebarOpen ? 'py-2.5' : 'h-10 py-0'" title="Déconnexion">
                    <i
                        class="fa-solid fa-power-off text-sm opacity-60 group-hover:opacity-100 group-hover:scale-110 transition-all"></i>
                </button>
            </form>
        </nav>
    </div>
</aside>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.1);
    }
</style>

{{-- Backdrop for mobile --}}
<div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-slate-900/80 backdrop-blur-md lg:hidden"
    @click="sidebarOpen = false" x-cloak>
</div>