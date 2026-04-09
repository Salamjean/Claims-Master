<aside id="sidebar" class="flex flex-col">

    {{-- LOGO --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
            style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)">
            <i class="fa-solid fa-handshake-angle text-white text-base"></i>
        </div>
        <div class="logo-text">
            <p class="text-white font-bold text-sm leading-none">Claims Master</p>
            <p class="text-white/50 text-xs mt-0.5">Espace Police</p>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        <p class="nav-label text-white/30 text-[10px] uppercase tracking-widest font-semibold px-3 mb-2">Principal</p>

        <a href="{{ route('police.dashboard') }}"
            class="nav-item {{ request()->routeIs('police.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-chart-pie text-sm"></i></span>
            <span class="nav-label text-sm">Tableau de bord</span>
        </a>

        <p class="nav-label text-white/30 text-[10px] uppercase tracking-widest font-semibold px-3 mt-4 mb-2">Sinistres
        </p>

        <a href="{{ route('police.sinistres.en_attente') }}"
            class="nav-item {{ request()->routeIs('police.sinistres.en_attente') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-hourglass-half text-sm"></i></span>
            <span class="nav-label text-sm">En attente</span>
            @php $nb = \App\Models\Sinistre::where('assigned_service_id', auth('user')->id())->where('status', 'en_attente')->count(); @endphp
            @if($nb > 0)
                <span
                    class="ml-auto bg-amber-400 text-amber-900 text-[10px] font-bold px-2 py-0.5 rounded-full nav-label">{{ $nb }}</span>
            @endif
        </a>

        <a href="{{ route('police.sinistres.historique') }}"
            class="nav-item {{ request()->routeIs('police.sinistres.historique') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-clock-rotate-left text-sm"></i></span>
            <span class="nav-label text-sm">Historique</span>
        </a>

        <p class="nav-label text-white/30 text-[10px] uppercase tracking-widest font-semibold px-3 mt-4 mb-2">Service</p>

        <a href="{{ route('police.agents.index') }}"
            class="nav-item {{ request()->routeIs('police.agents.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-users text-sm"></i></span>
            <span class="nav-label text-sm">Agents</span>
        </a>

    </nav>

    {{-- UTILISATEUR & DECONNEXION --}}
    <div class="px-4 py-4 border-t border-white/10">
        <div x-data="{ open: false }" class="relative" @click.away="open = false">

            <button @click="open = !open"
                class="flex items-center gap-3 w-full p-2 rounded-xl hover:bg-white/10 transition-colors">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 overflow-hidden">
                    @if(auth('user')->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-white text-xs font-bold">{{ strtoupper(substr(auth('user')->user()->name ?? 'P', 0, 1)) }}</span>
                    @endif
                </div>
                <div class="text-left nav-label flex-1 truncate">
                    <p class="text-white text-sm font-semibold truncate">{{ auth('user')->user()->name ?? 'Police' }}
                    </p>
                    <p class="text-white/50 text-xs truncate">OPJ</p>
                </div>
                <i class="fa-solid fa-ellipsis-vertical text-white/50 nav-label shrink-0"></i>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute bottom-full left-0 w-full mb-2 bg-white rounded-xl shadow-lg py-1 overflow-hidden nav-label border border-slate-100">

                <a href="{{ route('police.profile') }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-user-shield w-4 text-center text-slate-400 text-xs"></i> Mon Profil
                </a>

                <div class="h-px bg-slate-100 my-1"></div>

                <form method="POST" action="{{ route('police.logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center text-xs"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>

</aside>