{{-- ══════════ NAVBAR ══════════ --}}
<nav class="navbar" id="navbar">
    <a href="{{ route('home') }}" class="navbar-brand">
        <div class="logo-icon"><i class="fa-solid fa-shield-halved"></i></div>
        <span class="logo-text">Claims<span>Master</span></span>
    </a>

    {{-- Bouton Hamburger (Visible sur mobile) --}}
    <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menu">
        <i class="fa-solid fa-bars"></i>
    </button>

    {{-- Conteneur du Menu (Desktop + Mobile) --}}
    <div class="nav-menu" id="navMenu">
        <div class="navbar-links">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a>
            <a href="{{ route('home.services') }}" class="{{ request()->routeIs('home.services') ? 'active' : '' }}">Nos
                services</a>
            <a href="{{ route('home.comment') }}"
                class="{{ request()->routeIs('home.comment') ? 'active' : '' }}">Comment ça marche</a>
            <a href="{{ route('home.securite') }}"
                class="{{ request()->routeIs('home.securite') ? 'active' : '' }}">Sécurité</a>
            <a href="{{ route('home.contact') }}"
                class="{{ request()->routeIs('home.contact') ? 'active' : '' }}">Contact</a>
        </div>

        <div class="navbar-cta">
            <a href="{{ route('login') }}" class="btn-outline">Se connecter</a>
            <a href="{{ route('assure.register.form') }}" class="btn-primary">
                <i class="fa-solid fa-user-plus" style="font-size:12px;"></i> S'inscrire
            </a>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('mobileMenuBtn');
        const menu = document.getElementById('navMenu');
        if (btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('active');
                const i = btn.querySelector('i');
                if (menu.classList.contains('active')) {
                    i.classList.remove('fa-bars');
                    i.classList.add('fa-xmark');
                } else {
                    i.classList.remove('fa-xmark');
                    i.classList.add('fa-bars');
                }
            });
        }
    });
</script>