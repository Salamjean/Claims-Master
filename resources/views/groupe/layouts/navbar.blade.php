<header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 shadow-sm z-10">

    <div class="flex items-center gap-4">
        {{-- Logo / Marque --}}
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-gradient-to-br from-rose-600 to-rose-800 rounded-xl flex items-center justify-center text-white font-bold shadow-lg shadow-rose-600/20">
                <i class="fa-solid fa-shield-heart text-sm"></i>
            </div>
            <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-700 to-rose-900 hidden sm:block">
                Claims Master
            </span>
        </div>
    </div>

    {{-- Navigation Centrale --}}
    <nav class="hidden lg:flex items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-100">
        <a href="{{ route('groupe.dashboard') }}" class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('groupe.dashboard') ? 'bg-white text-rose-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100' }}">
            <i class="fa-solid fa-truck-medical mr-1.5"></i> Interventions en cours
        </a>
        <a href="{{ route('groupe.statistiques') }}" class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('groupe.statistiques') ? 'bg-white text-rose-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100' }}">
            <i class="fa-solid fa-chart-pie mr-1.5"></i> Statistiques
        </a>
        <a href="{{ route('groupe.historique') }}" class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('groupe.historique') ? 'bg-white text-rose-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100' }}">
            <i class="fa-solid fa-clock-rotate-left mr-1.5"></i> Historique
        </a>
    </nav>

    <div class="flex items-center gap-4">
        {{-- Bouton Déconnexion --}}
        <form method="POST" action="{{ route('groupe.logout') }}">
            @csrf
            <button type="submit" class="w-9 h-9 rounded-xl hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition-all" title="Se déconnecter">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </form>

        {{-- Profil --}}
        <div class="flex items-center gap-2 pl-3 border-l border-slate-100">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0 bg-rose-600 overflow-hidden">
                @if(auth('user')->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(auth('user')->user()->name ?? 'G', 0, 1)) }}
                @endif
            </div>
            <div class="hidden md:block">
                <p class="text-xs font-semibold text-slate-700 leading-none">
                    {{ auth('user')->user()->name ?? 'Groupe' }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ auth('user')->user()->email ?? 'Sapeurs-Pompiers' }}</p>
            </div>
        </div>
    </div>
</header>
