<aside id="sidebar" class="flex flex-col shrink-0 shadow-2xl relative z-50 lg:relative lg:z-auto"
    :class="{ 'collapsed': sidebarCollapsed, 'fixed inset-y-0 left-0': sidebarOpen, 'hidden lg:flex': !sidebarOpen }">

    {{-- LOGO --}}
    <div class="h-16 flex items-center gap-3 px-6 border-b border-white/10 overflow-hidden shrink-0">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 shadow-lg"
            style="background: linear-gradient(135deg, #457b9d, #1d3557)">
            <i class="fa-solid fa-user-tie text-white text-base"></i>
        </div>
        <div class="logo-text truncate transition-all duration-300">
            <p class="text-white font-black text-sm leading-none uppercase tracking-tight">Claims Master</p>
            <p class="text-white/40 text-[10px] font-bold uppercase tracking-widest mt-1">Espace Personnel</p>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">

        <p class="nav-label text-white/25 text-[10px] uppercase tracking-[0.2em] font-black px-3 mb-3">Principal</p>

        <a href="{{ route('personnel.dashboard') }}"
            class="nav-item {{ request()->routeIs('personnel.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-chart-pie text-sm"></i></span>
            <span class="nav-label text-xs font-semibold">Tableau de bord</span>
        </a>

        <p class="nav-label text-white/25 text-[10px] uppercase tracking-[0.2em] font-black px-3 mt-6 mb-3">Gestion</p>

        <a href="{{ route('personnel.mes-dossiers') }}"
            class="nav-item {{ request()->routeIs('personnel.mes-dossiers') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-briefcase text-sm"></i></span>
            <span class="nav-label text-xs font-semibold flex-1">Mes Dossiers</span>
            @if (!empty($sidebarCountMesDossiers) && $sidebarCountMesDossiers > 0)
                <span
                    class="nav-label ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-bold leading-none
                    {{ !empty($sidebarCountDocsIncomplets) && $sidebarCountDocsIncomplets > 0 ? 'bg-red-500 text-white' : 'bg-white/20 text-white' }}">
                    {{ $sidebarCountMesDossiers > 99 ? '99+' : $sidebarCountMesDossiers }}
                </span>
            @endif
        </a>

        {{-- Onglet Recherche --}}
        <a href="{{ route('personnel.search') }}"
            class="nav-item {{ request()->routeIs('personnel.search') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-magnifying-glass text-sm"></i></span>
            <span class="nav-label text-xs font-semibold">Recherche</span>
        </a>

        <div class="my-3 border-t border-white/10"></div>
        <p class="nav-label text-white/25 text-[10px] uppercase tracking-[0.2em] font-black px-3 mb-3">Compte</p>

        <a href="{{ route('personnel.profile') }}"
            class="nav-item {{ request()->routeIs('personnel.profile') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-circle-user text-sm"></i></span>
            <span class="nav-label text-xs font-semibold">Mon Profil</span>
        </a>

        <a href="{{ route('personnel.password.change') }}"
            class="nav-item {{ request()->routeIs('personnel.password.change') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-lock text-sm"></i></span>
            <span class="nav-label text-xs font-semibold">Mot de passe</span>
        </a>

    </nav>

    {{-- UTILISATEUR --}}
    <div class="p-4 border-t border-white/10">
        <div class="flex items-center gap-3 w-full">
            <div
                class="w-9 h-9 rounded-full bg-accent flex items-center justify-center shrink-0 overflow-hidden border-2 border-white/10">
                @if (auth('user')->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}"
                        class="w-full h-full object-cover">
                @else
                    <span
                        class="text-white text-xs font-black">{{ strtoupper(substr(auth('user')->user()->name ?? 'P', 0, 1)) }}</span>
                @endif
            </div>
            <div class="nav-label flex-1 truncate">
                <p class="text-white text-xs font-bold truncate leading-none mb-0.5">
                    {{ auth('user')->user()->name . ' ' . auth('user')->user()->prenom }}</p>
                <p class="text-white
                </p>
                <p class="text-white/35 text-[9px] font-bold
                    uppercase tracking-widest truncate">
                    {{ auth('user')->user()->code_user }}</p>
            </div>
            <form method="POST" action="{{ route('personnel.logout') }}" class="nav-label shrink-0">
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
