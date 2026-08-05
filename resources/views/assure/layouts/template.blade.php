<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mon Espace') — Claims Master</title>

    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        [x-cloak] { display: none !important; }

        :root {
            --sidebar-w-open: 260px;
            --sidebar-w-closed: 68px;
            --topbar-h: 60px;
            --accent: #6366f1;
            --accent-light: #eef2ff;
            --accent-mid: #c7d2fe;
        }

        * { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }

        body { background: #f1f5f9; color: #0f172a; overflow: hidden; }

        /* ─── SCROLLBAR ─── */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ─── LAYOUT ─── */
        .app-shell { display: flex; height: 100vh; overflow: hidden; }

        /* ─── SIDEBAR ─── */
        #cm-sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border-right: 1px solid #e8ecf4;
            box-shadow: 4px 0 32px rgba(99,102,241,0.04);
            transition: width 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            width: var(--sidebar-w-open);
        }
        #cm-sidebar.collapsed { width: var(--sidebar-w-closed); }

        /* ─── MAIN AREA ─── */
        #cm-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            transition: margin-left 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            margin-left: var(--sidebar-w-open);
        }
        #cm-main.collapsed { margin-left: var(--sidebar-w-closed); }

        /* Sur tablettes paysage / petits laptops : sidebar auto-réduite */
        @media (max-width: 1023px) {
            #cm-sidebar { width: var(--sidebar-w-open); transform: translateX(-100%); transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1), width 0.28s; }
            #cm-sidebar.mobile-open { transform: translateX(0); }
            #cm-main { margin-left: 0 !important; }
        }

        /* ─── TOPBAR ─── */
        #cm-topbar {
            height: var(--topbar-h);
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid #e8ecf4;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 40;
            flex-shrink: 0;
        }

        /* ─── PAGE CONTENT ─── */
        #cm-content {
            flex: 1;
            overflow-y: auto;
            padding: 28px;
            background: #f1f5f9;
        }

        /* ─── SIDEBAR NAV ITEMS ─── */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 12px;
            color: #64748b;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
            position: relative;
            border: 1px solid transparent;
            width: 100%;
            background: none;
        }
        .nav-item:hover { background: #f8fafc; color: #1e293b; }
        .nav-item.active {
            background: var(--accent-light);
            color: var(--accent);
            border-color: var(--accent-mid);
        }
        .nav-item .nav-icon {
            width: 34px;
            height: 34px;
            min-width: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.15s ease;
            background: #f1f5f9;
            color: #94a3b8;
        }
        .nav-item:hover .nav-icon { background: #e2e8f0; color: #475569; }
        .nav-item.active .nav-icon {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 4px 12px rgba(99,102,241,0.3);
        }
        /* Active left bar */
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 18px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }
        .nav-label { flex: 1; overflow: hidden; text-overflow: ellipsis; transition: opacity 0.2s, width 0.2s; }

        /* ─── SECTION LABEL ─── */
        .nav-section-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #c1c9d9;
            padding: 0 10px;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s;
        }

        /* ─── BADGE ─── */
        .nav-badge {
            padding: 2px 7px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 800;
            background: #fee2e2;
            color: #ef4444;
            flex-shrink: 0;
            transition: opacity 0.2s;
        }

        /* ─── SUBNAV ─── */
        .subnav-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px 7px 44px;
            font-size: 12.5px;
            font-weight: 500;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .subnav-item:hover { background: #f8fafc; color: #475569; }
        .subnav-item.active { color: var(--accent); font-weight: 700; background: #f5f3ff; }
        .subnav-dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; flex-shrink: 0; opacity: 0.5; }
        .subnav-item.active .subnav-dot { opacity: 1; }

        /* ─── PROFILE CARD (sidebar footer) ─── */
        .sidebar-profile { border-top: 1px solid #f1f5f9; padding: 10px; transition: padding 0.28s; }
        .sidebar-profile-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            border-radius: 12px;
            background: #fafafa;
            border: 1px solid #f1f5f9;
            transition: background 0.15s;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }
        .sidebar-profile-card:hover { background: #f1f5f9; }
        .avatar-ring {
            width: 34px;
            height: 34px;
            min-width: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-size: 13px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(99,102,241,0.25);
        }

        /* ─── TOPBAR SEARCH ─── */
        .topbar-search {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            background: #f8fafc;
            border: 1.5px solid #e8ecf4;
            border-radius: 11px;
            width: 240px;
            transition: all 0.2s;
        }
        .topbar-search:focus-within {
            background: #fff;
            border-color: var(--accent-mid);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
        }
        .topbar-search input { background: transparent; border: none; outline: none; font-size: 13px; font-weight: 500; color: #334155; width: 100%; }

        /* ─── TOPBAR ACTION BTN ─── */
        .topbar-btn {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            background: #f8fafc;
            border: 1.5px solid #e8ecf4;
            color: #64748b;
            cursor: pointer;
            transition: all 0.15s;
        }
        .topbar-btn:hover { background: var(--accent-light); border-color: var(--accent-mid); color: var(--accent); }

        /* ─── ANIMATIONS ─── */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .fu { animation: fadeUp 0.4s ease-out both; }
        .d1 { animation-delay: 0.05s; } .d2 { animation-delay: 0.1s; }
        .d3 { animation-delay: 0.15s; } .d4 { animation-delay: 0.2s; }

        /* ─── CARD ─── */
        .card { background: #fff; border-radius: 16px; border: 1px solid #e8ecf4; box-shadow: 0 1px 8px rgba(0,0,0,0.04); }

        /* ════════════════════════════════
           SIDEBAR COLLAPSED MODE
        ════════════════════════════════ */

        /* Logo centré en mode réduit */
        #cm-sidebar.collapsed .sidebar-logo-wrapper {
            justify-content: center;
            padding: 0;
        }
        #cm-sidebar.collapsed .sidebar-logo-text { display: none; }

        /* Nav items : icône seule centrée */
        #cm-sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 9px;
            gap: 0;
        }
        #cm-sidebar.collapsed .nav-label,
        #cm-sidebar.collapsed .nav-badge,
        #cm-sidebar.collapsed .nav-chevron,
        #cm-sidebar.collapsed .nav-section-label,
        #cm-sidebar.collapsed .subnav-group,
        #cm-sidebar.collapsed .sidebar-divider { display: none !important; }

        /* Profil footer : avatar centré */
        #cm-sidebar.collapsed .sidebar-profile { padding: 8px; }
        #cm-sidebar.collapsed .sidebar-profile-card {
            justify-content: center;
            padding: 8px;
            gap: 0;
        }
        #cm-sidebar.collapsed .profile-text,
        #cm-sidebar.collapsed .profile-dots { display: none; }

        /* Tooltip au hover en mode réduit */
        #cm-sidebar.collapsed .nav-item { position: relative; }
        #cm-sidebar.collapsed .nav-item[data-tip]::after {
            content: attr(data-tip);
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%);
            background: #1e293b;
            color: #fff;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s;
            z-index: 100;
        }
        #cm-sidebar.collapsed .nav-item[data-tip]:hover::after { opacity: 1; }
    </style>

    @stack('styles')
</head>

<body>

<div class="app-shell" x-data="{
    sidebarOpen: window.innerWidth >= 1024,
    isMobile: window.innerWidth < 1024,
    get sidebarClass() {
        if (this.isMobile) return this.sidebarOpen ? 'mobile-open' : '';
        return this.sidebarOpen ? '' : 'collapsed';
    },
    get mainClass() {
        if (this.isMobile) return '';
        return this.sidebarOpen ? '' : 'collapsed';
    }
}" @resize.window="
    isMobile = window.innerWidth < 1024;
    if (isMobile) sidebarOpen = false;
">

    {{-- ════════════════════════════════
         SIDEBAR
    ════════════════════════════════ --}}
    <aside id="cm-sidebar" :class="sidebarClass">

        {{-- Logo --}}
        <div class="sidebar-logo-wrapper flex items-center gap-3 h-[60px] px-4 border-b border-slate-100 shrink-0 transition-all duration-300">
            <div class="w-8 h-8 min-w-[32px] rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-md shadow-indigo-200">
                <i class="fa-solid fa-shield-halved text-white text-sm"></i>
            </div>
            <div class="sidebar-logo-text flex flex-col overflow-hidden">
                <span class="font-black text-slate-800 text-[14px] leading-none tracking-tight whitespace-nowrap">Claims<span class="text-indigo-500">Master</span></span>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.18em] mt-0.5 whitespace-nowrap">Espace Assuré</span>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="flex-1 overflow-y-auto overflow-x-hidden py-3 px-2 space-y-4" style="scrollbar-width: none;">

            {{-- Menu Principal --}}
            <div class="space-y-0.5">
                <p class="nav-section-label">Principal</p>

                {{-- Dashboard --}}
                <a href="{{ route('assure.dashboard') }}"
                    class="nav-item {{ request()->routeIs('assure.dashboard') ? 'active' : '' }}"
                    data-tip="Tableau de bord">
                    <div class="nav-icon"><i class="fa-solid fa-gauge-high"></i></div>
                    <span class="nav-label">Tableau de bord</span>
                </a>

                {{-- Sinistres (sous-menu) --}}
                @php $isSinistreSection = request()->routeIs('assure.sinistres.*') && !request()->routeIs('assure.sinistres.historique'); @endphp
                <div x-data="{ open: {{ $isSinistreSection ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="nav-item {{ $isSinistreSection ? 'active' : '' }}"
                        data-tip="Mes Sinistres">
                        <div class="nav-icon"><i class="fa-solid fa-bolt"></i></div>
                        <span class="nav-label">Mes Sinistres</span>
                        @if(isset($countMesSinistresTotal) && $countMesSinistresTotal > 0)
                            <span class="nav-badge">{{ $countMesSinistresTotal }}</span>
                        @endif
                        <i class="nav-chevron fa-solid fa-chevron-right text-[10px] text-slate-300 ml-auto transition-transform duration-200"
                            :class="open ? 'rotate-90' : ''"></i>
                    </button>

                    <div class="subnav-group" x-show="open" x-collapse x-cloak>
                        <div class="mt-0.5 space-y-0.5">
                            <a href="{{ route('assure.sinistres.create') }}"
                                class="subnav-item {{ request()->routeIs('assure.sinistres.create') ? 'active' : '' }}">
                                <span class="subnav-dot"></span> Déclarer
                            </a>
                            <a href="{{ route('assure.sinistres.en_attente') }}"
                                class="subnav-item {{ request()->routeIs('assure.sinistres.en_attente') ? 'active' : '' }}">
                                <span class="subnav-dot"></span> En attente
                                @if(isset($countSuivi) && $countSuivi > 0)
                                    <span class="ml-auto text-[10px] font-bold text-slate-400">{{ $countSuivi }}</span>
                                @endif
                            </a>
                            <a href="{{ route('assure.sinistres.en_cours') }}"
                                class="subnav-item {{ request()->routeIs('assure.sinistres.en_cours') ? 'active' : '' }}">
                                <span class="subnav-dot"></span> En cours
                                @if(isset($countEnCours) && $countEnCours > 0)
                                    <span class="ml-auto text-[10px] font-bold text-slate-400">{{ $countEnCours }}</span>
                                @endif
                            </a>
                            <a href="{{ route('assure.sinistres.documents') }}"
                                class="subnav-item {{ request()->routeIs('assure.sinistres.documents') ? 'active' : '' }}">
                                <span class="subnav-dot"></span> Mes pièces
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Assurances --}}
                <a href="{{ route('assure.contrats.index') }}"
                    class="nav-item {{ request()->routeIs('assure.contrats.*') ? 'active' : '' }}"
                    data-tip="Mes Assurances">
                    <div class="nav-icon"><i class="fa-solid fa-file-shield"></i></div>
                    <span class="nav-label">Mes Assurances</span>
                </a>

                {{-- Constats prêts --}}
                <a href="{{ route('assure.constats.prets') }}"
                    class="nav-item {{ request()->routeIs('assure.constats.*') ? 'active' : '' }}"
                    data-tip="Constats Prêts">
                    <div class="nav-icon"><i class="fa-solid fa-file-invoice"></i></div>
                    <span class="nav-label">Constats Prêts</span>
                    @if(isset($countConstatsNonRegles) && $countConstatsNonRegles > 0)
                        <span class="nav-badge">{{ $countConstatsNonRegles }}</span>
                    @endif
                </a>

                {{-- Historique --}}
                <a href="{{ route('assure.sinistres.historique') }}"
                    class="nav-item {{ request()->routeIs('assure.sinistres.historique') ? 'active' : '' }}"
                    data-tip="Historique">
                    <div class="nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <span class="nav-label">Historique</span>
                </a>
            </div>

            {{-- Divider --}}
            <div class="sidebar-divider h-px bg-slate-100"></div>

            {{-- Assistance --}}
            <div class="space-y-0.5">
                <p class="nav-section-label">Assistance</p>
                <a href="{{ route('assure.support') }}"
                    class="nav-item {{ request()->routeIs('assure.support') ? 'active' : '' }}"
                    data-tip="Support 24/7">
                    <div class="nav-icon"><i class="fa-solid fa-headset"></i></div>
                    <span class="nav-label">Support 24/7</span>
                </a>
            </div>

        </div>

        {{-- Profile footer --}}
        <div class="sidebar-profile">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="sidebar-profile-card">
                    <div class="avatar-ring">
                        @if(auth('user')->user()->profile_picture)
                            <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(auth('user')->user()->name ?? 'A', 0, 1)) }}
                        @endif
                    </div>
                    <div class="profile-text flex-1 min-w-0 text-left">
                        <p class="text-[12.5px] font-bold text-slate-700 truncate leading-tight">{{ auth('user')->user()->name }} {{ auth('user')->user()->prenom }}</p>
                        <p class="text-[10px] font-semibold text-slate-400 truncate">{{ auth('user')->user()->code_user ?? 'Assuré' }}</p>
                    </div>
                    <i class="profile-dots fa-solid fa-ellipsis-vertical text-slate-300 text-xs" :class="open ? 'text-indigo-400' : ''"></i>
                </button>

                <div x-show="open" @click.outside="open = false" x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="absolute bottom-full left-0 right-0 mb-2 bg-white border border-slate-200 rounded-2xl shadow-xl shadow-slate-200/60 overflow-hidden py-1 z-50">
                    <a href="{{ route('assure.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-[13px] font-semibold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                        <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs"><i class="fa-solid fa-user-pen"></i></div> Mon Profil
                    </a>
                    <a href="{{ route('assure.password.change') }}" class="flex items-center gap-3 px-4 py-2.5 text-[13px] font-semibold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                        <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs"><i class="fa-solid fa-key"></i></div> Sécurité
                    </a>
                    <div class="h-px bg-slate-100 my-1"></div>
                    <form action="{{ route('assure.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-[13px] font-semibold text-rose-500 hover:bg-rose-50 transition-colors">
                            <div class="w-7 h-7 rounded-lg bg-rose-50 flex items-center justify-center text-xs"><i class="fa-solid fa-right-from-bracket"></i></div> Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- ════════════════════════════════
         MAIN CONTAINER
    ════════════════════════════════ --}}
    <div id="cm-main" :class="mainClass">

        {{-- ─── TOPBAR ─── --}}
        <header id="cm-topbar">
            {{-- Left --}}
            <div class="flex items-center gap-4">
                {{-- Toggle button --}}
                <button @click="sidebarOpen = !sidebarOpen"
                    class="topbar-btn" title="Menu">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
                {{-- Page title --}}
                <div class="hidden sm:block">
                    <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">
                        <i class="fa-solid fa-shield-halved text-indigo-400"></i>
                        <span>Espace Assuré</span>
                    </div>
                    <h1 class="text-[16px] font-black text-slate-800 tracking-tight leading-none">@yield('page-title', 'Tableau de bord')</h1>
                </div>
            </div>

            {{-- Right --}}
            <div class="flex items-center gap-3">
                {{-- Search --}}
                <div class="topbar-search hidden lg:flex">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Rechercher un dossier, un sinistre...">
                </div>

                {{-- Notifications --}}
                <button class="topbar-btn relative" title="Notifications">
                    <i class="fa-regular fa-bell text-base"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 border-2 border-white rounded-full"></span>
                </button>

                {{-- Vertical divider --}}
                <div class="w-px h-7 bg-slate-200 hidden sm:block"></div>

                {{-- Profile dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-2.5 py-1.5 pl-1.5 pr-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-[13px] font-black shadow-sm overflow-hidden">
                            @if(auth('user')->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr(auth('user')->user()->name ?? 'A', 0, 1)) }}
                            @endif
                        </div>
                        <span class="hidden md:block text-[13px] font-bold text-slate-700">{{ auth('user')->user()->name }}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                        class="absolute right-0 top-full mt-2 w-64 bg-white border border-slate-200 rounded-2xl shadow-xl shadow-slate-200/60 z-50 overflow-hidden py-1.5">

                        {{-- Header --}}
                        <div class="px-4 py-3 bg-gradient-to-r from-indigo-50 to-violet-50 border-b border-slate-100 mb-1">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-black text-[14px] shadow-sm overflow-hidden">
                                    @if(auth('user')->user()->profile_picture)
                                        <img src="{{ asset('storage/' . auth('user')->user()->profile_picture) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr(auth('user')->user()->name ?? 'A', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-black text-[13px] text-slate-800 truncate">{{ auth('user')->user()->name }} {{ auth('user')->user()->prenom }}</p>
                                    <p class="text-[11px] text-indigo-500 font-bold truncate">{{ auth('user')->user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('assure.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-[13px] font-semibold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                            <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs"><i class="fa-solid fa-user-pen"></i></div>
                            Mon Profil
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-[13px] font-semibold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                            <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs"><i class="fa-solid fa-lock"></i></div>
                            Sécurité
                        </a>

                        <div class="h-px bg-slate-100 my-1 mx-3"></div>

                        <form action="{{ route('assure.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-[13px] font-semibold text-rose-500 hover:bg-rose-50 transition-colors">
                                <div class="w-7 h-7 rounded-lg bg-rose-50 flex items-center justify-center text-xs"><i class="fa-solid fa-right-from-bracket"></i></div>
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- ─── PAGE CONTENT ─── --}}
        <main id="cm-content">
            @yield('content')
        </main>
    </div>

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen && isMobile"
        x-transition:enter="transition-opacity ease duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm lg:hidden"
        x-cloak></div>

</div>

{{-- Scripts --}}
@stack('scripts')
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success', title: 'Succès !',
            text: @json(session('success')),
            confirmButtonText: 'OK', confirmButtonColor: '#6366f1',
            customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-5 py-2 font-bold' }
        });
    @endif
    @if(session('error'))
        Swal.fire({
            icon: 'error', title: 'Erreur',
            text: @json(session('error')),
            confirmButtonText: 'Fermer', confirmButtonColor: '#ef4444',
            customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-5 py-2 font-bold' }
        });
    @endif
</script>
</body>
</html>