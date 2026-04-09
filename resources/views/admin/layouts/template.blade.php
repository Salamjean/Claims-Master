<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Claims Master</title>

    <!-- Alpine.js (pour les dropdowns) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#243a8f',
                            50: '#eef1fb',
                            100: '#d4daf5',
                            600: '#243a8f',
                            700: '#1c2e72',
                            800: '#142258',
                        },
                        secondary: {
                            DEFAULT: '#7cb604',
                            50: '#f3fae0',
                            100: '#e3f5b0',
                            500: '#7cb604',
                            600: '#6a9c03',
                        },
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * {
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: #f1f5f9;
        }

        /* Sidebar */
        #sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #243a8f 0%, #1c2e72 100%);
            transition: width 0.3s ease;
            flex-shrink: 0;
        }

        #sidebar.collapsed {
            width: 72px;
        }

        #sidebar .nav-label {
            transition: opacity 0.2s ease;
        }

        #sidebar.collapsed .nav-label {
            opacity: 0;
            pointer-events: none;
            width: 0;
            overflow: hidden;
        }

        #sidebar.collapsed .logo-text {
            display: none;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .nav-item.active {
            background: rgba(124, 182, 4, 0.2);
            border-left: 3px solid #7cb604;
        }

        .nav-item .nav-icon {
            min-width: 20px;
            text-align: center;
        }

        /* Content */
        #main-content {
            flex: 1;
            overflow-y: auto;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #243a8f44;
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
            box-shadow: 0 4px 20px rgba(36, 58, 143, 0.12);
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
            animation: fadeUp 0.45s ease-out both;
        }
    </style>

    @stack('styles')
</head>

<body class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    @include('admin.layouts.sidebar')

    <div id="main-content" class="flex flex-col flex-1 overflow-hidden">

        {{-- NAVBAR --}}
        @include('admin.layouts.navbar')

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>

    </div>

    @stack('scripts')
    <script>
        // Toggle sidebar collapse
        const toggleBtn = document.getElementById('toggle-sidebar');
        const sidebar = document.getElementById('sidebar');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
            });
        }

        // SweetAlert2 — Messages flash globaux
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès !',
                text: @json(session('success')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#7cb604',
                showClass: { popup: 'animate__animated animate__fadeInDown' },
                hideClass: { popup: 'animate__animated animate__fadeOutUp' },
                customClass: {
                    popup: 'rounded-2xl',
                    title: 'font-bold text-slate-800',
                    confirmButton: 'rounded-xl px-6 py-2 text-white font-semibold',
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: @json(session('error')),
                confirmButtonText: 'Fermer',
                confirmButtonColor: '#ef4444',
                customClass: {
                    popup: 'rounded-2xl',
                    title: 'font-bold text-slate-800',
                    confirmButton: 'rounded-xl px-6 py-2 text-white font-semibold',
                }
            });
        @endif
    </script>
</body>

</html>