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
            --sidebar-w-open: 256px;
            --sidebar-w-closed: 72px;
            --topbar-h: 64px;
            --accent: #2563eb;
            --accent-light: #eff6ff;
            --accent-mid: #bfdbfe;
        }

        * { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        body { background: #f8fafc; color: #0f172a; overflow: hidden; }

        /* ─── SCROLLBAR ─── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ─── LAYOUT ─── */
        :root { --sidebar-gap: 12px; }
        .app-shell { display: flex; height: 100vh; overflow: hidden; }

        /* Sidebar flottante, détachée des bords */
        #cm-sidebar {
            position: fixed; z-index: 50;
            top: var(--sidebar-gap); bottom: var(--sidebar-gap); left: var(--sidebar-gap);
            display: flex; flex-direction: column;
            width: var(--sidebar-w-open);
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 20px;
            box-shadow: 0 20px 40px -20px rgba(15, 23, 42, .45);
            overflow: hidden;
            transition: width .25s ease, transform .25s ease;
        }
        #cm-sidebar.collapsed { width: var(--sidebar-w-closed); overflow: visible; }
        #cm-sidebar.collapsed .sidebar-nav { overflow: visible; }

        #cm-main {
            flex: 1; display: flex; flex-direction: column; min-width: 0;
            margin-left: calc(var(--sidebar-w-open) + var(--sidebar-gap));
            transition: margin-left .25s ease;
        }
        #cm-main.collapsed { margin-left: calc(var(--sidebar-w-closed) + var(--sidebar-gap)); }

        @media (max-width: 1023px) {
            #cm-sidebar, #cm-sidebar.collapsed { width: var(--sidebar-w-open); transform: translateX(calc(-100% - 2 * var(--sidebar-gap))); }
            #cm-sidebar.mobile-open { transform: translateX(0); }
            #cm-main, #cm-main.collapsed { margin-left: 0; }
        }

        #cm-topbar {
            height: var(--topbar-h); flex-shrink: 0;
            position: sticky; top: 0; z-index: 40;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            margin-top: var(--sidebar-gap);
            padding: 0 28px;
            background: #f8fafc;
        }

        #cm-content { flex: 1; overflow-y: auto; padding: 8px 28px 28px; background: #f8fafc; }
        @media (max-width: 640px) {
            #cm-topbar { padding: 0 16px; margin-top: 0; }
            #cm-content { padding: 8px 16px 20px; }
        }

        /* ─── NAVIGATION (sidebar sombre) ─── */
        .nav-section-label {
            padding: 0 12px; margin-bottom: 8px;
            font-size: 11px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
            color: #475569; white-space: nowrap;
        }
        .nav-item {
            position: relative;
            display: flex; align-items: center; gap: 12px;
            width: 100%; height: 40px; padding: 0 12px;
            border-radius: 10px;
            font-size: 13.5px; font-weight: 500; color: #94a3b8;
            text-decoration: none; text-align: left; white-space: nowrap;
            background: none; border: 0; outline: none; cursor: pointer;
            transition: background-color .15s, color .15s;
        }
        .nav-item:hover { background: rgba(255, 255, 255, .06); color: #f1f5f9; }
        .nav-item:focus-visible { box-shadow: 0 0 0 2px #3b82f6; }
        .nav-item.active { background: var(--accent); color: #fff; font-weight: 600; }
        .nav-icon { width: 18px; text-align: center; font-size: 15px; color: #64748b; flex-shrink: 0; transition: color .15s; }
        .nav-item:hover .nav-icon { color: #e2e8f0; }
        .nav-item.active .nav-icon { color: #fff; }
        .nav-label { flex: 1; overflow: hidden; text-overflow: ellipsis; }
        .nav-badge {
            min-width: 20px; height: 20px; padding: 0 6px; border-radius: 999px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 600; background: rgba(248, 113, 113, .15); color: #fca5a5; flex-shrink: 0;
        }
        .nav-item.active .nav-badge { background: rgba(255, 255, 255, .2); color: #fff; }
        .nav-chevron { font-size: 10px; color: #64748b; transition: transform .2s; }
        .nav-item.active .nav-chevron { color: #fff; }

        .subnav-group { margin: 4px 0 6px 21px; padding-left: 12px; border-left: 1px solid #1e293b; }
        .subnav-item {
            position: relative;
            display: flex; align-items: center; gap: 8px;
            height: 34px; padding: 0 10px; border-radius: 8px;
            font-size: 13px; font-weight: 500; color: #94a3b8;
            text-decoration: none; white-space: nowrap;
            transition: background-color .15s, color .15s;
        }
        .subnav-item:hover { background: rgba(255, 255, 255, .06); color: #f1f5f9; }
        .subnav-item.active { color: #fff; font-weight: 600; background: rgba(255, 255, 255, .06); }
        .subnav-item.active::before {
            content: ''; position: absolute; left: -13px; top: 8px; bottom: 8px;
            width: 2px; border-radius: 2px; background: #3b82f6;
        }
        .subnav-count { margin-left: auto; font-size: 11px; font-weight: 600; color: #64748b; }

        #cm-sidebar .topbar-btn { color: #94a3b8; }
        #cm-sidebar .topbar-btn:hover { background: rgba(255, 255, 255, .06); color: #fff; }

        /* ─── AVATAR ─── */
        .avatar {
            display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;
            background: var(--accent); color: #fff; font-weight: 600; border-radius: 999px;
        }

        /* ─── TOPBAR ─── */
        .topbar-btn {
            width: 38px; height: 38px; border-radius: 8px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            color: #64748b; background: transparent; border: 0; cursor: pointer;
            transition: background-color .15s, color .15s;
        }
        .topbar-btn:hover { background: #f1f5f9; color: #0f172a; }
        .topbar-search {
            display: flex; align-items: center; gap: 10px;
            height: 38px; width: 280px; padding: 0 12px;
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
            transition: border-color .15s, box-shadow .15s, background-color .15s;
        }
        .topbar-search:focus-within { background: #fff; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37, 99, 235, .1); }
        .topbar-search input { flex: 1; min-width: 0; background: transparent; border: 0; outline: none; font-size: 13px; color: #0f172a; }
        .topbar-search input::placeholder { color: #94a3b8; }

        .menu-panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 10px 30px -10px rgba(15, 23, 42, .15); padding: 6px; }
        .menu-item {
            display: flex; align-items: center; gap: 10px; width: 100%;
            padding: 8px 10px; border-radius: 6px;
            font-size: 13px; font-weight: 500; color: #334155; text-decoration: none; text-align: left;
            background: none; border: 0; cursor: pointer; transition: background-color .15s;
        }
        .menu-item:hover { background: #f1f5f9; }
        .menu-item i { width: 16px; text-align: center; color: #94a3b8; }
        .menu-item.danger { color: #dc2626; }
        .menu-item.danger i { color: #dc2626; }
        .menu-item.danger:hover { background: #fef2f2; }

        /* ─── UTILITAIRES CONSERVÉS POUR LES PAGES ─── */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .fu { animation: fadeUp 0.4s ease-out both; }
        .d1 { animation-delay: 0.05s; } .d2 { animation-delay: 0.1s; }
        .d3 { animation-delay: 0.15s; } .d4 { animation-delay: 0.2s; }
        .card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; }

        /* ─── SIDEBAR RÉDUITE ─── */
        #cm-sidebar.collapsed .hide-collapsed,
        #cm-sidebar.collapsed .nav-label,
        #cm-sidebar.collapsed .nav-badge,
        #cm-sidebar.collapsed .nav-chevron,
        #cm-sidebar.collapsed .subnav-group { display: none !important; }
        #cm-sidebar.collapsed .nav-section-label { visibility: hidden; height: 0; margin: 0; }
        #cm-sidebar.collapsed .nav-item,
        #cm-sidebar.collapsed .cta-btn { justify-content: center; padding: 0; gap: 0; }
        #cm-sidebar.collapsed .sidebar-brand { justify-content: center; padding: 0; }
        #cm-sidebar.collapsed .profile-btn { justify-content: center; padding: 6px; }

        #cm-sidebar.collapsed [data-tip]::after {
            content: attr(data-tip);
            position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
            padding: 6px 10px; border-radius: 6px;
            background: #0f172a; color: #fff; font-size: 12px; font-weight: 500; white-space: nowrap;
            opacity: 0; pointer-events: none; transition: opacity .15s; z-index: 100;
        }
        #cm-sidebar.collapsed [data-tip]:hover::after { opacity: 1; }
        @media (max-width: 1023px) {
            #cm-sidebar [data-tip]::after { display: none; }
        }
    </style>

    @stack('styles')
</head>

<body>

@php
    $authUser = auth('user')->user();
    $initials = strtoupper(mb_substr($authUser->name ?? 'A', 0, 1) . mb_substr($authUser->prenom ?? '', 0, 1));
    $isSinistreSection = request()->routeIs('assure.sinistres.*') && !request()->routeIs('assure.sinistres.historique');
@endphp

<div class="app-shell" x-data="{
    sidebarOpen: true,
    isMobile: window.innerWidth < 1024,
    init() {
        if (this.isMobile) { this.sidebarOpen = false; return; }
        try { this.sidebarOpen = localStorage.getItem('cm-assure-sidebar') !== 'collapsed'; } catch (e) {}
    },
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        if (!this.isMobile) {
            try { localStorage.setItem('cm-assure-sidebar', this.sidebarOpen ? 'open' : 'collapsed'); } catch (e) {}
        }
    },
    get sidebarClass() {
        if (this.isMobile) return this.sidebarOpen ? 'mobile-open' : '';
        return this.sidebarOpen ? '' : 'collapsed';
    },
    get mainClass() {
        return (!this.isMobile && !this.sidebarOpen) ? 'collapsed' : '';
    }
}" @resize.window.debounce.150ms="
    const mobile = window.innerWidth < 1024;
    if (mobile !== isMobile) { isMobile = mobile; sidebarOpen = !mobile; }
" @keydown.escape.window="if (isMobile) sidebarOpen = false">

    {{-- ════════════════ SIDEBAR ════════════════ --}}
    <aside id="cm-sidebar" :class="sidebarClass">

        {{-- Marque --}}
        <div class="sidebar-brand flex items-center gap-3 h-[72px] px-5 shrink-0">
            <a href="{{ route('assure.dashboard') }}" class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-shield-halved text-white text-[15px]"></i>
            </a>
            <div class="hide-collapsed min-w-0 flex-1">
                <p class="text-[15px] font-bold text-white leading-tight tracking-tight">Claims Master</p>
                <p class="text-xs text-slate-400 leading-tight">Espace assuré</p>
            </div>
            <button type="button" @click="sidebarOpen = false" class="topbar-btn lg:hidden -mr-2" title="Fermer le menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Action principale --}}
        <div class="px-3 pb-3 shrink-0">
            <a href="{{ route('assure.sinistres.create') }}" data-tip="Déclarer un sinistre"
                class="cta-btn relative flex items-center justify-center gap-2 h-10 px-3 rounded-[10px] bg-white hover:bg-slate-100 text-slate-900 text-[13px] font-semibold transition-colors">
                <i class="fa-solid fa-plus text-xs"></i>
                <span class="hide-collapsed whitespace-nowrap">Déclarer un sinistre</span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav flex-1 overflow-y-auto overflow-x-hidden px-3 py-2 space-y-6" style="scrollbar-width: thin;">
            <div class="space-y-0.5">
                <p class="nav-section-label">Menu</p>

                <a href="{{ route('assure.dashboard') }}" data-tip="Tableau de bord"
                    class="nav-item {{ request()->routeIs('assure.dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fa-solid fa-house"></i>
                    <span class="nav-label">Tableau de bord</span>
                </a>

                <div x-data="{ open: {{ $isSinistreSection ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open" data-tip="Mes sinistres"
                        class="nav-item {{ $isSinistreSection ? 'active' : '' }}" :aria-expanded="open">
                        <i class="nav-icon fa-solid fa-car-burst"></i>
                        <span class="nav-label">Mes sinistres</span>
                        @if(isset($countMesSinistresTotal) && $countMesSinistresTotal > 0)
                            <span class="nav-badge">{{ $countMesSinistresTotal }}</span>
                        @endif
                        <i class="nav-chevron fa-solid fa-chevron-down" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div class="subnav-group" x-show="open" x-collapse x-cloak>
                        <a href="{{ route('assure.sinistres.create') }}"
                            class="subnav-item {{ request()->routeIs('assure.sinistres.create') ? 'active' : '' }}">Déclarer</a>
                        <a href="{{ route('assure.sinistres.en_attente') }}"
                            class="subnav-item {{ request()->routeIs('assure.sinistres.en_attente') ? 'active' : '' }}">
                            En attente
                            @if(isset($countSuivi) && $countSuivi > 0)<span class="subnav-count">{{ $countSuivi }}</span>@endif
                        </a>
                        <a href="{{ route('assure.sinistres.en_cours') }}"
                            class="subnav-item {{ request()->routeIs('assure.sinistres.en_cours') ? 'active' : '' }}">
                            En cours
                            @if(isset($countEnCours) && $countEnCours > 0)<span class="subnav-count">{{ $countEnCours }}</span>@endif
                        </a>
                        <a href="{{ route('assure.sinistres.documents') }}"
                            class="subnav-item {{ request()->routeIs('assure.sinistres.documents') ? 'active' : '' }}">Mes pièces</a>
                    </div>
                </div>

                <a href="{{ route('assure.contrats.index') }}" data-tip="Mes assurances"
                    class="nav-item {{ request()->routeIs('assure.contrats.*') ? 'active' : '' }}">
                    <i class="nav-icon fa-solid fa-file-shield"></i>
                    <span class="nav-label">Mes assurances</span>
                </a>

                <a href="{{ route('assure.constats.prets') }}" data-tip="Constats prêts"
                    class="nav-item {{ request()->routeIs('assure.constats.*') ? 'active' : '' }}">
                    <i class="nav-icon fa-solid fa-file-invoice"></i>
                    <span class="nav-label">Constats prêts</span>
                    @if(isset($countConstatsNonRegles) && $countConstatsNonRegles > 0)
                        <span class="nav-badge">{{ $countConstatsNonRegles }}</span>
                    @endif
                </a>

                <a href="{{ route('assure.sinistres.historique') }}" data-tip="Historique"
                    class="nav-item {{ request()->routeIs('assure.sinistres.historique') ? 'active' : '' }}">
                    <i class="nav-icon fa-solid fa-clock-rotate-left"></i>
                    <span class="nav-label">Historique</span>
                </a>
            </div>

            <div class="space-y-0.5">
                <p class="nav-section-label">Aide</p>
                <a href="{{ route('assure.support') }}" data-tip="Support 24/7"
                    class="nav-item {{ request()->routeIs('assure.support') ? 'active' : '' }}">
                    <i class="nav-icon fa-solid fa-headset"></i>
                    <span class="nav-label">Support 24/7</span>
                </a>
            </div>
        </nav>

        {{-- Profil --}}
        <div class="p-3 shrink-0">
            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = !open" data-tip="{{ $authUser->name }}"
                    class="profile-btn relative w-full flex items-center gap-3 p-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 transition-colors text-left">
                    <span class="avatar w-9 h-9 text-[13px]">
                        @if($authUser->profile_picture)
                            <img src="{{ asset('storage/' . $authUser->profile_picture) }}" alt="" class="w-full h-full object-cover">
                        @else
                            {{ $initials }}
                        @endif
                    </span>
                    <span class="hide-collapsed flex-1 min-w-0">
                        <span class="block text-[13px] font-semibold text-white truncate">{{ $authUser->name }} {{ $authUser->prenom }}</span>
                        <span class="block text-xs text-slate-400 truncate">{{ $authUser->code_user ?? 'Assuré' }}</span>
                    </span>
                    <i class="hide-collapsed fa-solid fa-chevron-up text-[10px] text-slate-500 transition-transform" :class="open ? '' : 'rotate-180'"></i>
                </button>

                <div x-show="open" @click.outside="open = false" x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="menu-panel absolute bottom-full left-0 mb-2 w-60 z-50">
                    <a href="{{ route('assure.profile') }}" class="menu-item"><i class="fa-regular fa-user"></i> Mon profil</a>
                    <a href="{{ route('assure.password.change') }}" class="menu-item"><i class="fa-solid fa-key"></i> Sécurité</a>
                    <div class="h-px bg-slate-100 my-1"></div>
                    <form action="{{ route('assure.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="menu-item danger"><i class="fa-solid fa-arrow-right-from-bracket"></i> Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- ════════════════ CONTENU ════════════════ --}}
    <div id="cm-main" :class="mainClass">

        {{-- Navbar --}}
        <header id="cm-topbar">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" @click="toggleSidebar()" class="topbar-btn -ml-2"
                    :title="sidebarOpen ? 'Réduire le menu' : 'Ouvrir le menu'">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="hidden sm:block w-px h-6 bg-slate-200"></div>
                <nav class="flex items-center gap-2 min-w-0 text-[13px]" aria-label="Fil d'Ariane">
                    <a href="{{ route('assure.dashboard') }}" class="hidden sm:inline text-slate-500 hover:text-slate-900 transition-colors whitespace-nowrap">Espace assuré</a>
                    <i class="hidden sm:inline fa-solid fa-chevron-right text-[9px] text-slate-300"></i>
                    <h1 class="font-semibold text-slate-900 truncate">@yield('page-title', 'Tableau de bord')</h1>
                </nav>
            </div>

            <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                <label class="topbar-search hidden lg:flex">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs"></i>
                    <input type="search" placeholder="Rechercher un dossier, un sinistre…">
                </label>

                <a href="{{ route('assure.support') }}" class="topbar-btn" title="Aide & support">
                    <i class="fa-regular fa-circle-question text-[17px]"></i>
                </a>
                <button type="button" class="topbar-btn" title="Notifications">
                    <i class="fa-regular fa-bell text-[17px]"></i>
                </button>

                <div class="w-px h-6 bg-slate-200 mx-1 hidden sm:block"></div>

                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open"
                        class="flex items-center gap-2.5 h-10 pl-1 pr-2 rounded-lg hover:bg-slate-100 transition-colors">
                        <span class="avatar w-8 h-8 text-xs">
                            @if($authUser->profile_picture)
                                <img src="{{ asset('storage/' . $authUser->profile_picture) }}" alt="" class="w-full h-full object-cover">
                            @else
                                {{ $initials }}
                            @endif
                        </span>
                        <span class="hidden md:block text-[13px] font-medium text-slate-700 max-w-[140px] truncate">{{ $authUser->name }}</span>
                        <i class="hidden md:block fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="menu-panel absolute right-0 top-full mt-2 w-64 z-50">
                        <div class="flex items-center gap-3 px-2.5 py-2.5 mb-1 border-b border-slate-100">
                            <span class="avatar w-10 h-10 text-sm">
                                @if($authUser->profile_picture)
                                    <img src="{{ asset('storage/' . $authUser->profile_picture) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    {{ $initials }}
                                @endif
                            </span>
                            <div class="min-w-0">
                                <p class="text-[13px] font-semibold text-slate-900 truncate">{{ $authUser->name }} {{ $authUser->prenom }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $authUser->email }}</p>
                            </div>
                        </div>
                        <a href="{{ route('assure.profile') }}" class="menu-item"><i class="fa-regular fa-user"></i> Mon profil</a>
                        <a href="{{ route('assure.password.change') }}" class="menu-item"><i class="fa-solid fa-key"></i> Sécurité</a>
                        <a href="{{ route('assure.contrats.index') }}" class="menu-item"><i class="fa-solid fa-file-shield"></i> Mes assurances</a>
                        <div class="h-px bg-slate-100 my-1"></div>
                        <form action="{{ route('assure.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="menu-item danger"><i class="fa-solid fa-arrow-right-from-bracket"></i> Déconnexion</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main id="cm-content">
            @yield('content')
        </main>
    </div>

    {{-- Fond mobile --}}
    <div x-show="sidebarOpen && isMobile" x-cloak @click="sidebarOpen = false"
        x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-slate-900/30 lg:hidden"></div>

</div>

{{-- Partial Modal Agent Map Tracking --}}
@include('assure.partials.agent_map_modal')

{{-- Scripts --}}
@stack('scripts')
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success', title: 'Succès !',
            text: @json(session('success')),
            confirmButtonText: 'OK', confirmButtonColor: '#2563eb',
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