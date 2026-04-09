<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Claims Master — Gestion des sinistres')</title>
    <meta name="description"
        content="@yield('description', 'Plateforme digitale de gestion des déclarations de sinistres. Rapide, sécurisé, accessible 24h/24.')">

    {{-- Fonts & Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    {{-- CSS global du layout public --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- CSS spécifiques à chaque page --}}
    @stack('styles')
</head>

<body>

    {{-- ══════════ NAVBAR ══════════ --}}
    @include('layouts.partials.navbar')

    {{-- ══════════ CONTENU ══════════ --}}
    @yield('content')

    {{-- ══════════ FOOTER ══════════ --}}
    @include('layouts.partials.footer')

    {{-- ══════════ SCRIPTS COMMUNS ══════════ --}}
    <script>
        // Navbar scroll shadow
        window.addEventListener('scroll', () => {
            document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 10);
        });
        // Intersection Observer fade-in
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
        }, { threshold: 0.15 });
        document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
    </script>
    @stack('scripts')

</body>

</html>