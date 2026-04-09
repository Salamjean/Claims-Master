<nav id="topnav" class="flex items-center justify-between px-8 h-20 border-b border-slate-200 bg-white/80 backdrop-blur-xl sticky top-0 z-40 transition-all duration-300">
    
    {{-- Left side: Toggle & Dynamic Breadcrumb --}}
    <div class="flex items-center gap-8">
        <button @click="sidebarOpen = !sidebarOpen" 
            class="group w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-500 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition-all duration-300">
            <i class="fa-solid fa-bars-staggered text-lg group-hover:scale-110 transition-transform"></i>
        </button>
        
        <div class="hidden md:flex flex-col">
            <!-- <div class="flex items-center gap-2 text-[10px] uppercase tracking-[0.2em] font-black text-slate-400 mb-1">
                <span>Claims Master</span>
                <i class="fa-solid fa-chevron-right text-[8px] opacity-50"></i>
                <span class="text-blue-600">Espace Assuré</span>
            </div> -->
            <h2 class="text-lg font-black text-slate-900 tracking-tight">@yield('page-title', 'Overview')</h2>
        </div>
    </div>

    {{-- Right side: Utilities & Profile --}}
    <div class="flex items-center gap-6">
        
        {{-- Search Placeholder (Premium look) --}}
        <div class="hidden lg:flex items-center gap-3 px-4 py-2.5 bg-slate-100/50 border border-slate-200 rounded-xl w-64 group focus-within:bg-white focus-within:ring-4 focus-within:ring-blue-500/5 focus-within:border-blue-500/30 transition-all duration-300">
            <i class="fa-solid fa-magnifying-glass text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
            <input type="text" placeholder="Rechercher un dossier..." class="bg-transparent border-none outline-none text-xs font-bold text-slate-600 placeholder:text-slate-400 w-full">
        </div>

        {{-- Notifications --}}
        <div class="flex items-center gap-3 pr-2 border-r border-slate-200">
            <button class="relative w-11 h-11 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-500 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition-all duration-300 group">
                <i class="fa-regular fa-bell text-xl group-hover:shake transition-all text-[18px]"></i>
                <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
            </button>
        </div>

        {{-- User Section Simplified --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                class="flex items-center gap-4 py-1.5 pl-1.5 pr-3 rounded-2xl hover:bg-slate-50 transition-all duration-300 border border-transparent hover:border-slate-200 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20 border-2 border-white/50 overflow-hidden shrink-0 group-hover:scale-105 transition-transform duration-300">
                    @if(auth('user')->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-white font-black text-sm">{{ strtoupper(substr(auth('user')->user()->name ?? 'A', 0, 1)) }}</span>
                    @endif
                </div>
                <div class="hidden xl:block text-left">
                    <p class="text-[13px] font-black text-slate-900 leading-none mb-1 text-nowrap">{{ auth('user')->user()->name }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-nowrap">Mon Compte</p>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-300 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
            </button>

            {{-- Dropdown Menu --}}
            <div x-show="open" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                @click.outside="open = false"
                class="absolute right-0 mt-3 w-64 bg-white border border-slate-200 rounded-2xl shadow-2xl shadow-slate-900/10 py-2.5 z-50 overflow-hidden" 
                x-cloak>
                
                <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 mb-2">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Identité</p>
                    <p class="text-sm font-black text-slate-800 truncate mb-0.5">{{ auth('user')->user()->name }}</p>
                    <p class="text-[11px] font-bold text-blue-600 font-mono tracking-tight">{{ auth('user')->user()->email }}</p>
                </div>

                <a href="{{ route('assure.profile') }}" class="flex items-center gap-3.5 px-5 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center group-hover:bg-blue-500/10 group-hover:text-blue-500 transition-colors">
                        <i class="fa-regular fa-user-circle text-lg"></i>
                    </div>
                    Paramètres profil
                </a>
                
                <a href="#" class="flex items-center gap-3.5 px-5 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center group-hover:bg-blue-500/10 group-hover:text-blue-500 transition-colors">
                        <i class="fa-regular fa-shield-check text-lg"></i>
                    </div>
                    Sécurité
                </a>

                <div class="h-px bg-slate-100 my-2 mx-4"></div>

                <form action="{{ route('assure.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3.5 px-5 py-3 text-sm font-bold text-red-500 hover:bg-red-50 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-all">
                            <i class="fa-solid fa-power-off"></i>
                        </div>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
    @keyframes shake {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(10deg); }
        75% { transform: rotate(-10deg); }
    }
    .group:hover .group-hover\:shake {
        animation: shake 0.5s ease-in-out infinite;
    }
</style>