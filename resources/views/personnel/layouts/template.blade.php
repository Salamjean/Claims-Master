<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Personnel') — Claims Master</title>

    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#1d3557',
                            50: '#eef2f8',
                            100: '#d0daf0',
                            600: '#1d3557',
                            700: '#152840',
                        },
                        accent: {
                            DEFAULT: '#457b9d',
                            light: '#a8dadc',
                        }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
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

        /* Sidebar */
        #sidebar {
            width: 260px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(180deg, #1d3557 0%, #152840 100%);
        }

        #sidebar.collapsed {
            width: 80px;
        }

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
            color: rgba(255, 255, 255, 0.65);
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.10);
            color: #fff;
        }

        .nav-item.active {
            background: rgba(69, 123, 157, 0.25);
            color: #fff;
            border-left: 3px solid #a8dadc;
        }

        .nav-item .nav-icon {
            min-width: 20px;
            text-align: center;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #1d355744;
            border-radius: 10px;
        }

        .card-stat {
            background: white;
            border-radius: 16px;
            padding: 20px 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.07), 0 4px 16px rgba(0, 0, 0, 0.04);
            transition: box-shadow 0.2s ease;
        }

        .card-stat:hover {
            box-shadow: 0 4px 20px rgba(29, 53, 87, 0.12);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeUp 0.4s ease-out both;
        }
    </style>

    @stack('styles')
</head>

<body class="flex h-screen overflow-hidden bg-slate-50" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">

    {{-- Sidebar --}}
    @include('personnel.layouts.sidebar')

    {{-- Backdrop mobile --}}
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden" x-cloak>
    </div>

    <div id="main-content" class="flex flex-col flex-1 overflow-hidden h-full">
        @include('personnel.layouts.navbar')
        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès !',
                text: @json(session('success')),
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: @json(session('error')),
                timer: 4000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif
    </script>
</body>

</html>
