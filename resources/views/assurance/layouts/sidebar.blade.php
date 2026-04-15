<aside id="sidebar" class="flex flex-col">

    {{-- LOGO --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
            style="background: linear-gradient(135deg, #7cb604, #5a8a03)">
            <i class="fa-solid fa-shield-halved text-white text-base"></i>
        </div>
        <div class="logo-text">
            <p class="text-white font-bold text-sm leading-none">{{ Auth::user()->name }}</p>
            <p class="text-white/50 text-xs mt-0.5">Claims Master</p>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        <p class="nav-label text-white/30 text-[10px] uppercase tracking-widest font-semibold px-3 mb-2">Principal</p>

        <a href="{{ route('assurance.dashboard') }}"
            class="nav-item {{ request()->routeIs('assurance.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-chart-pie text-sm"></i></span>
            <span class="nav-label text-sm">Tableau de bord</span>
        </a>

        <a href="{{ route('assurance.sinistres.index') }}"
            class="nav-item {{ request()->routeIs('assurance.sinistres.*') ? 'active bg-white/10 text-white' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-folder-open text-sm"></i></span>
            <span class="nav-label text-sm flex-1">Dossiers Sinistres</span>
            @if (!empty($countSinistresNonCloturer) && $countSinistresNonCloturer > 0)
                <span
                    class="nav-label ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-bold bg-red-500 text-white leading-none">
                    {{ $countSinistresNonCloturer > 99 ? '99+' : $countSinistresNonCloturer }}
                </span>
            @endif
        </a>

        <a href="{{ route('assurance.search') }}"
            class="nav-item {{ request()->routeIs('assurance.search') ? 'active bg-white/10 text-white' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-magnifying-glass text-sm"></i></span>
            <span class="nav-label text-sm">Recherche</span>
        </a>

        {{-- DROPDOWN ASSURES --}}
        <div x-data="{ open: false }">
            <button @click="open = !open" class="nav-item w-full justify-between"
                :class="open ? 'bg-white/10 text-white' : ''">
                <span class="flex items-center gap-3">
                    <span class="nav-icon"><i class="fa-solid fa-user text-sm"></i></span>
                    <span class="nav-label text-sm">Assurés</span>
                </span>
                <span class="nav-label">
                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                        :class="open ? 'rotate-180' : ''"></i>
                </span>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                class="ml-8 mt-1 space-y-0.5 nav-label">
                <a href="{{ route('assurance.assures.index') }}" class="nav-item text-xs py-2 px-3">
                    <span class="nav-icon"><i class="fa-solid fa-list text-xs"></i></span>
                    <span>Liste des assurés</span>
                </a>
                <a href="{{ route('assurance.assures.create') }}" class="nav-item text-xs py-2 px-3">
                    <span class="nav-icon"><i class="fa-solid fa-plus text-xs"></i></span>
                    <span>Inscrire un assuré</span>
                </a>
                <a href="#" class="nav-item text-xs py-2 px-3">
                    <span class="nav-icon"><i class="fa-solid fa-box-archive text-xs"></i></span>
                    <span>Archives</span>
                </a>
            </div>
        </div>

        <a href="{{ route('assurance.experts.index') }}"
            class="nav-item {{ request()->routeIs('assurance.experts.*') ? 'active bg-white/10 text-white' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-user-tie text-sm"></i></span>
            <span class="nav-label text-sm">Nos Experts</span>
        </a>

        <a href="{{ route('assurance.garages.index') }}"
            class="nav-item {{ request()->routeIs('assurance.garages.*') ? 'active bg-white/10 text-white' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-wrench text-sm"></i></span>
            <span class="nav-label text-sm">Nos Garages</span>
        </a>

        {{-- DROPDOWN PERSONNEL --}}
        <div x-data="{ open: {{ request()->routeIs('assurance.personnel.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="nav-item w-full justify-between"
                :class="open ? 'bg-white/10 text-white' : ''">
                <span class="flex items-center gap-3">
                    <span class="nav-icon"><i class="fa-solid fa-users-gear text-sm"></i></span>
                    <span class="nav-label text-sm">Personnel</span>
                </span>
                <span class="nav-label">
                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                        :class="open ? 'rotate-180' : ''"></i>
                </span>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                class="ml-8 mt-1 space-y-0.5 nav-label">
                <a href="{{ route('assurance.personnel.index') }}"
                    class="nav-item text-xs py-2 px-3 {{ request()->routeIs('assurance.personnel.index') ? 'bg-white/10 text-white' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-list text-xs"></i></span>
                    <span>Liste du personnel</span>
                </a>
                <a href="{{ route('assurance.personnel.create') }}"
                    class="nav-item text-xs py-2 px-3 {{ request()->routeIs('assurance.personnel.create') ? 'bg-white/10 text-white' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-plus text-xs"></i></span>
                    <span>Ajouter un membre</span>
                </a>
            </div>
        </div>

        <!-- <div class="my-3 border-t border-white/10"></div>
        <p class="nav-label text-white/30 text-[10px] uppercase tracking-widest font-semibold px-3 mb-2">Finance</p> -->

        <!-- <a href="#" class="nav-item">
            <span class="nav-icon"><i class="fa-solid fa-file-invoice-dollar text-sm"></i></span>
            <span class="nav-label text-sm">Paiements</span>
        </a>

        <a href="#" class="nav-item">
            <span class="nav-icon"><i class="fa-solid fa-chart-bar text-sm"></i></span>
            <span class="nav-label text-sm">Rapports</span>
        </a> -->

        <div class="my-3 border-t border-white/10"></div>
        <p class="nav-label text-white/30 text-[10px] uppercase tracking-widest font-semibold px-3 mb-2">Système</p>

        <a href="{{ route('assurance.documents-requis.index') }}"
            class="nav-item {{ request()->routeIs('assurance.documents-requis.*') ? 'active bg-white/10 text-white' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-file-circle-check text-sm"></i></span>
            <span class="nav-label text-sm">Documents Requis</span>
        </a>

        <a href="{{ route('assurance.profile') }}"
            class="nav-item {{ request()->routeIs('assurance.profile') ? 'active bg-white/10 text-white' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-gear text-sm"></i></span>
            <span class="nav-label text-sm">Paramètres</span>
        </a>

    </nav>

    {{-- USER FOOTER --}}
    <div class="border-t border-white/10 px-4 py-4">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-secondary flex items-center justify-center shrink-0 overflow-hidden">
                @if (auth('user')->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}"
                        class="w-full h-full object-cover">
                @else
                    <span class="text-white text-xs font-bold">
                        {{ strtoupper(substr(auth('user')->user()->name ?? 'A', 0, 1)) }}
                    </span>
                @endif
            </div>
            <div class="nav-label flex-1 min-w-0">
                <p class="text-white text-xs font-medium truncate">{{ auth('user')->user()->name ?? 'Assurance' }}</p>
                <p class="text-white/40 text-[10px] truncate">{{ auth('user')->user()->email ?? '' }}</p>
            </div>
            <form action="{{ route('assurance.logout') }}" method="POST" class="nav-label">
                @csrf
                <button type="submit" title="Déconnexion"
                    class="w-7 h-7 rounded-lg flex items-center justify-center text-white/40 hover:text-white hover:bg-white/10 transition-all">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
