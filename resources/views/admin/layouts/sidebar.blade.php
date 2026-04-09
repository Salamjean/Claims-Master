<aside id="sidebar" class="flex flex-col">

    {{-- LOGO --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
            style="background: linear-gradient(135deg, #7cb604, #5a8a03)">
            <i class="fa-solid fa-shield-halved text-white text-base"></i>
        </div>
        <div class="logo-text">
            <p class="text-white font-bold text-sm leading-none">Claims Master</p>
            <p class="text-white/50 text-xs mt-0.5">Administration</p>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        <p class="nav-label text-white/30 text-[10px] uppercase tracking-widest font-semibold px-3 mb-2">Principal</p>

        <a href="{{ route('admin.dashboard') }}"
            class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-chart-pie text-sm"></i></span>
            <span class="nav-label text-sm">Tableau de bord</span>
        </a>

        {{-- DROPDOWN Assurances --}}
        <div x-data="{ open: false }">
            <button @click="open = !open" class="nav-item w-full justify-between"
                :class="open ? 'bg-white/10 text-white' : ''">
                <span class="flex items-center gap-3">
                    <span class="nav-icon"><i class="fa-solid fa-shield-halved text-sm"></i></span>
                    <span class="nav-label text-sm">Assurances</span>
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
                <a href="{{ route('admin.assurances.index') }}" class="nav-item text-xs py-2 px-3">
                    <span class="nav-icon"><i class="fa-solid fa-list text-xs"></i></span>
                    <span>Liste des assurances</span>
                </a>
                <a href="{{ route('admin.assurances.create') }}" class="nav-item text-xs py-2 px-3">
                    <span class="nav-icon"><i class="fa-solid fa-plus text-xs"></i></span>
                    <span>Inscrire une assurance</span>
                </a>
                <a href="#" class="nav-item text-xs py-2 px-3">
                    <span class="nav-icon"><i class="fa-solid fa-box-archive text-xs"></i></span>
                    <span>Archives</span>
                </a>
            </div>
        </div>

        {{-- DROPDOWN Constats --}}
        <div x-data="{ open: {{ request()->routeIs('admin.services.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="nav-item w-full justify-between"
                :class="open ? 'bg-white/10 text-white' : ''">
                <span class="flex items-center gap-3">
                    <span class="nav-icon"><i class="fa-solid fa-clipboard-list text-sm"></i></span>
                    <span class="nav-label text-sm">Service de constats</span>
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
                <a href="{{ route('admin.services.index') }}"
                    class="nav-item text-xs py-2 px-3 {{ request()->routeIs('admin.services.index') ? 'text-white bg-white/5' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-list text-xs"></i></span>
                    <span>Liste des services</span>
                </a>
                <a href="{{ route('admin.services.create') }}"
                    class="nav-item text-xs py-2 px-3 {{ request()->routeIs('admin.services.create') ? 'text-white bg-white/5' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-plus text-xs"></i></span>
                    <span>Ajouter un service</span>
                </a>
            </div>
        </div>

        <a href="#" class="nav-item">
            <span class="nav-icon"><i class="fa-solid fa-toolbox text-sm"></i></span>
            <span class="nav-label text-sm">Équipements</span>
        </a>

        <a href="#" class="nav-item">
            <span class="nav-icon"><i class="fa-solid fa-map-location-dot text-sm"></i></span>
            <span class="nav-label text-sm">Sites</span>
        </a>

        <div class="my-3 border-t border-white/10"></div>
        <p class="nav-label text-white/30 text-[10px] uppercase tracking-widest font-semibold px-3 mb-2">Finance</p>

        <a href="#" class="nav-item">
            <span class="nav-icon"><i class="fa-solid fa-file-invoice-dollar text-sm"></i></span>
            <span class="nav-label text-sm">Paiements</span>
        </a>

        <a href="#" class="nav-item">
            <span class="nav-icon"><i class="fa-solid fa-chart-bar text-sm"></i></span>
            <span class="nav-label text-sm">Rapports</span>
        </a>

        <div class="my-3 border-t border-white/10"></div>
        <p class="nav-label text-white/30 text-[10px] uppercase tracking-widest font-semibold px-3 mb-2">Système</p>

        <a href="#" class="nav-item">
            <span class="nav-icon"><i class="fa-solid fa-gear text-sm"></i></span>
            <span class="nav-label text-sm">Paramètres</span>
        </a>

    </nav>

    {{-- USER FOOTER --}}
    <div class="border-t border-white/10 px-4 py-4">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-secondary flex items-center justify-center shrink-0">
                <span class="text-white text-xs font-bold">
                    {{ strtoupper(substr(auth('user')->user()->name ?? 'A', 0, 1)) }}
                </span>
            </div>
            <div class="nav-label flex-1 min-w-0">
                <p class="text-white text-xs font-medium truncate">{{ auth('user')->user()->name ?? 'Admin' }}</p>
                <p class="text-white/40 text-[10px] truncate">{{ auth('user')->user()->email ?? '' }}</p>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="nav-label">
                @csrf
                <button type="submit" title="Déconnexion"
                    class="w-7 h-7 rounded-lg flex items-center justify-center text-white/40 hover:text-white hover:bg-white/10 transition-all">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</aside>