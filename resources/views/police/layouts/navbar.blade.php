<header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 shadow-sm z-10">

    <div class="flex items-center gap-4">
        {{-- Toggle Sidebar --}}
        <button id="toggle-sidebar"
            class="w-9 h-9 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all">
            <i class="fa-solid fa-bars text-base"></i>
        </button>

        {{-- Fil d'Ariane --}}
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <span>Espace Police</span>
            <i class="fa-solid fa-angle-right text-xs"></i>
            <span class="text-slate-700 font-medium">@yield('page-title', 'Tableau de bord')</span>
        </div>
    </div>

    <div class="flex items-center gap-3">
        {{-- Profil --}}
        <div class="flex items-center gap-2 pl-3 border-l border-slate-100">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0 bg-blue-600 overflow-hidden">
                @if(auth('user')->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(auth('user')->user()->name ?? 'P', 0, 1)) }}
                @endif
            </div>
            <div class="hidden md:block">
                <p class="text-xs font-semibold text-slate-700 leading-none">
                    {{ auth('user')->user()->name ?? 'Commissariat' }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ auth('user')->user()->email ?? 'Service OPJ' }}</p>
            </div>
        </div>
    </div>
</header>