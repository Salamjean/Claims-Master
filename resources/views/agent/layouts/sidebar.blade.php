<aside id="sidebar" class="flex flex-col shrink-0 bg-slate-900 border-r border-white/5 shadow-2xl relative"
    style="background: linear-gradient(180deg, #1e3a8a 0%, #172554 100%);"
    :class="{ 'collapsed': sidebarCollapsed, 'open': sidebarOpen }">

    {{-- LOGO --}}
    <div class="h-16 flex items-center gap-3 px-6 border-b border-white/10 overflow-hidden">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/20"
            style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)">
            <i class="fa-solid fa-handshake-angle text-white text-base"></i>
        </div>
        <div class="logo-text truncate transition-all duration-300">
            <p class="text-white font-black text-sm leading-none uppercase tracking-tight">Claims Master</p>
            <p class="text-blue-300/50 text-[10px] font-bold uppercase tracking-widest mt-1">Espace Agent</p>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 px-3 py-6 space-y-1.5 overflow-y-auto custom-scrollbar">

        <p class="nav-label text-white/20 text-[10px] uppercase tracking-[0.2em] font-black px-4 mb-3">Principal</p>

        <a href="{{ route('agent.dashboard') }}"
            class="nav-item {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-chart-pie text-sm"></i></span>
            <span class="nav-label text-xs font-black uppercase tracking-wider">Tableau de bord</span>
        </a>

        <p
            class="nav-label text-white/20 text-[10px] uppercase tracking-[0.2em] font-black px-4 mt-8 mb-3 whitespace-nowrap">
            Gestion des dossiers</p>

        <a href="{{ route('agent.sinistres.mes_dossiers') }}"
            class="nav-item {{ request()->routeIs('agent.sinistres.mes_dossiers') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-folder-open text-sm"></i></span>
            <span class="nav-label text-xs font-black uppercase tracking-wider">Mes Sinistres</span>
        </a>

        <a href="{{ route('agent.constats.rediges') }}"
            class="nav-item {{ request()->routeIs('agent.constats.rediges') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-file-lines text-sm"></i></span>
            <span class="nav-label text-xs font-black uppercase tracking-wider">Constats Rédigés</span>
        </a>

        <a href="{{ route('agent.constats.statistiques') }}"
            class="nav-item {{ request()->routeIs('agent.constats.statistiques') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-chart-pie text-sm"></i></span>
            <span class="nav-label text-xs font-black uppercase tracking-wider">Statistiques</span>
        </a>

        <a href="{{ route('agent.sinistres.historique') }}"
            class="nav-item {{ request()->routeIs('agent.sinistres.historique') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-clock-rotate-left text-sm"></i></span>
            <span class="nav-label text-xs font-black uppercase tracking-wider">Historique</span>
        </a>

        <p
            class="nav-label text-white/20 text-[10px] uppercase tracking-[0.2em] font-black px-4 mt-8 mb-3 whitespace-nowrap">
            Finance</p>

        <a href="{{ route('agent.wallet') }}"
            class="nav-item {{ request()->routeIs('agent.wallet') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-wallet text-sm text-emerald-400"></i></span>
            <span class="nav-label text-xs font-black uppercase tracking-wider">Mon Portefeuille</span>
            @if (auth('user')->user()->wallet_balance > 0)
                <span
                    class="ml-auto bg-emerald-500/20 text-emerald-400 text-[9px] font-black px-2 py-0.5 rounded-full border border-emerald-500/10">
                    {{ number_format(auth('user')->user()->wallet_balance, 0, ',', ' ') }}
                </span>
            @endif
        </a>

    </nav>

    {{-- UTILISATEUR --}}
    <div class="p-4 border-t border-white/10 bg-black/10">
        <div class="flex items-center gap-3 w-full p-2 rounded-xl transition-all">
            <div
                class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center shrink-0 overflow-hidden shadow-lg shadow-blue-500/20 border-2 border-white/10">
                @if (auth('user')->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}"
                        class="w-full h-full object-cover">
                @else
                    <span
                        class="text-white text-xs font-black">{{ strtoupper(substr(auth('user')->user()->name ?? 'A', 0, 1)) }}</span>
                @endif
            </div>
            <div class="text-left nav-label flex-1 truncate transition-all duration-300">
                <p class="text-white text-xs font-black truncate leading-none mb-1">{{ auth('user')->user()->name }}
                </p>
                <p class="text-blue-300/40 text-[9px] font-bold uppercase tracking-widest truncate">Agent Certifié</p>
            </div>
            <form method="POST" action="{{ route('agent.logout') }}" class="nav-label shrink-0">
                @csrf
                <button type="submit"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-white/30 hover:text-red-400 hover:bg-red-500/10 transition-all"
                    title="Déconnexion">
                    <i class="fa-solid fa-power-off text-xs"></i>
                </button>
            </form>
        </div>
    </div>

</aside>

</aside>
