<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Agent') — Claims Master</title>

    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#1e3a8a',
                            50: '#eff6ff',
                            100: '#dbeafe',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            font-family: 'Outfit', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        body {
            background-color: #f8fafc;
        }

        /* Sidebar Desktop behavior */
        #sidebar {
            width: 260px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #sidebar.collapsed {
            width: 80px;
        }

        /* Nav label handling */
        #sidebar.collapsed .nav-label {
            opacity: 0;
            width: 0;
            display: none;
        }

        #sidebar.collapsed .logo-text {
            display: none;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.6);
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .nav-item.active {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border-left: 3px solid #3b82f6;
        }

        #main-content {
            flex: 1;
            min-width: 0;
        }

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        /* Mobile specific overlay */
        @media (max-width: 1023px) {
            #sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                z-index: 50;
                transform: translateX(-100%);
            }
            #sidebar.open {
                transform: translateX(0);
            }
            #sidebar.collapsed {
                width: 260px; /* Reset on mobile */
            }
        }
    </style>
    @stack('styles')
</head>

<body class="flex h-screen overflow-hidden bg-slate-50" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">

    {{-- Sidebar (Include) --}}
    @include('agent.layouts.sidebar')

    {{-- Backdrop for mobile --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden" 
         x-cloak>
    </div>

    <div id="main-content" class="flex flex-col flex-1 overflow-hidden h-full">
        @include('agent.layouts.navbar')

        <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>