<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Espace Médical') — Claims Master</title>

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
                            DEFAULT: '#be123c', /* rose-700 */
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            600: '#e11d48',
                            700: '#be123c',
                            800: '#9f1239',
                            900: '#881337',
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

        body {
            background-color: #f8fafc;
        }

        #sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #be123c 0%, #881337 100%);
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
            background: rgba(225, 29, 72, 0.2);
            border-left: 3px solid #f43f5e;
        }

        .nav-item .nav-icon {
            min-width: 20px;
            text-align: center;
        }

        #main-content {
            flex: 1;
            overflow-y: auto;
        }

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        ::-webkit-scrollbar-thumb {
            background: #be123c44;
            border-radius: 10px;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
    </style>
    @stack('styles')
</head>

<body class="flex h-screen overflow-hidden">

    @include('hopital.layouts.sidebar')

    <div id="main-content" class="flex flex-col flex-1 overflow-hidden">
        @include('hopital.layouts.navbar')

        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
        const toggleBtn = document.getElementById('toggle-sidebar');
        const sidebar = document.getElementById('sidebar');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => { sidebar.classList.toggle('collapsed'); });
        }
    </script>
</body>

</html>
