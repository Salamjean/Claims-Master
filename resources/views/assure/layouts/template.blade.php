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
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        [x-cloak] {
            display: none !important;
        }

        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            background: #f8fafc;
            color: #0f172a;
        }

        /* ── LAYOUT ── */
        .app-wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Quand la sidebar est ouverte sur desktop, on décale le contenu */
        @media (min-width: 1024px) {
            .main-content-shifted {
                margin-left: 288px; /* w-72 */
            }
            .main-content-collapsed {
                margin-left: 80px; /* w-20 */
            }
        }

        /* ── TOPNAV ── */
        #topnav {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            height: 64px;
            z-index: 40;
            flex-shrink: 0;
        }

        .nav-container {
            max-width: 100%;
            padding: 0 32px;
            height: 66px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        /* Logo */
        .logo-mark {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            flex-shrink: 0;
        }

        /* Nav links */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2px;
            flex: 1;
            padding: 0 28px;
        }

        .nav-pill {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.65);
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.18s ease;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .nav-pill:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.12);
        }

        .nav-pill.active {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .nav-pill.active i {
            color: #aed953;
        }

        /* Right zone */
        .nav-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .code-tag {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            font-family: 'Courier New', monospace;
            font-size: 11.5px;
            font-weight: 700;
            color: #aed953;
            letter-spacing: .04em;
        }

        .user-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 14px 5px 5px;
            border-radius: 40px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            cursor: pointer;
            transition: all .18s;
        }

        .user-btn:hover {
            background: rgba(255, 255, 255, 0.18);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
        }

        /* Dropdown */
        .user-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            width: 220px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            z-index: 200;
        }

        .dd-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            font-size: 13.5px;
            color: #475569;
            text-decoration: none;
            transition: background .14s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .dd-item:hover {
            background: #f8fafc;
            color: #1e293b;
        }

        .dd-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        /* ── PAGE CONTENT ── */
        .page-content {
            flex: 1;
            overflow-y: auto;
            padding: 32px;
            background: #f8fafc;
        }

        /* ── CARDS ── */
        .card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e8edf5;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            transition: box-shadow .22s ease, transform .22s ease;
        }

        .card:hover {
            box-shadow: 0 8px 32px rgba(36, 58, 143, 0.1);
        }

        .stat-card {
            border-radius: 18px;
            padding: 22px 24px;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
        }

        .stat-card-primary {
            background: linear-gradient(135deg, #243a8f 0%, #1c2e72 100%);
            color: #fff;
        }

        .stat-card-light {
            background: #fff;
            border: 1px solid #e8edf5;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }

        /* ── BADGE ── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .badge-green {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #d1fae5;
        }

        .badge-yellow {
            background: #fffbeb;
            color: #d17800;
            border: 1px solid #fef3c7;
        }

        .badge-red {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fee2e2;
        }

        .badge-gray {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fu {
            animation: fadeUp .45s ease-out both;
        }

        .d1 {
            animation-delay: .05s;
        }

        .d2 {
            animation-delay: .1s;
        }

        .d3 {
            animation-delay: .15s;
        }

        .d4 {
            animation-delay: .2s;
        }

        .d5 {
            animation-delay: .25s;
        }

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9px;
        }
    </style>

    @stack('styles')
</head>

<body>

    <div class="app-wrapper" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" @resize.window="if(window.innerWidth < 1024) sidebarOpen = false">
        {{-- SIDEBAR --}}
        @include('assure.layouts.sidebar')

        <div class="main-container" :class="window.innerWidth >= 1024 ? (sidebarOpen ? 'main-content-shifted' : 'main-content-collapsed') : ''">
            {{-- TOPNAV --}}
            @include('assure.layouts.navbar')

            {{-- PAGE CONTENT --}}
            <div class="page-content">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success', title: 'Succès !',
                text: @json(session('success')),
                confirmButtonText: 'OK', confirmButtonColor: '#7cb604',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-5 py-2' }
            });
        @endif
        @if(session('error'))
            Swal.fire({
                icon: 'error', title: 'Erreur',
                text: @json(session('error')),
                confirmButtonText: 'Fermer', confirmButtonColor: '#ef4444',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-5 py-2' }
            });
        @endif
    </script>
</body>

</html>