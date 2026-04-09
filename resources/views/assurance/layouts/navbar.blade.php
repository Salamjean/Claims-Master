<header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 shadow-sm z-10">

    <div class="flex items-center gap-4">
        {{-- Toggle Sidebar --}}
        <button id="toggle-sidebar"
            class="w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-primary transition-all">
            <i class="fa-solid fa-bars text-base"></i>
        </button>

        {{-- Fil d'Ariane --}}
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <span>Assurance</span>
            <i class="fa-solid fa-angle-right text-xs"></i>
            <span class="text-slate-700 font-medium">@yield('page-title', 'Tableau de bord')</span>
        </div>
    </div>

    <div class="flex items-center gap-3">

        {{-- Recherche --}}
        <div class="relative hidden md:block">
            <div class="absolute inset-y-0 left-3 flex items-center text-slate-300">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </div>
            <input type="text" placeholder="Rechercher..."
                class="pl-9 pr-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm text-slate-600 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/30 w-48 transition-all">
        </div>

        {{-- Notifications --}}
        <div class="relative">
            <button
                class="w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-primary transition-all">
                <i class="fa-solid fa-bell text-base"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-secondary rounded-full border-2 border-white"></span>
            </button>
        </div>

        {{-- Profil --}}
        <div class="flex items-center gap-2 pl-3 border-l border-slate-100">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0 overflow-hidden"
                style="background: linear-gradient(135deg, #243a8f, #7cb604);">
                @if(auth('user')->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(auth('user')->user()->name ?? 'A', 0, 1)) }}
                @endif
            </div>
            <div class="hidden md:block text-right">
                <p class="text-xs font-semibold text-slate-700 leading-none">{{ auth('user')->user()->name ?? 'Assurance' }}
                </p>
                <p class="text-[10px] text-slate-400 mt-0.5 uppercase font-bold tracking-wider">Compte Assurance</p>
            </div>
        </div>
    </div>
</header>