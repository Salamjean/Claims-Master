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
            <i class="fa-solid fa-grid-2 mr-1.5"></i> Vue d'ensemble
        </a>
        <a href="{{ route('groupe.interventions') }}" class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all inline-flex items-center gap-2 {{ request()->routeIs('groupe.interventions') ? 'bg-white text-rose-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100' }}">
            <i class="fa-solid fa-truck-medical"></i>
            <span>Interventions en cours</span>
            <span class="px-2 py-0.5 text-xs font-extrabold rounded-full transition-colors {{ ($interventionsEnCoursCount ?? 0) > 0 ? (request()->routeIs('groupe.interventions') ? 'bg-rose-100 text-rose-700' : 'bg-rose-600 text-white shadow-sm') : 'bg-slate-200 text-slate-600' }}">
                {{ $interventionsEnCoursCount ?? 0 }}
            </span>
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

{{-- Navigation Mobile --}}
<div class="lg:hidden bg-white border-b border-slate-100 px-4 py-2 flex items-center gap-2 overflow-x-auto shrink-0 shadow-sm">
    <a href="{{ route('groupe.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-all flex items-center gap-1.5 {{ request()->routeIs('groupe.dashboard') ? 'bg-rose-50 text-rose-600 font-bold' : 'text-slate-500 hover:bg-slate-100' }}">
        <i class="fa-solid fa-grid-2"></i> Vue d'ensemble
    </a>
    <a href="{{ route('groupe.interventions') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-all flex items-center gap-1.5 {{ request()->routeIs('groupe.interventions') ? 'bg-rose-50 text-rose-600 font-bold' : 'text-slate-500 hover:bg-slate-100' }}">
        <i class="fa-solid fa-truck-medical"></i>
        <span>Interventions en cours</span>
        <span class="px-1.5 py-0.5 text-[10px] font-extrabold rounded-full {{ ($interventionsEnCoursCount ?? 0) > 0 ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-600' }}">
            {{ $interventionsEnCoursCount ?? 0 }}
        </span>
    </a>
    <a href="{{ route('groupe.statistiques') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-all flex items-center gap-1.5 {{ request()->routeIs('groupe.statistiques') ? 'bg-rose-50 text-rose-600 font-bold' : 'text-slate-500 hover:bg-slate-100' }}">
        <i class="fa-solid fa-chart-pie"></i> Statistiques
    </a>
    <a href="{{ route('groupe.historique') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-all flex items-center gap-1.5 {{ request()->routeIs('groupe.historique') ? 'bg-rose-50 text-rose-600 font-bold' : 'text-slate-500 hover:bg-slate-100' }}">
        <i class="fa-solid fa-clock-rotate-left"></i> Historique
    </a>
</div>
